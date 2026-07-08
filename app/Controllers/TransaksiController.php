<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\RajaOngkirService;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransaksiController extends BaseController
{
    protected $cart;
    protected $transactionModel;
    protected $transactionDetailModel;

    public function __construct()
    {
        helper(['number', 'form']);
        $this->cart = service('cart');
        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
    }

    public function index()
    {
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total()
        ];

        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
        $this->cart->insert([
            'id'      => $this->request->getPost('id'),
            'qty'     => 1,
            'price'   => $this->request->getPost('harga'),
            'name'    => $this->request->getPost('nama'),
            'options' => [
                'foto' => $this->request->getPost('foto')
            ]
        ]);

        session()->setFlashdata(
            'success',
            'Produk berhasil ditambahkan ke keranjang. 
        <a href="' . base_url('keranjang') . '">Lihat</a>'
        );

        return redirect()->to(base_url('/'));
    }

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $item) {
            $qty = $this->request->getPost('qty' . $i++);

            $this->cart->update([
                'rowid' => $item['rowid'],
                'qty'   => $qty
            ]);
        }

        session()->setFlashdata(
            'success',
            'Keranjang berhasil diperbarui'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);

        session()->setFlashdata(
            'success',
            'Produk berhasil dihapus dari keranjang'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();

        session()->setFlashdata(
            'success',
            'Keranjang berhasil dikosongkan'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function checkout()
    {
        // Hanya mengirimkan data keranjang ke view
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total(),
        ];

        return view('v_checkout', $data);
    }

    public function destinations()
    {
        $search = $this->request->getGet('q');

        $service = new RajaOngkirService();
        $response = $service->getDestination($search);

        $results = [];
        $data = $response['data'] ?? [];

        foreach ($data as $item) {
            $results[] = [
                'id'   => $item['id'],
                'text' => $item['label']
            ];
        }

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    public function costs()
    {
        $origin = '64999';
        $destination = $this->request->getGet('destination');
        $weight = '1000';
        $courier = 'jne';

        $service = new RajaOngkirService();
        $response = $service->getCost($origin, $destination, $weight, $courier);

        $results = [];
        $data = $response['data'] ?? [];

        foreach ($data as $item) {
            $results[] = [
                'service'     => $item['service'],
                'description' => $item['description'],
                'cost'        => $item['cost'],
                'etd'         => $item['etd']
            ];
        }

        return $this->response->setJSON($results);
    }

    public function buy()
    {
        $cartItems = $this->cart->contents();

        if (empty($cartItems)) {
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Ambil subtotal murni dari keranjang
        $subtotal = $this->cart->total();
        
        // 2. Tangkap input form
        $ongkir = (int) $this->request->getPost('ongkir');
        $voucher_code = $this->request->getPost('voucher_code');

        // 3. Proses Kalkulasi Promo Akhir Tahun
        $biaya_jasa = $this->hitung_biaya_jasa($subtotal);
        $diskon_voucher = $this->hitung_diskon_voucher($subtotal, $voucher_code);
        $free_mouse = $this->hitung_free_mouse($subtotal);

        // 4. Hitung Grand Total
        $subtotal_baru = $subtotal - $diskon_voucher + $biaya_jasa - $free_mouse;
        $grand_total = $subtotal_baru + $ongkir;

        // 5. Masukkan ke array transaksi beserta field baru
        $transaction = [
            'username'       => $this->request->getPost('username'),
            'alamat'         => $this->request->getPost('alamat'),
            'ongkir'         => $ongkir,
            'total_harga'    => $grand_total,
            'status'         => 0,
            'biaya_jasa'     => $biaya_jasa,
            'voucher_code'   => $voucher_code,
            'diskon_voucher' => $diskon_voucher,
            'free_mouse'     => $free_mouse,
        ];

        // insert transaction
        if (!$this->transactionModel->insert($transaction)) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal membuat transaksi');
        }

        $transactionId = $this->transactionModel->getInsertID();

        // insert transaction detail
        foreach ($cartItems as $item) {
            $this->transactionDetailModel->insert([
                'transaction_id' => $transactionId,
                'product_id'     => $item['id'],
                'jumlah'         => $item['qty'],
                'diskon'         => 0,
                'subtotal_harga' => $item['qty'] * $item['price']
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal membuat transaksi');
        }

        //hapus session keranjang belanja 
        $this->cart->destroy();
        return redirect()->to(base_url());
    }

    public function history()
    {
        $username = session()->get('username');

        $transactions = $this->transactionModel->where('username', $username)->findAll();
        $transactionIds = array_column($transactions, 'id');

        $products = $this->transactionDetailModel->getProductsByTransactionIds($transactionIds);

        $data = [
            'username'      => $username,
            'transactions'  => $transactions,
            'products'      => $products
        ];

        return view('v_history', $data);
    }

    private function hitung_biaya_jasa($total_harga)
    {
        if ($total_harga <= 10000000) {
            return $total_harga * 0.01;
        }
        return $total_harga * 0.02;
    }

    private function hitung_diskon_voucher($total_harga, $voucher_code)
    {
        $voucher_code = strtoupper(trim($voucher_code ?? ''));
        if ($voucher_code === 'PROMO2025') return $total_harga * 0.10;
        if ($voucher_code === 'PROMO2026') return $total_harga * 0.15;
        if ($voucher_code === 'AKHIRTAHUN') return $total_harga * 0.25;
        return 0;
    }

    private function hitung_free_mouse($total_harga)
    {
        if ($total_harga >= 15000000) {
            return 150000;
        }
        return 0;
    }
}