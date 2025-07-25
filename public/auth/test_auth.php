<?php
session_start();

// Include database configuration
require_once '../includes/config.php';

echo "<h1>Authentication System Test</h1>";

// Test database connection
try {
    $connection = getDBConnection();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    mysqli_close($connection);
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Check if staff table exists
try {
    $connection = getDBConnection();
    $result = mysqli_query($connection, "SHOW TABLES LIKE 'staff'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: green;'>✓ Staff table exists</p>";

        // Check if default admin exists
        $stmt = mysqli_prepare($connection, "SELECT * FROM staff WHERE email = 'admin@edlivkyhospital.com'");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            echo "<p style='color: green;'>✓ Default admin user exists</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Default admin user not found</p>";
        }
        mysqli_stmt_close($stmt);

        // Show all staff
        $result = mysqli_query($connection, "SELECT staff_id, first_name, last_name, email, role, status FROM staff");

        echo "<h3>Current Staff Users:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
        while ($user = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $user['staff_id'] . "</td>";
            echo "<td>" . $user['first_name'] . " " . $user['last_name'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . $user['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";

    } else {
        echo "<p style='color: red;'>✗ Staff table does not exist</p>";
    }
    mysqli_close($connection);
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error checking staff table: " . $e->getMessage() . "</p>";
}

// Show session information
echo "<h3>Current Session:</h3>";
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo "<p style='color: green;'>✓ User is logged in</p>";
    echo "<ul>";
    echo "<li>Staff ID: " . ($_SESSION['staff_id'] ?? 'Not set') . "</li>";
    echo "<li>Name: " . ($_SESSION['staff_name'] ?? 'Not set') . "</li>";
    echo "<li>Email: " . ($_SESSION['staff_email'] ?? 'Not set') . "</li>";
    echo "<li>Role: " . ($_SESSION['staff_role'] ?? 'Not set') . "</li>";
    echo "<li>Department: " . ($_SESSION['staff_department'] ?? 'Not set') . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>⚠ No user logged in</p>";
}

echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='login.php'>Login Page</a></li>";
echo "<li><a href='../dashboard.php'>Dashboard</a></li>";
echo "<li><a href='../../www/index.php'>Landing Page</a></li>";
echo "<li><a href='logout.php'>Logout</a></li>";
echo "</ul>";

echo "<h3>Default Login Credentials:</h3>";
echo "<p><strong>Email:</strong> admin@edlivkyhospital.com</p>";
echo "<p><strong>Password:</strong> admin123</p>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { width: 100%; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>
