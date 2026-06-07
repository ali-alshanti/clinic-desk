<?php

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function sanitize(string $val): string
{
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

function formatDate(string $date): string
{
    return date('d/m/Y', strtotime($date));
}

function formatTime(string $time): string
{
    return date('H:i', strtotime($time));
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
