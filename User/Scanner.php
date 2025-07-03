<?php
// Default values
$scanResult = '';
$equipmentID = '';
$equipmentName = '';
$category = '';
$building = '';
$floor = '';
$room = '';
$status = '';
$lastMaintenance = '';
$remarks = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['scan_data'])) {
    $scanResult = trim($_POST['scan_data']);

    // Updated regex parsing
    preg_match('/QR-ID:\s*(EQP-\d+)/', $scanResult, $idMatch);
    preg_match('/Equipment:\s*(.*?)\s*(?:\||$)/', $scanResult, $equipMatch);
    preg_match('/Category:\s*(.*?)\s*(?:\||$)/', $scanResult, $catMatch);
    preg_match('/Location:\s*(.*?)\s*(?:\||$)/', $scanResult, $locMatch);
    preg_match('/Status:\s*(.*?)\s*(?:\||$)/', $scanResult, $statMatch);
    preg_match('/Last Maintenance:\s*(.*?)\s*(?:\||$)/', $scanResult, $maintMatch);
    preg_match('/Remarks:\s*(.*)/', $scanResult, $remMatch);

    $equipmentID = $idMatch[1] ?? '';
    $equipmentName = $equipMatch[1] ?? '';
    $category = $catMatch[1] ?? '';
    $location = $locMatch[1] ?? '';
    $status = $statMatch[1] ?? '';
    $lastMaintenance = $maintMatch[1] ?? '';
    $remarks = $remMatch[1] ?? '';

    // Split location into building-floor-room
    if (strpos($location, '-') !== false) {
        $parts = explode('-', $location);
        $building = $parts[0] ?? '';
        $floor = $parts[1] ?? '';
        $room = $parts[2] ?? '';
    } else {
        $building = $location;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>QR Code Equipment Scanner</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 800px;
      margin: 30px auto;
      background: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    header {
      text-align: center;
      font-size: 2rem;
      color: #007BFF;
    }

    .scanner {
      background: #000;
      border-radius: 8px;
      overflow: hidden;
      margin: 20px 0;
      position: relative;
    }

    video {
      width: 100%;
      height: auto;
    }

    .overlay {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      border: 4px dashed rgba(255,255,255,0.6);
      pointer-events: none;
    }

    .message {
      text-align: center;
      color: #555;
      margin-top: 10px;
    }

    .buttons {
      text-align: center;
      margin: 20px 0;
    }

    .buttons label,
    .buttons button {
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      margin: 5px;
      cursor: pointer;
    }

    .buttons label:hover,
    .buttons button:hover {
      background-color: #0056b3;
    }

    #result {
      margin-top: 20px;
      background: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
    }

    #result h3 {
      color: #007BFF;
      text-align: center;
    }

    #result p {
      margin: 6px 0;
      padding: 4px 8px;
      border-bottom: 1px solid #ddd;
    }

    #manualModal {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.6);
      display: none;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 8px;
      width: 90%;
      max-width: 400px;
      text-align: center;
    }

    .modal-content input[type="text"] {
      width: 90%;
      padding: 10px;
      margin: 12px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1rem;
    }

    .modal-content button {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }

    .modal-content button:hover {
      background-color: #218838;
    }

    @media (max-width: 600px) {
      .buttons label, .buttons button {
        width: 90%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <header>QR Code Equipment Scanner</header>

    <div class="scanner">
      <video id="video" autoplay muted playsinline></video>
      <canvas id="canvas" hidden></canvas>
      <div class="overlay"></div>
    </div>

    <div class="message">Point your camera at the QR code</div>

    <div class="buttons">
      <label for="uploadInput">📷 Upload QR Image</label>
      <button type="button" id="manualBtn">✏️ Type QR Text</button>
    </div>

    <form method="post" id="scanForm">
      <input type="hidden" name="scan_data" id="scan_data">
    </form>

    <input type="file" id="uploadInput" accept="image/*" hidden>

    <div id="result">
      <?php if ($scanResult): ?>
        <h3>Scanned Equipment Details</h3>
        <p><strong>QR ID:</strong> <?= htmlspecialchars($equipmentID) ?></p>
        <p><strong>Equipment Number:</strong> <?= htmlspecialchars($equipmentName) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($category) ?></p>
        <p><strong>Building:</strong> <?= htmlspecialchars($building) ?></p>
        <p><strong>Floor:</strong> <?= htmlspecialchars($floor) ?></p>
        <p><strong>Room:</strong> <?= htmlspecialchars($room) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($status) ?></p>
        <p><strong>Last Maintenance:</strong> <?= htmlspecialchars($lastMaintenance) ?></p>
        <p><strong>Remarks:</strong> <?= htmlspecialchars($remarks) ?></p>
        <hr>
        <p><em>Raw QR Data:</em> <?= htmlspecialchars($scanResult) ?></p>
      <?php else: ?>
        <p style="text-align:center;">No scan result yet. Scan a QR code to see equipment details.</p>
      <?php endif; ?>
    </div>
  </div>

  <div id="manualModal">
    <div class="modal-content">
      <h3>Enter QR Code Text</h3>
      <input type="text" id="manualInput" placeholder="Paste full QR text here">
      <button id="manualSubmit">Submit</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
  <script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const scanInput = document.getElementById('scan_data');
    const scanForm = document.getElementById('scanForm');
    const uploadInput = document.getElementById('uploadInput');
    const manualBtn = document.getElementById('manualBtn');
    const manualModal = document.getElementById('manualModal');
    const manualInput = document.getElementById('manualInput');
    const manualSubmit = document.getElementById('manualSubmit');

    // Start video stream
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
      .then(stream => {
        video.srcObject = stream;
        requestAnimationFrame(tick);
      })
      .catch(err => {
        alert('Unable to access camera');
      });

    function tick() {
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.height = video.videoHeight;
        canvas.width = video.videoWidth;
        ctx.drawImage(video, 0, 0);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        if (code) {
          scanInput.value = code.data.trim();
          scanForm.submit();
        } else {
          requestAnimationFrame(tick);
        }
      } else {
        requestAnimationFrame(tick);
      }
    }

    // Handle image upload
    uploadInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = () => {
        const img = new Image();
        img.onload = () => {
          canvas.width = img.width;
          canvas.height = img.height;
          ctx.drawImage(img, 0, 0);
          const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
          const code = jsQR(imageData.data, imageData.width, imageData.height);
          if (code) {
            scanInput.value = code.data.trim();
            scanForm.submit();
          } else {
            alert('No QR code found in the image.');
          }
        };
        img.src = reader.result;
      };
      reader.readAsDataURL(file);
    });

    // Manual input
    manualBtn.addEventListener('click', () => {
      manualModal.style.display = 'flex';
    });

    manualSubmit.addEventListener('click', () => {
      const val = manualInput.value.trim();
      if (val) {
        scanInput.value = val;
        scanForm.submit();
      }
    });

    manualModal.addEventListener('click', e => {
      if (e.target === manualModal) {
        manualModal.style.display = 'none';
      }
    });
  </script>
</body>
</html>
