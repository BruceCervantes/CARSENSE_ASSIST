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

    public static function findById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id, name, email, password_hash, initials FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function update(int $id, string $name, string $email, ?string $newPassword): array {
        $db       = Database::getInstance();
        $initials = mb_strtoupper(mb_substr($name, 0, 2));
        if ($newPassword) {
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $db->prepare('UPDATE users SET name=?, email=?, initials=?, password_hash=? WHERE id=?');
            $stmt->execute([$name, $email, $initials, $hash, $id]);
        } else {
            $stmt = $db->prepare('UPDATE users SET name=?, email=?, initials=? WHERE id=?');
            $stmt->execute([$name, $email, $initials, $id]);
        }
        return ['id' => $id, 'name' => $name, 'email' => $email, 'initials' => $initials];
    }

    public static function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }
}
