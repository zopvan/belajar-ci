<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="card">
    <div class="card-body pt-3">
        <div class="tab-content pt-2">
            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                <h5 class="card-title">
                    Profile Information
                </h5>

                <div class="row">
                    <div class="col-lg-3 col-md-4 label">
                        Username
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <?php echo $username;
                        echo ' (' . $role . ')' ?>

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-4 label">
                        Email
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <?php echo $email ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-4 label">
                        Login Time
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <?php echo $loginTime ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-4 label">
                        Status
                    </div>
                    <div class="col-lg-9 col-md-8">
                        aku login cihuy
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>