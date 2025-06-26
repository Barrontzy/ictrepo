<?php
require_once 'config/database.php';
require_once 'models/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

// Get statistics
$stats = $inventory->getStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Templates - Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .template-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .template-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.2s;
        }

        .template-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .template-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-bottom: 1rem;
        }

        .template-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .template-desc {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1rem;
        }

        .template-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-outline {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .badge {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Report Templates</h1>
                <p class="text-muted">Quick access to pre-configured reports</p>
            </div>
            <div>
                <a href="generate_report.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Custom Report
                </a>
                <a href="index.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="card">
            <h2 class="mb-4">Available Templates</h2>
            <div class="template-grid">
                <!-- Monthly Inventory Report -->
                <div class="template-card">
                    <div class="template-icon" style="background: #3b82f6;">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="template-title">Monthly Inventory Report</div>
                    <div class="template-desc">Complete monthly overview of all inventory items and changes</div>
                    <div class="template-meta">
                        <span>Last: <?php echo date('Y-m-d'); ?></span>
                        <span class="badge">2.3 MB</span>
                    </div>
                    <div class="btn-group">
                        <a href="download_report.php?type=inventory&format=pdf" class="btn btn-outline">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                        <a href="generate_report.php?template=monthly" class="btn btn-outline">
                            <i class="fas fa-edit"></i>
                            Customize
                        </a>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="template-card">
                    <div class="template-icon" style="background: #10b981;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="template-title">Financial Summary</div>
                    <div class="template-desc">Asset values, depreciation, and financial analysis</div>
                    <div class="template-meta">
                        <span>Last: <?php echo date('Y-m-d', strtotime('-5 days')); ?></span>
                        <span class="badge">1.8 MB</span>
                    </div>
                    <div class="btn-group">
                        <a href="download_report.php?type=financial&format=excel&include_financial=1" class="btn btn-outline">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                        <a href="generate_report.php?template=financial" class="btn btn-outline">
                            <i class="fas fa-edit"></i>
                            Customize
                        </a>
                    </div>
                </div>

                <!-- Department Breakdown -->
                <div class="template-card">
                    <div class="template-icon" style="background: #8b5cf6;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="template-title">Department Breakdown</div>
                    <div class="template-desc">Equipment distribution and usage by department</div>
                    <div class="template-meta">
                        <span>Last: <?php echo date('Y-m-d', strtotime('-3 days')); ?></span>
                        <span class="badge">3.1 MB</span>
                    </div>
                    <div class="btn-group">
                        <a href="download_report.php?type=department&format=pdf" class="btn btn-outline">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                        <a href="generate_report.php?template=department" class="btn btn-outline">
                            <i class="fas fa-edit"></i>
                            Customize
                        </a>
                    </div>
                </div>

                <!-- Maintenance Report -->
                <div class="template-card">
                    <div class="template-icon" style="background: #f59e0b;">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="template-title">Maintenance Report</div>
                    <div class="template-desc">Items requiring attention and maintenance schedules</div>
                    <div class="template-meta">
                        <span>Last: <?php echo date('Y-m-d', strtotime('-2 days')); ?></span>
                        <span class="badge">0.9 MB</span>
                    </div>
                    <div class="btn-group">
                        <a href="download_report.php?type=maintenance&format=csv" class="btn btn-outline">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                        <a href="generate_report.php?template=maintenance" class="btn btn-outline">
                            <i class="fas fa-edit"></i>
                            Customize
                        </a>
                    </div>
                </div>

                <!-- Incomplete Items -->
                <div class="template-card">
                    <div class="template-icon" style="background: #ef4444;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="template-title">Incomplete Items Report</div>
                    <div class="template-desc">Items missing data that need completion</div>
                    <div class="template-meta">
                        <span>Last: <?php echo date('Y-m-d', strtotime('-1 day')); ?></span>
                        <span class="badge">0.5 MB</span>
                    </div>
                    <div class="btn-group">
                        <a href="download_report.php?type=incomplete&format=html" class="btn btn-outline">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                        <a href="generate_report.php?template=incomplete" class="btn btn-outline">
                            <i class="fas fa-edit"></i>
                            Customize
                        </a>
                    </div>
                </div>

                <!-- Complete Inventory -->
                <div class="template-card">
                    <div class="template-icon" style="background: #6366f1;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="template-title">Complete Inventory</div>
                    <div class="template-desc">Full detailed listing of all inventory items</div>
                    <div class="template-meta">
                        <span>Last: <?php echo date('Y-m-d'); ?></span>
                        <span class="badge">4.2 MB</span>
                    </div>
                    <div class="btn-group">
                        <a href="download_report.php?type=inventory&format=excel&include_specs=1&include_financial=1" class="btn btn-outline">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                        <a href="generate_report.php?template=complete" class="btn btn-outline">
                            <i class="fas fa-edit"></i>
                            Customize
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <h2 class="mb-4">Report Statistics</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-primary"><?php echo number_format($stats['total_items']); ?></h3>
                        <p class="text-muted">Total Items</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-success">₱<?php echo number_format($stats['total_value']); ?></h3>
                        <p class="text-muted">Total Value</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-info"><?php echo $stats['total_departments']; ?></h3>
                        <p class="text-muted">Departments</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-warning"><?php echo $stats['incomplete_items']; ?></h3>
                        <p class="text-muted">Incomplete Items</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

