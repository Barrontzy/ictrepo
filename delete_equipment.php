<?php
require_once 'config/database.php';
require_once 'models/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header('Location: index.php?error=Invalid equipment ID');
    exit;
}

$equipment = $inventory->getEquipmentById($id);

if (!$equipment) {
    header('Location: index.php?error=Equipment not found');
    exit;
}

if ($_POST && isset($_POST['confirm_delete'])) {
    if ($inventory->deleteEquipment($id)) {
        header('Location: index.php?success=Equipment deleted successfully');
        exit;
    } else {
        $error = 'Error deleting equipment. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Equipment - ICT Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; }
        .delete-container { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .delete-header { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; padding: 1.5rem; border-radius: 12px 12px 0 0; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="delete-container">
                    <div class="delete-header">
                        <h2><i class="fas fa-exclamation-triangle me-2"></i>Delete Equipment</h2>
                        <p class="mb-0">This action cannot be undone</p>
                    </div>
                    
                    <div class="p-4">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning!</strong> You are about to permanently delete this equipment from the inventory.
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Equipment Details</h5>
                                <p><strong>Asset Tag:</strong> <?php echo htmlspecialchars($equipment['asset_tag']); ?></p>
                                <p><strong>Equipment Type:</strong> <?php echo htmlspecialchars($equipment['property_equipment']); ?></p>
                                <p><strong>Department:</strong> <?php echo htmlspecialchars($equipment['department'] ?: 'Not assigned'); ?></p>
                                <p><strong>Assigned Person:</strong> <?php echo htmlspecialchars($equipment['assigned_person'] ?: 'Unassigned'); ?></p>
                                <?php if ($equipment['unit_price']): ?>
                                <p><strong>Unit Price:</strong> ₱<?php echo number_format($equipment['unit_price'], 2); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <form method="POST" class="mt-4">
                            <div class="d-flex gap-2">
                                <button type="submit" name="confirm_delete" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i>Yes, Delete Equipment
                                </button>
                                <a href="view_equipment.php?id=<?php echo $equipment['id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <a href="index.php" class="btn btn-outline-secondary">
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