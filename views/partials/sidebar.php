<?php
$role        = Auth::role();
$currentPage = $_GET['page'] ?? 'dashboard';

if (!function_exists('isActive')) {
    function isActive(string $page): string
    {
        global $currentPage;
        return $currentPage === $page ? 'active' : '';
    }
}
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="brand-link text-center">
        <span class="brand-text font-weight-bold">ClinicDesk</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="nav-link <?= isActive('dashboard') ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if ($role === 'admin'): ?>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=users" class="nav-link <?= isActive('users') ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=doctors" class="nav-link <?= isActive('doctors') ?>">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Doctors</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=specializations" class="nav-link <?= isActive('specializations') ?>">
                            <i class="nav-icon fas fa-stethoscope"></i>
                            <p>Specializations</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=all" class="nav-link <?= isActive('appointments') ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Appointments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=reports" class="nav-link <?= isActive('reports') ?>">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reports</p>
                        </a>
                    </li>

                <?php elseif ($role === 'doctor'): ?>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=schedule" class="nav-link <?= isActive('appointments') ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>My Schedule</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=my" class="nav-link <?= isActive('prescriptions') ?>">
                            <i class="nav-icon fas fa-file-medical"></i>
                            <p>My Prescriptions</p>
                        </a>
                    </li>

                <?php elseif ($role === 'patient'): ?>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=book" class="nav-link <?= isActive('appointments') ?>">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Book Appointment</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=appointments&action=my" class="nav-link <?= isActive('appointments') ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>My Appointments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/index.php?page=prescriptions&action=my" class="nav-link <?= isActive('prescriptions') ?>">
                            <i class="nav-icon fas fa-file-medical"></i>
                            <p>My Prescriptions</p>
                        </a>
                    </li>

                <?php endif ?>

            </ul>
        </nav>
    </div>

</aside>
