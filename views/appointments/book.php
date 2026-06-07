<?php
$pageTitle = 'Book Appointment';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../models/BaseModel.php';
require_once __DIR__ . '/../../models/DoctorModel.php';

Auth::requireRole('patient');

$doctorModel = new DoctorModel();
$doctors = $doctorModel->getAll();

$timeSlots = [];
for ($h = 9; $h <= 15; $h++) {
    $timeSlots[] = sprintf('%02d:00', $h);
    $timeSlots[] = sprintf('%02d:30', $h);
}
$timeSlots[] = '16:00';
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Book Appointment</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item active">Book Appointment</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Appointment Details</h3>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=appointments&action=store">
                    <?= csrfField() ?>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="doctor_id">Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="form-control" required>
                                <option value="">-- Select a Doctor --</option>
                                <?php foreach ($doctors as $doc): ?>
                                    <option value="<?= (int) $doc['id'] ?>">
                                        <?= htmlspecialchars($doc['name']) ?> — <?= htmlspecialchars($doc['specialization_name']) ?> ($<?= number_format($doc['consultation_fee'], 2) ?>)
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="appt_date">Date</label>
                            <input type="date" name="appt_date" id="appt_date" class="form-control"
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="appt_time">Time</label>
                            <select name="appt_time" id="appt_time" class="form-control" required>
                                <option value="">-- Select a Time --</option>
                                <?php foreach ($timeSlots as $slot): ?>
                                    <option value="<?= $slot ?>"><?= $slot ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="reason">Reason for Visit</label>
                            <textarea name="reason" id="reason" class="form-control" rows="3"></textarea>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-plus mr-1"></i> Book Appointment
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=my" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
