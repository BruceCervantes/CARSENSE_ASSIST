<?php
require_once __DIR__ . '/../config/database.php';

class Vehicle {
    public static function getByUser(int $userId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT id, brand, model, year, km, plate, color, accent_color, is_active
             FROM vehicles WHERE user_id = ? ORDER BY id'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO vehicles (user_id, brand, model, year, km, plate, color, accent_color, is_active)
             VALUES (:user_id, :brand, :model, :year, :km, :plate, :color, :accent_color, 0)'
        );
        $stmt->execute([
            ':user_id'      => (int) $data['user_id'],
            ':brand'        => htmlspecialchars($data['brand'] ?? '', ENT_QUOTES),
            ':model'        => htmlspecialchars($data['model'] ?? '', ENT_QUOTES),
            ':year'         => htmlspecialchars($data['year'] ?? '', ENT_QUOTES),
            ':km'           => htmlspecialchars($data['km'] ?? '0', ENT_QUOTES),
            ':plate'        => htmlspecialchars($data['plate'] ?? '-', ENT_QUOTES),
            ':color'        => htmlspecialchars($data['color'] ?? 'Sin especificar', ENT_QUOTES),
            ':accent_color' => htmlspecialchars($data['accent_color'] ?? '#e03030', ENT_QUOTES),
        ]);
        $id = (int) $db->lastInsertId();
        return self::findById($id);
    }

    public static function setActive(int $userId, int $vehicleId): bool {
        $db = Database::getInstance();
        $db->prepare('UPDATE vehicles SET is_active = 0 WHERE user_id = ?')->execute([$userId]);
        $stmt = $db->prepare('UPDATE vehicles SET is_active = 1 WHERE id = ? AND user_id = ?');
        $stmt->execute([$vehicleId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public static function delete(int $id, int $userId): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM vehicles WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        return $stmt->rowCount() > 0;
    }

    private static function findById(int $id): array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id, brand, model, year, km, plate, color, accent_color, is_active FROM vehicles WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
