<?php
namespace App\Models;

use App\Core\Model;

class PromotionModel extends Model
{
    public function getAllPromotions(): array
    {
        return $this->db->query("
            SELECT p.id,
                   p.name                                         AS nom,
                   p.year                                         AS annee,
                   CONCAT(pr.first_name, ' ', pr.last_name)      AS pilote,
                   COUNT(sp.student_id)                           AS nb_etudiants
            FROM promotion p
            JOIN user u       ON p.pilot_id  = u.id
            JOIN profile pr   ON u.id        = pr.user_id
            LEFT JOIN student_promotion sp ON p.id = sp.promotion_id
            GROUP BY p.id
            ORDER BY p.year DESC
        ");
    }

    public function getPromotionById(int $id): ?array
    {
        $results = $this->db->query("
            SELECT p.id,
                   p.name                                    AS nom,
                   p.year                                    AS annee,
                   CONCAT(pr.first_name, ' ', pr.last_name) AS pilote,
                   (SELECT COUNT(*) FROM student_promotion sp2 WHERE sp2.promotion_id = p.id) AS nb_etudiants
            FROM promotion p
            JOIN user u     ON p.pilot_id = u.id
            JOIN profile pr ON u.id       = pr.user_id
            WHERE p.id = ?
        ", [$id]);

        return $results[0] ?? null;
    }

    public function getStudentsByPromotion(int $promotionId): array
    {
        return $this->db->query("
            SELECT u.id, u.email,
                   p.first_name  AS prenom,
                   p.last_name   AS nom,
                   COUNT(a.id)   AS nb_candidatures
            FROM student_promotion sp
            JOIN user u    ON sp.student_id  = u.id
            JOIN profile p ON u.id           = p.user_id
            LEFT JOIN application a ON u.id  = a.student_id
            WHERE sp.promotion_id = ?
            GROUP BY u.id
        ", [$promotionId]);
    }

    public function createPromotion(string $name, int $year, int $pilotId): bool
    {
        return $this->db->execute("
            INSERT INTO promotion (name, year, pilot_id) VALUES (?, ?, ?)
        ", [$name, $year, $pilotId]);
    }

    public function addStudentToPromotion(int $studentId, int $promotionId): bool
    {
        return $this->db->execute("
            INSERT IGNORE INTO student_promotion (student_id, promotion_id) VALUES (?, ?)
        ", [$studentId, $promotionId]);
    }

    public function removeStudentFromPromotion(int $studentId, int $promotionId): bool
    {
        return $this->db->execute("
            DELETE FROM student_promotion WHERE student_id = ? AND promotion_id = ?
        ", [$studentId, $promotionId]);
    }

    public function deletePromotion(int $id): bool
    {
        return $this->db->execute("DELETE FROM promotion WHERE id = ?", [$id]);
    }
}
