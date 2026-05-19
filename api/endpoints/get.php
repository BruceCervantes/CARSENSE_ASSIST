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
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); echo json_encode(['success'=>false,'data'=>null,'error'=>'Method not allowed']); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/System.php';
require_once __DIR__ . '/../models/Component.php';
require_once __DIR__ . '/../models/DiagnosticResult.php';
require_once __DIR__ . '/../models/Vehicle.php';
require_once __DIR__ . '/../models/ConsultationHistory.php';

function ok($data): void { echo json_encode(['success'=>true,'data'=>$data,'error'=>'']); exit; }
function fail(string $err, int $code = 400): void { http_response_code($code); echo json_encode(['success'=>false,'data'=>null,'error'=>$err]); exit; }

$resource = $_GET['resource'] ?? '';

switch ($resource) {

    case 'systems':
        ok(System::getAll());

    case 'system':
        $slug = $_GET['slug'] ?? '';
        if (!$slug) fail('slug required');
        $sys = System::getBySlug($slug);
        if (!$sys) fail('System not found', 404);
        ok($sys);

    case 'component':
        $slug = $_GET['slug'] ?? '';
        if (!$slug) fail('slug required');
        $comp = Component::getBySlug($slug);
        if (!$comp) fail('Component not found', 404);
        ok($comp);

    case 'diagnostics':
        ok(DiagnosticResult::getAll());

    case 'diagnostic':
        $slug = $_GET['slug'] ?? '';
        if (!$slug) fail('slug required');
        $res = DiagnosticResult::getBySlug($slug);
        if (!$res) fail('Diagnostic not found', 404);
        ok($res);

    case 'vehicles':
        $userId = (int) ($_GET['user_id'] ?? 0);
        if (!$userId) fail('user_id required');
        ok(Vehicle::getByUser($userId));

    case 'history':
        $userId = (int) ($_GET['user_id'] ?? 0);
        if (!$userId) fail('user_id required');
        ok(ConsultationHistory::getByUser($userId));

    default:
        fail('Unknown resource');
}
