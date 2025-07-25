<?php
// Initialize staff table and default admin user
require_once '../includes/config.php';

echo "<h1>Staff Table Initialization</h1>";

try {
    $connection = getDBConnection();
    
    // Check if staff table exists
    $checkTable = mysqli_query($connection, "SHOW TABLES LIKE 'staff'");
    
    if (mysqli_num_rows($checkTable) == 0) {
        echo "<p>Creating staff table...</p>";
        
        // Create staff table
        $createStaffTable = "
            CREATE TABLE staff (
                staff_id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'doctor', 'nurse', 'staff') DEFAULT 'staff',
                department VARCHAR(100),
                phone VARCHAR(20),
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ";
        
        if (mysqli_query($connection, $createStaffTable)) {
            echo "<p style='color: green;'>✓ Staff table created successfully</p>";
        } else {
            echo "<p style='color: red;'>✗ Error creating staff table: " . mysqli_error($connection) . "</p>";
            exit;
        }
    } else {
        echo "<p style='color: green;'>✓ Staff table already exists</p>";
    }
    
    // Check if default admin exists
    $stmt = mysqli_prepare($connection, "SELECT * FROM staff WHERE email = 'admin@edlivkyhospital.com'");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        echo "<p>Creating default admin user...</p>";
        
        // Insert default admin user
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = "
            INSERT INTO staff (first_name, last_name, email, password, role, department) 
            VALUES ('Admin', 'User', 'admin@edlivkyhospital.com', ?, 'admin', 'Administration')
        ";
        
        $stmt = mysqli_prepare($connection, $insertAdmin);
        mysqli_stmt_bind_param($stmt, 's', $defaultPassword);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<p style='color: green;'>✓ Default admin user created successfully</p>";
        } else {
            echo "<p style='color: red;'>✗ Error creating admin user: " . mysqli_stmt_error($stmt) . "</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p style='color: green;'>✓ Default admin user already exists</p>";
    }
    
    // Show current staff users
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
    
    mysqli_close($connection);
    
    echo "<h3>Default Login Credentials:</h3>";
    echo "<p><strong>Email:</strong> admin@edlivkyhospital.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    
    echo "<h3>Test Links:</h3>";
    echo "<ul>";
    echo "<li><a href='login.php'>Login Page</a></li>";
    echo "<li><a href='test_auth.php'>Test Authentication</a></li>";
    echo "<li><a href='../../www/index.php'>Landing Page</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { width: 100%; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>
