<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="card">
    <div class="card-body pt-3">
        <div class="tab-content pt-2">
            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                <h5 class="card-title" style="color: #012970; font-weight: 500;">
                    Profile Information
                </h5>

                <div class="row mb-2">
                    <div class="col-lg-3 col-md-4 label text-primary fw-bold" style="color: #4154f1 !important;">
                        Username
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <?= $username; ?> 
                        <span class="badge bg-danger"><?= $role; ?></span>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3 col-md-4 label text-primary fw-bold" style="color: #4154f1 !important;">
                        Email
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <a href="mailto:<?= $email; ?>" class="text-primary"><?= $email; ?></a>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3 col-md-4 label text-primary fw-bold" style="color: #4154f1 !important;">
                        Login Time
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <?= $loginTime; ?>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-lg-3 col-md-4 label text-primary fw-bold" style="color: #4154f1 !important;">
                        Status
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle"></i> Sudah Login
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>