<?php
require_once __DIR__ . '/../config/database.php';

class Component {
    public static function getBySlug(string $slug): ?array {
        $db = Database::getInstance();

        $stmt = $db->prepare(
            'SELECT c.slug, c.name, c.description, c.wear, c.wear_status, c.wear_label, c.image_url,
                    s.slug AS system_slug, s.name AS system_name, s.color AS system_color
             FROM components c
             JOIN systems s ON s.slug = c.system_slug
             WHERE c.slug = ?'
        );
        $stmt->execute([$slug]);
        $comp = $stmt->fetch();
        if (!$comp) return null;

        $stmt = $db->prepare(
            'SELECT spec_label, spec_value
             FROM component_specs WHERE component_slug = ? ORDER BY sort_order'
        );
        $stmt->execute([$slug]);
        $comp['specs'] = $stmt->fetchAll();

        $stmt = $db->prepare(
            'SELECT symptom FROM component_symptoms WHERE component_slug = ? ORDER BY sort_order'
        );
        $stmt->execute([$slug]);
        $comp['symptoms'] = array_column($stmt->fetchAll(), 'symptom');

        $stmt = $db->prepare(
            'SELECT tip FROM component_tips WHERE component_slug = ? ORDER BY sort_order'
        );
        $stmt->execute([$slug]);
        $comp['tips'] = array_column($stmt->fetchAll(), 'tip');

        return $comp;
    }
}
