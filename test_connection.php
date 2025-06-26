<?php
echo "<h2>🔍 Debugging File Paths</h2>";
echo "<p><strong>Current working directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Script location:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Looking for config file at:</strong> " . __DIR__ . "/config/database.php</p>";

// Check if config directory exists
if (is_dir(__DIR__ . '/config')) {
    echo "<p style='color: green;'>✅ Config directory exists</p>";
    
    // Check if database.php exists
    if (file_exists(__DIR__ . '/config/database.php')) {
        echo "<p style='color: green;'>✅ database.php file exists</p>";
        
        // Try to include the file
        try {
            require_once __DIR__ . '/config/database.php';
            echo "<p style='color: green;'>✅ File included successfully</p>";
            
            // Test the database connection
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                echo "<p style='color: green;'>✅ Database connection successful!</p>";
                
                // Test query
                $query = "SELECT COUNT(*) as total FROM inventory";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo "<p style='color: blue;'>📊 Total inventory items: " . $result['total'] . "</p>";
                
            } else {
                echo "<p style='color: red;'>❌ Database connection failed!</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error including file: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ database.php file does NOT exist</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Config directory does NOT exist</p>";
}

// List all files in the directory
echo "<h3>📁 Files in current directory:</h3>";
$files = scandir(__DIR__);
foreach($files as $file) {
    if ($file != '.' && $file != '..') {
        $type = is_dir(__DIR__ . '/' . $file) ? '📁 Folder' : '📄 File';
        echo "<p>$type: $file</p>";
    }
}
?>