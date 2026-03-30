<?php
namespace App\Models;

class CandidatureModel extends Model
{
    public function getApplicationsByStudent(int $studentId): array
    {
        return $this->db->query("
            SELECT a.id,
                   a.student_id,
                   a.offer_id    AS offre_id,
                   a.status      AS statut,
                   a.message,
                   a.cv_path,
                   a.lm_path,
                   a.application_date AS date,
                   o.title       AS titre,
                   o.type,
                   c.name        AS entreprise,
                   l.city        AS ville
            FROM application a
            JOIN offer o    ON a.offer_id   = o.id
            JOIN company c  ON o.company_id = c.id
            JOIN location l ON o.location_id = l.id
            WHERE a.student_id = ?
            ORDER BY a.application_date DESC
        ", [$studentId]);
    }

    public function getApplicationsByOffer(int $offerId): array
    {
        return $this->db->query("
            SELECT a.*, p.first_name AS prenom, p.last_name AS nom, u.email
            FROM application a
            JOIN user u    ON a.student_id = u.id
            JOIN profile p ON u.id         = p.user_id
            WHERE a.offer_id = ?
            ORDER BY a.application_date DESC
        ", [$offerId]);
    }

    public function getApplicationsByPilot(int $pilotId): array
    {
        return $this->db->query("
            SELECT a.id,
                   a.offer_id    AS offre_id,
                   a.status      AS statut,
                   a.cv_path,
                   a.lm_path,
                   a.application_date AS date,
                   o.title       AS titre,
                   c.name        AS entreprise,
                   p.first_name  AS prenom,
                   p.last_name   AS nom,
                   u.email
            FROM application a
            JOIN offer o    ON a.offer_id   = o.id
            JOIN company c  ON o.company_id = c.id
            JOIN user u     ON a.student_id = u.id
            JOIN profile p  ON u.id         = p.user_id
            JOIN student_promotion sp ON u.id = sp.student_id
            JOIN promotion pr ON sp.promotion_id = pr.id
            WHERE pr.pilot_id = ?
            ORDER BY a.application_date DESC
        ", [$pilotId]);
    }

    public function createApplication(int $studentId, int $offerId, string $message, string $cvPath = '', string $lmPath = ''): bool
    {
        return $this->db->execute("
            INSERT INTO application (student_id, offer_id, message, cv_path, lm_path)
            VALUES (?, ?, ?, ?, ?)
        ", [$studentId, $offerId, $message, $cvPath, $lmPath]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->db->execute("
            UPDATE application SET status = ? WHERE id = ?
        ", [$status, $id]);
    }

    public function hasAlreadyApplied(int $studentId, int $offerId): bool
    {
        $result = $this->db->query("
            SELECT COUNT(*) AS total FROM application
            WHERE student_id = ? AND offer_id = ?
        ", [$studentId, $offerId]);
        return (int) $result[0]['total'] > 0;
    }
}
