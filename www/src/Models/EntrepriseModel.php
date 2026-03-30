<?php
namespace App\Models;

class EntrepriseModel extends Model
{
    public function getAllCompanies(): array
    {
        return $this->db->query("
            SELECT c.*, COUNT(o.id) AS offer_count
            FROM company c
            LEFT JOIN offer o ON c.id = o.company_id
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
    }

    public function countAllCompanies(): int
    {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM company");
        return (int) $result[0]['total'];
    }

    public function getCompaniesPaginated(int $limit, int $offset): array
    {
        return $this->db->query("
            SELECT c.*, COUNT(o.id) AS offer_count
            FROM company c
            LEFT JOIN offer o ON c.id = o.company_id
            GROUP BY c.id
            ORDER BY c.name ASC
            LIMIT ? OFFSET ?
        ", [$limit, $offset]);
    }

    public function getCompanyById(int $id): ?array
    {
        $results = $this->db->query("
            SELECT * FROM company WHERE id = ?
        ", [$id]);

        return $results[0] ?? null;
    }

    public function createCompany(array $data): bool
    {
        return $this->db->execute("
            INSERT INTO company (name, description, email, phone)
            VALUES (?, ?, ?, ?)
        ", [$data['name'], $data['description'], $data['email'], $data['phone']]);
    }

    public function updateCompany(int $id, array $data): bool
    {
        return $this->db->execute("
            UPDATE company SET name = ?, description = ?, email = ?, phone = ?
            WHERE id = ?
        ", [$data['name'], $data['description'], $data['email'], $data['phone'], $id]);
    }

    public function updateRating(int $id, float $rating): bool
    {
        return $this->db->execute("
            UPDATE company SET rating = ? WHERE id = ?
        ", [$rating, $id]);
    }

    public function deleteCompany(int $id): bool
    {
        return $this->db->execute("DELETE FROM company WHERE id = ?", [$id]);
    }
}
