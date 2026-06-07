<?php
$pageTitle = 'My Prescriptions';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../models/BaseModel.php';
require_once __DIR__ . '/../../models/PrescriptionModel.php';
Auth::requireRole('patient', 'doctor');

$prescriptionModel = new PrescriptionModel();
$prescriptions     = $prescriptionModel->getByPatient(Auth::currentUser()['id']);
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">My Prescriptions</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item active">My Prescriptions</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Prescriptions</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Diagnosis</th>
                                <th>Download PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($prescriptions)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No prescriptions found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($prescriptions as $presc): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($presc['doctor_name']) ?></td>
                                        <td><?= formatDate($presc['appt_date']) ?></td>
                                        <td>
                                            <?php
                                            $diag = htmlspecialchars($presc['diagnosis'] ?? '');
                                            echo mb_strlen($diag) > 50 ? mb_substr($diag, 0, 50) . '…' : $diag;
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($presc['file_path'])): ?>
                                                <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=download&id=<?= (int) $presc['id'] ?>"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-file-download mr-1"></i> Download
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">No file</span>
                                            <?php endif ?>
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
