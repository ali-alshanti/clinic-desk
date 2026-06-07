<?php
require_once __DIR__ . '/../../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 | ClinicDesk</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
    <style>
        body {
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-box {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #dc3545;
            line-height: 1;
        }
        .error-title {
            font-size: 24px;
            margin: 20px 0 10px;
        }
        .error-msg {
            color: #666;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="error-code">404</div>
        <h3 class="error-title">Page Not Found</h3>
        <p class="error-msg">The page you are looking for does not exist.</p>
        <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="btn btn-danger btn-lg">
            <i class="fas fa-home mr-2"></i> Go to Dashboard
        </a>
    </div>
</body>
</html>
