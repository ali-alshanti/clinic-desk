<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page   = $_GET['page']   ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id     = (int) ($_GET['id'] ?? 0);

switch ($page) {

    case 'auth':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        if ($action === 'login') {
            $_SERVER['REQUEST_METHOD'] === 'POST'
                ? $controller->login()
                : $controller->showLogin();
        } elseif ($action === 'logout') {
            $controller->logout();
        }
        break;

    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        (new DashboardController())->index();
        break;

    case 'users':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        switch ($action) {
            case 'create': $controller->create();          break;
            case 'store':  $controller->store();           break;
            case 'edit':   $controller->edit($id);         break;
            case 'update': $controller->update($id);       break;
            case 'toggle': $controller->toggleActive($id); break;
            default:       $controller->index();           break;
        }
        break;

    case 'doctors':
        require_once __DIR__ . '/controllers/DoctorController.php';
        $controller = new DoctorController();
        switch ($action) {
            case 'create': $controller->create();    break;
            case 'store':  $controller->store();     break;
            case 'edit':   $controller->edit($id);   break;
            case 'update': $controller->update($id); break;
            default:       $controller->index();     break;
        }
        break;

    case 'specializations':
        require_once __DIR__ . '/controllers/SpecializationController.php';
        $controller = new SpecializationController();
        switch ($action) {
            case 'store':  $controller->store();     break;
            case 'delete': $controller->delete($id); break;
            default:       $controller->index();     break;
        }
        break;

    case 'appointments':
        require_once __DIR__ . '/controllers/AppointmentController.php';
        $controller = new AppointmentController();
        switch ($action) {
            case 'book':         $controller->book();                  break;
            case 'store':        $controller->store();                 break;
            case 'my':           $controller->myAppointments();        break;
            case 'cancel':       $controller->cancel($id);             break;
            case 'schedule':     $controller->schedule();              break;
            case 'updateStatus': $controller->updateStatus($id);       break;
            case 'all':          $controller->allAppointments();       break;
            case 'adminUpdate':  $controller->adminUpdateStatus($id);  break;
            default:             require __DIR__ . '/views/errors/404.php'; break;
        }
        break;

    case 'prescriptions':
        require_once __DIR__ . '/controllers/PrescriptionController.php';
        $controller = new PrescriptionController();
        switch ($action) {
            case 'add':      $controller->add($id);             break;
            case 'store':    $controller->store();              break;
            case 'download': $controller->download($id);        break;
            case 'my':       $controller->myPrescriptions();    break;
            default:         require __DIR__ . '/views/errors/404.php'; break;
        }
        break;

    case 'reports':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController();
        $action === 'generate'
            ? $controller->generate()
            : $controller->index();
        break;

    case 'errors':
        if ($action === '403') {
            require __DIR__ . '/views/errors/403.php';
        } else {
            require __DIR__ . '/views/errors/404.php';
        }
        break;

    default:
        require __DIR__ . '/views/errors/404.php';
        break;
}
