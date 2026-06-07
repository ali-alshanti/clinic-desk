<?php
$currentUser = Auth::currentUser();
$role        = Auth::role();
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item d-flex align-items-center mr-3">
            <span class="mr-2"><?= htmlspecialchars($currentUser['name']) ?></span>
            <span class="badge badge-secondary"><?= htmlspecialchars(ucfirst($role)) ?></span>
        </li>
        <li class="nav-item">
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=auth&action=logout">
                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger mt-1">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </li>
    </ul>

</nav>
