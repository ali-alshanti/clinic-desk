<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class ReportController
{
    public function index(): void
    {
        Auth::requireRole('admin');

        $doctors = (new DoctorModel())->getAll();

        require __DIR__ . '/../views/reports/index.php';
    }

    public function generate(): void
    {
        Auth::requireRole('admin');

        $startDate = trim($_GET['start_date'] ?? '');
        $endDate   = trim($_GET['end_date'] ?? '');
        $doctorId  = (int) ($_GET['doctor_id'] ?? 0);
        $status    = trim($_GET['status'] ?? '');

        $doctors = (new DoctorModel())->getAll();

        if (!$startDate || !$endDate) {
            setFlash('error', 'Start date and end date are required.');
            require __DIR__ . '/../views/reports/index.php';
            return;
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            setFlash('error', 'Start date cannot be after end date.');
            require __DIR__ . '/../views/reports/index.php';
            return;
        }

        $conditions = ['a.appt_date >= ?', 'a.appt_date <= ?'];
        $types      = 'ss';
        $params     = [$startDate, $endDate];

        if ($doctorId > 0) {
            $conditions[] = 'a.doctor_id = ?';
            $types .= 'i';
            $params[] = $doctorId;
        }

        if ($status !== '') {
            $conditions[] = 'a.status = ?';
            $types .= 's';
            $params[] = $status;
        }

        $where = implode(' AND ', $conditions);

        $db = Database::getInstance();

        $dataResult = $db->query(
            "SELECT p.name AS patient_name,
                    u.name AS doctor_name,
                    s.name AS specialization_name,
                    a.appt_date, a.appt_time, a.status, a.reason
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE $where
             ORDER BY a.appt_date DESC",
            $types,
            $params
        );
        $rows = $dataResult->fetch_all(MYSQLI_ASSOC);

        $summaryResult = $db->query(
            "SELECT COUNT(*) AS total,
                    SUM(status = 'pending')   AS pending,
                    SUM(status = 'confirmed') AS confirmed,
                    SUM(status = 'completed') AS completed,
                    SUM(status = 'cancelled') AS cancelled
             FROM appointments a
             WHERE $where",
            $types,
            $params
        );
        $summary = $summaryResult->fetch_assoc();

        if (($_GET['export'] ?? '') === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="report.csv"');

            $out = fopen('php://output', 'w');
            fputcsv($out, ['Patient', 'Doctor', 'Specialization', 'Date', 'Time', 'Status', 'Reason']);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['patient_name'],
                    $row['doctor_name'],
                    $row['specialization_name'],
                    $row['appt_date'],
                    $row['appt_time'],
                    $row['status'],
                    $row['reason'],
                ]);
            }

            fclose($out);
            exit;
        }

        require __DIR__ . '/../views/reports/index.php';
    }
}
