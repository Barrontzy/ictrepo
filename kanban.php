<?php
session_start();

// Initialize tasks if not yet set
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [
        'To Do' => [
            ['id' => 1, 'title' => 'Software Update', 'device' => 'PC-001 • Desktop', 'assignee' => 'N/A'],
            ['id' => 2, 'title' => 'Configuration Update', 'device' => 'RT-008 • Router', 'assignee' => 'N/A']
        ],
        'In Progress' => [
            ['id' => 3, 'title' => 'Hard Drive Replacement', 'device' => 'LP-045 • Laptop', 'assignee' => 'Jane Smith']
        ],
        'Completed' => [
            ['id' => 4, 'title' => 'Driver Optimization', 'device' => 'SC-007 • Scanner', 'assignee' => 'Carlos De Lara']
        ]
    ];
}

$tasks = &$_SESSION['tasks'];

// Handle new task submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newTask'])) {
    $title = trim($_POST['title']) ?: 'Untitled';
    $device = trim($_POST['device']) ?: 'Unknown';
    $assignee = trim($_POST['assignee']) ?: 'N/A';
    $status = 'To Do';

    $maxId = 0;
    foreach ($tasks as $list) {
        foreach ($list as $t) {
            if ($t['id'] > $maxId) {
                $maxId = $t['id'];
            }
        }
    }

    $newId = $maxId + 1;
    $tasks[$status][] = [
        'id' => $newId,
        'title' => $title,
        'device' => $device,
        'assignee' => $assignee
    ];

    $_SESSION['success_message'] = "New task added successfully!";
    header('Location: kanban.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Kanban Board</title>
    <style>
        body {
            font-family: Arial, sans-serif;
          background: linear-gradient(15deg, rgba(0, 0, 0, 0.65) 0%, rgba(201, 44, 44, 0.65) 47%, rgba(244, 162, 97, 0.65) 100%);
            margin: 0;
            padding: 1rem;
        }

        nav {
            background: rgb(27, 132, 180);
            color: white;
            padding: 1rem;
            text-align: center;
        }

        nav a {
            color: white;
            margin: 0 1rem;
            text-decoration: none;
            font-weight: bold;
        }

        h1 {
            text-align: center;
            margin-top: 1rem;
            color: #333;
        }

        .board {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
        }

        .column {
            flex: 1 1 250px;
            background: white;
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            max-width: 300px;
        }

        .column h3 {
            margin-top: 0;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.5rem;
            color: #2563eb;
        }

        .task {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: background 0.2s;
        }

        .task:hover {
            background: #e0f2fe;
        }

        .add-task-btn {
            display: block;
            margin: 2rem auto;
            padding: 0.75rem 1.5rem;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background 0.3s;
        }

        .add-task-btn:hover {
            background: #1d4ed8;
        }

        #taskForm {
            display: none;
            background: #ffffff;
            max-width: 450px;
            margin: 2rem auto;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        #taskForm h2 {
            margin-top: 0;
            margin-bottom: 1rem;
            color: #2563eb;
            font-size: 1.3rem;
            text-align: center;
        }

        #taskForm label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }

        #taskForm input {
            width: 100%;
            margin-bottom: 1.5rem;
            padding: 0.6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            background: #f9fafb;
            transition: border 0.3s;
        }

        #taskForm input:focus {
            border-color:rgb(37, 205, 235);
            outline: none;
        }

        #taskForm button {
            background: #2563eb;
            color: white;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            transition: background 0.3s;
        }

        #taskForm button:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="add_equipment.php">Add New Item</a>
        <a href="generate_report.php">Generate Reports</a>
    </nav>

    <h1>Manage and Track Maintenance Tasks</h1>

    <?php if (!empty($_SESSION['success_message'])): ?>
        <script>
            alert('<?= addslashes($_SESSION["success_message"]) ?>');
        </script>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <button class="add-task-btn" onclick="toggleTaskForm()">+ New Task</button>

    <form id="taskForm" method="POST">
        <h2>Add New Task</h2>
        <input type="hidden" name="newTask" value="1">

        <label for="title">Task Title</label>
        <input type="text" id="title" name="title" placeholder="e.g., Update Firmware" required>

        <label for="device">Device</label>
        <input type="text" id="device" name="device" placeholder="e.g., PC-001 • Desktop" required>

        <label for="assignee">Assignee</label>
        <input type="text" id="assignee" name="assignee" placeholder="e.g., Juan Dela Cruz">

        <button type="submit">➕ Add Task</button>
    </form>

    <div class="board">
        <?php foreach ($tasks as $status => $list): ?>
            <div class="column">
                <h3><?= htmlspecialchars($status) ?></h3>
                <?php foreach ($list as $task): ?>
                    <a class="task" href="task_detail.php?id=<?= $task['id'] ?>">
                        <strong><?= htmlspecialchars($task['title']) ?></strong>
                        <p><?= htmlspecialchars($task['device']) ?></p>
                        <p>Assigned: <?= htmlspecialchars($task['assignee']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function toggleTaskForm() {
            const form = document.getElementById('taskForm');
            form.style.display = (form.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</body>
</html>
