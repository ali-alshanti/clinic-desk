<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';

class DashboardController
{
    public function index(): void
    {
        if (!Auth::check()) {
            redirect('index.php?page=auth&action=login');
        }

        $role = Auth::role();

        if ($role === 'admin') {
            $this->adminDashboard();
        } elseif ($role === 'doctor') {
            $this->doctorDashboard();
        } else {
            $this->patientDashboard();
        }
    }

    private function adminDashboard(): void
    {
        Auth::requireRole('admin');

        $db = Database::getInstance();

        $userStatsResult = $db->query('SELECT role, COUNT(*) AS total FROM users GROUP BY role');
        $userStats = [];
        while ($row = $userStatsResult->fetch_assoc()) {
            $userStats[$row['role']] = (int) $row['total'];
        }

        $todayResult = $db->query(
            'SELECT COUNT(*) AS total FROM appointments WHERE appt_date = CURDATE()'
        );
        $todayCount = (int) $todayResult->fetch_assoc()['total'];

        $weekStatsResult = $db->query(
            'SELECT status, COUNT(*) AS total FROM appointments
             WHERE WEEK(appt_date) = WEEK(NOW()) GROUP BY status'
        );
        $weekStats = [];
        while ($row = $weekStatsResult->fetch_assoc()) {
            $weekStats[$row['status']] = (int) $row['total'];
        }

        $recentResult = $db->query(
            'SELECT a.id, a.appt_date, a.appt_time, a.status,
                    p.name AS patient_name,
                    u.name AS doctor_name
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users u ON u.id = d.user_id
             ORDER BY a.created_at DESC
             LIMIT 5'
        );
        $recentAppointments = $recentResult->fetch_all(MYSQLI_ASSOC);

        require __DIR__ . '/../views/dashboard/admin.php';
    }

    private function doctorDashboard(): void
    {
        Auth::requireRole('doctor');

        $user   = Auth::currentUser();
        $db     = Database::getInstance();

        $doctorModel = new DoctorModel();
        $doctor      = $doctorModel->findByUserId($user['id']);

        $apptModel        = new AppointmentModel();
        $todayAppointments = $apptModel->getTodayByDoctor($doctor['id']);

        $monthStatsResult = $db->query(
            'SELECT
                COUNT(*) AS total,
                SUM(status = "pending")   AS pending,
                SUM(status = "completed") AS completed
             FROM appointments
             WHERE doctor_id = ? AND MONTH(appt_date) = MONTH(NOW()) AND YEAR(appt_date) = YEAR(NOW())',
            'i',
            [$doctor['id']]
        );
        $monthStats = $monthStatsResult->fetch_assoc();

        $upcomingResult = $db->query(
            'SELECT a.*, p.name AS patient_name
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             WHERE a.doctor_id = ? AND a.appt_date >= CURDATE()
               AND a.status IN ("pending", "confirmed")
             ORDER BY a.appt_date ASC, a.appt_time ASC
             LIMIT 5',
            'i',
            [$doctor['id']]
        );
        $upcomingAppointments = $upcomingResult->fetch_all(MYSQLI_ASSOC);

        require __DIR__ . '/../views/dashboard/doctor.php';
    }

    private function patientDashboard(): void
    {
        Auth::requireRole('patient');

        $user = Auth::currentUser();
        $db   = Database::getInstance();

        $activeResult = $db->query(
            'SELECT COUNT(*) AS total FROM appointments
             WHERE patient_id = ? AND status IN ("pending", "confirmed")',
            'i',
            [$user['id']]
        );
        $activeCount = (int) $activeResult->fetch_assoc()['total'];

        $completedResult = $db->query(
            'SELECT COUNT(*) AS total FROM appointments
             WHERE patient_id = ? AND status = "completed"',
            'i',
            [$user['id']]
        );
        $completedCount = (int) $completedResult->fetch_assoc()['total'];

        $prescriptionResult = $db->query(
            'SELECT COUNT(*) AS total FROM prescriptions pr
             JOIN appointments a ON a.id = pr.appointment_id
             WHERE a.patient_id = ?',
            'i',
            [$user['id']]
        );
        $prescriptionCount = (int) $prescriptionResult->fetch_assoc()['total'];

        $nextResult = $db->query(
            'SELECT a.*, u.name AS doctor_name, s.name AS specialization_name
             FROM appointments a
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE a.patient_id = ? AND a.appt_date >= CURDATE()
               AND a.status IN ("pending", "confirmed")
             ORDER BY a.appt_date ASC, a.appt_time ASC
             LIMIT 1',
            'i',
            [$user['id']]
        );
        $nextAppointment = $nextResult->fetch_assoc();

        require __DIR__ . '/../views/dashboard/patient.php';
    }
}
