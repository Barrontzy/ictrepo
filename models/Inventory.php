<?php
class Inventory {
    private $conn;
    private $table_name = "inventory";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Existing methods...
    public function getAllItems() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getStats() {
        $stats = [];
        
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $query = "SELECT SUM(unit_price) as total_value FROM " . $this->table_name . " WHERE unit_price IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_value'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'] ?? 0;
        
        $query = "SELECT COUNT(DISTINCT department) as total_departments FROM " . $this->table_name . " WHERE department IS NOT NULL AND department != ''";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total_departments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_departments'];
        
        try {
            $query = "SELECT COUNT(*) as incomplete FROM " . $this->table_name . " WHERE status LIKE '%incomplete%' OR status IS NULL";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['incomplete_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['incomplete'];
        } catch (PDOException $e) {
            $query = "SELECT COUNT(*) as incomplete FROM " . $this->table_name . " WHERE property_equipment IS NULL OR property_equipment = '' OR property_equipment = 'N/A'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['incomplete_items'] = $stmt->fetch(PDO::FETCH_ASSOC)['incomplete'];
        }
        
        return $stats;
    }

    public function getEquipmentStats() {
        $query = "SELECT property_equipment, COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE property_equipment IS NOT NULL AND property_equipment != '' AND property_equipment != 'N/A'
                  GROUP BY property_equipment 
                  ORDER BY count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getDepartmentStats() {
        $query = "SELECT department, COUNT(*) as count, SUM(unit_price) as total_value FROM " . $this->table_name . " 
                  WHERE department IS NOT NULL AND department != '' 
                  GROUP BY department 
                  ORDER BY count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getAllDepartments() {
        $query = "SELECT DISTINCT department FROM " . $this->table_name . " 
                  WHERE department IS NOT NULL AND department != '' 
                  ORDER BY department";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getAllEquipmentTypes() {
        $query = "SELECT DISTINCT property_equipment FROM " . $this->table_name . " 
                  WHERE property_equipment IS NOT NULL AND property_equipment != '' AND property_equipment != 'N/A'
                  ORDER BY property_equipment";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function searchAndFilter($search_term, $department, $equipment) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];
        
        if (!empty($search_term)) {
            $query .= " AND (asset_tag LIKE ? OR property_equipment LIKE ? OR assigned_person LIKE ? OR location LIKE ?)";
            $search_param = "%{$search_term}%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }
        
        if (!empty($department)) {
            $query .= " AND department = ?";
            $params[] = $department;
        }
        
        if (!empty($equipment)) {
            $query .= " AND property_equipment = ?";
            $params[] = $equipment;
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    // NEW CRUD METHODS

    // Add new equipment
    public function addEquipment($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (asset_tag, property_equipment, department, assigned_person, location, 
                   unit_price, date_acquired, useful_life, hardware_specifications, 
                   software_specifications, inventory_item_no, remarks, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['asset_tag'],
            $data['property_equipment'],
            $data['department'],
            $data['assigned_person'],
            $data['location'],
            $data['unit_price'],
            $data['date_acquired'],
            $data['useful_life'],
            $data['hardware_specifications'],
            $data['software_specifications'],
            $data['inventory_item_no'],
            $data['remarks'],
            $data['status']
        ]);
    }

    // Get equipment by ID
    public function getEquipmentById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update equipment
    public function updateEquipment($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET asset_tag = ?, property_equipment = ?, department = ?, 
                      assigned_person = ?, location = ?, unit_price = ?, 
                      date_acquired = ?, useful_life = ?, hardware_specifications = ?, 
                      software_specifications = ?, inventory_item_no = ?, 
                      remarks = ?, status = ?, updated_at = NOW()
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            $data['asset_tag'],
            $data['property_equipment'],
            $data['department'],
            $data['assigned_person'],
            $data['location'],
            $data['unit_price'],
            $data['date_acquired'],
            $data['useful_life'],
            $data['hardware_specifications'],
            $data['software_specifications'],
            $data['inventory_item_no'],
            $data['remarks'],
            $data['status'],
            $id
        ]);
    }

    // Delete equipment
    public function deleteEquipment($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Check if asset tag exists (for validation)
    public function assetTagExists($asset_tag, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE asset_tag = ?";
        $params = [$asset_tag];
        
        if ($exclude_id) {
            $query .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Get status for display
    public function getItemStatus($row) {
        if (isset($row['status']) && !empty($row['status'])) {
            return $row['status'];
        } elseif (empty($row['property_equipment']) || $row['property_equipment'] == 'N/A') {
            return 'Incomplete - Needs Data Entry';
        } else {
            return 'Working Unit';
        }
    }
}
?>