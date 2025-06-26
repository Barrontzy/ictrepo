<?php
require_once 'config/database.php';
require_once 'models/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header('Location: index.php');
    exit;
}

$equipment = $inventory->getEquipmentById($id);

if (!$equipment) {
    header('Location: index.php?error=Equipment not found');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Equipment - ICT Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; }
        .equipment-card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .equipment-header { background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; padding: 1.5rem; border-radius: 12px 12px 0 0; }
        .info-row { border-bottom: 1px solid #f1f5f9; padding: 1rem 0; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #64748b; }
        .info-value { color: #1e293b; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="equipment-card">
                    <div class="equipment-header">
                        <h2><i class="fas fa-eye me-2"></i>Equipment Details</h2>
                        <p class="mb-0">Asset Tag: <?php echo htmlspecialchars($equipment['asset_tag']); ?></p>
                    </div>
                    
                    <div class="p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Asset Tag</div>
                                    <div class="info-value fw-bold"><?php echo htmlspecialchars($equipment['asset_tag']); ?></div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">Equipment Type</div>
                                    <div class="info-value">
                                        <?php 
                                        $icon = '';
                                        switch(strtolower($equipment['property_equipment'])) {
                                            case 'printer': $icon = 'fas fa-print'; break;
                                            case 'laptop': $icon = 'fas fa-laptop'; break;
                                            case 'desktop computer': $icon = 'fas fa-desktop'; break;
                                            case 'telephone': $icon = 'fas fa-phone'; break;
                                            case 'wireless access point': $icon = 'fas fa-wifi'; break;
                                            case 'switch': $icon = 'fas fa-network-wired'; break;
                                            default: $icon = 'fas fa-cube';
                                        }
                                        ?>
                                        <i class="<?php echo $icon; ?> me-2"></i>
                                        <?php echo htmlspecialchars($equipment['property_equipment'] ?: 'Not specified'); ?>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">Department</div>
                                    <div class="info-value"><?php echo htmlspecialchars($equipment['department'] ?: 'Not assigned'); ?></div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">Assigned Person</div>
                                    <div class="info-value"><?php echo htmlspecialchars($equipment['assigned_person'] ?: 'Unassigned'); ?></div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">Location</div>
                                    <div class="info-value"><?php echo htmlspecialchars($equipment['location'] ?: 'Not specified'); ?></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Unit Price</div>
                                    <div class="info-value fw-bold text-success">
                                        <?php if ($equipment['unit_price']): ?>
                                            ₱<?php echo number_format($equipment['unit_price'], 2); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not specified</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">Date Acquired</div>
                                    <div class="info-value">
                                        <?php if ($equipment['date_acquired']): ?>
                                            <?php echo date('F d, Y', strtotime($equipment['date_acquired'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not specified</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">Useful Life</div>
                                    <div class="info-value"><?php echo htmlspecialchars($equipment['useful_life'] ?: 'Not specified'); ?></div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">Status</div>
                                    <div class="info-value">
                                        <?php 
                                        $statusClass = 'bg-secondary';
                                        $statusText = $equipment['status'] ?: 'Unknown';
                                        
                                        if (strpos(strtolower($statusText), 'working') !== false) {
                                            $statusClass = 'bg-success';
                                        } elseif (strpos(strtolower($statusText), 'incomplete') !== false) {
                                            $statusClass = 'bg-warning';
                                        } elseif (strpos(strtolower($statusText), 'maintenance') !== false) {
                                            $statusClass = 'bg-danger';
                                        }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($statusText); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">Inventory Item No.</div>
                                    <div class="info-value"><?php echo htmlspecialchars($equipment['inventory_item_no'] ?: 'Not specified'); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($equipment['hardware_specifications']): ?>
                        <div class="info-row">
                            <div class="info-label">Hardware Specifications</div>
                            <div class="info-value"><?php echo nl2br(htmlspecialchars($equipment['hardware_specifications'])); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($equipment['software_specifications']): ?>
                        <div class="info-row">
                            <div class="info-label">Software Specifications</div>
                            <div class="info-value"><?php echo nl2br(htmlspecialchars($equipment['software_specifications'])); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($equipment['remarks']): ?>
                        <div class="info-row">
                            <div class="info-label">Remarks</div>
                            <div class="info-value"><?php echo htmlspecialchars($equipment['remarks']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="info-row">
                            <div class="info-label">Date Added</div>
                            <div class="info-value"><?php echo date('F d, Y g:i A', strtotime($equipment['created_at'])); ?></div>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <a href="edit_equipment.php?id=<?php echo $equipment['id']; ?>" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="delete_equipment.php?id=<?php echo $equipment['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this equipment?')">
                                <i class="fas fa-trash me-1"></i>Delete
                            </a>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>