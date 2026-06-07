<?php

class UserModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        $result = $this->execute('SELECT * FROM users WHERE id = ?', 'i', [$id]);
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $result = $this->execute('SELECT * FROM users WHERE email = ?', 's', [$email]);
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)',
            'sssss',
            [$data['name'], $data['email'], $data['password'], $data['role'], $data['phone']]
        );
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return $this->execute(
            'UPDATE users SET name = ?, phone = ?, avatar = ? WHERE id = ?',
            'sssi',
            [$data['name'], $data['phone'], $data['avatar'], $id]
        );
    }

    public function updatePassword(int $id, string $newHash): bool
    {
        return $this->execute(
            'UPDATE users SET password = ? WHERE id = ?',
            'si',
            [$newHash, $id]
        );
    }

    public function getAllPaginated(int $page, string $role = ''): array
    {
        $limit  = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        if ($role !== '') {
            $result = $this->execute(
                'SELECT * FROM users WHERE role = ? ORDER BY created_at DESC LIMIT ? OFFSET ?',
                'sii',
                [$role, $limit, $offset]
            );
        } else {
            $result = $this->execute(
                'SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?',
                'ii',
                [$limit, $offset]
            );
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll(string $role = ''): int
    {
        if ($role !== '') {
            $result = $this->execute(
                'SELECT COUNT(*) AS total FROM users WHERE role = ?',
                's',
                [$role]
            );
        } else {
            $result = $this->execute('SELECT COUNT(*) AS total FROM users');
        }

        return (int) $result->fetch_assoc()['total'];
    }

    public function toggleActive(int $id): bool
    {
        return $this->execute(
            'UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?',
            'i',
            [$id]
        );
    }
}
