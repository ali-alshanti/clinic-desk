<?php
$pageTitle = 'Add Prescription';
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('doctor');
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Add Prescription</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=appointments&action=schedule">Schedule</a></li>
                <li class="breadcrumb-item active">Add Prescription</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <!-- Appointment info -->
            <div class="callout callout-info">
                <h5>Appointment Details</h5>
                <strong>Patient:</strong> <?= htmlspecialchars($appointment['patient_name']) ?> &nbsp;|&nbsp;
                <strong>Date:</strong> <?= formatDate($appointment['appt_date']) ?> &nbsp;|&nbsp;
                <strong>Time:</strong> <?= formatTime($appointment['appt_time']) ?>
            </div>

            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">Prescription Details</h3></div>
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=prescriptions&action=store"
                      enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <input type="hidden" name="appointment_id" value="<?= (int) $appointment['id'] ?>">

                    <div class="card-body">

                        <div class="form-group">
                            <label>Diagnosis <span class="text-danger">*</span></label>
                            <textarea name="diagnosis" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Medications <span class="text-danger">*</span></label>
                            <textarea name="medications" class="form-control" rows="4" required
                                      placeholder="One medication per line"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Attach PDF</label>
                            <input type="file" name="pdf" class="form-control-file" accept="application/pdf">
                            <small class="text-muted">Optional. PDF only, max 3MB.</small>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Save
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=schedule" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
