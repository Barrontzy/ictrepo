<?php
$qrData = '';
$qrUrl = '';
$filename = '';
$generatedQRID = '';

// Incrementing ID counter
function getNextQRID($file = 'last_qr_id.txt') {
    if (!file_exists($file)) {
        file_put_contents($file, "1000");
    }
    $lastID = intval(file_get_contents($file));
    $nextID = $lastID + 1;
    file_put_contents($file, strval($nextID));
    return $nextID;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $uniqueID = getNextQRID();
    $generatedQRID = 'EQP-' . $uniqueID;

    if ($type === 'single') {
        $equipment = htmlspecialchars($_POST['equipment_number'] ?? '');
        $category = htmlspecialchars($_POST['category'] ?? '');
        $building = htmlspecialchars($_POST['building'] ?? '');
        $floor = htmlspecialchars($_POST['floor'] ?? '');
        $room = htmlspecialchars($_POST['room'] ?? '');
        $status = htmlspecialchars($_POST['status'] ?? '');
        $lastMaintenance = htmlspecialchars($_POST['last_maintenance_date'] ?? '');
        $remarks = htmlspecialchars($_POST['remarks'] ?? '');

        $location = "$building-$floor-$room";
        $qrData = "QR-ID: $generatedQRID | Equipment: $equipment | Category: $category | Location: $location | Status: $status | Last Maintenance: $lastMaintenance | Remarks: $remarks";

    } elseif ($type === 'batch') {
        $category = htmlspecialchars($_POST['category'] ?? '');
        $location = htmlspecialchars($_POST['location'] ?? '');
        $status = htmlspecialchars($_POST['status'] ?? '');
        $lastMaintenance = htmlspecialchars($_POST['last_maintenance_date'] ?? '');
        $remarks = htmlspecialchars($_POST['remarks'] ?? '');

        $qrData = "QR-ID: $generatedQRID | Batch QR | Category: $category | Location: $location | Status: $status | Last Maintenance: $lastMaintenance | Remarks: $remarks";
    }

    // QR image URL (external API)
    $filename = 'qr_' . time() . '.png';
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qrData);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>QR Code Generator</title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f7fa;
      color: #333;
    }
    .container {
      max-width: 800px;
      margin: 30px auto;
      background: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #007BFF;
      margin-bottom: 5px;
    }
    p.subtitle {
      text-align: center;
      color: #666;
      margin-bottom: 20px;
    }
    .tabs {
      display: flex;
      justify-content: center;
      border-bottom: 2px solid #ddd;
      margin-bottom: 20px;
    }
    .tab {
      padding: 10px 20px;
      cursor: pointer;
      border-radius: 6px 6px 0 0;
      margin: 0 5px;
      background-color: #f1f1f1;
      transition: background-color 0.3s;
      font-weight: bold;
      color: #007BFF;
    }
    .tab:hover {
      background-color: #e1e1e1;
    }
    .tab.active {
      background-color: #007BFF;
      color: #fff;
      border-bottom: 2px solid #007BFF;
    }
    .form-section {
      display: none;
      padding: 10px 0;
    }
    .form-section.active {
      display: block;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    input[type="text"],
    input[type="date"],
    textarea {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1rem;
      width: 100%;
    }
    button[type="submit"] {
      background-color: #28a745;
      color: #fff;
      border: none;
      padding: 12px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
      transition: background-color 0.3s;
    }
    button[type="submit"]:hover {
      background-color: #218838;
    }
    .qr-result {
      margin-top: 30px;
      padding: 20px;
      background-color: #f8f9fa;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .qr-result h3 {
      margin-top: 0;
      color: #007BFF;
    }
    .qr-result img {
      margin: 20px 0;
      border: 4px solid #007BFF;
      border-radius: 8px;
    }
    .download-print {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 15px;
    }
    .download-print a,
    .download-print button {
      text-decoration: none;
      background-color: #007BFF;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 0.95rem;
      transition: background-color 0.3s;
    }
    .download-print a:hover,
    .download-print button:hover {
      background-color: #0056b3;
    }
    .qr-data-preview {
      margin-top: 20px;
      background-color: #fff;
      border: 1px dashed #007BFF;
      padding: 10px;
      border-radius: 5px;
      font-size: 0.9rem;
      color: #555;
      word-wrap: break-word;
    }
    @media (max-width: 600px) {
      .download-print {
        flex-direction: column;
        gap: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>QR Code Generator</h2>
    <p class="subtitle">Manage and track all ICT equipment</p>

    <div class="tabs">
      <div class="tab <?php echo (!$_POST || $_POST['type'] === 'single') ? 'active' : ''; ?>" onclick="switchTab('single')">Single Equipment</div>
      <div class="tab <?php echo (isset($_POST['type']) && $_POST['type'] === 'batch') ? 'active' : ''; ?>" onclick="switchTab('batch')">Batch Generation</div>
    </div>

    <!-- Single Form -->
    <div id="single" class="form-section <?php echo (!$_POST || $_POST['type'] === 'single') ? 'active' : ''; ?>">
      <form method="post">
        <input type="hidden" name="type" value="single">
        <input type="text" name="equipment_number" placeholder="Equipment Number" required>
        <input type="text" name="category" placeholder="Category" required>
        <input type="text" name="building" placeholder="Building" required>
        <input type="text" name="floor" placeholder="Floor" required>
        <input type="text" name="room" placeholder="Room" required>
        <input type="text" name="status" placeholder="Status" required>
        <input type="date" name="last_maintenance_date" required>
        <textarea name="remarks" placeholder="Remarks / Comments" rows="3"></textarea>
        <button type="submit">⚙️ Generate QR Code</button>
      </form>
    </div>

    <!-- Batch Form -->
    <div id="batch" class="form-section <?php echo (isset($_POST['type']) && $_POST['type'] === 'batch') ? 'active' : ''; ?>">
      <form method="post">
        <input type="hidden" name="type" value="batch">
        <input type="text" name="category" placeholder="Category" required>
        <input type="text" name="location" placeholder="Location (e.g. Building-Floor-Room)" required>
        <input type="text" name="status" placeholder="Status" required>
        <input type="date" name="last_maintenance_date" required>
        <textarea name="remarks" placeholder="Remarks / Comments" rows="3"></textarea>
        <button type="submit">⚙️ Generate Batch QR Code</button>
      </form>
    </div>

    <?php if ($qrUrl): ?>
      <div class="qr-result">
        <h3>Your QR Code</h3>
        <img src="<?php echo $qrUrl; ?>" alt="QR Code" id="qr-image">
        <p><strong>QR CODE ID:</strong> <?php echo htmlspecialchars($generatedQRID); ?></p>
        <div class="download-print">
          <a href="<?php echo $qrUrl; ?>" download="<?php echo $filename; ?>">⬇️ Download</a>
          <button onclick="printQR()">🖨️ Print</button>
        </div>
        <div class="qr-data-preview">
          <strong>QR Data:</strong> <?php echo htmlspecialchars($qrData); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script>
    function switchTab(tab) {
      document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
      document.querySelectorAll('.form-section').forEach(el => el.classList.remove('active'));
      document.querySelector(`.tab[onclick="switchTab('${tab}')"]`).classList.add('active');
      document.getElementById(tab).classList.add('active');
    }
    function printQR() {
      const printContent = document.getElementById('qr-image').outerHTML + document.querySelector('.qr-data-preview').outerHTML;
      const original = document.body.innerHTML;
      document.body.innerHTML = printContent;
      window.print();
      document.body.innerHTML = original;
      window.location.reload();
    }
  </script>
</body>
</html>
