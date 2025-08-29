<?php
require_once 'config/database.php';
require_once 'models/Inventory.php';

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $database = new Database();
    $db = $database->getConnection();
    $inventory = new Inventory($db);

    if ($inventory->deleteEquipment($id)) {
        $response['success'] = true;
    }
}

echo json_encode($response);