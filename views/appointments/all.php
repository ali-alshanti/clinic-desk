<?php
$pageTitle = 'All Appointments';
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('admin');
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';

$badgeMap = ['pending' => 'warning', 'confirmed' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">All Appointments</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item active">Appointments</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <!-- Filter bar -->
            <div class="card card-outline card-secondary mb-3">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="form-inline flex-wrap gap-2">
                        <input type="hidden" name="page" value="appointments">
                        <input type="hidden" name="action" value="all">

                        <select name="status" class="form-control form-control-sm mr-2 mb-2">
                            <option value="">All Statuses</option>
                            <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>>
                                    <?= ucfirst($s) ?>
                                </option>
                            <?php endforeach ?>
                        </select>

                        <select name="doctor_id" class="form-control form-control-sm mr-2 mb-2">
                            <option value="">All Doctors</option>
                            <?php foreach ($doctors as $doc): ?>
                                <option value="<?= $doc['id'] ?>"
                                    <?= (int) ($_GET['doctor_id'] ?? 0) === (int) $doc['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($doc['name']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>

                        <input type="text" name="patient_name" class="form-control form-control-sm mr-2 mb-2"
                               placeholder="Patient name"
                               value="<?= htmlspecialchars($_GET['patient_name'] ?? '') ?>">

                        <input type="date" name="start_date" class="form-control form-control-sm mr-2 mb-2"
                               value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                        <input type="date" name="end_date" class="form-control form-control-sm mr-2 mb-2"
                               value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">

                        <button type="submit" class="btn btn-sm btn-primary mb-2">
                            <i class="fas fa-search mr-1"></i> Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Appointments table -->
            <div class="card">
                <div class="card-header"><h3 class="card-title">Appointments</h3></div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr><td colspan="7" class="text-center">No appointments found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $appt): ?>
                                    <?php $badge = $badgeMap[$appt['status']] ?? 'secondary' ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appt['patient_name']) ?></td>
                                        <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($appt['specialization_name']) ?></td>
                                        <td><?= formatDate($appt['appt_date']) ?></td>
                                        <td><?= formatTime($appt['appt_time']) ?></td>
                                        <td><span class="badge badge-<?= $badge ?>"><?= ucfirst($appt['status']) ?></span></td>
                                        <td>
                                            <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=adminUpdate&id=<?= $appt['id'] ?>"
                                                  class="form-inline">
                                                <?= csrfField() ?>
                                                <select name="status" class="form-control form-control-sm mr-1">
                                                    <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $sv): ?>
                                                        <option value="<?= $sv ?>" <?= $appt['status'] === $sv ? 'selected' : '' ?>>
                                                            <?= ucfirst($sv) ?>
                                                        </option>
                                                    <?php endforeach ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($paginator->totalPages() > 1): ?>
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <?php if ($paginator->hasPrev()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=appointments&action=all&p=<?= $paginator->currentPage() - 1 ?>&status=<?= htmlspecialchars($_GET['status'] ?? '') ?>">«</a>
                                </li>
                            <?php endif ?>
                            <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                                <li class="page-item <?= $i === $paginator->currentPage() ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=appointments&action=all&p=<?= $i ?>&status=<?= htmlspecialchars($_GET['status'] ?? '') ?>"><?= $i ?></a>
                                </li>
                            <?php endfor ?>
                            <?php if ($paginator->hasNext()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=appointments&action=all&p=<?= $paginator->currentPage() + 1 ?>&status=<?= htmlspecialchars($_GET['status'] ?? '') ?>">»</a>
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
