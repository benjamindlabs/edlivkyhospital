<?php
// Test login functionality step by step
require_once '../includes/config.php';

echo "<h1>Login Test - Step by Step</h1>";

// Test credentials
$test_email = 'admin@edlivkyhospital.com';
$test_password = 'admin123';

echo "<h2>Testing with credentials:</h2>";
echo "<p><strong>Email:</strong> " . $test_email . "</p>";
echo "<p><strong>Password:</strong> " . $test_password . "</p>";

// Step 1: Check database connection
echo "<h3>Step 1: Database Connection</h3>";
try {
    $connection = getDBConnection();
    echo "<p style='color: green;'>✓ Database connected successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Step 2: Check if staff table exists
echo "<h3>Step 2: Staff Table Check</h3>";
$checkTable = mysqli_query($connection, "SHOW TABLES LIKE 'staff'");
if (mysqli_num_rows($checkTable) == 0) {
    echo "<p style='color: red;'>✗ Staff table does not exist</p>";
    echo "<p><a href='init_staff_table.php'>Initialize staff table first</a></p>";
    exit;
} else {
    echo "<p style='color: green;'>✓ Staff table exists</p>";
}

// Step 3: Check if admin user exists
echo "<h3>Step 3: Admin User Check</h3>";
$stmt = mysqli_prepare($connection, "SELECT * FROM staff WHERE email = ?");
mysqli_stmt_bind_param($stmt, 's', $test_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<p style='color: red;'>✗ Admin user not found in database</p>";
    echo "<p>Creating admin user...</p>";
    
    // Create admin user
    $defaultPassword = password_hash($test_password, PASSWORD_DEFAULT);
    $insertAdmin = "INSERT INTO staff (first_name, last_name, email, password, role, department) VALUES ('Admin', 'User', ?, ?, 'admin', 'Administration')";
    $stmt2 = mysqli_prepare($connection, $insertAdmin);
    mysqli_stmt_bind_param($stmt2, 'ss', $test_email, $defaultPassword);
    
    if (mysqli_stmt_execute($stmt2)) {
        echo "<p style='color: green;'>✓ Admin user created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create admin user: " . mysqli_stmt_error($stmt2) . "</p>";
        exit;
    }
    mysqli_stmt_close($stmt2);
    
    // Re-fetch the user
    $stmt = mysqli_prepare($connection, "SELECT * FROM staff WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $test_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

$staff = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

echo "<p style='color: green;'>✓ Admin user found in database</p>";
echo "<p><strong>User ID:</strong> " . $staff['staff_id'] . "</p>";
echo "<p><strong>Name:</strong> " . $staff['first_name'] . " " . $staff['last_name'] . "</p>";
echo "<p><strong>Email:</strong> " . $staff['email'] . "</p>";
echo "<p><strong>Role:</strong> " . $staff['role'] . "</p>";
echo "<p><strong>Status:</strong> " . $staff['status'] . "</p>";

// Step 4: Test password verification
echo "<h3>Step 4: Password Verification</h3>";
echo "<p><strong>Stored password hash:</strong> " . substr($staff['password'], 0, 50) . "...</p>";

if (password_verify($test_password, $staff['password'])) {
    echo "<p style='color: green;'>✓ Password verification successful</p>";
} else {
    echo "<p style='color: red;'>✗ Password verification failed</p>";
    
    // Let's try to update the password
    echo "<p>Updating password with fresh hash...</p>";
    $newHash = password_hash($test_password, PASSWORD_DEFAULT);
    $updateStmt = mysqli_prepare($connection, "UPDATE staff SET password = ? WHERE staff_id = ?");
    mysqli_stmt_bind_param($updateStmt, 'si', $newHash, $staff['staff_id']);
    
    if (mysqli_stmt_execute($updateStmt)) {
        echo "<p style='color: green;'>✓ Password updated successfully</p>";
        echo "<p>Please try logging in again</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to update password</p>";
    }
    mysqli_stmt_close($updateStmt);
}

// Step 5: Test the validateStaffCredentials function
echo "<h3>Step 5: Test validateStaffCredentials Function</h3>";

// Include the function from login.php
function validateStaffCredentials($email, $password) {
    try {
        $connection = getDBConnection();
        
        // Query staff by email
        $stmt = mysqli_prepare($connection, "SELECT * FROM staff WHERE email = ? AND status = 'active'");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $staff = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
        
        if ($staff && password_verify($password, $staff['password'])) {
            return $staff;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Database error in validateStaffCredentials: " . $e->getMessage());
        return false;
    }
}

$loginResult = validateStaffCredentials($test_email, $test_password);

if ($loginResult) {
    echo "<p style='color: green;'>✓ validateStaffCredentials function works correctly</p>";
    echo "<p><strong>Returned user:</strong> " . $loginResult['first_name'] . " " . $loginResult['last_name'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ validateStaffCredentials function failed</p>";
}

// Step 6: Test AJAX login simulation
echo "<h3>Step 6: Simulate AJAX Login</h3>";

// Simulate the AJAX request
$_POST['ajax_login'] = '1';
$_POST['email'] = $test_email;
$_POST['password'] = $test_password;
$_POST['remember'] = '0';

echo "<p>Simulating AJAX request with POST data:</p>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

// Capture the output
ob_start();
include 'login.php';
$output = ob_get_clean();

echo "<p><strong>Login response:</strong></p>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

mysqli_close($connection);

echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li><a href='../../www/index.php'>Test on Landing Page</a></li>";
echo "<li><a href='login.php'>Direct Login Page</a></li>";
echo "<li><a href='debug_connection.php'>Run Full Debug</a></li>";
echo "</ul>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>
