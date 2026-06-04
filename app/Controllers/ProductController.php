<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\ProductModel;

class ProductController extends BaseController
{

    protected $productModel;
    function __construct()
    {
        helper('form');
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        return view('product/index', [
            'products' => $this->productModel->findAll()
        ]);
    }

    public function create()
    {
        $dataFoto = $this->request->getFile('foto');

        $dataForm = [
            'nama' => $this->request->getPost('nama'),
            'harga' => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah')
        ];

        if ($dataFoto->isValid()) {
            $fileName = $dataFoto->getRandomName();
            $dataFoto->move('img/', $fileName);

            $dataForm['foto'] = $fileName;
        }

        $this->productModel->insert($dataForm);

        return redirect('product')->with('success', 'Data Berhasil Ditambah');
    }
    public function edit($id)
    {
        $dataProduct = $this->productModel->find($id);

        $dataForm = [
            'nama' => $this->request->getPost('nama'),
            'harga' => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah')
        ];

        if ($this->request->getPost('check') == 1) {
            if ($dataProduct['foto'] != '' and file_exists("img/" . $dataProduct['foto'] . "")) {
                unlink("img/" . $dataProduct['foto']);
            }

            $dataFoto = $this->request->getFile('foto');

            if ($dataFoto->isValid()) {
                $fileName = $dataFoto->getRandomName();
                $dataFoto->move('img/', $fileName);

                $dataForm['foto'] = $fileName;
            }
        }

        $this->productModel->update($id, $dataForm);

        return redirect('product')->with('success', 'Data Berhasil Diubah');
    }

    public function delete($id)
    {
        $dataProduct = $this->productModel->find($id);
        $this->productModel->delete($id);

        return redirect('product')->with('success', 'Data Berhasil Dihapus');
    }
}
    