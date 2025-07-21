<?php
// test_connection.php - Place this in your web root and access via browser

echo "<h3>Database Connection Test</h3>";

// Test environment variables
echo "<h4>Environment Variables:</h4>";
echo "MYSQL_HOST: " . (getenv("MYSQL_HOST") ?: "NOT SET") . "<br>";
echo "MYSQL_DATABASE: " . (getenv("MYSQL_DATABASE") ?: "NOT SET") . "<br>";
echo "MYSQL_USER: " . (getenv("MYSQL_USER") ?: "NOT SET") . "<br>";
echo "MYSQL_PASSWORD: " . (getenv("MYSQL_PASSWORD") ? "***SET***" : "NOT SET") . "<br>";

// Get values
$host = getenv("MYSQL_HOST") ?: ($_ENV["MYSQL_HOST"] ?? "db");
$database = getenv("MYSQL_DATABASE") ?: ($_ENV["MYSQL_DATABASE"] ?? "regmondb");
$user = getenv("MYSQL_USER") ?: ($_ENV["MYSQL_USER"] ?? "dbuser");
$password = getenv("MYSQL_PASSWORD") ?: ($_ENV["MYSQL_PASSWORD"] ?? "regmon123");

echo "<h4>Using Values:</h4>";
echo "Host: $host<br>";
echo "Database: $database<br>";
echo "User: $user<br>";
echo "Password: " . (strlen($password) > 0 ? "***" . strlen($password) . " chars***" : "EMPTY") . "<br>";

// Test connection
echo "<h4>Connection Test:</h4>";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    echo "Attempting to connect...<br>";
    $connection = mysqli_connect($host, $user, $password, $database);
    
    if ($connection) {
        echo "<span style='color: green;'>✓ SUCCESS: Connected to MySQL</span><br>";
        
        // Test query
        $result = mysqli_query($connection, "SELECT 1 as test");
        if ($result) {
            echo "<span style='color: green;'>✓ SUCCESS: Query executed</span><br>";
        }
        
        // Show connection info
        echo "Connection ID: " . mysqli_thread_id($connection) . "<br>";
        echo "Server Info: " . mysqli_get_server_info($connection) . "<br>";
        
        mysqli_close($connection);
    } else {
        echo "<span style='color: red;'>✗ FAILED: Could not connect</span><br>";
        echo "Error: " . mysqli_connect_error() . "<br>";
    }
    
} catch (mysqli_sql_exception $e) {
    echo "<span style='color: red;'>✗ EXCEPTION: " . $e->getMessage() . "</span><br>";
    echo "Error Code: " . $e->getCode() . "<br>";
}

// Test with root user
echo "<h4>Root Connection Test:</h4>";
try {
    echo "Attempting to connect as root...<br>";
    $root_connection = mysqli_connect($host, "root", "root123", $database);
    
    if ($root_connection) {
        echo "<span style='color: green;'>✓ SUCCESS: Connected as root</span><br>";
        
        // Check if dbuser exists
        $user_check = mysqli_query($root_connection, "SELECT User, Host FROM mysql.user WHERE User = 'dbuser'");
        if ($user_check && mysqli_num_rows($user_check) > 0) {
            echo "<span style='color: green;'>✓ User 'dbuser' exists</span><br>";
            while ($row = mysqli_fetch_assoc($user_check)) {
                echo "Found: " . $row['User'] . "@" . $row['Host'] . "<br>";
            }
        } else {
            echo "<span style='color: red;'>✗ User 'dbuser' NOT found</span><br>";
        }
        
        mysqli_close($root_connection);
    } else {
        echo "<span style='color: red;'>✗ FAILED: Could not connect as root</span><br>";
        echo "Error: " . mysqli_connect_error() . "<br>";
    }
    
} catch (mysqli_sql_exception $e) {
    echo "<span style='color: red;'>✗ ROOT EXCEPTION: " . $e->getMessage() . "</span><br>";
}
?>