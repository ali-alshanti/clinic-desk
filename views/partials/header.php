<?php
$pageTitle = $pageTitle ?? 'ClinicDesk';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | ClinicDesk</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
