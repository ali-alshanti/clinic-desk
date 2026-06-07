<?php
$pageTitle = 'Patient Dashboard';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('patient');

$activeCount       = $activeCount       ?? 0;
$completedCount    = $completedCount    ?? 0;
$prescriptionCount = $prescriptionCount ?? 0;
$nextAppointment   = $nextAppointment   ?? null;
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Patient Dashboard</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <!-- Next upcoming appointment -->
            <?php if ($nextAppointment): ?>
                <div class="card card-primary card-outline mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-check mr-2"></i>Next Appointment</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Doctor:</strong><br>
                                <?= htmlspecialchars($nextAppointment['doctor_name']) ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Specialization:</strong><br>
                                <?= htmlspecialchars($nextAppointment['specialization_name']) ?>
                            </div>
                            <div class="col-md-2">
                                <strong>Date:</strong><br>
                                <?= formatDate($nextAppointment['appt_date']) ?>
                            </div>
                            <div class="col-md-2">
                                <strong>Time:</strong><br>
                                <?= formatTime($nextAppointment['appt_time']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-4">
                    You have no upcoming appointments.
                    <a href="<?= BASE_URL ?>/index.php?page=appointments&action=book" class="alert-link ml-2">Book one now</a>
                </div>
            <?php endif ?>

            <!-- Stats small-boxes -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= (int) $activeCount ?></h3>
                            <p>Active Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=my" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= (int) $completedCount ?></h3>
                            <p>Completed Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=my&status=completed" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= (int) $prescriptionCount ?></h3>
                            <p>Prescriptions</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-medical"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=my" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
