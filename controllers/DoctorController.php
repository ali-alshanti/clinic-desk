<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';

class DoctorController
{
    public function index(): void
    {
        Auth::requireRole('admin');

        $page        = max(1, (int) ($_GET['p'] ?? 1));
        $doctorModel = new DoctorModel();
        $doctors     = $doctorModel->getAllPaginated($page);
        $total       = $doctorModel->countAll();
        $paginator   = new Paginator($total, ITEMS_PER_PAGE, $page);

        require __DIR__ . '/../views/doctors/index.php';
    }

    public function create(): void
    {
        Auth::requireRole('admin');

        $userId          = (int) ($_GET['user_id'] ?? 0);
        $specModel       = new SpecializationModel();
        $specializations = $specModel->getAll();

        require __DIR__ . '/../views/doctors/create.php';
    }

    public function store(): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=doctors&action=create');
        }

        $userId           = (int) ($_POST['user_id'] ?? 0);
        $specializationId = (int) ($_POST['specialization_id'] ?? 0);
        $bio              = trim($_POST['bio'] ?? '');
        $fee              = (float) ($_POST['consultation_fee'] ?? 0);
        $days             = $_POST['available_days'] ?? [];
        $availableDays    = implode(',', $days);

        $photo = '';
        if (!empty($_FILES['photo']['name'])) {
            $photo = $this->uploadPhoto($userId, $_FILES['photo']);
            if ($photo === null) {
                redirect('index.php?page=doctors&action=create&user_id=' . $userId);
            }
        }

        $doctorModel = new DoctorModel();
        $doctorModel->create([
            'user_id'           => $userId,
            'specialization_id' => $specializationId,
            'bio'               => $bio,
            'consultation_fee'  => $fee,
            'available_days'    => $availableDays,
        ]);

        if ($photo) {
            // store photo path on user avatar field via UserModel
            require_once __DIR__ . '/../models/UserModel.php';
            $userModel = new UserModel();
            $user      = $userModel->findById($userId);
            $userModel->update($userId, [
                'name'   => $user['name'],
                'phone'  => $user['phone'],
                'avatar' => $photo,
            ]);
        }

        setFlash('success', 'Doctor profile created successfully.');
        redirect('index.php?page=doctors');
    }

    public function edit(int $id): void
    {
        Auth::requireRole('admin');

        $doctorModel     = new DoctorModel();
        $doctor          = $doctorModel->findById($id);

        if (!$doctor) {
            redirect('index.php?page=doctors');
        }

        $specModel       = new SpecializationModel();
        $specializations = $specModel->getAll();

        require __DIR__ . '/../views/doctors/edit.php';
    }

    public function update(int $id): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=doctors');
        }

        $doctorModel      = new DoctorModel();
        $doctor           = $doctorModel->findById($id);

        if (!$doctor) {
            redirect('index.php?page=doctors');
        }

        $specializationId = (int) ($_POST['specialization_id'] ?? 0);
        $bio              = trim($_POST['bio'] ?? '');
        $fee              = (float) ($_POST['consultation_fee'] ?? 0);
        $days             = $_POST['available_days'] ?? [];
        $availableDays    = implode(',', $days);

        $photo = $doctor['avatar'];
        if (!empty($_FILES['photo']['name'])) {
            $newPhoto = $this->uploadPhoto($doctor['user_id'], $_FILES['photo']);
            if ($newPhoto === null) {
                redirect('index.php?page=doctors&action=edit&id=' . $id);
            }
            // delete old photo if it exists
            if ($photo && file_exists(UPLOAD_PATH_DOCTORS . $photo)) {
                unlink(UPLOAD_PATH_DOCTORS . $photo);
            }
            $photo = $newPhoto;

            require_once __DIR__ . '/../models/UserModel.php';
            $userModel = new UserModel();
            $userModel->update($doctor['user_id'], [
                'name'   => $doctor['name'],
                'phone'  => $doctor['phone'],
                'avatar' => $photo,
            ]);
        }

        $doctorModel->update($id, [
            'specialization_id' => $specializationId,
            'bio'               => $bio,
            'consultation_fee'  => $fee,
            'available_days'    => $availableDays,
        ]);

        setFlash('success', 'Doctor profile updated successfully.');
        redirect('index.php?page=doctors');
    }

    // Returns filename on success, null on failure (also sets flash)
    private function uploadPhoto(int $userId, array $file): ?string
    {
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            setFlash('error', 'Uploaded file is not a valid image.');
            return null;
        }

        $mime = $imageInfo['mime'];
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            setFlash('error', 'Only JPEG and PNG images are allowed.');
            return null;
        }

        if ($file['size'] > AVATAR_MAX_SIZE) {
            setFlash('error', 'Image must be 1MB or smaller.');
            return null;
        }

        $ext      = ($mime === 'image/png') ? 'png' : 'jpg';
        $filename = 'doctor_' . $userId . '_' . time() . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH_DOCTORS . $filename)) {
            setFlash('error', 'Failed to save image. Please try again.');
            return null;
        }

        return $filename;
    }
}
