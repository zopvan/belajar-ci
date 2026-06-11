<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<!-- Table with stripped rows -->
<?php
if (session()->getFlashData('success')) {
?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
}
?>
<?php
if (session()->getFlashData('failed')) {
?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashData('failed') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
}
?>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    Tambah Data
</button>
<a class="btn btn-success" target="_blank" href="<?= base_url() ?>product/download">
    Download Data
</a>
<table class="table datatable">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nama</th>
            <th scope="col">Harga</th>
            <th scope="col">Jumlah</th>
            <th scope="col">Foto</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $index => $product) : ?>
            <tr>
                <th scope="row"><?php echo $index + 1 ?></th>
                <td><?php echo $product['nama'] ?></td>
                <td><?php echo $product['harga'] ?></td>
                <td><?php echo $product['jumlah'] ?></td>
                <td>
                    <?php if ($product['foto'] != '' and file_exists("img/" . $product['foto'] . "")) : ?>
                        <img src="<?php echo base_url() . "img/" . $product['foto'] ?>" width="100">
                    <?php endif; ?>
                </td>
                <td>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal-<?= $product['id'] ?>">
                        Ubah
                    </button>
                    <a href="<?= base_url('product/delete/' . $product['id']) ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus data ini ?')">
                        Hapus
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<!-- End Table with stripped rows -->
<?= $this->include('product/modal_add') ?>
<?= $this->include('product/modal_edit') ?>
<?= $this->endSection() ?>