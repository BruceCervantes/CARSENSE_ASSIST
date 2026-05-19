<?php
require_once __DIR__ . '/../config/database.php';

class System {
    public static function getAll(): array {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT slug, name, description, color, criticality, image_url,
                    component_count, symptom_count
             FROM systems ORDER BY id'
        );
        return $stmt->fetchAll();
    }

    public static function getBySlug(string $slug): ?array {
        $db = Database::getInstance();

        $stmt = $db->prepare(
            'SELECT slug, name, description, color, criticality, image_url
             FROM systems WHERE slug = ?'
        );
        $stmt->execute([$slug]);
        $sys = $stmt->fetch();
        if (!$sys) return null;

        $stmt = $db->prepare(
            'SELECT component_slug, name, x_pos, y_pos
             FROM system_hotpoints WHERE system_slug = ? ORDER BY sort_order'
        );
        $stmt->execute([$slug]);
        $sys['hotpoints'] = $stmt->fetchAll();

        $stmt = $db->prepare(
            'SELECT slug, name, description, wear, wear_status, wear_label
             FROM components WHERE system_slug = ? ORDER BY id'
        );
        $stmt->execute([$slug]);
        $sys['components'] = $stmt->fetchAll();

        $stmt = $db->prepare(
            'SELECT label, result_slug
             FROM system_symptoms WHERE system_slug = ? ORDER BY sort_order'
        );
        $stmt->execute([$slug]);
        $sys['symptoms'] = $stmt->fetchAll();

        $stmt = $db->prepare(
            'SELECT label, interval_text
             FROM system_maintenance WHERE system_slug = ? ORDER BY sort_order'
        );
        $stmt->execute([$slug]);
        $sys['maintenance'] = $stmt->fetchAll();

        return $sys;
    }
}
