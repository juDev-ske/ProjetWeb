<?php
namespace App\Models;

use App\Core\Model;

class WishlistModel extends Model
{
    public function getWishlistByStudent(int $studentId): array
    {
        return $this->db->query("
            SELECT o.id,
                   o.title       AS titre,
                   o.description,
                   o.salary      AS remuneration,
                   o.type,
                   o.mode,
                   o.publication_date AS date,
                   c.name        AS entreprise,
                   l.city        AS ville,
                   GROUP_CONCAT(s.name ORDER BY s.name SEPARATOR '|') AS skill_names
            FROM wishlist w
            JOIN offer o    ON w.offer_id    = o.id
            JOIN company c  ON o.company_id  = c.id
            JOIN location l ON o.location_id = l.id
            LEFT JOIN offer_skill os ON o.id = os.offer_id
            LEFT JOIN skill s        ON os.skill_id = s.id
            WHERE w.student_id = ?
            GROUP BY o.id
            ORDER BY w.added_at DESC
        ", [$studentId]);
    }

    public function addToWishlist(int $studentId, int $offerId): bool
    {
        return $this->db->execute("
            INSERT IGNORE INTO wishlist (student_id, offer_id) VALUES (?, ?)
        ", [$studentId, $offerId]);
    }

    public function removeFromWishlist(int $studentId, int $offerId): bool
    {
        return $this->db->execute("
            DELETE FROM wishlist WHERE student_id = ? AND offer_id = ?
        ", [$studentId, $offerId]);
    }

    public function getWishlistOfferIds(int $studentId): array
    {
        $rows = $this->db->query("
            SELECT offer_id FROM wishlist WHERE student_id = ?
        ", [$studentId]);
        return array_map('intval', array_column($rows, 'offer_id'));
    }

    public function isInWishlist(int $studentId, int $offerId): bool
    {
        $result = $this->db->query("
            SELECT COUNT(*) AS total FROM wishlist
            WHERE student_id = ? AND offer_id = ?
        ", [$studentId, $offerId]);

        return (int) $result[0]['total'] > 0;
    }
}
