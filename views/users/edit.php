<?php
$pageTitle = 'Edit User';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';

Auth::requireRole('admin');

$user = $user ?? [];
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Edit User</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=users">Users</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Edit User: <?= htmlspecialchars($user['name'] ?? '') ?></h3>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=users&action=update&id=<?= (int) ($user['id'] ?? 0) ?>">
                    <?= csrfField() ?>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="<?= htmlspecialchars($_POST['name'] ?? $user['name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? $user['phone'] ?? '') ?>">
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Save Changes
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=users" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
