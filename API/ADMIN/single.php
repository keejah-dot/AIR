<?php
// api/products/single.php
header("Content-Type: application/json; charset=UTF-8");

// Database configuration
$host = "localhost";
$db   = "air";
$user = "root";
$pass = "";

try {
    // Check if ID parameter exists
    if (!isset($_GET['id'])) {
        echo json_encode(["error" => "Product ID is required"]);
        exit;
    }
    
    // Validate ID
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false || $id <= 0) {
        echo json_encode(["error" => "Invalid product ID"]);
        exit;
    }
    
    // Connect to database
    $conn = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(["error" => "Product not found"]);
        exit;
    }
    
    // Ensure price is numeric
    $product['price'] = floatval($product['price']);
    
    // Return product data
    echo json_encode($product, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode([
        "error" => "Database error",
        "message" => $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>