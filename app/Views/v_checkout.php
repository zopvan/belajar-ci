<?php

/**
 * @var int|float $total
 * @var array $items
 */
?>
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-6">
        <?= form_open('buy', 'class="row g-3"') ?>

        <?= form_hidden('username', session()->get('username')) ?>

        <?= form_input([
            'type' => 'hidden',
            'name' => 'total_harga',
            'id' => 'total_harga'
        ]) ?>

        <div class="col-12">
            <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'     => 'nama',
                'id'       => 'nama',
                'class'    => 'form-control',
                'value'    => session()->get('username'),
                'readonly' => true
            ]) ?>
        </div>
        <div class="col-12">
            <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'  => 'alamat',
                'id'    => 'alamat',
                'class' => 'form-control'
            ]) ?>
        </div>
        <div class="col-12">
            <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>
            <?= form_dropdown('kelurahan', [], '', ['id' => 'kelurahan', 'class' => 'form-control']) ?>
        </div>
        <div class="col-12">
            <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?>
            <?= form_dropdown('layanan', [], '', ['id' => 'layanan', 'class' => 'form-control']) ?>
        </div>
        <div class="col-12">
            <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'     => 'ongkir',
                'id'       => 'ongkir',
                'class'    => 'form-control',
                'readonly' => true
            ]) ?>
        </div>

        <!-- TAMBAHAN: Input Kode Voucher -->
        <div class="col-12">
            <?= form_label('Kode Voucher', 'voucher_code', ['class' => 'form-label']) ?>
            <?= form_input([
                'name'  => 'voucher_code',
                'id'    => 'voucher_code',
                'class' => 'form-control',
                'placeholder' => 'Contoh: PROMO2026'
            ]) ?>
            <small class="text-muted">Tersedia: PROMO2025 (10%), PROMO2026 (15%), AKHIRTAHUN (25%)</small>
        </div>

        <div class="col-12">
            <?= form_submit(
                'submit',
                'Buat Pesanan',
                ['class' => 'btn btn-primary']
            ) ?>
        </div>

        <?= form_close() ?>
    </div>
    <div class="col-lg-6">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($items)) :
                    foreach ($items as $index => $item) :
                ?>
                        <tr>
                            <td><?= $item['name'] ?></td>
                            <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                            <td><?= $item['qty'] ?></td>
                            <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                        </tr>
                <?php
                    endforeach;
                endif;
                ?>
                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal</td>
                    <td><?= number_to_currency($total, 'IDR') ?></td>
                </tr>

                <!-- TAMBAHAN: Rincian Promo -->
                <tr>
                    <td colspan="2"></td>
                    <td class="text-danger">Diskon Voucher <span id="diskon_persen"></span></td>
                    <td class="text-danger"><span id="diskon_nominal">-IDR 0</span></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Biaya Jasa</td>
                    <td><span id="biaya_jasa">IDR 0</span></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-success">Free Mouse</td>
                    <td class="text-success"><span id="free_mouse">-IDR 0</span></td>
                </tr>
                <!-- Akhir Tambahan Promo -->

                <tr>
                    <td colspan="2"></td>
                    <td>Total (incl. Ongkir)</td>
                    <td><span id="total"><?= number_to_currency($total, 'IDR') ?></span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
    $(document).ready(function() {

        let ongkir = 0;
        let subtotal = <?= $total ?>;
        hitungTotal();

        function hitungTotal() {
            let voucher = $('#voucher_code').val();
            if (voucher) {
                voucher = voucher.toUpperCase();
            } else {
                voucher = '';
            }

            let biayaJasa = (subtotal <= 10000000) ? (subtotal * 0.01) : (subtotal * 0.02);

            let diskon = 0;
            let diskonPersen = 0;
            if (voucher === 'PROMO2025') {
                diskon = subtotal * 0.10;
                diskonPersen = 10;
            } else if (voucher === 'PROMO2026') {
                diskon = subtotal * 0.15;
                diskonPersen = 15;
            } else if (voucher === 'AKHIRTAHUN') {
                diskon = subtotal * 0.25;
                diskonPersen = 25;
            }

            let freeMouse = (subtotal >= 15000000) ? 150000 : 0;

            let subtotalBaru = subtotal - diskon + biayaJasa - freeMouse;
            let total = subtotalBaru + ongkir;

            $("#ongkir").val(ongkir);

            // Render text promo
            $("#diskon_persen").text(diskonPersen > 0 ? `(${diskonPersen}%)` : '');
            $("#diskon_nominal").text(`-IDR ${diskon.toLocaleString('id-ID')}`);
            $("#biaya_jasa").text(`IDR ${biayaJasa.toLocaleString('id-ID')}`);
            $("#free_mouse").text(`-IDR ${freeMouse.toLocaleString('id-ID')}`);

            $("#total").text(`IDR ${total.toLocaleString('id-ID')}`);
            $("#total_harga").val(total);
        }

        // Trigger fungsi saat user mengetik kode voucher
        $('#voucher_code').on('keyup', function() {
            hitungTotal();
        });

        $('#kelurahan').select2({
            placeholder: 'Cari daerah tujuan',
            minimumInputLength: 3,
            ajax: {
                url: '<?= site_url('ajax/destinations') ?>',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return data;
                },
                cache: true
            }
        });
        $("#kelurahan").on('change', function() {
            let id_kelurahan = $(this).val();

            $("#layanan").empty();
            ongkir = 0;
            hitungTotal();

            $.ajax({
                url: "<?= site_url('ajax/costs') ?>",
                dataType: "json",
                data: {
                    destination: id_kelurahan
                },
                success: function(data) {
                    data.forEach(function(item) {
                        $("#layanan").append(
                            $('<option>', {
                                value: item.cost,
                                text: `${item.description} (${item.service}) : estimasi ${item.etd}`
                            })
                        );
                    });
                }
            });
        });
        $("#layanan").on('change', function() {
            ongkir = parseInt($(this).val());
            hitungTotal();
        });
    });
</script>
<?= $this->endSection() ?>