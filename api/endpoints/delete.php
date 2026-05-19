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
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') { http_response_code(405); echo json_encode(['success'=>false,'data'=>null,'error'=>'Method not allowed']); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Vehicle.php';

function ok($data): void { echo json_encode(['success'=>true,'data'=>$data,'error'=>'']); exit; }
function fail(string $err, int $code = 400): void { http_response_code($code); echo json_encode(['success'=>false,'data'=>null,'error'=>$err]); exit; }

$resource = $_GET['resource'] ?? '';

switch ($resource) {

    case 'vehicle':
        $id     = (int) ($_GET['id']      ?? 0);
        $userId = (int) ($_GET['user_id'] ?? 0);
        if (!$id || !$userId) fail('id y user_id requeridos');
        if (!Vehicle::delete($id, $userId)) fail('Vehículo no encontrado', 404);
        ok(['deleted_id' => $id]);

    default:
        fail('Unknown resource');
}
