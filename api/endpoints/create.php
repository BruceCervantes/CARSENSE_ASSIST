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
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'data'=>null,'error'=>'Method not allowed']); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Vehicle.php';
require_once __DIR__ . '/../models/ConsultationHistory.php';

function ok($data): void { echo json_encode(['success'=>true,'data'=>$data,'error'=>'']); exit; }
function fail(string $err, int $code = 400): void { http_response_code($code); echo json_encode(['success'=>false,'data'=>null,'error'=>$err]); exit; }

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$resource = $body['resource'] ?? '';

switch ($resource) {

    case 'vehicle':
        if (empty($body['user_id']) || empty($body['brand']) || empty($body['model']) || empty($body['year'])) {
            fail('user_id, brand, model y year son requeridos');
        }
        $plate = trim($body['plate'] ?? '');
        if ($plate !== '' && $plate !== '-' && Vehicle::plateExists($plate)) {
            fail('Esta matrícula ya está registrada en el sistema');
        }
        ok(Vehicle::create($body));

    case 'consultation':
        if (empty($body['user_id'])) fail('user_id requerido');
        ok(ConsultationHistory::create($body));

    default:
        fail('Unknown resource');
}
