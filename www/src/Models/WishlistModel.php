<?php
namespace App\Models;

use App\Core\Model;

class WishlistModel extends Model
{
    public function getWishlistByStudent(int $studentId): array
    {
        return $this->db->query("
            SELECT o.*, c.name AS company_name, l.city
            FROM wishlist w
            JOIN offer o    ON w.offer_id    = o.id
            JOIN company c  ON o.company_id  = c.id
            JOIN location l ON o.location_id = l.id
            WHERE w.student_id = ?
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

    public function isInWishlist(int $studentId, int $offerId): bool
    {
        $result = $this->db->query("
            SELECT COUNT(*) AS total FROM wishlist
            WHERE student_id = ? AND offer_id = ?
        ", [$studentId, $offerId]);

        return (int) $result[0]['total'] > 0;
    }
}
