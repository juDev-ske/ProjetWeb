<?php
namespace App\Models;

use App\Core\Model;

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

    // -------------------------------------------------------
    // Étudiants
    // -------------------------------------------------------

    public function countStudentsFiltered(string $search = ''): int
    {
        $where  = $search ? " AND (p.first_name LIKE ? OR p.last_name LIKE ? OR u.email LIKE ?)" : '';
        $params = $search ? ["%$search%", "%$search%", "%$search%"] : [];
        $r = $this->db->query(
            "SELECT COUNT(*) AS total FROM user u JOIN profile p ON u.id = p.user_id WHERE u.role = 'etudiant' $where",
            $params
        );
        return (int) $r[0]['total'];
    }

    public function getStudentsFiltered(int $limit, int $offset, string $search = ''): array
    {
        $where  = $search ? " AND (p.first_name LIKE ? OR p.last_name LIKE ? OR u.email LIKE ?)" : '';
        $params = $search
            ? ["%$search%", "%$search%", "%$search%", $limit, $offset]
            : [$limit, $offset];
        return $this->db->query("
            SELECT u.id, u.email,
                   p.first_name  AS prenom,
                   p.last_name   AS nom,
                   p.is_active,
                   p.stage_status,
                   COUNT(a.id)   AS nb_candidatures
            FROM user u
            JOIN profile p ON u.id = p.user_id
            LEFT JOIN application a ON u.id = a.student_id
            WHERE u.role = 'etudiant' $where
            GROUP BY u.id
            ORDER BY p.last_name ASC
            LIMIT ? OFFSET ?
        ", $params);
    }

    public function getAllStudents(): array
    {
        return $this->db->query("
            SELECT u.id, u.email,
                   p.first_name  AS prenom,
                   p.last_name   AS nom,
                   p.is_active,
                   p.stage_status,
                   COUNT(a.id)   AS nb_candidatures
            FROM user u
            JOIN profile p ON u.id = p.user_id
            LEFT JOIN application a ON u.id = a.student_id
            WHERE u.role = 'etudiant'
            GROUP BY u.id
            ORDER BY p.last_name ASC
        ");
    }

    public function countStudents(): int
    {
        return $this->countStudentsFiltered();
    }

    public function getStudentsPaginated(int $limit, int $offset): array
    {
        return $this->getStudentsFiltered($limit, $offset);
    }

    // -------------------------------------------------------
    // Pilotes
    // -------------------------------------------------------

    public function countPilotsFiltered(string $search = ''): int
    {
        $where  = $search ? " AND (p.first_name LIKE ? OR p.last_name LIKE ? OR u.email LIKE ?)" : '';
        $params = $search ? ["%$search%", "%$search%", "%$search%"] : [];
        $r = $this->db->query(
            "SELECT COUNT(*) AS total FROM user u JOIN profile p ON u.id = p.user_id WHERE u.role = 'pilote' $where",
            $params
        );
        return (int) $r[0]['total'];
    }

    public function getPilotsFiltered(int $limit, int $offset, string $search = ''): array
    {
        $where  = $search ? " AND (p.first_name LIKE ? OR p.last_name LIKE ? OR u.email LIKE ?)" : '';
        $params = $search
            ? ["%$search%", "%$search%", "%$search%", $limit, $offset]
            : [$limit, $offset];
        return $this->db->query("
            SELECT u.id, u.email,
                   p.first_name AS prenom,
                   p.last_name  AS nom,
                   p.is_active
            FROM user u
            JOIN profile p ON u.id = p.user_id
            WHERE u.role = 'pilote' $where
            ORDER BY p.last_name ASC
            LIMIT ? OFFSET ?
        ", $params);
    }

    public function getAllPilots(): array
    {
        return $this->db->query("
            SELECT u.id, u.email,
                   p.first_name AS prenom,
                   p.last_name  AS nom,
                   p.is_active
            FROM user u
            JOIN profile p ON u.id = p.user_id
            WHERE u.role = 'pilote'
            ORDER BY p.last_name ASC
        ");
    }

    public function countPilots(): int
    {
        return $this->countPilotsFiltered();
    }

    public function getPilotsPaginated(int $limit, int $offset): array
    {
        return $this->getPilotsFiltered($limit, $offset);
    }

    // -------------------------------------------------------
    // Écriture
    // -------------------------------------------------------

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

    public function updateEmail(int $userId, string $email): bool
    {
        return $this->db->execute("
            UPDATE user SET email = ? WHERE id = ?
        ", [$email, $userId]);
    }

    public function updateStageStatus(int $userId, string $status): bool
    {
        return $this->db->execute("
            UPDATE profile SET stage_status = ? WHERE user_id = ?
        ", [$status, $userId]);
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
