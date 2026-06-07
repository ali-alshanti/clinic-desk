<?php
$pageTitle = 'Doctors';
require_once __DIR__ . '/../../core/Auth.php';
Auth::requireRole('admin');
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
require __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="m-0">Doctors</h1>
            <ol class="breadcrumb float-sm-right m-0">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                <li class="breadcrumb-item active">Doctors</li>
            </ol>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <?php require __DIR__ . '/../partials/alerts.php' ?>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">All Doctors</h3>
                    <a href="<?= BASE_URL ?>/index.php?page=users&action=create" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add Doctor
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Specialization</th>
                                <th>Fee</th>
                                <th>Available Days</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($doctors)): ?>
                                <tr><td colspan="7" class="text-center">No doctors found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($doctors as $doc): ?>
                                    <tr>
                                        <td><?= $doc['id'] ?></td>
                                        <td><?= htmlspecialchars($doc['name']) ?></td>
                                        <td><?= htmlspecialchars($doc['specialization_name']) ?></td>
                                        <td>$<?= number_format($doc['consultation_fee'], 2) ?></td>
                                        <td><?= htmlspecialchars($doc['available_days']) ?></td>
                                        <td>
                                            <?php if ($doc['is_active']): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/index.php?page=doctors&action=edit&id=<?= $doc['id'] ?>"
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($paginator->totalPages() > 1): ?>
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <?php if ($paginator->hasPrev()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=doctors&p=<?= $paginator->currentPage() - 1 ?>">«</a>
                                </li>
                            <?php endif ?>
                            <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                                <li class="page-item <?= $i === $paginator->currentPage() ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=doctors&p=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor ?>
                            <?php if ($paginator->hasNext()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=doctors&p=<?= $paginator->currentPage() + 1 ?>">»</a>
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
