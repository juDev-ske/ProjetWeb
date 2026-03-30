<?php
namespace App\Models;

class OffreModel extends Model
{
    public function getAllOffers(): array
    {
        return $this->db->query("
            SELECT o.*, c.name AS company_name, l.city, l.department
            FROM offer o
            JOIN company c  ON o.company_id  = c.id
            JOIN location l ON o.location_id = l.id
            WHERE o.is_active = 1
            ORDER BY o.publication_date DESC
        ");
    }

    public function countAllOffers(): int
    {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM offer WHERE is_active = 1");
        return (int) $result[0]['total'];
    }


    public function getOffersPaginated(int $limit, int $offset): array
    {
        $limit = (int) $limit;
        $offset = (int) $offset;
        return $this->db->query("
        SELECT o.*, c.name AS company_name, l.city, l.department
        FROM offer o
        JOIN company c  ON o.company_id  = c.id
        JOIN location l ON o.location_id = l.id
        WHERE o.is_active = 1
        ORDER BY o.publication_date DESC
        LIMIT {$limit} OFFSET {$offset}
    ");
    }

    public function getOfferById(int $id): ?array
    {
        $results = $this->db->query("
            SELECT o.*, c.name AS company_name, c.email AS company_email,
                   c.phone AS company_phone, c.rating AS company_rating,
                   c.description AS company_description,
                   l.city, l.department
            FROM offer o
            JOIN company c  ON o.company_id  = c.id
            JOIN location l ON o.location_id = l.id
            WHERE o.id = ?
        ", [$id]);

        return $results[0] ?? null;
    }

    public function getOfferSkills(int $offerId): array
    {
        return $this->db->query("
            SELECT s.name
            FROM skill s
            JOIN offer_skill os ON s.id = os.skill_id
            WHERE os.offer_id = ?
        ", [$offerId]);
    }

    public function getOfferApplicationCount(int $offerId): int
    {
        $result = $this->db->query("
            SELECT COUNT(*) AS total FROM application WHERE offer_id = ?
        ", [$offerId]);

        return (int) $result[0]['total'];
    }

    public function createOffer(array $data): bool
    {
        return $this->db->execute("
            INSERT INTO offer (title, description, salary, type, mode, publication_date, company_id, location_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $data['title'],
            $data['description'],
            $data['salary'],
            $data['type'],
            $data['mode'],
            $data['publication_date'],
            $data['company_id'],
            $data['location_id']
        ]);
    }

    public function updateOffer(int $id, array $data): bool
    {
        return $this->db->execute("
            UPDATE offer SET title = ?, description = ?, salary = ?, type = ?, mode = ?
            WHERE id = ?
        ", [$data['title'], $data['description'], $data['salary'], $data['type'], $data['mode'], $id]);
    }

    public function deleteOffer(int $id): bool
    {
        return $this->db->execute("DELETE FROM offer WHERE id = ?", [$id]);
    }

    public function addSkillToOffer(int $offerId, int $skillId): bool
    {
        return $this->db->execute("
            INSERT IGNORE INTO offer_skill (offer_id, skill_id) VALUES (?, ?)
        ", [$offerId, $skillId]);
    }
}
