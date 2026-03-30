<?php
namespace App\Models;

class UserModel extends Model
{
    public function getUserByEmail(string $email): ?array
    {
        $results = $this->db->query("
            SELECT u.*, p.first_name, p.last_name, p.is_active
            FROM user u
            JOIN profile p ON u.id = p.user_id
            WHERE u.email = ?
        ", [$email]);

        return $results[0] ?? null;
    }

    public function getUserById(int $id): ?array
    {
        $results = $this->db->query("
            SELECT u.*, p.first_name, p.last_name, p.is_active
            FROM user u
            JOIN profile p ON u.id = p.user_id
            WHERE u.id = ?
        ", [$id]);

        return $results[0] ?? null;
    }

    public function getAllStudents(): array
    {
        return $this->db->query("
            SELECT u.id, u.email, p.first_name, p.last_name, p.is_active
            FROM user u
            JOIN profile p ON u.id = p.user_id
            WHERE u.role = 'etudiant'
        ");
    }

    public function countStudents(): int
    {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM user WHERE role = 'etudiant'");
        return (int) $result[0]['total'];
    }

    public function getStudentsPaginated(int $limit, int $offset): array
    {
        return $this->db->query("
            SELECT u.id, u.email, p.first_name, p.last_name, p.is_active
            FROM user u
            JOIN profile p ON u.id = p.user_id
            WHERE u.role = 'etudiant'
            LIMIT ? OFFSET ?
        ", [$limit, $offset]);
    }

    public function getAllPilots(): array
    {
        return $this->db->query("
            SELECT u.id, u.email, p.first_name, p.last_name, p.is_active
            FROM user u
            JOIN profile p ON u.id = p.user_id
            WHERE u.role = 'pilote'
        ");
    }

    public function countPilots(): int
    {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM user WHERE role = 'pilote'");
        return (int) $result[0]['total'];
    }

    public function getPilotsPaginated(int $limit, int $offset): array
    {
        return $this->db->query("
            SELECT u.id, u.email, p.first_name, p.last_name, p.is_active
            FROM user u
            JOIN profile p ON u.id = p.user_id
            WHERE u.role = 'pilote'
            LIMIT ? OFFSET ?
        ", [$limit, $offset]);
    }

    public function createUser(string $email, string $password, string $role): int
    {
        $this->db->execute("
            INSERT INTO user (email, password, role) VALUES (?, ?, ?)
        ", [$email, password_hash($password, PASSWORD_BCRYPT), $role]);

        return $this->db->lastInsertId();
    }

    public function createProfile(int $userId, string $firstName, string $lastName): bool
    {
        return $this->db->execute("
            INSERT INTO profile (user_id, first_name, last_name) VALUES (?, ?, ?)
        ", [$userId, $firstName, $lastName]);
    }

    public function updateProfile(int $userId, array $data): bool
    {
        return $this->db->execute("
            UPDATE profile SET first_name = ?, last_name = ? WHERE user_id = ?
        ", [$data['first_name'], $data['last_name'], $userId]);
    }

    public function toggleActive(int $userId, bool $isActive): bool
    {
        return $this->db->execute("
            UPDATE profile SET is_active = ? WHERE user_id = ?
        ", [$isActive, $userId]);
    }

    public function deleteUser(int $id): bool
    {
        return $this->db->execute("DELETE FROM user WHERE id = ?", [$id]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
