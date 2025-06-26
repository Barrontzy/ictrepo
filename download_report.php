<?php
require_once 'config/database.php';
require_once 'models/Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);

// Get parameters
$reportType = $_GET['type'] ?? 'inventory';
$format = $_GET['format'] ?? 'pdf';
$dateRange = $_GET['date_range'] ?? 'all';
$departments = !empty($_GET['departments']) ? explode(',', $_GET['departments']) : [];
$equipment = !empty($_GET['equipment']) ? explode(',', $_GET['equipment']) : [];
$includeSpecs = $_GET['include_specs'] ?? '0';
$includeFinancial = $_GET['include_financial'] ?? '0';
$includeImages = $_GET['include_images'] ?? '0';

// Get filtered data based on parameters
$items = $inventory->searchAndFilter('', 
    !empty($departments) ? $departments[0] : '', 
    !empty($equipment) ? $equipment[0] : ''
);

// Generate report based on format
switch ($format) {
    case 'csv':
        generateCSVReport($items, $reportType, $includeSpecs, $includeFinancial);
        break;
    case 'excel':
        generateExcelReport($items, $reportType, $includeSpecs, $includeFinancial);
        break;
    case 'html':
        generateHTMLReport($items, $reportType, $includeSpecs, $includeFinancial);
        break;
    case 'pdf':
    default:
        generatePDFReport($items, $reportType, $includeSpecs, $includeFinancial);
        break;
}

function generateCSVReport($items, $reportType, $includeSpecs, $includeFinancial) {
    $filename = "inventory_report_" . date('Y-m-d') . ".csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // CSV Headers
    $headers = ['Asset Tag', 'Equipment', 'Department', 'Assigned Person', 'Location'];
    
    if ($includeFinancial === '1') {
        $headers[] = 'Unit Price';
        $headers[] = 'Date Acquired';
    }
    
    if ($includeSpecs === '1') {
        $headers[] = 'Hardware Specifications';
        $headers[] = 'Software Specifications';
    }
    
    $headers[] = 'Status';
    
    fputcsv($output, $headers);
    
    // Data rows
    while ($row = $items->fetch(PDO::FETCH_ASSOC)) {
        $data = [
            $row['asset_tag'],
            $row['property_equipment'] ?: 'Not specified',
            $row['department'] ?: 'Not specified',
            $row['assigned_person'] ?: 'Not specified',
            $row['location'] ?: 'Not specified'
        ];
        
        if ($includeFinancial === '1') {
            $data[] = $row['unit_price'] ? '₱' . number_format($row['unit_price'], 2) : 'N/A';
            $data[] = $row['date_acquired'] ?: 'N/A';
        }
        
        if ($includeSpecs === '1') {
            $data[] = $row['hardware_specifications'] ?: 'N/A';
            $data[] = $row['software_specifications'] ?: 'N/A';
        }
        
        $data[] = $row['status'] ?: 'Working Unit';
        
        fputcsv($output, $data);
    }
    
    fclose($output);
    exit;
}

function generateExcelReport($items, $reportType, $includeSpecs, $includeFinancial) {
    // For Excel generation, you would typically use a library like PhpSpreadsheet
    // For now, we'll generate a CSV with Excel-friendly formatting
    
    $filename = "inventory_report_" . date('Y-m-d') . ".xls";
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo '<table border="1">';
    echo '<tr style="background-color: #f0f0f0; font-weight: bold;">';
    echo '<td>Asset Tag</td>';
    echo '<td>Equipment</td>';
    echo '<td>Department</td>';
    echo '<td>Assigned Person</td>';
    echo '<td>Location</td>';
    
    if ($includeFinancial === '1') {
        echo '<td>Unit Price</td>';
        echo '<td>Date Acquired</td>';
    }
    
    if ($includeSpecs === '1') {
        echo '<td>Hardware Specifications</td>';
        echo '<td>Software Specifications</td>';
    }
    
    echo '<td>Status</td>';
    echo '</tr>';
    
    while ($row = $items->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['asset_tag']) . '</td>';
        echo '<td>' . htmlspecialchars($row['property_equipment'] ?: 'Not specified') . '</td>';
        echo '<td>' . htmlspecialchars($row['department'] ?: 'Not specified') . '</td>';
        echo '<td>' . htmlspecialchars($row['assigned_person'] ?: 'Not specified') . '</td>';
        echo '<td>' . htmlspecialchars($row['location'] ?: 'Not specified') . '</td>';
        
        if ($includeFinancial === '1') {
            echo '<td>' . ($row['unit_price'] ? '₱' . number_format($row['unit_price'], 2) : 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['date_acquired'] ?: 'N/A') . '</td>';
        }
        
        if ($includeSpecs === '1') {
            echo '<td>' . htmlspecialchars($row['hardware_specifications'] ?: 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['software_specifications'] ?: 'N/A') . '</td>';
        }
        
        echo '<td>' . htmlspecialchars($row['status'] ?: 'Working Unit') . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    exit;
}

