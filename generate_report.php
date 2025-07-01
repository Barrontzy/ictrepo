<?php
require_once 'config/database.php';
require_once 'models/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

// Get statistics for report summary
$stats = $inventory->getStats();
$equipmentStats = $inventory->getEquipmentStats();
$departmentStats = $inventory->getDepartmentStats();

// Get filter options
$departments = $inventory->getAllDepartments();
$equipmentTypes = $inventory->getAllEquipmentTypes();

// Handle form submission
if ($_POST) {
    $reportType = $_POST['report_type'] ?? 'inventory';
    $format = $_POST['format'] ?? 'pdf';
    $dateRange = $_POST['date_range'] ?? 'all';
    $selectedDepartments = $_POST['departments'] ?? [];
    $selectedEquipment = $_POST['equipment'] ?? [];
    $includeSpecs = isset($_POST['include_specs']);
    $includeFinancial = isset($_POST['include_financial']);
    $includeImages = isset($_POST['include_images']);
    
    // Redirect to report generation
    $params = http_build_query([
        'type' => $reportType,
        'format' => $format,
        'date_range' => $dateRange,
        'departments' => implode(',', $selectedDepartments),
        'equipment' => implode(',', $selectedEquipment),
        'include_specs' => $includeSpecs ? '1' : '0',
        'include_financial' => $includeFinancial ? '1' : '0',
        'include_images' => $includeImages ? '1' : '0'
    ]);
    
    header("Location: download_report.php?" . $params);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports - Inventory Management</title>
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

        .header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .header p {
            color: #64748b;
            margin: 0.5rem 0 0 0;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .report-template {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 1rem;
        }

        .report-template:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }

        .report-template.selected {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .report-template-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-bottom: 1rem;
        }

        .report-template-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .report-template-desc {
            font-size: 0.875rem;
            color: #64748b;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control, .form-select {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-item input[type="checkbox"] {
            margin: 0;
        }

        .selected-items {
            margin-top: 0.5rem;
        }

        .selected-badge {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin: 0.25rem 0.25rem 0 0;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }

        .btn-outline:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .summary-label {
            font-size: 0.875rem;
            color: #64748b;
        }

        .summary-value {
            font-weight: 600;
            color: #1e293b;
        }

        .grid {
            display: grid;
            gap: 1.5rem;
        }

        .grid-cols-2 {
            grid-template-columns: 2fr 1fr;
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        @media (max-width: 768px) {
            .grid-cols-2, .grid-cols-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="index.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            <div>
                <h1>Generate Reports</h1>
                <p>Create comprehensive inventory reports and analytics</p>
            </div>
        </div>

        <form method="POST" id="reportForm">
            <div class="grid grid-cols-2">
                <!-- Report Configuration -->
                <div>
                    <!-- Report Type Selection -->
                    <div class="card">
                        <h2 class="card-title">Select Report Type</h2>
                        <div class="grid grid-cols-2">
                            <div class="report-template" data-type="inventory">
                                <div class="report-template-icon" style="background: #3b82f6;">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div class="report-template-title">Complete Inventory Report</div>
                                <div class="report-template-desc">Full listing of all inventory items with details</div>
                            </div>

                            <div class="report-template" data-type="financial">
                                <div class="report-template-icon" style="background: #10b981;">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="report-template-title">Financial Summary Report</div>
                                <div class="report-template-desc">Asset values and financial breakdown by department</div>
                            </div>

                            <div class="report-template" data-type="department">
                                <div class="report-template-icon" style="background: #8b5cf6;">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="report-template-title">Department Analysis Report</div>
                                <div class="report-template-desc">Equipment distribution and usage by department</div>
                            </div>

                            <div class="report-template" data-type="maintenance">
                                <div class="report-template-icon" style="background: #f59e0b;">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div class="report-template-title">Maintenance & Status Report</div>
                                <div class="report-template-desc">Items requiring attention and maintenance schedules</div>
                            </div>

                            <div class="report-template" data-type="incomplete">
                                <div class="report-template-icon" style="background: #ef4444;">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="report-template-title">Incomplete Items Report</div>
                                <div class="report-template-desc">Items missing data that need completion</div>
                            </div>

                            <div class="report-template" data-type="acquisition">
                                <div class="report-template-icon" style="background: #6366f1;">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="report-template-title">Acquisition Timeline Report</div>
                                <div class="report-template-desc">Equipment acquisition patterns and trends</div>
                            </div>
                        </div>
                        <input type="hidden" name="report_type" id="reportType" value="inventory">
                    </div>

                    <!-- Filters -->
                    <div class="card">
                        <h2 class="card-title">
                            <i class="fas fa-filter me-2"></i>
                            Report Filters
                        </h2>

                        <div class="form-section">
                            <div class="form-group">
                                <label class="form-label">Date Range</label>
                                <select name="date_range" class="form-select">
                                    <option value="all">All Time</option>
                                    <option value="last30">Last 30 Days</option>
                                    <option value="last90">Last 90 Days</option>
                                    <option value="thisyear">This Year</option>
                                    <option value="lastyear">Last Year</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Departments</label>
                                <div class="checkbox-group">
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="all-departments" onchange="toggleAllDepartments()">
                                        <label for="all-departments"><strong>All Departments</strong></label>
                                    </div>
                                    <?php while ($dept = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="departments[]" value="<?php echo htmlspecialchars($dept['department']); ?>" id="dept-<?php echo htmlspecialchars($dept['department']); ?>">
                                        <label for="dept-<?php echo htmlspecialchars($dept['department']); ?>"><?php echo htmlspecialchars($dept['department']); ?></label>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                                <div class="selected-items" id="selectedDepartments"></div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Equipment Types</label>
                                <div class="checkbox-group">
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="all-equipment" onchange="toggleAllEquipment()">
                                        <label for="all-equipment"><strong>All Equipment Types</strong></label>
                                    </div>
                                    <?php 
                                    $equipmentTypes->execute(); // Re-execute
                                    while ($equip = $equipmentTypes->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="equipment[]" value="<?php echo htmlspecialchars($equip['property_equipment']); ?>" id="equip-<?php echo htmlspecialchars($equip['property_equipment']); ?>">
                                        <label for="equip-<?php echo htmlspecialchars($equip['property_equipment']); ?>"><?php echo htmlspecialchars($equip['property_equipment']); ?></label>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                                <div class="selected-items" id="selectedEquipment"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Options -->
                    <div class="card">
                        <h2 class="card-title">Report Options</h2>

                        <div class="form-group">
                            <label class="form-label">Output Format</label>
                            <select name="format" class="form-select">
                                <option value="pdf">PDF Document</option>
                                <option value="excel">Excel Spreadsheet</option>
                                <option value="csv">CSV File</option>
                                <option value="html">HTML Report</option>
                            </select>
                        </div>

                        <div class="form-section">
                            <label class="form-label">Include in Report</label>
                            
                            <div class="checkbox-item mb-2">
                                <input type="checkbox" name="include_specs" id="include_specs" checked>
                                <label for="include_specs">Technical Specifications</label>
                            </div>

                            <div class="checkbox-item mb-2">
                                <input type="checkbox" name="include_financial" id="include_financial" checked>
                                <label for="include_financial">Financial Information</label>
                            </div>

                            <div class="checkbox-item mb-2">
                                <input type="checkbox" name="include_images" id="include_images">
                                <label for="include_images">Equipment Images (if available)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Preview & Actions -->
                <div>
                    <!-- Report Summary -->
                    <div class="card">
                        <h2 class="card-title">Report Summary</h2>
                        <div class="summary-card">
                            <div class="summary-item">
                                <span class="summary-label">Report Type</span>
                                <span class="summary-value" id="summaryType">Complete Inventory Report</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Format</span>
                                <span class="summary-value" id="summaryFormat">PDF</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Date Range</span>
                                <span class="summary-value" id="summaryDateRange">All Time</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Departments</span>
                                <span class="summary-value" id="summaryDepartments">All Departments</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Equipment Types</span>
                                <span class="summary-value" id="summaryEquipment">All Types</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card">
                        <h2 class="card-title">Report Statistics</h2>
                        <div class="summary-card">
                            <div class="summary-item">
                                <span class="summary-label">Estimated Items</span>
                                <span class="summary-value"><?php echo number_format($stats['total_items']); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Total Value</span>
                                <span class="summary-value">₱<?php echo number_format($stats['total_value']); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Departments</span>
                                <span class="summary-value"><?php echo $stats['total_departments']; ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Report Size</span>
                                <span class="summary-value">~2.5 MB</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card">
                        <h2 class="card-title">Generate Report</h2>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-download"></i>
                            Generate & Download
                        </button>

                        <button type="button" class="btn btn-outline w-100 mb-3" onclick="previewReport()">
                            <i class="fas fa-eye"></i>
                            Preview Report
                        </button>

                        <div style="padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                            <p style="font-size: 0.75rem; color: #64748b; text-align: center; margin: 0;">
                                Reports are generated in real-time and may take a few moments for large datasets.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Report type selection
        document.querySelectorAll('.report-template').forEach(template => {
            template.addEventListener('click', function() {
                // Remove selected class from all templates
                document.querySelectorAll('.report-template').forEach(t => t.classList.remove('selected'));
                
                // Add selected class to clicked template
                this.classList.add('selected');
                
                // Update hidden input
                const type = this.dataset.type;
                document.getElementById('reportType').value = type;
                
                // Update summary
                const titles = {
                    'inventory': 'Complete Inventory Report',
                    'financial': 'Financial Summary Report',
                    'department': 'Department Analysis Report',
                    'maintenance': 'Maintenance & Status Report',
                    'incomplete': 'Incomplete Items Report',
                    'acquisition': 'Acquisition Timeline Report'
                };
                document.getElementById('summaryType').textContent = titles[type];
            });
        });

        // Set default selection
        document.querySelector('.report-template[data-type="inventory"]').classList.add('selected');

        // Toggle all departments
        function toggleAllDepartments() {
            const allCheckbox = document.getElementById('all-departments');
            const deptCheckboxes = document.querySelectorAll('input[name="departments[]"]');
            
            deptCheckboxes.forEach(checkbox => {
                checkbox.checked = allCheckbox.checked;
            });
            
            updateSelectedDepartments();
        }

        // Toggle all equipment
        function toggleAllEquipment() {
            const allCheckbox = document.getElementById('all-equipment');
            const equipCheckboxes = document.querySelectorAll('input[name="equipment[]"]');
            
            equipCheckboxes.forEach(checkbox => {
                checkbox.checked = allCheckbox.checked;
            });
            
            updateSelectedEquipment();
        }

        // Update selected departments display
        function updateSelectedDepartments() {
            const selected = Array.from(document.querySelectorAll('input[name="departments[]"]:checked'))
                .map(cb => cb.value);
            
            const container = document.getElementById('selectedDepartments');
            container.innerHTML = selected.map(dept => 
                `<span class="selected-badge">${dept}</span>`
            ).join('');
            
            // Update summary
            const summaryText = selected.length === 0 ? 'None Selected' : 
                               selected.length > 3 ? `${selected.length} Selected` : 
                               selected.join(', ');
            document.getElementById('summaryDepartments').textContent = summaryText;
        }

        // Update selected equipment display
        function updateSelectedEquipment() {
            const selected = Array.from(document.querySelectorAll('input[name="equipment[]"]:checked'))
                .map(cb => cb.value);
            
            const container = document.getElementById('selectedEquipment');
            container.innerHTML = selected.map(equip => 
                `<span class="selected-badge">${equip}</span>`
            ).join('');
            
            // Update summary
            const summaryText = selected.length === 0 ? 'None Selected' : 
                               selected.length > 3 ? `${selected.length} Selected` : 
                               selected.join(', ');
            document.getElementById('summaryEquipment').textContent = summaryText;
        }

        // Add event listeners for checkboxes
        document.querySelectorAll('input[name="departments[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedDepartments);
        });

        document.querySelectorAll('input[name="equipment[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedEquipment);
        });

        // Update format summary
        document.querySelector('select[name="format"]').addEventListener('change', function() {
            document.getElementById('summaryFormat').textContent = this.value.toUpperCase();
        });

        // Update date range summary
        document.querySelector('select[name="date_range"]').addEventListener('change', function() {
            const options = {
                'all': 'All Time',
                'last30': 'Last 30 Days',
                'last90': 'Last 90 Days',
                'thisyear': 'This Year',
                'lastyear': 'Last Year',
                'custom': 'Custom Range'
            };
            document.getElementById('summaryDateRange').textContent = options[this.value];
        });

        // Preview report function
        function previewReport() {
            alert('Report preview functionality would open a modal or new window with a sample of the report.');
        }

        // Form validation
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            const reportType = document.getElementById('reportType').value;
            if (!reportType) {
                e.preventDefault();
                alert('Please select a report type.');
                return;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating Report...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
