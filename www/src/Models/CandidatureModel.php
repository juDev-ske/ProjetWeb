<?php
namespace App\Models;

class CandidatureModel extends Model
{
    public function getApplicationsByStudent(int $studentId): array
    {
        return $this->db->query("
            SELECT a.*, o.title AS offer_title, c.name AS company_name, l.city
            FROM application a
            JOIN offer o   ON a.offer_id   = o.id
            JOIN company c ON o.company_id = c.id
            JOIN location l ON o.location_id = l.id
            WHERE a.student_id = ?
            ORDER BY a.application_date DESC
        ", [$studentId]);
    }

    public function getApplicationsByOffer(int $offerId): array
    {
        return $this->db->query("
            SELECT a.*, p.first_name, p.last_name, u.email
            FROM application a
            JOIN user u    ON a.student_id = u.id
            JOIN profile p ON u.id         = p.user_id
            WHERE a.offer_id = ?
            ORDER BY a.application_date DESC
        ", [$offerId]);
    }

    public function createApplication(int $studentId, int $offerId, string $message): bool
    {
        return $this->db->execute("
            INSERT INTO application (student_id, offer_id, message)
            VALUES (?, ?, ?)
        ", [$studentId, $offerId, $message]);
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
