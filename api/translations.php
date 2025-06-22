<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../src/TranslationUnit.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Extract ID from URL if present
$id = null;
if (count($pathParts) > 2 && $pathParts[1] === 'api' && $pathParts[2] === 'translations.php') {
    if (isset($pathParts[3])) {
        $id = $pathParts[3];
    }
}

switch ($method) {
    case 'GET':
        if ($id) {
            $unit = TranslationUnit::getById($id);
            if ($unit) {
                echo json_encode($unit->toArray());
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Translation unit not found']);
            }
        } else {
            $translations = TranslationUnit::fetchAll();
            echo json_encode($translations);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['source'])) {
            $unit = new TranslationUnit($input['source'], $input['target'] ?? '');
            $unit->save();
            http_response_code(201);
            echo json_encode($unit->toArray());
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Source text is required']);
        }
        break;

    case 'PUT':
        if ($id) {
            $unit = TranslationUnit::getById($id);
            if ($unit) {
                $input = json_decode(file_get_contents('php://input'), true);
                if (isset($input['target'])) {
                    $unit->update($input['target']);
                    echo json_encode($unit->toArray());
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Target text is required']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Translation unit not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID is required for update']);
        }
        break;

    case 'DELETE':
        if ($id) {
            $success = TranslationUnit::delete($id);
            if ($success) {
                http_response_code(204);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Translation unit not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID is required for deletion']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
