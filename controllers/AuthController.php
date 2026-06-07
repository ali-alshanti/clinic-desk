<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            redirect('index.php?page=dashboard');
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void
    {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=auth&action=login');
        }

        $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $userModel = new UserModel();
        $user      = $userModel->findByEmail($email);

        if (!$user) {
            setFlash('error', 'Invalid credentials');
            redirect('index.php?page=auth&action=login');
        }

        if ((int) $user['is_active'] !== 1) {
            setFlash('error', 'Account suspended. Contact admin.');
            redirect('index.php?page=auth&action=login');
        }

        if (!password_verify($password, $user['password'])) {
            setFlash('error', 'Invalid credentials');
            redirect('index.php?page=auth&action=login');
        }

        Auth::login($user);
        redirect('index.php?page=dashboard');
    }

    public function logout(): void
    {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=dashboard');
        }

        Auth::logout();
    }
}
