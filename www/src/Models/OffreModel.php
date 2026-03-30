<?php
namespace App\Models;

class OffreModel extends Model
{
    // -------------------------------------------------------
    // Helpers internes
    // -------------------------------------------------------

    private function buildFilters(array $f): array
    {
        $where  = '';
        $params = [];

        if (!empty($f['search'])) {
            $where   .= ' AND (o.title LIKE ? OR c.name LIKE ?)';
            $params[] = '%' . $f['search'] . '%';
            $params[] = '%' . $f['search'] . '%';
        }

        $types = array_filter((array) ($f['type'] ?? []));
        if (!empty($types)) {
            $placeholders = implode(',', array_fill(0, count($types), '?'));
            $where       .= " AND o.type IN ($placeholders)";
            $params       = array_merge($params, array_values($types));
        }

        $modes = array_filter((array) ($f['mode'] ?? []));
        if (!empty($modes)) {
            $placeholders = implode(',', array_fill(0, count($modes), '?'));
            $where       .= " AND o.mode IN ($placeholders)";
            $params       = array_merge($params, array_values($modes));
        }

        if (!empty($f['ville'])) {
            $where   .= ' AND l.city LIKE ?';
            $params[] = '%' . $f['ville'] . '%';
        }

        return [$where, $params];
    }

    private function selectOffres(): string
    {
        return "
            SELECT o.id,
                   o.title       AS titre,
                   o.description,
                   o.salary      AS remuneration,
                   o.type,
                   o.mode,
                   o.publication_date AS date,
                   o.is_active,
                   o.company_id,
                   o.location_id,
                   c.name        AS entreprise,
                   l.city        AS ville,
                   l.department,
                   GROUP_CONCAT(s.name ORDER BY s.name SEPARATOR '|') AS skill_names
            FROM offer o
            JOIN company c  ON o.company_id  = c.id
            JOIN location l ON o.location_id = l.id
            LEFT JOIN offer_skill os ON o.id = os.offer_id
            LEFT JOIN skill s        ON os.skill_id = s.id
        ";
    }

    // -------------------------------------------------------
    // Lecture
    // -------------------------------------------------------

    public function getAllOffers(): array
    {
        return $this->db->query(
            $this->selectOffres() .
            " WHERE o.is_active = 1
              GROUP BY o.id
              ORDER BY o.publication_date DESC"
        );
    }

    public function countOffersFiltered(array $filters = []): int
    {
        [$where, $params] = $this->buildFilters($filters);
        $result = $this->db->query("
            SELECT COUNT(*) AS total
            FROM offer o
            JOIN company c  ON o.company_id  = c.id
            JOIN location l ON o.location_id = l.id
            WHERE o.is_active = 1 $where
        ", $params);
        return (int) $result[0]['total'];
    }

    public function getOffersFiltered(int $limit, int $offset, array $filters = []): array
    {
        [$where, $params] = $this->buildFilters($filters);
        $params[] = $limit;
        $params[] = $offset;
        return $this->db->query(
            $this->selectOffres() .
            " WHERE o.is_active = 1 $where
              GROUP BY o.id
              ORDER BY o.publication_date DESC
              LIMIT ? OFFSET ?",
            $params
        );
    }

    public function countAllOffers(): int
    {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM offer WHERE is_active = 1");
        return (int) $result[0]['total'];
    }

    public function getOffersPaginated(int $limit, int $offset): array
    {
        return $this->getOffersFiltered($limit, $offset);
    }

    public function getOfferById(int $id): ?array
    {
        $results = $this->db->query("
            SELECT o.id,
                   o.title        AS titre,
                   o.description,
                   o.salary       AS remuneration,
                   o.type,
                   o.mode,
                   o.publication_date AS date,
                   o.is_active,
                   c.name         AS entreprise,
                   c.email        AS entreprise_email,
                   c.phone        AS entreprise_telephone,
                   c.rating       AS entreprise_note,
                   c.description  AS entreprise_description,
                   l.city         AS ville,
                   l.department
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

    // -------------------------------------------------------
    // Statistiques (SFx11)
    // -------------------------------------------------------

    public function countTotal(): int
    {
        $r = $this->db->query("SELECT COUNT(*) AS total FROM offer WHERE is_active = 1");
        return (int) $r[0]['total'];
    }

    public function avgCandidaturesParOffre(): float
    {
        $r = $this->db->query("
            SELECT COALESCE(AVG(cnt), 0) AS moy FROM (
                SELECT COUNT(*) AS cnt
                FROM application
                GROUP BY offer_id
            ) AS sub
        ");
        return round((float) ($r[0]['moy'] ?? 0), 1);
    }

    public function repartitionParType(): array
    {
        return $this->db->query("
            SELECT type, COUNT(*) AS total
            FROM offer WHERE is_active = 1
            GROUP BY type
            ORDER BY total DESC
        ");
    }

    public function topWishlist(int $limit = 5): array
    {
        return $this->db->query("
            SELECT o.id, o.title AS titre, c.name AS entreprise,
                   COUNT(w.offer_id) AS nb_wishlist
            FROM offer o
            JOIN company c ON o.company_id = c.id
            LEFT JOIN wishlist w ON o.id = w.offer_id
            WHERE o.is_active = 1
            GROUP BY o.id
            ORDER BY nb_wishlist DESC
            LIMIT ?
        ", [$limit]);
    }

    // -------------------------------------------------------
    // Écriture
    // -------------------------------------------------------

    public function createOffer(array $data): bool
    {
        return $this->db->execute("
            INSERT INTO offer (title, description, salary, type, mode, publication_date, company_id, location_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $data['title'], $data['description'], $data['salary'],
            $data['type'],  $data['mode'],        $data['publication_date'],
            $data['company_id'], $data['location_id'],
        ]);
    }

    public function updateOffer(int $id, array $data): bool
    {
        return $this->db->execute("
            UPDATE offer
            SET title = ?, description = ?, salary = ?, type = ?, mode = ?
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

    public function getAllSkills(): array
    {
        return $this->db->query("SELECT * FROM skill ORDER BY name ASC");
    }
}
