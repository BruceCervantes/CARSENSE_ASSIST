<?php
require_once __DIR__ . '/../config/database.php';

class ConsultationHistory {
    public static function getByUser(int $userId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT id, result_slug, title, system_name, severity, created_at
             FROM consultation_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 20'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO consultation_history (user_id, result_slug, title, system_name, severity)
             VALUES (:user_id, :result_slug, :title, :system_name, :severity)'
        );
        $stmt->execute([
            ':user_id'     => (int) $data['user_id'],
            ':result_slug' => htmlspecialchars($data['result_slug'] ?? '', ENT_QUOTES),
            ':title'       => htmlspecialchars($data['title'] ?? '', ENT_QUOTES),
            ':system_name' => htmlspecialchars($data['system_name'] ?? '', ENT_QUOTES),
            ':severity'    => htmlspecialchars($data['severity'] ?? 'media', ENT_QUOTES),
        ]);
        return ['id' => (int) $db->lastInsertId()];
    }
}