function generateHTMLReport($items, $reportType, $includeSpecs, $includeFinancial) {
    $filename = "inventory_report_" . date('Y-m-d') . ".html";
    
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $reportTitles = [
        'inventory' => 'Complete Inventory Report',
        'financial' => 'Financial Summary Report',
        'department' => 'Department Analysis Report',
        'maintenance' => 'Maintenance & Status Report',
        'incomplete' => 'Incomplete Items Report',
        'acquisition' => 'Acquisition Timeline Report'
    ];
    
    $title = $reportTitles[$reportType] ?? 'Inventory Report';
    
    echo '<!DOCTYPE html>';
    echo '<html><head>';
    echo '<title>' . $title . '</title>';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; margin: 20px; }';
    echo 'h1 { color: #333; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }';
    echo 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
    echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
    echo 'th { background-color: #f8fafc; font-weight: bold; }';
    echo 'tr:nth-child(even) { background-color: #f9f9f9; }';
    echo '.header-info { background: #eff6ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; }';
    echo '</style>';
    echo '</head><body>';
    
    echo '<h1>' . $title . '</h1>';
    echo '<div class="header-info">';
    echo '<p><strong>Generated:</strong> ' . date('F j, Y g:i A') . '</p>';
    echo '<p><strong>Report Type:</strong> ' . $title . '</p>';
    echo '</div>';
    
    echo '<table>';
    echo '<thead><tr>';
    echo '<th>Asset Tag</th>';
    echo '<th>Equipment</th>';
    echo '<th>Department</th>';
    echo '<th>Assigned Person</th>';
    echo '<th>Location</th>';
    
    if ($includeFinancial === '1') {
        echo '<th>Unit Price</th>';
        echo '<th>Date Acquired</th>';
    }
    
    if ($includeSpecs === '1') {
        echo '<th>Hardware Specifications</th>';
        echo '<th>Software Specifications</th>';
    }
    
    echo '<th>Status</th>';
    echo '</tr></thead><tbody>';
    
    while ($row = $items->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['asset_tag']) . '</td>';
        echo '<td>' . htmlspecialchars($row['property_equipment'] ?: 'Not specified') . '</td>';
        echo '<td>' . htmlspecialchars($row['department'] ?: 'Not specified') . '</td>';
        echo '<td>' . htmlspecialchars($row['assigned_person'] ?: 'Not specified') . '</td>';
        echo '<td>' . htmlspecialchars($row['location'] ?: 'Not specified') . '</td>';
        
        if ($includeFinancial === '1') {
            echo '<td>' . ($row['unit_price'] ? '₱' . number_format($row['unit_price'], 2) : 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['date_acquired'] ?: 'N/A') . '</td>';
        }
        
        if ($includeSpecs === '1') {
            echo '<td>' . htmlspecialchars($row['hardware_specifications'] ?: 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['software_specifications'] ?: 'N/A') . '</td>';
        }
        
        echo '<td>' . htmlspecialchars($row['status'] ?: 'Working Unit') . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody></table>';
    echo '</body></html>';
    exit;
}

function generatePDFReport($items, $reportType, $includeSpecs, $includeFinancial) {
    // For PDF generation, you would typically use a library like TCPDF or FPDF
    // For now, we'll redirect to HTML version
    header("Location: download_report.php?type=$reportType&format=html&include_specs=$includeSpecs&include_financial=$includeFinancial");
    exit;
}
?>
