<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class AppointmentController
{
    // ── PATIENT ──────────────────────────────────────────────

    public function book(): void
    {
        Auth::requireRole('patient');
        require __DIR__ . '/../views/appointments/book.php';
    }

    public function store(): void
    {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=appointments&action=book');
        }

        $doctorId = (int) ($_POST['doctor_id'] ?? 0);
        $date     = trim($_POST['appt_date'] ?? '');
        $time     = trim($_POST['appt_time'] ?? '');
        $reason   = trim($_POST['reason'] ?? '');

        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            setFlash('error', 'Appointment date cannot be in the past.');
            redirect('index.php?page=appointments&action=book');
        }

        $doctorModel   = new DoctorModel();
        $availableDays = $doctorModel->getAvailableDays($doctorId);
        $dayOfWeek     = date('D', strtotime($date));

        if (!in_array($dayOfWeek, $availableDays, true)) {
            setFlash('error', 'The doctor is not available on that day.');
            redirect('index.php?page=appointments&action=book');
        }

        $apptModel = new AppointmentModel();

        if ($apptModel->hasConflict($doctorId, $date, $time)) {
            setFlash('error', 'That time slot is already booked. Please choose another.');
            redirect('index.php?page=appointments&action=book');
        }

        $apptModel->book([
            'patient_id' => Auth::currentUser()['id'],
            'doctor_id'  => $doctorId,
            'appt_date'  => $date,
            'appt_time'  => $time,
            'reason'     => $reason,
        ]);

        setFlash('success', 'Appointment booked successfully.');
        redirect('index.php?page=appointments&action=my');
    }

    public function myAppointments(): void
    {
        Auth::requireRole('patient');

        $page    = max(1, (int) ($_GET['p'] ?? 1));
        $filters = [
            'status'     => $_GET['status'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date'   => $_GET['end_date'] ?? '',
        ];

        $userId    = Auth::currentUser()['id'];
        $apptModel = new AppointmentModel();

        $appointments = $apptModel->getByPatient($userId, $page, $filters);
        $total        = $apptModel->countFiltered('patient', $userId, $filters);
        $paginator    = new Paginator($total, ITEMS_PER_PAGE, $page);

        require __DIR__ . '/../views/appointments/my-list.php';
    }

    public function cancel(int $id): void
    {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=appointments&action=my');
        }

        $apptModel   = new AppointmentModel();
        $appointment = $apptModel->findById($id);

        if (!$appointment || (int) $appointment['patient_id'] !== (int) Auth::currentUser()['id']) {
            setFlash('error', 'Appointment not found.');
            redirect('index.php?page=appointments&action=my');
        }

        if ($appointment['status'] !== 'pending') {
            setFlash('error', 'Only pending appointments can be cancelled.');
            redirect('index.php?page=appointments&action=my');
        }

        $apptModel->updateStatus($id, 'cancelled');

        setFlash('success', 'Appointment cancelled.');
        redirect('index.php?page=appointments&action=my');
    }

    // ── DOCTOR ───────────────────────────────────────────────

    public function schedule(): void
    {
        Auth::requireRole('doctor');

        $user        = Auth::currentUser();
        $doctorModel = new DoctorModel();
        $doctor      = $doctorModel->findByUserId($user['id']);

        $page    = max(1, (int) ($_GET['p'] ?? 1));
        $filters = [
            'status'     => $_GET['status'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date'   => $_GET['end_date'] ?? '',
        ];

        $apptModel         = new AppointmentModel();
        $todayAppointments = $apptModel->getTodayByDoctor($doctor['id']);
        $appointments      = $apptModel->getByDoctor($doctor['id'], $page, $filters);
        $total             = $apptModel->countFiltered('doctor', $doctor['id'], $filters);
        $paginator         = new Paginator($total, ITEMS_PER_PAGE, $page);

        require __DIR__ . '/../views/appointments/schedule.php';
    }

    public function updateStatus(int $id): void
    {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=appointments&action=schedule');
        }

        $user        = Auth::currentUser();
        $doctorModel = new DoctorModel();
        $doctor      = $doctorModel->findByUserId($user['id']);

        $apptModel   = new AppointmentModel();
        $appointment = $apptModel->findById($id);

        if (!$appointment || (int) $appointment['doctor_id'] !== (int) $doctor['id']) {
            setFlash('error', 'Appointment not found.');
            redirect('index.php?page=appointments&action=schedule');
        }

        $status = $_POST['status'] ?? '';
        $notes  = trim($_POST['doctor_notes'] ?? '');

        $apptModel->updateStatus($id, $status, $notes);

        setFlash('success', 'Appointment status updated.');
        redirect('index.php?page=appointments&action=schedule');
    }

    // ── ADMIN ────────────────────────────────────────────────

    public function allAppointments(): void
    {
        Auth::requireRole('admin');

        $page    = max(1, (int) ($_GET['p'] ?? 1));
        $filters = [
            'status'       => $_GET['status'] ?? '',
            'doctor_id'    => (int) ($_GET['doctor_id'] ?? 0) ?: '',
            'start_date'   => $_GET['start_date'] ?? '',
            'end_date'     => $_GET['end_date'] ?? '',
            'patient_name' => $_GET['patient_name'] ?? '',
        ];

        $apptModel    = new AppointmentModel();
        $appointments = $apptModel->getAll($page, $filters);
        $total        = $apptModel->countFiltered('all', 0, $filters);
        $paginator    = new Paginator($total, ITEMS_PER_PAGE, $page);

        $doctorModel = new DoctorModel();
        $doctors     = $doctorModel->getAll();

        require __DIR__ . '/../views/appointments/all.php';
    }

    public function adminUpdateStatus(int $id): void
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request');
            redirect('index.php?page=appointments&action=all');
        }

        $status = $_POST['status'] ?? '';
        $notes  = trim($_POST['doctor_notes'] ?? '');

        $apptModel = new AppointmentModel();
        $apptModel->updateStatus($id, $status, $notes);

        setFlash('success', 'Appointment status updated.');
        redirect('index.php?page=appointments&action=all');
    }
}
