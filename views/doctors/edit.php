<?php
$pageTitle = 'Edit Doctor';
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('admin');
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';

$days        = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
$checkedDays = $doctor['available_days'] ? explode(',', $doctor['available_days']) : [];
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Edit Doctor</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=doctors">Doctors</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="card card-warning">
                <div class="card-header"><h3 class="card-title">Edit Doctor: <?= htmlspecialchars($doctor['name']) ?></h3></div>
                <form method="POST"
                      action="<?= BASE_URL ?>/index.php?page=doctors&action=update&id=<?= $doctor['id'] ?>"
                      enctype="multipart/form-data">
                    <?= csrfField() ?>

                    <div class="card-body">

                        <div class="form-group">
                            <label>Specialization</label>
                            <select name="specialization_id" class="form-control" required>
                                <option value="">-- Select --</option>
                                <?php foreach ($specializations as $spec): ?>
                                    <option value="<?= $spec['id'] ?>"
                                        <?= (int) $spec['id'] === (int) $doctor['specialization_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($spec['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($doctor['bio'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Consultation Fee ($)</label>
                            <input type="number" name="consultation_fee" class="form-control"
                                   min="0" step="0.01" value="<?= htmlspecialchars($doctor['consultation_fee']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label><br>
                            <?php foreach ($days as $day): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           name="available_days[]" value="<?= $day ?>" id="day_<?= $day ?>"
                                           <?= in_array($day, $checkedDays, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="day_<?= $day ?>"><?= $day ?></label>
                                </div>
                            <?php endforeach ?>
                        </div>

                        <div class="form-group">
                            <label>Profile Photo</label>
                            <?php if (!empty($doctor['avatar'])): ?>
                                <div class="mb-2">
                                    <img src="<?= BASE_URL ?>/<?= UPLOAD_PATH_DOCTORS . htmlspecialchars($doctor['avatar']) ?>"
                                         alt="Current photo" style="height:80px; border-radius:4px;">
                                    <small class="text-muted ml-2">Current photo</small>
                                </div>
                            <?php endif ?>
                            <input type="file" name="photo" class="form-control-file" accept="image/jpeg,image/png">
                            <small class="text-muted">Leave empty to keep current photo. JPEG or PNG, max 1MB.</small>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=doctors" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
