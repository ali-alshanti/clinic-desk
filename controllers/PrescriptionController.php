<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class PrescriptionController
{
    public function add(int $appointmentId): void
    {
        Auth::requireRole('doctor');

        $doctor      = (new DoctorModel())->findByUserId(Auth::currentUser()['id']);
        $appointment = (new AppointmentModel())->findById($appointmentId);

        if (!$appointment || (int) $appointment['doctor_id'] !== (int) $doctor['id']) {
            setFlash('error', 'Appointment not found.');
            redirect('index.php?page=appointments&action=schedule');
        }

        if ($appointment['status'] !== 'completed') {
            setFlash('error', 'Prescriptions can only be added to completed appointments.');
            redirect('index.php?page=appointments&action=schedule');
        }

        if ((new PrescriptionModel())->existsForAppointment($appointmentId)) {
            setFlash('error', 'A prescription already exists for this appointment.');
            redirect('index.php?page=appointments&action=schedule');
        }

        require __DIR__ . '/../views/prescriptions/add.php';
    }

    public function store(): void
    {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=appointments&action=schedule');
        }

        $appointmentId = (int) ($_POST['appointment_id'] ?? 0);
        $diagnosis     = trim($_POST['diagnosis'] ?? '');
        $medications   = trim($_POST['medications'] ?? '');
        $notes         = trim($_POST['notes'] ?? '');

        $doctor      = (new DoctorModel())->findByUserId(Auth::currentUser()['id']);
        $appointment = (new AppointmentModel())->findById($appointmentId);

        if (!$appointment || (int) $appointment['doctor_id'] !== (int) $doctor['id']) {
            setFlash('error', 'Appointment not found.');
            redirect('index.php?page=appointments&action=schedule');
        }

        if ($appointment['status'] !== 'completed') {
            setFlash('error', 'Prescriptions can only be added to completed appointments.');
            redirect('index.php?page=appointments&action=schedule');
        }

        $filePath = null;
        if (!empty($_FILES['pdf']['name'])) {
            $filePath = $this->uploadPdf($appointmentId, $_FILES['pdf']);
            if ($filePath === null) {
                redirect('index.php?page=prescriptions&action=add&appointment_id=' . $appointmentId);
            }
        }

        (new PrescriptionModel())->create([
            'appointment_id' => $appointmentId,
            'diagnosis'      => $diagnosis,
            'medications'    => $medications,
            'notes'          => $notes,
            'file_path'      => $filePath,
        ]);

        setFlash('success', 'Prescription saved successfully.');
        redirect('index.php?page=appointments&action=schedule');
    }

    public function download(int $id): void
    {
        if (!Auth::check()) {
            redirect('index.php?page=auth&action=login');
        }

        $prescription = (new PrescriptionModel())->findById($id);

        if (!$prescription) {
            setFlash('error', 'Prescription not found.');
            redirect('index.php?page=dashboard');
        }

        $user = Auth::currentUser();
        $role = Auth::role();

        if ($role === 'patient') {
            if ((int) $prescription['patient_id'] !== (int) $user['id']) {
                redirect('index.php?page=errors&action=403');
            }
        } elseif ($role === 'doctor') {
            $doctor = (new DoctorModel())->findByUserId($user['id']);
            if ((int) $prescription['doctor_id'] !== (int) $doctor['id']) {
                redirect('index.php?page=errors&action=403');
            }
        }
        // admin always allowed — no check needed

        if (empty($prescription['file_path'])) {
            setFlash('error', 'No file attached to this prescription.');
            redirect('index.php?page=dashboard');
        }

        $fullPath = UPLOAD_PATH_PRESCRIPTIONS . $prescription['file_path'];

        if (!file_exists($fullPath)) {
            setFlash('error', 'Prescription file not found on server.');
            redirect('index.php?page=dashboard');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription.pdf"');
        readfile($fullPath);
        exit;
    }

    public function myPrescriptions(): void
    {
        Auth::requireRole('patient', 'doctor');

        $prescriptions = (new PrescriptionModel())->getByPatient(Auth::currentUser()['id']);

        require __DIR__ . '/../views/prescriptions/my-list.php';
    }

    private function uploadPdf(int $appointmentId, array $file): ?string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'application/pdf') {
            setFlash('error', 'Only PDF files are allowed.');
            return null;
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            setFlash('error', 'File must be 3MB or smaller.');
            return null;
        }

        $filename = 'prescription_' . $appointmentId . '_' . time() . '.pdf';

        if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH_PRESCRIPTIONS . $filename)) {
            setFlash('error', 'Failed to save file. Please try again.');
            return null;
        }

        return $filename;
    }
}
