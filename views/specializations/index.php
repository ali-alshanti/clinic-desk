<?php
$pageTitle = 'Specializations';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';

Auth::requireRole('admin');

$specializations = $specializations ?? [];
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Specializations</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item active">Specializations</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="row">

                <!-- Specializations table -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Specializations</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($specializations)): ?>
                                        <tr><td colspan="3" class="text-center text-muted py-3">No specializations found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($specializations as $spec): ?>
                                            <tr>
                                                <td><?= (int) $spec['id'] ?></td>
                                                <td><?= htmlspecialchars($spec['name']) ?></td>
                                                <td>
                                                    <form method="POST"
                                                          action="<?= BASE_URL ?>/index.php?page=specializations&action=delete&id=<?= (int) $spec['id'] ?>"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Delete this specialization?')">
                                                        <?= csrfField() ?>
                                                        <button type="submit" class="btn btn-xs btn-danger">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add specialization form -->
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add Specialization</h3>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>/index.php?page=specializations&action=store">
                            <?= csrfField() ?>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                           placeholder="e.g. Cardiology" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-plus mr-1"></i> Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php' ?>
