<?php
namespace App\Models;

use App\Core\Model;

class EntrepriseModel extends Model
{
    private function selectCompanies(): string
    {
        return "
            SELECT c.id, c.name AS nom, c.description, c.email, c.phone, c.rating,
                   COUNT(o.id) AS nb_offres
            FROM company c
            LEFT JOIN offer o ON c.id = o.company_id
        ";
    }

    // -------------------------------------------------------
    // Lecture
    // -------------------------------------------------------

    public function getAllCompanies(): array
    {
        return $this->db->query(
            $this->selectCompanies() .
            " GROUP BY c.id ORDER BY c.name ASC"
        );
    }

    public function countCompaniesFiltered(string $search = ''): int
    {
        $where  = $search ? " WHERE c.name LIKE ? OR c.description LIKE ? OR c.email LIKE ?" : '';
        $params = $search ? ["%$search%", "%$search%", "%$search%"] : [];
        $r = $this->db->query(
            "SELECT COUNT(*) AS total FROM company c $where",
            $params
        );
        return (int) $r[0]['total'];
    }

    public function getCompaniesFiltered(int $limit, int $offset, string $search = ''): array
    {
        $where  = $search ? " WHERE c.name LIKE ? OR c.description LIKE ? OR c.email LIKE ?" : '';
        $params = $search ? ["%$search%", "%$search%", "%$search%", $limit, $offset] : [$limit, $offset];
        return $this->db->query(
            $this->selectCompanies() .
            "$where GROUP BY c.id ORDER BY c.name ASC LIMIT ? OFFSET ?",
            $params
        );
    }

    public function countAllCompanies(): int
    {
        $r = $this->db->query("SELECT COUNT(*) AS total FROM company");
        return (int) $r[0]['total'];
    }

    public function getCompaniesPaginated(int $limit, int $offset): array
    {
        return $this->getCompaniesFiltered($limit, $offset);
    }

    public function getCompanyById(int $id): ?array
    {
        $results = $this->db->query("
            SELECT c.id, c.name AS nom, c.description, c.email, c.phone, c.rating,
                   COUNT(DISTINCT o.id) AS nb_offres,
                   COUNT(DISTINCT a.student_id) AS nb_stagiaires,
                   (SELECT COUNT(*) FROM company_rating cr WHERE cr.company_id = c.id) AS nb_evaluations
            FROM company c
            LEFT JOIN offer o       ON c.id = o.company_id
            LEFT JOIN application a ON o.id = a.offer_id
            WHERE c.id = ?
            GROUP BY c.id
        ", [$id]);
        return $results[0] ?? null;
    }

    public function getOffersByCompany(int $companyId): array
    {
        return $this->db->query("
            SELECT o.id, o.title AS titre, o.type, o.mode,
                   o.publication_date AS date, o.is_active,
                   COUNT(a.id) AS nb_candidatures
            FROM offer o
            LEFT JOIN application a ON o.id = a.offer_id
            WHERE o.company_id = ?
            GROUP BY o.id
            ORDER BY o.publication_date DESC
        ", [$companyId]);
    }

    // -------------------------------------------------------
    // Écriture
    // -------------------------------------------------------

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

    public function updateRating(int $companyId, float $rating, int $userId): bool
    {
        // Insérer ou mettre à jour la note de cet utilisateur
        $this->db->execute("
            INSERT INTO company_rating (company_id, user_id, rating)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating), created_at = CURRENT_TIMESTAMP
        ", [$companyId, $userId, $rating]);

        // Recalculer la moyenne et mettre à jour la colonne company.rating
        $result = $this->db->query("
            SELECT ROUND(AVG(rating), 1) AS avg_rating FROM company_rating WHERE company_id = ?
        ", [$companyId]);

        $avg = (float) ($result[0]['avg_rating'] ?? 0);
        return $this->db->execute("
            UPDATE company SET rating = ? WHERE id = ?
        ", [$avg, $companyId]);
    }

    public function deleteCompany(int $id): bool
    {
        return $this->db->execute("DELETE FROM company WHERE id = ?", [$id]);
    }
}
