<?php
// check_products.php - Fixed version
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Content-Type: text/html; charset=UTF-8");

// Database configuration
$host = "localhost";
$db   = "air";
$user = "root";
$pass = "";

echo "<!DOCTYPE html>
<html>
<head>
    <title>Check Products Table</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        table { border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        pre { background-color: #f5f5f5; padding: 10px; border-radius: 5px; }
        .section { margin: 30px 0; padding: 20px; border-left: 4px solid #3498db; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <h1>Products Table Check</h1>";

try {
    // Attempt database connection
    echo "<div class='section'>";
    echo "<h2>1. Database Connection</h2>";
    
    $conn = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<p class='success'>✓ Connected to database '$db' successfully</p>";
    echo "</div>";
    
    // Check if products table exists
    echo "<div class='section'>";
    echo "<h2>2. Table Existence Check</h2>";
    
    $tablesQuery = $conn->query("SHOW TABLES");
    $allTables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>All tables in database:</p>";
    echo "<ul>";
    foreach ($allTables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    $tableExists = in_array('products', $allTables);
    
    if ($tableExists) {
        echo "<p class='success'>✓ 'products' table exists</p>";
    } else {
        echo "<p class='error'>✗ 'products' table does NOT exist</p>";
        echo "</div>";
        exit;
    }
    echo "</div>";
    
    // Show table structure
    echo "<div class='section'>";
    echo "<h2>3. Table Structure</h2>";
    
    $structureQuery = $conn->query("DESCRIBE products");
    $columns = $structureQuery->fetchAll();
    
    echo "<table>
            <tr>
                <th>Field</th>
                <th>Type</th>
                <th>Null</th>
                <th>Key</th>
                <th>Default</th>
                <th>Extra</th>
            </tr>";
    
    foreach ($columns as $col) {
        echo "<tr>
                <td>{$col['Field']}</td>
                <td>{$col['Type']}</td>
                <td>{$col['Null']}</td>
                <td>{$col['Key']}</td>
                <td>" . ($col['Default'] ?? 'NULL') . "</td>
                <td>{$col['Extra']}</td>
              </tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // Count total products
    echo "<div class='section'>";
    echo "<h2>4. Data Statistics</h2>";
    
    $countQuery = $conn->query("SELECT COUNT(*) as total FROM products");
    $countResult = $countQuery->fetch();
    $totalProducts = $countResult['total'];
    
    echo "<p>Total products in table: <strong>$totalProducts</strong></p>";
    
    // Get min and max IDs
    $idRangeQuery = $conn->query("SELECT MIN(id) as min_id, MAX(id) as max_id FROM products");
    $idRange = $idRangeQuery->fetch();
    
    echo "<p>ID Range: {$idRange['min_id']} to {$idRange['max_id']}</p>";
    
    // Check if ID=3 exists - FIXED: Use a different column name
    $checkId3 = $conn->prepare("SELECT COUNT(*) as count_exists FROM products WHERE id = 3");
    $checkId3->execute();
    $id3Exists = $checkId3->fetch()['count_exists'] > 0;
    
    if ($id3Exists) {
        echo "<p class='success'>✓ Product with ID=3 exists</p>";
    } else {
        echo "<p class='warning'>⚠ Product with ID=3 does NOT exist</p>";
    }
    echo "</div>";
    
    // Show sample data
    echo "<div class='section'>";
    echo "<h2>5. Sample Data (All Products)</h2>";
    
    $sampleQuery = $conn->query("SELECT * FROM products ORDER BY id ASC");
    $sampleProducts = $sampleQuery->fetchAll();
    
    if (count($sampleProducts) > 0) {
        echo "<table>";
        echo "<tr>";
        foreach (array_keys($sampleProducts[0]) as $column) {
            echo "<th>$column</th>";
        }
        echo "</tr>";
        
        foreach ($sampleProducts as $product) {
            echo "<tr>";
            foreach ($product as $key => $value) {
                if ($key === 'image') {
                    echo "<td>";
                    if (!empty($value)) {
                        $imagePath1 = __DIR__ . "/" . $value;
                        $imagePath2 = __DIR__ . "/Admin/" . $value;
                        $imagePath3 = dirname(__DIR__) . "/Admin/" . $value;
                        
                        echo htmlspecialchars($value) . "<br>";
                        if (file_exists($imagePath1)) {
                            echo "<small style='color:green;'>File exists in current dir</small>";
                        } elseif (file_exists($imagePath2)) {
                            echo "<small style='color:green;'>File exists in Admin dir</small>";
                        } elseif (file_exists($imagePath3)) {
                            echo "<small style='color:green;'>File exists in ../Admin dir</small>";
                        } else {
                            echo "<small style='color:red;'>File not found</small>";
                        }
                    } else {
                        echo "No image";
                    }
                    echo "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>No products found in the table</p>";
    }
    echo "</div>";
    
    // Test specific product ID=3
    echo "<div class='section'>";
    echo "<h2>6. Testing Product ID=3 (Your Test Case)</h2>";
    
    $productQuery = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $productQuery->execute([3]);
    $product3 = $productQuery->fetch();
    
    if ($product3) {
        echo "<p class='success'>✓ Product ID=3 found successfully</p>";
        echo "<h3>Product Details:</h3>";
        echo "<table>";
        foreach ($product3 as $key => $value) {
            echo "<tr>
                    <th>" . htmlspecialchars($key) . "</th>
                    <td>" . htmlspecialchars($value ?? '') . "</td>
                  </tr>";
        }
        echo "</table>";
        
        // Show raw JSON output (what your API should return)
        echo "<h3>Raw JSON Output:</h3>";
        echo "<pre>" . json_encode($product3, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</pre>";
    } else {
        echo "<p class='error'>✗ Product ID=3 not found in database</p>";
        
        // Show all available IDs
        $idsQuery = $conn->query("SELECT id, name FROM products ORDER BY id ASC");
        $availableIds = $idsQuery->fetchAll();
        
        if (!empty($availableIds)) {
            echo "<p>Available product IDs:</p>";
            echo "<ul>";
            foreach ($availableIds as $product) {
                echo "<li>ID: {$product['id']} - {$product['name']}</li>";
            }
            echo "</ul>";
        }
    }
    echo "</div>";
    
    // Test the actual API endpoint
    echo "<div class='section'>";
    echo "<h2>7. Testing Your single.php API</h2>";
    
    // Get the current URL path
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    $apiUrl = $protocol . $hostname . $path . "/single.php?id=3";
    
    echo "<p>Testing API URL: <code>$apiUrl</code></p>";
    
    // Test using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $curlError = curl_error($ch);
        echo "<p class='error'>✗ cURL Error: $curlError</p>";
    } else {
        echo "<p>HTTP Status Code: <strong>$httpCode</strong></p>";
        
        if ($httpCode === 200) {
            echo "<p class='success'>✓ API returned HTTP 200 OK</p>";
            
            // Try to decode JSON
            $jsonData = json_decode($apiResponse, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "<h3>API Response (JSON):</h3>";
                echo "<pre>" . json_encode($jsonData, JSON_PRETTY_PRINT) . "</pre>";
            } else {
                echo "<p class='error'>✗ API returned invalid JSON: " . json_last_error_msg() . "</p>";
                echo "<h3>Raw API Response:</h3>";
                echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
            }
        } else {
            echo "<p class='error'>✗ API returned HTTP $httpCode</p>";
            echo "<h3>Raw API Response:</h3>";
            echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
        }
    }
    curl_close($ch);
    echo "</div>";
    
    // Direct file test
    echo "<div class='section'>";
    echo "<h2>8. Direct File Test</h2>";
    
    $singlePhpPath = __DIR__ . '/single.php';
    echo "<p>Single.php path: <code>$singlePhpPath</code></p>";
    
    if (file_exists($singlePhpPath)) {
        echo "<p class='success'>✓ single.php file exists</p>";
        
        // Check file permissions
        $permissions = substr(sprintf('%o', fileperms($singlePhpPath)), -4);
        echo "<p>File permissions: $permissions</p>";
        
        // Check file size
        $fileSize = filesize($singlePhpPath);
        echo "<p>File size: " . number_format($fileSize) . " bytes</p>";
        
        // Read the file content to check for syntax errors
        $fileContent = file_get_contents($singlePhpPath);
        
        // Try to include the file to check for syntax errors
        echo "<h3>Syntax Check:</h3>";
        
        // Save current GET params
        $originalGet = $_GET;
        
        // Set test parameter
        $_GET['id'] = 3;
        
        // Start output buffering
        ob_start();
        
        try {
            include($singlePhpPath);
            $output = ob_get_clean();
            
            echo "<p class='success'>✓ File executed without PHP errors</p>";
            echo "<h3>Output:</h3>";
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
            
        } catch (Exception $e) {
            $output = ob_get_clean();
            echo "<p class='error'>✗ PHP Exception: " . $e->getMessage() . "</p>";
            echo "<h3>Output before error:</h3>";
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
        }
        
        // Restore original GET params
        $_GET = $originalGet;
        
    } else {
        echo "<p class='error'>✗ single.php file not found</p>";
    }
    echo "</div>";
    
    // Show your current single.php content
    echo "<div class='section'>";
    echo "<h2>9. Current single.php Content</h2>";
    
    if (file_exists($singlePhpPath)) {
        $content = file_get_contents($singlePhpPath);
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
    } else {
        echo "<p class='error'>File not found</p>";
    }
    echo "</div>";
    
    // Close connection
    $conn = null;
    
} catch (PDOException $e) {
    echo "<div class='section'>";
    echo "<h2 class='error'>Database Connection Error</h2>";
    echo "<p><strong>Error Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
    
    if ($e->errorInfo) {
        echo "<p><strong>SQL State:</strong> " . $e->errorInfo[0] . "</p>";
        echo "<p><strong>Driver Error Code:</strong> " . $e->errorInfo[1] . "</p>";
        echo "<p><strong>Driver Error Message:</strong> " . $e->errorInfo[2] . "</p>";
    }
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='section'>";
    echo "<h2 class='error'>General Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "</body>
</html>";
?>