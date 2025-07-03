<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $asset_tag = $_POST['asset_tag'];
    $equipment = $_POST['equipment'];
    $department = $_POST['department'];
    $person = $_POST['person'];
    $location = $_POST['location'];
    $value = $_POST['value'];
    $status = $_POST['status'];

    $db = new Database();
    $conn = $db->getConnection();

    $query = "UPDATE items SET 
                asset_tag = :asset_tag,
                property_equipment = :equipment,
                department = :department,
                assigned_person = :person,
                location = :location,
                unit_price = :value,
                status = :status
              WHERE id = :id";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':asset_tag', $asset_tag);
    $stmt->bindParam(':equipment', $equipment);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':person', $person);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':value', $value);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header("Location: index.php"); // go back to inventory
        exit;
    } else {
        echo "Error updating record.";
    }
}
?>
