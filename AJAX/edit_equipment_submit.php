<!-- api/edit_modal_submit.php -->

<?php
require_once 'config/database.php';
require_once 'models/Inventory.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing equipment ID.']);
    exit;
}

$data = [
    'asset_tag' => $_POST['asset_tag'] ?? '',
    'property_equipment' => $_POST['property_equipment'] ?? '',
    'department' => $_POST['department'] ?? '',
    'assigned_person' => $_POST['assigned_person'] ?? '',
    'location' => $_POST['location'] ?? '',
    'unit_price' => $_POST['unit_price'] ?? null,
    'date_acquired' => $_POST['date_acquired'] ?? null,
    'useful_life' => $_POST['useful_life'] ?? '',
    'hardware_specifications' => $_POST['hardware_specifications'] ?? '',
    'software_specifications' => $_POST['software_specifications'] ?? '',
    'inventory_item_no' => $_POST['inventory_item_no'] ?? '',
    'remarks' => $_POST['remarks'] ?? '',
    'status' => $_POST['status'] ?? 'Working Unit'
];

if ($inventory->updateEquipment($id, $data)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed.']);
}
?>