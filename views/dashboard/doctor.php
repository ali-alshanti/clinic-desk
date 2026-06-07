<?php
$pageTitle = 'Doctor Dashboard';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../models/BaseModel.php';
require_once __DIR__ . '/../../models/DoctorModel.php';
require_once __DIR__ . '/../../models/AppointmentModel.php';
Auth::requireRole('doctor');

$monthStats           = $monthStats           ?? ['total' => 0, 'pending' => 0, 'completed' => 0];
$todayAppointments    = $todayAppointments    ?? [];
$upcomingAppointments = $upcomingAppointments ?? [];

$badgeMap = ['pending' => 'warning', 'confirmed' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Doctor Dashboard</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= (int) ($monthStats['total'] ?? 0) ?></h3>
                            <p>Appointments This Month</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=schedule" class="small-box-footer">
                            View Schedule <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= (int) ($monthStats['pending'] ?? 0) ?></h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=schedule&status=pending" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= (int) ($monthStats['completed'] ?? 0) ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=schedule&status=completed" class="small-box-footer">
                            View <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-day mr-2"></i>Today's Appointments</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Phone</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($todayAppointments)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">No appointments today.</td></tr>
                            <?php else: ?>
                                <?php foreach ($todayAppointments as $appt): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                                        <td><?= htmlspecialchars($appt['patient_phone'] ?? '-') ?></td>
                                        <td><?= formatTime($appt['appt_time']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $badgeMap[$appt['status']] ?? 'secondary' ?>">
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-check mr-2"></i>Upcoming Appointments</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($upcomingAppointments)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">No upcoming appointments.</td></tr>
                            <?php else: ?>
                                <?php foreach ($upcomingAppointments as $appt): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                                        <td><?= formatDate($appt['appt_date']) ?></td>
                                        <td><?= formatTime($appt['appt_time']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $badgeMap[$appt['status']] ?? 'secondary' ?>">
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
