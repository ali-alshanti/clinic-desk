<?php

class PrescriptionModel extends BaseModel
{
    public function findByAppointmentId(int $apptId): ?array
    {
        $result = $this->execute(
            'SELECT * FROM prescriptions WHERE appointment_id = ?',
            'i',
            [$apptId]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT pr.*,
                    p.name AS patient_name, p.email AS patient_email,
                    u.name AS doctor_name,
                    a.appt_date, a.appt_time, a.status AS appt_status
             FROM prescriptions pr
             JOIN appointments a ON a.id = pr.appointment_id
             JOIN users p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users u ON u.id = d.user_id
             WHERE pr.id = ?',
            'i',
            [$id]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path)
             VALUES (?, ?, ?, ?, ?)',
            'issss',
            [$data['appointment_id'], $data['diagnosis'], $data['medications'], $data['notes'], $data['file_path']]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return $this->execute(
            'UPDATE prescriptions SET diagnosis = ?, medications = ?, notes = ?, file_path = ?
             WHERE id = ?',
            'ssssi',
            [$data['diagnosis'], $data['medications'], $data['notes'], $data['file_path'], $id]
        );
    }

    public function getByPatient(int $patientId): array
    {
        $result = $this->execute(
            'SELECT pr.*, u.name AS doctor_name, a.appt_date, a.status AS appt_status
             FROM prescriptions pr
             JOIN appointments a ON a.id = pr.appointment_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users u ON u.id = d.user_id
             WHERE a.patient_id = ? AND a.status = "completed"
             ORDER BY a.appt_date DESC',
            'i',
            [$patientId]
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function existsForAppointment(int $apptId): bool
    {
        $result = $this->execute(
            'SELECT id FROM prescriptions WHERE appointment_id = ?',
            'i',
            [$apptId]
        );
        return $result->num_rows > 0;
    }
}
