<?php
$pageTitle = 'Users';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
require_once __DIR__ . '/../../core/helpers.php';

Auth::requireRole('admin');

$users     = $users     ?? [];
$paginator = $paginator ?? null;
$roleFilter = $_GET['role'] ?? '';
$currentUser = Auth::currentUser();
?>
<?php require __DIR__ . '/../partials/header.php' ?>
<?php require __DIR__ . '/../partials/navbar.php' ?>
<?php require __DIR__ . '/../partials/sidebar.php' ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Users</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <a href="<?= BASE_URL ?>/index.php?page=users" class="btn btn-sm <?= $roleFilter === '' ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
                        <a href="<?= BASE_URL ?>/index.php?page=users&role=admin" class="btn btn-sm <?= $roleFilter === 'admin' ? 'btn-primary' : 'btn-outline-primary' ?> ml-1">Admin</a>
                        <a href="<?= BASE_URL ?>/index.php?page=users&role=doctor" class="btn btn-sm <?= $roleFilter === 'doctor' ? 'btn-primary' : 'btn-outline-primary' ?> ml-1">Doctor</a>
                        <a href="<?= BASE_URL ?>/index.php?page=users&role=patient" class="btn btn-sm <?= $roleFilter === 'patient' ? 'btn-primary' : 'btn-outline-primary' ?> ml-1">Patient</a>
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?page=users&action=create" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add User
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr><td colspan="7" class="text-center text-muted py-3">No users found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><?= (int) $u['id'] ?></td>
                                        <td><?= htmlspecialchars($u['name']) ?></td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td>
                                            <?php
                                            $roleColors = ['admin' => 'danger', 'doctor' => 'info', 'patient' => 'success'];
                                            $roleColor  = $roleColors[$u['role']] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?= $roleColor ?>"><?= htmlspecialchars($u['role']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                                        <td>
                                            <?php if ((int) $u['is_active']): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactive</span>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/index.php?page=users&action=edit&id=<?= (int) $u['id'] ?>"
                                               class="btn btn-xs btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if ((int) $u['id'] !== (int) $currentUser['id']): ?>
                                                <form method="POST"
                                                      action="<?= BASE_URL ?>/index.php?page=users&action=toggle&id=<?= (int) $u['id'] ?>"
                                                      class="d-inline">
                                                    <?= csrfField() ?>
                                                    <button type="submit"
                                                            class="btn btn-xs <?= (int) $u['is_active'] ? 'btn-danger' : 'btn-success' ?>">
                                                        <?= (int) $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                                                    </button>
                                                </form>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($paginator && $paginator->totalPages() > 1): ?>
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <?php if ($paginator->hasPrev()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=users&role=<?= htmlspecialchars($roleFilter) ?>&p=<?= $paginator->currentPage() - 1 ?>">«</a>
                                </li>
                            <?php endif ?>
                            <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                                <li class="page-item <?= $i === $paginator->currentPage() ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=users&role=<?= htmlspecialchars($roleFilter) ?>&p=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor ?>
                            <?php if ($paginator->hasNext()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=users&role=<?= htmlspecialchars($roleFilter) ?>&p=<?= $paginator->currentPage() + 1 ?>">»</a>
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
