<?php
header("Content-Type: text/html; charset=UTF-8");

$host = "localhost";
$db   = "air";
$user = "root";
$pass = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<h2>Current Orders in Database</h2>";
    
    $stmt = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 10");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($orders)) {
        echo "<p>No orders found in database</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr>";
        foreach (array_keys($orders[0]) as $column) {
            echo "<th>$column</th>";
        }
        echo "</tr>";
        
        foreach ($orders as $order) {
            echo "<tr>";
            foreach ($order as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>Order Items</h2>";
    
    $itemStmt = $conn->query("SELECT * FROM order_items ORDER BY id DESC LIMIT 10");
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($items)) {
        echo "<p>No order items found</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr>";
        foreach (array_keys($items[0]) as $column) {
            echo "<th>$column</th>";
        }
        echo "</tr>";
        
        foreach ($items as $item) {
            echo "<tr>";
            foreach ($item as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>