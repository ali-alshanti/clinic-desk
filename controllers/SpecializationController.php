<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

class SpecializationController
{
    public function index(): void
    {
        Auth::requireRole('admin');

        $model = new SpecializationModel();
        $specializations = $model->getAll();

        require __DIR__ . '/../views/specializations/index.php';
    }

    public function store(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('index.php?page=specializations');
        }

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            setFlash('error', 'Specialization name is required.');
            redirect('index.php?page=specializations');
        }

        $model = new SpecializationModel();
        $model->create($name);

        setFlash('success', 'Specialization created successfully.');
        redirect('index.php?page=specializations');
    }

    public function delete(int $id): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('index.php?page=specializations');
        }

        $model = new SpecializationModel();

        if (!$model->isSafeToDelete($id)) {
            setFlash('error', 'Cannot delete: this specialization is assigned to one or more doctors.');
            redirect('index.php?page=specializations');
        }

        $model->delete($id);

        setFlash('success', 'Specialization deleted.');
        redirect('index.php?page=specializations');
    }
}
