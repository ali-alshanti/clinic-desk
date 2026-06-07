<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

define('APP_NAME', 'ClinicDesk');
define('BASE_URL', 'http://localhost/clinicdesk');
define('ITEMS_PER_PAGE', 10);
define('MAX_UPLOAD_SIZE', 3145728);   // 3MB
define('AVATAR_MAX_SIZE', 1048576);   // 1MB
define('UPLOAD_PATH_AVATARS', 'public/uploads/avatars/');
define('UPLOAD_PATH_DOCTORS', 'public/uploads/doctor_photos/');
define('UPLOAD_PATH_PRESCRIPTIONS', 'public/uploads/prescriptions/');
