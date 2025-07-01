<?php
// Sample dynamic variables (can be replaced with database queries later)
$totalEquipment = 245;
$pendingTasks = 18;
$completedTasks = 132;
$equipmentCategories = 8;

$tasks = [
  'To Do' => [
    ['title' => 'Software Update', 'device' => 'PC-001 • Desktop', 'date' => '4/24/2025', 'user' => 'John Doe', 'priority' => 'medium'],
    ['title' => 'Configuration Update', 'device' => 'RT-008 • Router', 'date' => '4/22/2025', 'user' => 'Sarah Williams', 'priority' => 'low'],
    ['title' => 'Disk Cleanup', 'device' => 'PC-078 • Desktop', 'date' => '4/24/2025', 'user' => 'John Doe', 'priority' => 'low']
  ],
  'In Progress' => [
    ['title' => 'Hard Drive Replacement', 'device' => 'LP-045 • Laptop', 'date' => '4/24/2025', 'user' => 'Jane Smith', 'priority' => 'high'],
    ['title' => 'Cartridge Replacement', 'device' => 'PR-012 • Printer', 'date' => '4/26/2025', 'user' => 'Mike Johnson', 'priority' => 'medium']
  ],
  'Completed' => [
    ['title' => 'Port Configuration', 'device' => 'SW-075 • Switch', 'date' => '3/26/2025', 'user' => 'Mike Johnson', 'priority' => 'medium'],
    ['title' => 'Signal Optimization', 'device' => 'AP-022 • Access Point', 'date' => '3/25/2025', 'user' => 'Sarah Williams', 'priority' => 'medium'],
    ['title' => 'Driver Update', 'device' => 'SC-007 • Scanner', 'date' => '3/24/2025', 'user' => 'Jane Smith', 'priority' => 'low']
  ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Kanban Dashboard - ICT Services</title>
  <style>
    body {
      background: linear-gradient(15deg, rgba(0, 0, 0, 0.65) 0%, rgba(201, 44, 44, 0.65) 47%, rgba(244, 162, 97, 0.65) 100%);
      font-family: sans-serif;
      color: #1f2937;
      margin: 0;
      padding: 0;
    }
    .header {
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
    }
    .logo {
      font-weight: bold;
      font-size: 1.25rem;
    }
    .nav a {
      margin-left: 1rem;
      text-decoration: none;
      color: #1f2937;
      font-size: 0.875rem;
    }
    .sign-in {
      padding: 0.25rem 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      background-color: #f1f1f1;
    }
    .main {
      padding: 2rem;
    }
    .dashboard-header h2 {
      font-size: 1.5rem;
      font-weight: 600;
    }
    .dashboard-header p {
      font-size: 0.875rem;
      color: #6b7280;
    }
    .stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      margin-top: 1.5rem;
    }
    .stat-card {
      background-color: #fff;
      padding: 1rem;
      border-radius: 0.5rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .label {
      font-size: 0.875rem;
      color: #6b7280;
    }
    .count {
      font-size: 1.5rem;
      font-weight: bold;
    }
    .trend {
      font-size: 0.875rem;
    }
    .positive {
      color: #10b981;
    }
    .negative {
      color: #ef4444;
    }
    .sub-label {
      font-size: 0.75rem;
      color: #6b7280;
    }
    .kanban {
      margin-top: 2rem;
    }
    .kanban h3 {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    .kanban-columns {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }
    .kanban-column {
      background-color: #fff;
      padding: 1rem;
      border-radius: 0.5rem;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .kanban-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
    }
    .count-badge {
      background-color: #e5e7eb;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
    }
    .task {
      background-color: #f9fafb;
      padding: 0.75rem;
      border: 1px solid #e5e7eb;
      border-radius: 0.375rem;
      margin-bottom: 1rem;
    }
    .task-title {
      font-size: 0.875rem;
      font-weight: 500;
    }
    .task-meta {
      font-size: 0.75rem;
      color: #6b7280;
    }
    .priority {
      display: inline-block;
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 0.375rem;
      margin-top: 0.5rem;
    }
    .priority.low {
      background-color: #d1fae5;
      color: #065f46;
    }
    .priority.medium {
      background-color: #fef3c7;
      color: #92400e;
    }
    .priority.high {
      background-color: #fee2e2;
      color: #991b1b;
    }
    .actions {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      margin-top: 2rem;
    }
    .action {
      background-color: #fff;
      padding: 1rem;
      border-radius: 0.5rem;
      text-align: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .action-title {
      font-weight: 600;
      margin-bottom: 0.25rem;
    }
    .action-desc {
      font-size: 0.875rem;
      color: #6b7280;
    }
  </style>
</head>
<body>
  <header class="header">
    <h1 class="logo">ICT Services Management System</h1>
    <nav class="nav">
      <a href="sample.php">Dashboard</a>
      <a href="#">Equipment</a>
      <a href="#">Maintenance Logs</a>
      <a href="#">QR Generator</a>
      <a href="login.php" class="sign-in">Sign In</a>
    </nav>
  </header>

  <main class="main">
    <div class="dashboard-header">
      <h2>Kanban Dashboard</h2>
      <p>Manage and track maintenance tasks</p>
    </div>

    <div class="stats">
      <div class="stat-card">
        <p class="label">Total Equipment</p>
        <p class="count"><?= $totalEquipment ?></p>
        <p class="trend positive">+12 from last month</p>
      </div>
      <div class="stat-card">
        <p class="label">Pending Tasks</p>
        <p class="count"><?= $pendingTasks ?></p>
        <p class="trend negative">-3 from last week</p>
      </div>
      <div class="stat-card">
        <p class="label">Completed Tasks</p>
        <p class="count"><?= $completedTasks ?></p>
        <p class="trend positive">+28 this month</p>
      </div>
      <div class="stat-card">
        <p class="label">Equipment Categories</p>
        <p class="count"><?= $equipmentCategories ?></p>
        <p class="sub-label">Laptops, Desktops, Printers, etc.</p>
      </div>
    </div>

    <div class="kanban">
      <h3>Task Board</h3>
      <div class="kanban-columns">
        <?php foreach ($tasks as $columnName => $taskList): ?>
          <div class="kanban-column">
            <div class="kanban-header">
              <h4><?= $columnName ?></h4>
              <span class="count-badge"><?= count($taskList) ?></span>
            </div>
            <?php foreach ($taskList as $task): ?>
              <div class="task">
                <p class="task-title"><?= $task['title'] ?></p>
                <p class="task-meta"><?= $task['device'] ?></p>
                <p class="task-meta"><?= $task['date'] ?> • <?= $task['user'] ?></p>
                <span class="priority <?= $task['priority'] ?>"><?= ucfirst($task['priority']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="actions">
      <div class="action">
        <p class="action-title">Log Maintenance Task</p>
        <p class="action-desc">Record a new maintenance or repair activity</p>
      </div>
      <div class="action">
        <p class="action-title">Add New Equipment</p>
        <p class="action-desc">Register a new device in the inventory</p>
      </div>
      <div class="action">
        <p class="action-title">Generate QR Codes</p>
        <p class="action-desc">Create QR codes for equipment tracking</p>
      </div>
      <div class="action">
        <p class="action-title">Search Equipment</p>
        <p class="action-desc">Find equipment by PC number or category</p>
      </div>
    </div>
  </main>
</body>
</html>
