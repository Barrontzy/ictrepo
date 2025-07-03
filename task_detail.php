<?php
session_start();

$tasks = $_SESSION['tasks'] ?? [];
$taskId = $_GET['id'] ?? null;
$taskFound = null;

if ($taskId !== null) {
  foreach ($tasks as $status => $list) {
    foreach ($list as $task) {
      if ($task['id'] == $taskId) {
        $taskFound = $task;
        $taskFound['status'] = $status;
        break 2;
      }
    }
  }
}

if (!$taskFound) {
  echo "<h2>Task not found.</h2>";
  exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateTask'])) {
  $newAssignee = trim($_POST['assignee']) ?: $taskFound['assignee'];
  $newCondition = trim($_POST['condition']) ?: 'Good';
  $newStatus = $_POST['status'] ?? $taskFound['status'];

  foreach ($tasks as $status => &$list) {
    foreach ($list as $index => &$task) {
      if ($task['id'] == $taskId) {
        $task['assignee'] = $newAssignee;
        $task['condition'] = $newCondition;
        if ($status !== $newStatus) {
          $movedTask = $task;
          unset($list[$index]);
          $tasks[$newStatus][] = $movedTask;
          $list = array_values($list);
        }
        break 2;
      }
    }
  }
  $_SESSION['tasks'] = $tasks;
  $_SESSION['success_message'] = "Task updated successfully!";
  header("Location: kanban.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Details</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f5; padding: 2rem; }
    .card { background: white; padding: 2rem; max-width: 600px; margin: auto; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    h2 { color: #2563eb; }
    input, select { width: 100%; margin: 0.5rem 0; padding: 0.5rem; border: 1px solid #ccc; border-radius: 5px; }
    button { background: #2563eb; color: white; padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer; }
    a { text-decoration: none; display: inline-block; margin-top: 2rem; background: #2563eb; color: white; padding: 0.5rem 1rem; border-radius: 5px; }
  </style>
</head>
<body>
  <div class="card">
    <h2><?= htmlspecialchars($taskFound['title']) ?></h2>
    <p><strong>Device:</strong> <?= htmlspecialchars($taskFound['device']) ?></p>
    <form method="POST">
      <label>Assignee:
        <input type="text" name="assignee" value="<?= htmlspecialchars($taskFound['assignee']) ?>">
      </label>
      <label>Condition:
        <select name="condition">
          <option value="Good" <?= ($taskFound['condition'] ?? '') === 'Good' ? 'selected' : '' ?>>Good</option>
          <option value="Needs Repair" <?= ($taskFound['condition'] ?? '') === 'Needs Repair' ? 'selected' : '' ?>>Needs Repair</option>
          <option value="Replaced" <?= ($taskFound['condition'] ?? '') === 'Replaced' ? 'selected' : '' ?>>Replaced</option>
        </select>
      </label>
      <label>Status:
        <select name="status">
          <option value="To Do" <?= $taskFound['status'] === 'To Do' ? 'selected' : '' ?>>To Do</option>
          <option value="In Progress" <?= $taskFound['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
          <option value="Completed" <?= $taskFound['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
        </select>
      </label>
      <button type="submit" name="updateTask">Update Task</button>
    </form>
    <a href="kanban.php">← Back to Board</a>
  </div>
</body>
</html>
