<?php
ini_set('display_errors', 0);
error_reporting(0);
set_exception_handler(function(Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'data'=>null,'error'=>$e->getMessage()]);
    exit;
});
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') { http_response_code(405); echo json_encode(['success'=>false,'data'=>null,'error'=>'Method not allowed']); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Vehicle.php';
require_once __DIR__ . '/../models/User.php';

function ok($data): void { echo json_encode(['success'=>true,'data'=>$data,'error'=>'']); exit; }
function fail(string $err, int $code = 400): void { http_response_code($code); echo json_encode(['success'=>false,'data'=>null,'error'=>$err]); exit; }

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$resource = $body['resource'] ?? '';

switch ($resource) {

    case 'vehicle_active':
        $userId    = (int) ($body['user_id']    ?? 0);
        $vehicleId = (int) ($body['vehicle_id'] ?? 0);
        if (!$userId || !$vehicleId) fail('user_id y vehicle_id requeridos');
        Vehicle::setActive($userId, $vehicleId);
        ok(['user_id' => $userId, 'active_vehicle_id' => $vehicleId]);

    case 'user_profile':
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user'])) fail('No autenticado', 401);

        $userId      = (int) $_SESSION['user']['id'];
        $name        = trim($body['name']             ?? '');
        $email       = strtolower(trim($body['email'] ?? ''));
        $currentPass = $body['current_password']      ?? '';
        $newPass     = $body['new_password']          ?? '';
        $confirmPass = $body['confirm_password']      ?? '';

        if (!$name || !$email)                         fail('Nombre y correo son requeridos');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Correo electrónico inválido');

        $userRow = User::findById($userId);
        if (!$userRow) fail('Usuario no encontrado', 404);

        if ($newPass !== '' || $currentPass !== '') {
            if (!$currentPass)                                              fail('Ingresa tu contraseña actual para cambiarla');
            if (!User::verifyPassword($currentPass, $userRow['password_hash'])) fail('Contraseña actual incorrecta');
            if (strlen($newPass) < 6)                                       fail('La nueva contraseña debe tener al menos 6 caracteres');
            if ($newPass !== $confirmPass)                                   fail('Las contraseñas nuevas no coinciden');
        }

        if ($email !== $userRow['email']) {
            $existing = User::findByEmail($email);
            if ($existing) fail('Este correo ya está registrado por otra cuenta');
        }

        $updated = User::update($userId, $name, $email, $newPass !== '' ? $newPass : null);
        $_SESSION['user'] = array_merge($_SESSION['user'], $updated);
        ok($updated);

    default:
        fail('Unknown resource');
}
