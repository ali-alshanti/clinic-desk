<?php

class AppointmentModel extends BaseModel
{
    public function book(array $data): bool
    {
        $result = $this->execute(
            'INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason)
             VALUES (?, ?, ?, ?, ?)',
            'iisss',
            [$data['patient_id'], $data['doctor_id'], $data['appt_date'], $data['appt_time'], $data['reason']]
        );
        return $result !== false;
    }

    public function hasConflict(int $doctorId, string $date, string $time): bool
    {
        $result = $this->execute(
            'SELECT id FROM appointments WHERE doctor_id = ? AND appt_date = ? AND appt_time = ?',
            'iss',
            [$doctorId, $date, $time]
        );
        return $result->num_rows > 0;
    }

    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT a.*,
                    p.name AS patient_name, p.phone AS patient_phone, p.email AS patient_email,
                    u.name AS doctor_name,
                    s.name AS specialization_name
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE a.id = ?',
            'i',
            [$id]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function getByPatient(int $patientId, int $page, array $filters = []): array
    {
        $conditions = ['a.patient_id = ?'];
        $types      = 'i';
        $params     = [$patientId];

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $types .= 's';
            $params[] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $types .= 's';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $types .= 's';
            $params[] = $filters['end_date'];
        }

        $where  = implode(' AND ', $conditions);
        $limit  = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;

        $result = $this->execute(
            "SELECT a.*, u.name AS doctor_name, s.name AS specialization_name
             FROM appointments a
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE $where
             ORDER BY a.appt_date DESC, a.appt_time DESC
             LIMIT ? OFFSET ?",
            $types,
            $params
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByDoctor(int $doctorId, int $page, array $filters = []): array
    {
        $conditions = ['a.doctor_id = ?'];
        $types      = 'i';
        $params     = [$doctorId];

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $types .= 's';
            $params[] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $types .= 's';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $types .= 's';
            $params[] = $filters['end_date'];
        }

        $where  = implode(' AND ', $conditions);
        $limit  = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;

        $result = $this->execute(
            "SELECT a.*, p.name AS patient_name, p.phone AS patient_phone
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             WHERE $where
             ORDER BY a.appt_date DESC, a.appt_time DESC
             LIMIT ? OFFSET ?",
            $types,
            $params
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll(int $page, array $filters = []): array
    {
        $conditions = ['1 = 1'];
        $types      = '';
        $params     = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $types .= 's';
            $params[] = $filters['status'];
        }
        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $types .= 'i';
            $params[] = $filters['doctor_id'];
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $types .= 's';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $types .= 's';
            $params[] = $filters['end_date'];
        }
        if (!empty($filters['patient_name'])) {
            $conditions[] = 'p.name LIKE ?';
            $types .= 's';
            $params[] = '%' . $filters['patient_name'] . '%';
        }

        $where  = implode(' AND ', $conditions);
        $limit  = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $types .= 'ii';
        $params[] = $limit;
        $params[] = $offset;

        $result = $this->execute(
            "SELECT a.*,
                    p.name AS patient_name,
                    u.name AS doctor_name,
                    s.name AS specialization_name
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE $where
             ORDER BY a.appt_date DESC, a.appt_time DESC
             LIMIT ? OFFSET ?",
            $types,
            $params
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countFiltered(string $scope, int $scopeId, array $filters = []): int
    {
        $conditions = ['1 = 1'];
        $types      = '';
        $params     = [];

        if ($scope === 'patient') {
            $conditions[] = 'a.patient_id = ?';
            $types .= 'i';
            $params[] = $scopeId;
        } elseif ($scope === 'doctor') {
            $conditions[] = 'a.doctor_id = ?';
            $types .= 'i';
            $params[] = $scopeId;
        }

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $types .= 's';
            $params[] = $filters['status'];
        }
        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $types .= 'i';
            $params[] = $filters['doctor_id'];
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $types .= 's';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $types .= 's';
            $params[] = $filters['end_date'];
        }
        if (!empty($filters['patient_name'])) {
            $conditions[] = 'p.name LIKE ?';
            $types .= 's';
            $params[] = '%' . $filters['patient_name'] . '%';
        }

        $where  = implode(' AND ', $conditions);

        $result = $this->execute(
            "SELECT COUNT(*) AS total
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             WHERE $where",
            $types,
            $params
        );
        return (int) $result->fetch_assoc()['total'];
    }

    public function updateStatus(int $id, string $status, string $notes = ''): bool
    {
        return $this->execute(
            'UPDATE appointments SET status = ?, doctor_notes = ? WHERE id = ?',
            'ssi',
            [$status, $notes, $id]
        );
    }

    public function getTodayByDoctor(int $doctorId): array
    {
        $result = $this->execute(
            'SELECT a.*, p.name AS patient_name, p.phone AS patient_phone
             FROM appointments a
             JOIN users p ON p.id = a.patient_id
             WHERE a.doctor_id = ? AND a.appt_date = CURDATE()
             ORDER BY a.appt_time ASC',
            'i',
            [$doctorId]
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
