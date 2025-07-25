<?php
// Debug connection and authentication system
echo "<h1>Debug Connection and Authentication</h1>";

// Test 1: Include config file
echo "<h2>1. Testing Config File</h2>";
try {
    require_once '../includes/config.php';
    echo "<p style='color: green;'>✓ Config file loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error loading config: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Database connection
echo "<h2>2. Testing Database Connection</h2>";
try {
    $connection = getDBConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test query
    $result = mysqli_query($connection, "SELECT 1 as test");
    if ($result) {
        echo "<p style='color: green;'>✓ Database query test successful</p>";
    } else {
        echo "<p style='color: red;'>✗ Database query failed: " . mysqli_error($connection) . "</p>";
    }
    
    mysqli_close($connection);
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test 3: Check if staff table exists
echo "<h2>3. Testing Staff Table</h2>";
try {
    $connection = getDBConnection();
    $result = mysqli_query($connection, "SHOW TABLES LIKE 'staff'");
    
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: green;'>✓ Staff table exists</p>";
        
        // Check table structure
        $structure = mysqli_query($connection, "DESCRIBE staff");
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = mysqli_fetch_assoc($structure)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check for admin user
        $adminCheck = mysqli_query($connection, "SELECT * FROM staff WHERE email = 'admin@edlivkyhospital.com'");
        if (mysqli_num_rows($adminCheck) > 0) {
            echo "<p style='color: green;'>✓ Admin user exists</p>";
            $admin = mysqli_fetch_assoc($adminCheck);
            echo "<p>Admin details: " . $admin['first_name'] . " " . $admin['last_name'] . " (" . $admin['role'] . ")</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Admin user not found</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Staff table does not exist</p>";
        echo "<p><a href='init_staff_table.php'>Click here to initialize staff table</a></p>";
    }
    
    mysqli_close($connection);
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error checking staff table: " . $e->getMessage() . "</p>";
}

// Test 4: Test password hashing
echo "<h2>4. Testing Password Functions</h2>";
$testPassword = 'admin123';
$hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
echo "<p>Test password: " . $testPassword . "</p>";
echo "<p>Hashed password: " . $hashedPassword . "</p>";

if (password_verify($testPassword, $hashedPassword)) {
    echo "<p style='color: green;'>✓ Password verification works</p>";
} else {
    echo "<p style='color: red;'>✗ Password verification failed</p>";
}

// Test 5: Test validateStaffCredentials function
echo "<h2>5. Testing validateStaffCredentials Function</h2>";
if (function_exists('validateStaffCredentials')) {
    echo "<p style='color: green;'>✓ validateStaffCredentials function exists</p>";
    
    // Test with admin credentials
    $testResult = validateStaffCredentials('admin@edlivkyhospital.com', 'admin123');
    if ($testResult) {
        echo "<p style='color: green;'>✓ Admin login test successful</p>";
        echo "<p>Staff ID: " . $testResult['staff_id'] . "</p>";
        echo "<p>Name: " . $testResult['first_name'] . " " . $testResult['last_name'] . "</p>";
        echo "<p>Role: " . $testResult['role'] . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Admin login test failed</p>";
    }
} else {
    echo "<p style='color: red;'>✗ validateStaffCredentials function not found</p>";
}

echo "<h2>6. File Paths</h2>";
echo "<p>Current file: " . __FILE__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script name: " . $_SERVER['SCRIPT_NAME'] . "</p>";

echo "<h2>7. Test Links</h2>";
echo "<ul>";
echo "<li><a href='init_staff_table.php'>Initialize Staff Table</a></li>";
echo "<li><a href='login.php'>Login Page</a></li>";
echo "<li><a href='test_auth.php'>Test Authentication</a></li>";
echo "<li><a href='../../www/index.php'>Landing Page</a></li>";
echo "</ul>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { width: 100%; margin: 10px 0; }
    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
    th { background-color: #f2f2f2; }
</style>
