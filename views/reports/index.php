<?php
$pageTitle = 'Reports';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../models/BaseModel.php';
require_once __DIR__ . '/../../models/DoctorModel.php';
Auth::requireRole('admin');

$doctors = $doctors ?? [];
$rows    = $rows    ?? [];
$summary = $summary ?? null;

$badgeMap = ['pending' => 'warning', 'confirmed' => 'info', 'completed' => 'success', 'cancelled' => 'danger'];
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Reports</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item active">Reports</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <!-- Filter form -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Generate Report</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>/index.php" class="form-inline flex-wrap">
                        <input type="hidden" name="page" value="reports">
                        <input type="hidden" name="action" value="generate">

                        <div class="form-group mr-2 mb-2">
                            <label class="mr-1">From</label>
                            <input type="date" name="start_date" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" required>
                        </div>

                        <div class="form-group mr-2 mb-2">
                            <label class="mr-1">To</label>
                            <input type="date" name="end_date" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" required>
                        </div>

                        <div class="form-group mr-2 mb-2">
                            <select name="doctor_id" class="form-control form-control-sm">
                                <option value="">All Doctors</option>
                                <?php foreach ($doctors as $doc): ?>
                                    <option value="<?= (int) $doc['id'] ?>"
                                        <?= (int) ($_GET['doctor_id'] ?? 0) === (int) $doc['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($doc['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

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

                        <button type="submit" class="btn btn-sm btn-primary mr-2 mb-2">
                            <i class="fas fa-chart-bar mr-1"></i> Generate
                        </button>
                        <button type="submit" name="export" value="csv" class="btn btn-sm btn-success mb-2">
                            <i class="fas fa-file-csv mr-1"></i> Export CSV
                        </button>
                    </form>
                </div>
            </div>

            <!-- Results table -->
            <?php if (!empty($rows)): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Results
                            <small class="text-muted ml-2">
                                <?= htmlspecialchars($_GET['start_date'] ?? '') ?> –
                                <?= htmlspecialchars($_GET['end_date'] ?? '') ?>
                            </small>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['patient_name']) ?></td>
                                        <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                        <td><?= htmlspecialchars($row['specialization_name']) ?></td>
                                        <td><?= formatDate($row['appt_date']) ?></td>
                                        <td><?= formatTime($row['appt_time']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $badgeMap[$row['status']] ?? 'secondary' ?>">
                                                <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row['reason'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                            <?php if ($summary): ?>
                                <tfoot class="font-weight-bold bg-light">
                                    <tr>
                                        <td colspan="2">Summary</td>
                                        <td>Total: <?= (int) ($summary['total'] ?? 0) ?></td>
                                        <td>Pending: <?= (int) ($summary['pending'] ?? 0) ?></td>
                                        <td>Confirmed: <?= (int) ($summary['confirmed'] ?? 0) ?></td>
                                        <td>Completed: <?= (int) ($summary['completed'] ?? 0) ?></td>
                                        <td>Cancelled: <?= (int) ($summary['cancelled'] ?? 0) ?></td>
                                    </tr>
                                </tfoot>
                            <?php endif ?>
                        </table>
                    </div>
                </div>
            <?php elseif (isset($_GET['action']) && $_GET['action'] === 'generate'): ?>
                <div class="alert alert-info">No results found for the selected filters.</div>
            <?php endif ?>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
