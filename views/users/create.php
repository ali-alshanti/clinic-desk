<?php
$pageTitle = 'Create User';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';

Auth::requireRole('admin');
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Create User</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=users">Users</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New User Details</h3>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=users&action=store">
                    <?= csrfField() ?>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">-- Select Role --</option>
                                <?php foreach (['admin', 'doctor', 'patient'] as $r): ?>
                                    <option value="<?= $r ?>" <?= ($_POST['role'] ?? '') === $r ? 'selected' : '' ?>>
                                        <?= ucfirst($r) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Create User
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?page=users" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
