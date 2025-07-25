<?php
// Simple test for the new authentication system
session_start();

echo "<h1>Simple Authentication Test</h1>";

// Include the login file to get the simpleLogin function
require_once 'login.php';

// Test credentials
$test_credentials = [
    ['email' => 'admin@edlivkyhospital.com', 'password' => 'admin'],
    ['email' => 'doctor@edlivkyhospital.com', 'password' => 'doctor'],
    ['email' => 'nurse@edlivkyhospital.com', 'password' => 'nurse'],
    ['email' => 'wrong@email.com', 'password' => 'wrong']
];

echo "<h2>Testing Simple Login Function</h2>";

foreach ($test_credentials as $cred) {
    echo "<h3>Testing: " . $cred['email'] . " / " . $cred['password'] . "</h3>";
    
    $result = simpleLogin($cred['email'], $cred['password']);
    
    if ($result) {
        echo "<p style='color: green;'>✓ Login successful!</p>";
        echo "<ul>";
        echo "<li><strong>Name:</strong> " . $result['name'] . "</li>";
        echo "<li><strong>Role:</strong> " . $result['role'] . "</li>";
        echo "<li><strong>Department:</strong> " . $result['department'] . "</li>";
        echo "<li><strong>ID:</strong> " . $result['id'] . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ Login failed</p>";
    }
    echo "<hr>";
}

echo "<h2>Current Session Status</h2>";
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo "<p style='color: green;'>✓ User is currently logged in</p>";
    echo "<ul>";
    echo "<li><strong>Name:</strong> " . ($_SESSION['staff_name'] ?? 'Not set') . "</li>";
    echo "<li><strong>Email:</strong> " . ($_SESSION['staff_email'] ?? 'Not set') . "</li>";
    echo "<li><strong>Role:</strong> " . ($_SESSION['staff_role'] ?? 'Not set') . "</li>";
    echo "<li><strong>Department:</strong> " . ($_SESSION['staff_department'] ?? 'Not set') . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>⚠ No user currently logged in</p>";
}

echo "<h2>Easy Login Credentials</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>Choose any of these super simple credentials:</h3>";
echo "<p><strong>Admin Access:</strong></p>";
echo "<p>Email: admin@edlivkyhospital.com</p>";
echo "<p>Password: admin</p>";
echo "<br>";
echo "<p><strong>Doctor Access:</strong></p>";
echo "<p>Email: doctor@edlivkyhospital.com</p>";
echo "<p>Password: doctor</p>";
echo "<br>";
echo "<p><strong>Nurse Access:</strong></p>";
echo "<p>Email: nurse@edlivkyhospital.com</p>";
echo "<p>Password: nurse</p>";
echo "</div>";

echo "<h2>Test Links</h2>";
echo "<ul>";
echo "<li><a href='login.php'>Direct Login Page</a></li>";
echo "<li><a href='../../www/index.php'>Landing Page (Test Modal)</a></li>";
echo "<li><a href='../dashboard.php'>Dashboard (requires login)</a></li>";
echo "<li><a href='logout.php'>Logout</a></li>";
echo "</ul>";

echo "<h2>AJAX Test</h2>";
echo "<p>Test the AJAX login directly:</p>";
echo "<form id='ajaxTestForm'>";
echo "<p>";
echo "<label>Email:</label><br>";
echo "<input type='email' id='testEmail' value='admin@edlivkyhospital.com' style='width: 250px; padding: 5px;'>";
echo "</p>";
echo "<p>";
echo "<label>Password:</label><br>";
echo "<input type='password' id='testPassword' value='admin' style='width: 250px; padding: 5px;'>";
echo "</p>";
echo "<p>";
echo "<button type='button' onclick='testAjaxLogin()' style='padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 5px;'>Test AJAX Login</button>";
echo "</p>";
echo "</form>";

echo "<div id='ajaxResult' style='margin-top: 20px; padding: 10px; border-radius: 5px;'></div>";
?>

<script>
function testAjaxLogin() {
    const email = document.getElementById('testEmail').value;
    const password = document.getElementById('testPassword').value;
    const resultDiv = document.getElementById('ajaxResult');
    
    resultDiv.innerHTML = '<p style="color: blue;">Testing AJAX login...</p>';
    
    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    formData.append('ajax_login', '1');
    
    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<p style="color: green; background: #e8f5e8; padding: 10px;">✓ AJAX Login Successful!</p><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        } else {
            resultDiv.innerHTML = '<p style="color: red; background: #ffe8e8; padding: 10px;">✗ AJAX Login Failed: ' + data.message + '</p>';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<p style="color: red; background: #ffe8e8; padding: 10px;">✗ Connection Error: ' + error.message + '</p>';
    });
}
</script>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
    h1, h2, h3 { color: #333; }
    hr { margin: 20px 0; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>
