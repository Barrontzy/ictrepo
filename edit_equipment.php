<?php
require_once 'config/database.php';
require_once 'models/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

$id = isset($_GET['id']) ? $_GET['id'] : null;
$message = '';
$message_type = '';

if (!$id) {
    header('Location: index.php');
    exit;
}

$equipment = $inventory->getEquipmentById($id);

if (!$equipment) {
    header('Location: index.php?error=Equipment not found');
    exit;
}

if ($_POST) {
    $required_fields = ['asset_tag', 'property_equipment'];
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    // Check if asset tag already exists (excluding current record)
    if (!empty($_POST['asset_tag']) && $inventory->assetTagExists($_POST['asset_tag'], $id)) {
        $errors[] = 'Asset tag already exists';
    }
    
    if (empty($errors)) {
        $data = [
            'asset_tag' => $_POST['asset_tag'],
            'property_equipment' => $_POST['property_equipment'],
            'department' => $_POST['department'],
            'assigned_person' => $_POST['assigned_person'],
            'location' => $_POST['location'],
            'unit_price' => !empty($_POST['unit_price']) ? $_POST['unit_price'] : null,
            'date_acquired' => !empty($_POST['date_acquired']) ? $_POST['date_acquired'] : null,
            'useful_life' => $_POST['useful_life'],
            'hardware_specifications' => $_POST['hardware_specifications'],
            'software_specifications' => $_POST['software_specifications'],
            'inventory_item_no' => $_POST['inventory_item_no'],
            'remarks' => $_POST['remarks'],
            'status' => $_POST['status']
        ];
        
        if ($inventory->updateEquipment($id, $data)) {
            $message = 'Equipment updated successfully!';
            $message_type = 'success';
            // Refresh equipment data
            $equipment = $inventory->getEquipmentById($id);
        } else {
            $message = 'Error updating equipment. Please try again.';
            $message_type = 'danger';
        }
    } else {
        $message = implode('<br>', $errors);
        $message_type = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Equipment - ICT Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; }
        .form-container { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .form-header { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: white; padding: 1.5rem; border-radius: 12px 12px 0 0; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container">
                    <div class="form-header">
                        <h2><i class="fas fa-edit me-2"></i>Edit Equipment</h2>
                        <p class="mb-0">Update equipment details</p>
                    </div>
                    
                    <div class="p-4">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Asset Tag *</label>
                                    <input type="text" class="form-control" name="asset_tag" value="<?php echo htmlspecialchars($equipment['asset_tag']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Equipment Type *</label>
                                    <select class="form-select" name="property_equipment" required>
                                        <option value="">Select Equipment Type</option>
                                        <option value="Printer" <?php echo $equipment['property_equipment'] == 'Printer' ? 'selected' : ''; ?>>Printer</option>
                                        <option value="Laptop" <?php echo $equipment['property_equipment'] == 'Laptop' ? 'selected' : ''; ?>>Laptop</option>
                                        <option value="Desktop Computer" <?php echo $equipment['property_equipment'] == 'Desktop Computer' ? 'selected' : ''; ?>>Desktop Computer</option>
                                        <option value="Telephone" <?php echo $equipment['property_equipment'] == 'Telephone' ? 'selected' : ''; ?>>Telephone</option>
                                        <option value="Wireless Access Point" <?php echo $equipment['property_equipment'] == 'Wireless Access Point' ? 'selected' : ''; ?>>Wireless Access Point</option>
                                        <option value="Switch" <?php echo $equipment['property_equipment'] == 'Switch' ? 'selected' : ''; ?>>Network Switch</option>
                                        <option value="Other" <?php echo $equipment['property_equipment'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Department</label>
                                    <input type="text" class="form-control" name="department" value="<?php echo htmlspecialchars($equipment['department']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Assigned Person</label>
                                    <input type="text" class="form-control" name="assigned_person" value="<?php echo htmlspecialchars($equipment['assigned_person']); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Location</label>
                                <input type="text" class="form-control" name="location" value="<?php echo htmlspecialchars($equipment['location']); ?>">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Unit Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" name="unit_price" step="0.01" value="<?php echo $equipment['unit_price']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Date Acquired</label>
                                    <input type="date" class="form-control" name="date_acquired" value="<?php echo $equipment['date_acquired']; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Useful Life</label>
                                    <input type="text" class="form-control" name="useful_life" value="<?php echo htmlspecialchars($equipment['useful_life']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="Working Unit" <?php echo $equipment['status'] == 'Working Unit' ? 'selected' : ''; ?>>Working Unit</option>
                                        <option value="Under Maintenance" <?php echo $equipment['status'] == 'Under Maintenance' ? 'selected' : ''; ?>>Under Maintenance</option>
                                        <option value="Out of Order" <?php echo $equipment['status'] == 'Out of Order' ? 'selected' : ''; ?>>Out of Order</option>
                                        <option value="Incomplete - Needs Data Entry" <?php echo $equipment['status'] == 'Incomplete - Needs Data Entry' ? 'selected' : ''; ?>>Incomplete - Needs Data Entry</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hardware Specifications</label>
                                <textarea class="form-control" name="hardware_specifications" rows="3"><?php echo htmlspecialchars($equipment['hardware_specifications']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Software Specifications</label>
                                <textarea class="form-control" name="software_specifications" rows="3"><?php echo htmlspecialchars($equipment['software_specifications']); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Inventory Item No.</label>
                                    <input type="text" class="form-control" name="inventory_item_no" value="<?php echo htmlspecialchars($equipment['inventory_item_no']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Remarks</label>
                                    <input type="text" class="form-control" name="remarks" value="<?php echo htmlspecialchars($equipment['remarks']); ?>">
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i>Update Equipment
                                </button>
                                <a href="view_equipment.php?id=<?php echo $equipment['id']; ?>" class="btn btn-info">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>