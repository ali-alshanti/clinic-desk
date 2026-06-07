<?php

class DoctorModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT d.*, u.name, u.email, u.phone, u.avatar, u.is_active,
                    s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE d.id = ?',
            'i',
            [$id]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $result = $this->execute(
            'SELECT d.*, u.name, u.email, u.phone, u.avatar, u.is_active,
                    s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE d.user_id = ?',
            'i',
            [$userId]
        );
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function getAll(): array
    {
        $result = $this->execute(
            'SELECT d.id, u.name, s.name AS specialization_name,
                    d.available_days, d.consultation_fee
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             ORDER BY u.name ASC'
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllPaginated(int $page): array
    {
        $limit  = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $result = $this->execute(
            'SELECT d.id, d.consultation_fee, d.available_days,
                    u.name, u.email, u.phone, u.is_active,
                    s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             ORDER BY u.name ASC
             LIMIT ? OFFSET ?',
            'ii',
            [$limit, $offset]
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll(): int
    {
        $result = $this->execute('SELECT COUNT(*) AS total FROM doctors');
        return (int) $result->fetch_assoc()['total'];
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days)
             VALUES (?, ?, ?, ?, ?)',
            'iisds',
            [$data['user_id'], $data['specialization_id'], $data['bio'], $data['consultation_fee'], $data['available_days']]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $doctorId, array $data): bool
    {
        return $this->execute(
            'UPDATE doctors SET specialization_id = ?, bio = ?, consultation_fee = ?, available_days = ?
             WHERE id = ?',
            'isdsi',
            [$data['specialization_id'], $data['bio'], $data['consultation_fee'], $data['available_days'], $doctorId]
        );
    }

    public function getAvailableDays(int $doctorId): array
    {
        $result = $this->execute(
            'SELECT available_days FROM doctors WHERE id = ?',
            'i',
            [$doctorId]
        );
        $row = $result->fetch_assoc();
        if (!$row || empty($row['available_days'])) {
            return [];
        }
        return explode(',', $row['available_days']);
    }
}
