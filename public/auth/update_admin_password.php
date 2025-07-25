<?php
// Update admin password to a more secure one
require_once '../includes/config.php';

echo "<h1>Update Admin Password</h1>";

// New secure password
$new_password = 'EdlivkyAdmin2024!';
$admin_email = 'admin@edlivkyhospital.com';

echo "<p><strong>New Admin Credentials:</strong></p>";
echo "<p><strong>Email:</strong> " . $admin_email . "</p>";
echo "<p><strong>Password:</strong> " . $new_password . "</p>";

try {
    $connection = getDBConnection();
    
    // Check if admin user exists
    $stmt = mysqli_prepare($connection, "SELECT staff_id FROM staff WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $admin_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = mysqli_prepare($connection, "UPDATE staff SET password = ? WHERE staff_id = ?");
        mysqli_stmt_bind_param($update_stmt, 'si', $hashed_password, $admin['staff_id']);
        
        if (mysqli_stmt_execute($update_stmt)) {
            echo "<p style='color: green;'>✓ Admin password updated successfully!</p>";
            
            // Test the new password
            if (password_verify($new_password, $hashed_password)) {
                echo "<p style='color: green;'>✓ Password verification test passed</p>";
            } else {
                echo "<p style='color: red;'>✗ Password verification test failed</p>";
            }
            
        } else {
            echo "<p style='color: red;'>✗ Failed to update password: " . mysqli_stmt_error($update_stmt) . "</p>";
        }
        mysqli_stmt_close($update_stmt);
        
    } else {
        echo "<p style='color: red;'>✗ Admin user not found</p>";
        echo "<p><a href='init_staff_table.php'>Initialize staff table first</a></p>";
    }
    
    mysqli_close($connection);
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='test_login.php'>Test Login with New Password</a></li>";
echo "<li><a href='../../www/index.php'>Test on Landing Page</a></li>";
echo "<li><a href='login.php'>Direct Login Page</a></li>";
echo "</ul>";

echo "<h3>Copy These Credentials:</h3>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>Email:</strong> admin@edlivkyhospital.com</p>";
echo "<p><strong>Password:</strong> EdlivkyAdmin2024!</p>";
echo "</div>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
</style>
