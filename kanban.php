<?php
$totalEquipment = 245;
$pendingTasks = 18;
$inProgressTasks = 13;
$completedTasks = 8;

$tasks = [
  'To Do' => [
    ['title' => 'Software Update', 'device' => 'PC-001 • Desktop', 'count' => 245, 'percent' => 25],
    ['title' => 'Configuration Update', 'device' => 'RT-008 • Router', 'count' => 18, 'percent' => 26],
    ['title' => 'Disk Cleanup', 'device' => 'PC-078 • Desktop', 'count' => 'low', 'percent' => 20]
  ],
  'In Progress' => [
    ['title' => 'Hard Drive Replacement', 'device' => 'LP-045 • Laptop', 'assignee' => 'Jane Smith'],
    ['title' => 'Cartridge Replacement', 'device' => 'PR-012 • Printer', 'assignee' => 'Mike Johnson']
  ],
  'Completed' => [
    ['title' => 'Port Configuration', 'device' => 'SW-075 • Switch', 'progress' => 80],
    ['title' => 'Driver Optimization', 'device' => 'SC-007 • Scanner', 'progress' => 50]
  ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kanban Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9fafb;
      color: #111827;
    }
    .container {
      max-width: 1200px;
      margin: auto;
      padding: 2rem;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }
    .header h1 {
      font-size: 1.8rem;
      font-weight: 700;
    }
    .header nav a {
      margin-left: 1.25rem;
      font-size: 0.95rem;
      text-decoration: none;
      color: #2563eb;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .card {
      background: #ffffff;
      border-radius: 0.5rem;
      padding: 1.25rem;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .card h2 {
      font-size: 2rem;
      font-weight: bold;
      margin: 0;
    }

    .card p {
      margin: 0.25rem 0;
      font-size: 0.9rem;
      color: #6b7280;
    }

    .task-section {
      margin-bottom: 2rem;
    }

    .task-title {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .task-list {
      background: #fff;
      border-radius: 0.5rem;
      padding: 1rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      margin-bottom: 1rem;
    }

    .task {
      margin-bottom: 1rem;
    }

    .task .title {
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    .progress-bar {
      background-color: #e5e7eb;
      height: 6px;
      border-radius: 5px;
      overflow: hidden;
    }

    .progress {
      height: 100%;
      background-color: #3b82f6;
    }

    .footer-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
    }

    .action-box {
      text-align: center;
      background: white;
      padding: 1rem;
      border-radius: 0.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .action-box p {
      font-size: 0.85rem;
      color: #6b7280;
    }

    .action-box strong {
      display: block;
      font-weight: 600;
      margin-bottom: 0.25rem;
      color: #111827;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Kanban Dashboard</h1>
      <nav>
        <a href="#">Dashboard</a>
        <a href="#">Help</a>
        <a href="#">Sign In</a>
      </nav>
    </div>

    <div class="grid">
      <div class="card">
        <h2><?= $totalEquipment ?></h2>
        <p>Total Equipment</p>
        <p style="color: #10b981;">+12.5% from last month</p>
      </div>
      <div class="card">
        <h2><?= $pendingTasks ?></h2>
        <p>Pending Tasks</p>
        <p style="color: #ef4444;">-1 from previous</p>
      </div>
      <div class="card">
        <h2><?= $inProgressTasks ?></h2>
        <p>In Progress</p>
        <p>Items need attention</p>
      </div>
      <div class="card">
        <h2><?= $completedTasks ?></h2>
        <p>Completed</p>
        <p>Need data entry</p>
      </div>
    </div>

    <div class="task-section">
      <h3 class="task-title">Task Board</h3>
      <div class="grid">
        <!-- To-Do Column -->
        <div class="task-list">
          <h4>To Do</h4>
          <?php foreach ($tasks['To Do'] as $task): ?>
            <div class="task">
              <div class="title"><?= $task['title'] ?></div>
              <small><?= $task['device'] ?></small>
              <div class="progress-bar">
                <div class="progress" style="width: <?= $task['percent'] ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- In Progress Column -->
        <div class="task-list">
          <h4>In Progress</h4>
          <?php foreach ($tasks['In Progress'] as $task): ?>
            <div class="task">
              <div class="title"><?= $task['title'] ?></div>
              <small><?= $task['device'] ?> • <?= $task['assignee'] ?></small>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Completed Column -->
        <div class="task-list">
          <h4>Completed</h4>
          <?php foreach ($tasks['Completed'] as $task): ?>
            <div class="task">
              <div class="title"><?= $task['title'] ?></div>
              <small><?= $task['device'] ?></small>
              <div class="progress-bar">
                <div class="progress" style="width: <?= $task['progress'] ?>%;"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="footer-actions">
      <div class="action-box">
        <strong>Log Maintenance Task</strong>
        <p>Record a new maintenance or repair activity</p>
      </div>
      <div class="action-box">
        <strong>Add New Equipment</strong>
        <p>Register a new device in the inventory</p>
      </div>
      <div class="action-box">
        <strong>Generate QR Codes</strong>
        <p>Create QR codes for equipment tracking</p>
      </div>
      <div class="action-box">
        <strong>Search Equipment</strong>
        <p>Find equipment by PC number or category</p>
      </div>
    </div>
  </div>
</body>
</html>
