<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin');

$userStats          = $userStats          ?? [];
$todayCount         = $todayCount         ?? 0;
$weekStats          = $weekStats          ?? [];
$recentAppointments = $recentAppointments ?? [];

$badgeMap = ['pending' => 'warning', 'confirmed' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Admin Dashboard</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <!-- User stats small-boxes -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= (int) ($userStats['admin'] ?? 0) ?></h3>
                            <p>Admins</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-shield"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=users&role=admin" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= (int) ($userStats['doctor'] ?? 0) ?></h3>
                            <p>Doctors</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=doctors" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= (int) ($userStats['patient'] ?? 0) ?></h3>
                            <p>Patients</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=users&role=patient" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= (int) $todayCount ?></h3>
                            <p>Appointments Today</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=all" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Week stats info-boxes -->
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending This Week</span>
                            <span class="info-box-number"><?= (int) ($weekStats['pending'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Confirmed This Week</span>
                            <span class="info-box-number"><?= (int) ($weekStats['confirmed'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-double"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Completed This Week</span>
                            <span class="info-box-number"><?= (int) ($weekStats['completed'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Cancelled This Week</span>
                            <span class="info-box-number"><?= (int) ($weekStats['cancelled'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent appointments -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Appointments</h3>
                    <div class="card-tools">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=all" class="btn btn-sm btn-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentAppointments)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No appointments found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentAppointments as $appt): ?>
                                    <?php $badge = $badgeMap[$appt['status']] ?? 'secondary' ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                                        <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
                                        <td><?= formatDate($appt['appt_date']) ?></td>
                                        <td><?= formatTime($appt['appt_time']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $badge ?>">
                                                <?= ucfirst($appt['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
