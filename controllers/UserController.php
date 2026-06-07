<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    public function index(): void
    {
        Auth::requireRole('admin');

        $page      = max(1, (int) ($_GET['p'] ?? 1));
        $role      = $_GET['role'] ?? '';

        $userModel = new UserModel();
        $users     = $userModel->getAllPaginated($page, $role);
        $total     = $userModel->countAll($role);
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);

        require __DIR__ . '/../views/users/index.php';
    }

    public function create(): void
    {
        Auth::requireRole('admin');

        require __DIR__ . '/../views/users/create.php';
    }

    public function store(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=users&action=create');
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? '';
        $phone    = trim($_POST['phone'] ?? '');

        if (!$name || !$email || !$password || !$role) {
            setFlash('error', 'All required fields must be filled.');
            redirect('index.php?page=users&action=create');
        }

        $userModel = new UserModel();

        if ($userModel->findByEmail($email)) {
            setFlash('error', 'Email is already in use.');
            redirect('index.php?page=users&action=create');
        }

        $hash  = password_hash($password, PASSWORD_BCRYPT);
        $newId = $userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $hash,
            'role'     => $role,
            'phone'    => $phone,
        ]);

        if ($role === 'doctor') {
            redirect('index.php?page=doctors&action=create&user_id=' . $newId);
        }

        setFlash('success', 'User created successfully.');
        redirect('index.php?page=users');
    }

    public function edit(int $id): void
    {
        Auth::requireRole('admin');

        $userModel = new UserModel();
        $user      = $userModel->findById($id);

        if (!$user) {
            redirect('index.php?page=users');
        }

        require __DIR__ . '/../views/users/edit.php';
    }

    public function update(int $id): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=users');
        }

        $name   = trim($_POST['name'] ?? '');
        $phone  = trim($_POST['phone'] ?? '');
        $avatar = trim($_POST['avatar'] ?? '');

        if (!$name) {
            setFlash('error', 'Name cannot be empty.');
            redirect('index.php?page=users&action=edit&id=' . $id);
        }

        $userModel = new UserModel();
        $userModel->update($id, [
            'name'   => $name,
            'phone'  => $phone,
            'avatar' => $avatar,
        ]);

        setFlash('success', 'User updated successfully.');
        redirect('index.php?page=users');
    }

    public function toggleActive(int $id): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=users');
        }

        if ($id === (int) Auth::currentUser()['id']) {
            setFlash('error', 'You cannot deactivate your own account.');
            redirect('index.php?page=users');
        }

        $userModel = new UserModel();
        $userModel->toggleActive($id);

        setFlash('success', 'User status updated.');
        redirect('index.php?page=users');
    }
}
