<?php
require_once __DIR__ . '/../config/database.php';

class User {
    public static function findByEmail(string $email): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id, name, email, password_hash, initials FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function create(string $name, string $email, string $password): array {
        $db = Database::getInstance();
        $hash     = password_hash($password, PASSWORD_BCRYPT);
        $initials = mb_strtoupper(mb_substr($name, 0, 2));
        $stmt = $db->prepare(
            'INSERT INTO users (name, email, password_hash, initials) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $email, $hash, $initials]);
        $id = (int) $db->lastInsertId();
        return ['id' => $id, 'name' => $name, 'email' => $email, 'initials' => $initials];
    }

    public static function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }
}
