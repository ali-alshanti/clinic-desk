<?php
$pageTitle = 'Add Doctor Profile';
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('admin');
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';

$days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Add Doctor Profile</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=doctors">Doctors</a></li>
                <li class="breadcrumb-item active">Add</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">Doctor Details</h3></div>
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=doctors&action=store"
                      enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <input type="hidden" name="user_id" value="<?= (int) $userId ?>">

                    <div class="card-body">

                        <div class="form-group">
                            <label>Specialization</label>
                            <select name="specialization_id" class="form-control" required>
                                <option value="">-- Select --</option>
                                <?php foreach ($specializations as $spec): ?>
                                    <option value="<?= $spec['id'] ?>">
                                        <?= htmlspecialchars($spec['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Consultation Fee ($)</label>
                            <input type="number" name="consultation_fee" class="form-control"
                                   min="0" step="0.01" value="0" required>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label><br>
                            <?php foreach ($days as $day): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           name="available_days[]" value="<?= $day ?>" id="day_<?= $day ?>">
                                    <label class="form-check-label" for="day_<?= $day ?>"><?= $day ?></label>
                                </div>
                            <?php endforeach ?>
                        </div>

                        <div class="form-group">
                            <label>Profile Photo</label>
                            <input type="file" name="photo" class="form-control-file" accept="image/jpeg,image/png">
                            <small class="text-muted">JPEG or PNG, max 1MB</small>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Save
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=doctors" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
