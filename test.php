<?php
// test_db.php
header("Content-Type: text/html; charset=UTF-8");

$host = "localhost";
$db   = "air";
$user = "root";
$pass = "";

echo "<h2>Database Connection Test</h2>";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check tables
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tables in database '$db':</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
        
        // Show table structure
        $descStmt = $conn->query("DESCRIBE $table");
        $columns = $descStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<ul>";
        foreach ($columns as $col) {
            echo "<li>{$col['Field']} ({$col['Type']})</li>";
        }
        echo "</ul>";
    }
    echo "</ul>";
    
    // Test insert
    echo "<h3>Test INSERT:</h3>";
    
    // Check if orders table exists
    if (in_array('orders', $tables)) {
        $testData = [
            'order_id' => 'TEST' . time(),
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'customer_address' => 'Test Address',
            'product_id' => 1,
            'product_name' => 'Test Product',
            'product_price' => 99.99,
            'total_amount' => 99.99
        ];
        
        $sql = "INSERT INTO orders (
            order_id, customer_name, customer_email, customer_phone, 
            customer_address, product_id, product_name, product_price, total_amount
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $testStmt = $conn->prepare($sql);
        $result = $testStmt->execute(array_values($testData));
        
        if ($result) {
            echo "<p style='color: green;'>✓ Test insert successful</p>";
            echo "<p>Last insert ID: " . $conn->lastInsertId() . "</p>";
            
            // Show all orders
            $selectStmt = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
            $orders = $selectStmt->fetchAll();
            
            echo "<h3>Last 5 orders:</h3>";
            echo "<pre>" . print_r($orders, true) . "</pre>";
        } else {
            echo "<p style='color: red;'>✗ Test insert failed</p>";
        }
    } else {
        echo "<p style='color: orange;'>Orders table doesn't exist</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Connection failed: " . $e->getMessage() . "</p>";
}
?>