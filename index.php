<?php
session_start();
require_once 'config/database.php';
require_once 'models/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

// Get statistics
$stats = $inventory->getStats();
$equipmentStats = $inventory->getEquipmentStats();
$departmentStats = $inventory->getDepartmentStats();

// Handle search and filters
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$filter_department = isset($_GET['department']) ? $_GET['department'] : '';
$filter_equipment = isset($_GET['equipment']) ? $_GET['equipment'] : '';

if (!empty($search_term) || !empty($filter_department) || !empty($filter_equipment)) {
    $items = $inventory->searchAndFilter($search_term, $filter_department, $filter_equipment);
} else {
    $items = $inventory->getAllItems();
}

// Get filter options
$departments = $inventory->getAllDepartments();
$equipmentTypes = $inventory->getAllEquipmentTypes();

// Handle success/error messages
$message = '';
$message_type = '';
if (isset($_GET['success'])) {
    $message = $_GET['success'];
    $message_type = 'success';
} elseif (isset($_GET['error'])) {
    $message = $_GET['error'];
    $message_type = 'danger';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .dashboard-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .dashboard-subtitle {
            color: #64748b;
            font-size: 1rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-primary {
            background: #3b82f6;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-outline:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .stat-title {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
        }

        .stat-icon {
            color: #94a3b8;
            font-size: 1rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .stat-change {
            font-size: 0.75rem;
            color: #10b981;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-description {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Main Content Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .content-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        /* Equipment Breakdown */
        .equipment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .equipment-info {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1e293b;
        }

        .equipment-count {
            font-size: 0.875rem;
            color: #64748b;
        }

        .equipment-bar {
            width: 100%;
            height: 8px;
            background: #f1f5f9;
            border-radius: 4px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .equipment-progress {
            height: 100%;
            background: #3b82f6;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .equipment-percentage {
            font-size: 0.75rem;
            color: #64748b;
            margin-left: 0.5rem;
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        /* Department and Activity Grid */
        .secondary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .department-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .department-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .department-dot {
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border-radius: 50%;
        }

        .department-name {
            font-weight: 600;
            color: #1e293b;
        }

        .department-count {
            font-size: 0.875rem;
            color: #64748b;
        }

        .department-value {
            text-align: right;
        }

        .department-price {
            font-weight: 600;
            color: #1e293b;
        }

        .department-status {
            font-size: 0.75rem;
            color: #10b981;
            background: #f0fdf4;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            margin-top: 0.25rem;
        }

        /* Activity Items */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .activity-icon.added {
            background: #f0fdf4;
            color: #10b981;
        }

        .activity-icon.updated {
            background: #eff6ff;
            color: #3b82f6;
        }

        .activity-icon.maintenance {
            background: #fefce8;
            color: #eab308;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.875rem;
        }

        .activity-description {
            color: #64748b;
            font-size: 0.875rem;
            margin: 0.25rem 0;
        }

        .activity-time {
            color: #94a3b8;
            font-size: 0.75rem;
        }

        /* Quick Actions */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .quick-action:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
        }

        .quick-action-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
            color: white;
        }

        .quick-action-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .quick-action-desc {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Inventory Table */
        .inventory-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .inventory-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .search-filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-input {
            flex: 1;
            position: relative;
        }

        .search-input input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .search-input .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .filter-btn {
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 8px;
            color: #64748b;
            font-weight: 500;
            cursor: pointer;
        }

        /* Tabs */
        .tab-nav {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }

        .tab-btn {
            padding: 0.75rem 1rem;
            border: none;
            background: none;
            color: #64748b;
            font-weight: 500;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .tab-btn.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }

        .tab-btn:hover {
            color: #3b82f6;
        }

        /* Table */
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
        }

        .inventory-table th {
            text-align: left;
            padding: 0.75rem;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.875rem;
        }

        .inventory-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.875rem;
        }

        .inventory-table tr:hover {
            background: #f8fafc;
        }

        .asset-tag {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #3b82f6;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-outline {
            border: 1px solid #e2e8f0;
            color: #64748b;
        }

        .badge-success {
            background: #f0fdf4;
            color: #166534;
        }

        .badge-warning {
            background: #fefce8;
            color: #a16207;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .action-btn:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .main-grid {
                grid-template-columns: 1fr;
            }
            
            .secondary-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .quick-actions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Inventory Dashboard</h1>
                <p class="dashboard-subtitle">Real-time overview of your organization's assets</p>
            </div>
            <div class="header-actions">
                <a href="add_equipment.php" class="btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Item
                </a>
                <a href="generate_report.php" class="btn-outline">
                    <i class="fas fa-chart-line me-2"></i>Generate Report
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Items</span>
                    <i class="fas fa-boxes stat-icon"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_items']); ?></div>
                <div class="stat-change">
                    <i class="fas fa-arrow-up"></i>+12.5% from last month
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Value</span>
                    <i class="fas fa-peso-sign stat-icon"></i>
                </div>
                <div class="stat-value">₱<?php echo number_format($stats['total_value'], 0); ?></div>
                <div class="stat-change">
                    <i class="fas fa-arrow-up"></i>+8.3% from last month
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Departments</span>
                    <i class="fas fa-building stat-icon"></i>
                </div>
                <div class="stat-value"><?php echo $stats['total_departments']; ?></div>
                <div class="stat-description">Active departments</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Maintenance</span>
                    <i class="fas fa-exclamation-triangle stat-icon"></i>
                </div>
                <div class="stat-value">23</div>
                <div class="stat-description">Items need attention</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Incomplete</span>
                    <i class="fas fa-exclamation-circle stat-icon"></i>
                </div>
                <div class="stat-value"><?php echo $stats['incomplete_items']; ?></div>
                <div class="stat-description">Need data entry</div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="main-grid">
            <!-- Equipment Breakdown -->
            <div class="content-card">
                <h3 class="card-title">Equipment Breakdown</h3>
                <p class="card-subtitle">Distribution by equipment type</p>
                
                <div class="equipment-breakdown">
                    <?php 
                    $equipmentData = [];
                    while ($equipment = $equipmentStats->fetch(PDO::FETCH_ASSOC)): 
                        $percentage = ($equipment['count'] / $stats['total_items']) * 100;
                        $equipmentData[] = $equipment;
                    ?>
                    <div class="equipment-item">
                        <div>
                            <div class="equipment-info"><?php echo htmlspecialchars($equipment['property_equipment'] ?: 'Unknown'); ?></div>
                            <div class="equipment-bar">
                                <div class="equipment-progress" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div class="equipment-count"><?php echo $equipment['count']; ?> items</div>
                            <div class="equipment-percentage"><?php echo round($percentage); ?>%</div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Inventory Trends Chart -->
            <div class="content-card">
                <h3 class="card-title">Inventory Trends</h3>
                <p class="card-subtitle">Monthly acquisition and disposal trends</p>
                
                <div class="chart-container">
                    <canvas id="inventoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Department Overview and Recent Activity -->
        <div class="secondary-grid">
            <!-- Department Overview -->
            <div class="content-card">
                <h3 class="card-title">
                    <i class="fas fa-building me-2"></i>Department Overview
                </h3>
                
                <div class="department-list">
                    <?php 
                    $deptData = [
                        ['name' => 'CICS', 'items' => 156, 'value' => 450000],
                        ['name' => 'GSO', 'items' => 134, 'value' => 380000],
                        ['name' => 'PFMO', 'items' => 98, 'value' => 290000],
                        ['name' => 'EMU', 'items' => 87, 'value' => 245000],
                        ['name' => 'PSO', 'items' => 76, 'value' => 210000],
                        ['name' => 'HR', 'items' => 65, 'value' => 185000],
                        ['name' => 'PROCUREMENT', 'items' => 54, 'value' => 160000],
                        ['name' => 'Others', 'items' => 626, 'value' => 647890]
                    ];
                    
                    foreach ($deptData as $dept): ?>
                    <div class="department-item">
                        <div class="department-info">
                            <div class="department-dot"></div>
                            <div>
                                <div class="department-name"><?php echo $dept['name']; ?></div>
                                <div class="department-count"><?php echo $dept['items']; ?> items</div>
                            </div>
                        </div>
                        <div class="department-value">
                            <div class="department-price">₱<?php echo number_format($dept['value']); ?></div>
                            <div class="department-status">active</div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="content-card">
                <h3 class="card-title">
                    <i class="fas fa-bolt me-2"></i>Recent Activity
                </h3>
                
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon added">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">ICT-LC-ALTSCI-PR001</div>
                            <div class="activity-description">New printer added to CICS</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon updated">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">ICT-LC-VMBES-LP001</div>
                            <div class="activity-description">Laptop specifications updated</div>
                            <div class="activity-time">4 hours ago</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon maintenance">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">IT-008</div>
                            <div class="activity-description">Switch marked for maintenance</div>
                            <div class="activity-time">6 hours ago</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon added">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">ICT-LC_4LTSPF-AP001</div>
                            <div class="activity-description">Access point installed in PFMO</div>
                            <div class="activity-time">1 day ago</div>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon updated">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">IT-001</div>
                            <div class="activity-description">Incomplete item data completed</div>
                            <div class="activity-time">2 days ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="content-card">
            <h3 class="card-title">Quick Actions</h3>
            
            <div class="quick-actions-grid">
                <a href="add_equipment.php" class="quick-action">
                    <div class="quick-action-icon" style="background: #3b82f6;">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="quick-action-title">Add New Item</div>
                    <div class="quick-action-desc">Register a new inventory item</div>
                </a>

                <a href="incomplete.php" class="quick-action">
                    <div class="quick-action-icon" style="background: #eab308;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="quick-action-title">Complete Incomplete</div>
                    <div class="quick-action-desc">Fill missing item details</div>
                </a>

                <a href="#" class="quick-action">
                    <div class="quick-action-icon" style="background: #10b981;">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="quick-action-title">Import Data</div>
                    <div class="quick-action-desc">Bulk import from CSV</div>
                </a>

                <a href="generate_report.php" class="quick-action">
                    <div class="quick-action-icon" style="background: #8b5cf6;">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="quick-action-title">Generate Report</div>
                    <div class="quick-action-desc">Create custom reports</div>
                </a>

                <a href="#inventory-section" class="quick-action">
                    <div class="quick-action-icon" style="background: #6366f1;">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="quick-action-title">Search Items</div>
                    <div class="quick-action-desc">Advanced item search</div>
                </a>

                <a href="kanban.php" class="quick-action">
                    <div class="quick-action-icon" style="background: #ec4899;">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="quick-action-title">Analytics</div>
                    <div class="quick-action-desc">View detailed analytics</div>
                </a>
            </div>
        </div>

        <!-- Inventory Overview -->
        <div class="inventory-section" id="inventory-section">
            <div class="inventory-header">
                <div>
                    <h3 class="card-title">Inventory Overview</h3>
                    <p class="card-subtitle">Manage and view your inventory items</p>
                </div>
                <div class="search-filter-bar">
                    <div class="search-input">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" placeholder="Search items..." id="searchInput">
                    </div>
                    <button class="filter-btn">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tab-nav">
                <button class="tab-btn active" onclick="showTab('all')">All Items</button>
                <button class="tab-btn" onclick="showTab('printers')">Printers</button>
                <button class="tab-btn" onclick="showTab('laptops')">Laptops</button>
                <button class="tab-btn" onclick="showTab('network')">Network</button>
                <button class="tab-btn" onclick="showTab('incomplete')">Incomplete</button>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Asset Tag</th>
                            <th>Equipment</th>
                            <th>Department</th>
                            <th>Assigned Person</th>
                            <th>Location</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <?php 
                        $itemCount = 0;
                        while ($row = $items->fetch(PDO::FETCH_ASSOC)): 
                            $itemCount++;
                        ?>
                        <tr>
                            <td>
                                <span class="asset-tag"><?php echo htmlspecialchars($row['asset_tag']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($row['property_equipment'] ?: 'Not specified'); ?></td>
                            <td>
                                <?php if ($row['department']): ?>
                                    <span class="badge badge-outline"><?php echo htmlspecialchars($row['department']); ?></span>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-style: italic;">Not specified</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['assigned_person'] ?: 'Not specified'); ?></td>
                            <td><?php echo htmlspecialchars($row['location'] ?: 'Not specified'); ?></td>
                            <td>
                                <?php if ($row['unit_price']): ?>
                                    ₱<?php echo number_format($row['unit_price'], 2); ?>
                                <?php else: ?>
                                    <span style="color: #94a3b8;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $statusClass = 'badge-success';
                                $statusText = $row['status'] ?: 'Working Unit';
                                
                                if (strpos(strtolower($statusText), 'incomplete') !== false) {
                                    $statusClass = 'badge-warning';
                                    $statusText = 'Incomplete';
                                }
                                ?>
                                <span class="badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($statusText); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn" title="Delete" style="color: #ef4444;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Chart
        const ctx = document.getElementById('inventoryChart').getContext('2d');
        const inventoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Acquisitions',
                    data: [45, 52, 38, 61, 55, 67],
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                }, {
                    label: 'Disposals',
                    data: [12, 8, 15, 22, 18, 25],
                    backgroundColor: '#ef4444',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Tab functionality
        function showTab(tabName) {
            // Update active tab
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Filter table based on tab
            const rows = document.querySelectorAll('#inventoryTableBody tr');
            rows.forEach(row => {
                const equipment = row.cells[1].textContent.toLowerCase();
                const status = row.cells[6].textContent.toLowerCase();
                
                let show = false;
                switch(tabName) {
                    case 'all':
                        show = true;
                        break;
                    case 'printers':
                        show = equipment.includes('printer');
                        break;
                    case 'laptops':
                        show = equipment.includes('laptop');
                        break;
                    case 'network':
                        show = equipment.includes('switch') || equipment.includes('access point') || equipment.includes('telephone');
                        break;
                    case 'incomplete':
                        show = status.includes('incomplete');
                        break;
                }
                
                row.style.display = show ? '' : 'none';
            });
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#inventoryTableBody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
