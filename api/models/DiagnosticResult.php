<?php
require_once __DIR__ . '/../config/database.php';

class DiagnosticResult {
    private static function attachRelated(PDO $db, array &$result): void {
        $slug = $result['slug'];

        $s = $db->prepare('SELECT tag FROM diagnostic_tags WHERE result_slug = ? ORDER BY sort_order');
        $s->execute([$slug]);
        $result['tags'] = array_column($s->fetchAll(), 'tag');

        $s = $db->prepare('SELECT zone FROM diagnostic_zones WHERE result_slug = ?');
        $s->execute([$slug]);
        $result['zones'] = array_column($s->fetchAll(), 'zone');

        $s = $db->prepare('SELECT when_text FROM diagnostic_when WHERE result_slug = ?');
        $s->execute([$slug]);
        $result['when'] = array_column($s->fetchAll(), 'when_text');

        $s = $db->prepare('SELECT sensation FROM diagnostic_sensations WHERE result_slug = ?');
        $s->execute([$slug]);
        $result['sensations'] = array_column($s->fetchAll(), 'sensation');
    }

    public static function getAll(): array {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT dr.numeric_id, dr.slug, dr.title, dr.description, dr.priority,
                    s.slug AS system_slug, s.name AS system_name
             FROM diagnostic_results dr
             LEFT JOIN systems s ON s.slug = dr.system_slug
             ORDER BY dr.id'
        );
        $results = $stmt->fetchAll();
        foreach ($results as &$r) {
            self::attachRelated($db, $r);
        }
        return $results;
    }

    public static function getBySlug(string $slug): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT dr.numeric_id, dr.slug, dr.title, dr.description, dr.priority,
                    s.slug AS system_slug, s.name AS system_name
             FROM diagnostic_results dr
             LEFT JOIN systems s ON s.slug = dr.system_slug
             WHERE dr.slug = ? OR dr.numeric_id = ?'
        );
        $stmt->execute([$slug, $slug]);
        $result = $stmt->fetch();
        if (!$result) return null;
        self::attachRelated($db, $result);
        return $result;
    }
}
