<?php
$pageTitle = 'My Schedule';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../models/BaseModel.php';
require_once __DIR__ . '/../../models/DoctorModel.php';
require_once __DIR__ . '/../../models/AppointmentModel.php';
Auth::requireRole('doctor');

$todayAppointments = $todayAppointments ?? [];
$appointments      = $appointments      ?? [];
$paginator         = $paginator         ?? null;
$badgeMap = ['pending' => 'warning', 'confirmed' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">My Schedule</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item active">My Schedule</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <!-- Today's appointments -->
            <div class="card card-success">
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

            <!-- Filter bar -->
            <div class="card card-outline card-secondary">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="form-inline flex-wrap">
                        <input type="hidden" name="page" value="appointments">
                        <input type="hidden" name="action" value="schedule">

                        <div class="form-group mr-2 mb-2">
                            <select name="status" class="form-control form-control-sm">
                                <option value="">All Statuses</option>
                                <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>>
                                        <?= ucfirst($s) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group mr-2 mb-2">
                            <input type="date" name="start_date" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                        </div>

                        <div class="form-group mr-2 mb-2">
                            <input type="date" name="end_date" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary mb-2">
                            <i class="fas fa-search mr-1"></i> Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- All appointments -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Appointments</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr><td colspan="7" class="text-center text-muted py-3">No appointments.</td></tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $appt): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                                        <td><?= formatDate($appt['appt_date']) ?></td>
                                        <td><?= formatTime($appt['appt_time']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $badgeMap[$appt['status']] ?? 'secondary' ?>">
                                                <?= ucfirst($appt['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($appt['reason'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($appt['doctor_notes'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($appt['status'] === 'pending'): ?>
                                                <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=updateStatus&id=<?= (int) $appt['id'] ?>" class="d-inline">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <button type="submit" class="btn btn-xs btn-info">Confirm</button>
                                                </form>
                                                <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=updateStatus&id=<?= (int) $appt['id'] ?>" class="d-inline">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-xs btn-danger">Cancel</button>
                                                </form>

                                            <?php elseif ($appt['status'] === 'confirmed'): ?>
                                                <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=updateStatus&id=<?= (int) $appt['id'] ?>">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="status" value="completed">
                                                    <textarea name="doctor_notes" class="form-control form-control-sm mb-1" rows="1"
                                                              placeholder="Notes (optional)"><?= htmlspecialchars($appt['doctor_notes'] ?? '') ?></textarea>
                                                    <button type="submit" class="btn btn-xs btn-success">Complete</button>
                                                </form>
                                                <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=updateStatus&id=<?= (int) $appt['id'] ?>" class="d-inline">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-xs btn-danger">Cancel</button>
                                                </form>

                                            <?php elseif ($appt['status'] === 'completed'): ?>
                                                <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=add&id=<?= (int) $appt['id'] ?>"
                                                   class="btn btn-xs btn-primary">
                                                    <i class="fas fa-file-medical"></i> Prescription
                                                </a>

                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($paginator && $paginator->totalPages() > 1): ?>
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <?php if ($paginator->hasPrev()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=appointments&action=schedule&p=<?= $paginator->currentPage() - 1 ?>">«</a>
                                </li>
                            <?php endif ?>
                            <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                                <li class="page-item <?= $i === $paginator->currentPage() ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=appointments&action=schedule&p=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor ?>
                            <?php if ($paginator->hasNext()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=appointments&action=schedule&p=<?= $paginator->currentPage() + 1 ?>">»</a>
                                </li>
                            <?php endif ?>
                        </ul>
                    </div>
                <?php endif ?>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
