<?php
session_start();

// Include database configuration
require_once '../includes/config.php';

// Simple login function - much easier!
function simpleLogin($email, $password) {
    // Simple hardcoded credentials for easy access
    $valid_users = [
        'admin@edlivkyhospital.com' => [
            'password' => 'admin',
            'name' => 'Admin User',
            'role' => 'admin',
            'department' => 'Administration',
            'id' => 1
        ],
        'doctor@edlivkyhospital.com' => [
            'password' => 'doctor',
            'name' => 'Dr. Smith',
            'role' => 'doctor',
            'department' => 'General Medicine',
            'id' => 2
        ],
        'nurse@edlivkyhospital.com' => [
            'password' => 'nurse',
            'name' => 'Nurse Johnson',
            'role' => 'nurse',
            'department' => 'Emergency',
            'id' => 3
        ]
    ];

    // Check if user exists and password matches
    if (isset($valid_users[$email]) && $valid_users[$email]['password'] === $password) {
        return $valid_users[$email];
    }

    return false;
}

// Handle AJAX login request
if (isset($_POST['ajax_login'])) {
    header('Content-Type: application/json');

    // Log the login attempt
    error_log("AJAX login attempt received");
    error_log("POST data: " . print_r($_POST, true));

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) && $_POST['remember'] == '1';

    // Validate input
    if (empty($email) || empty($password)) {
        error_log("Login failed: Empty email or password");
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Login failed: Invalid email format: " . $email);
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }
    
    try {
        // Use simple login function
        $user = simpleLogin($email, $password);

        if ($user) {
            // Set session variables
            $_SESSION['staff_id'] = $user['id'];
            $_SESSION['staff_email'] = $email;
            $_SESSION['staff_name'] = $user['name'];
            $_SESSION['staff_role'] = $user['role'];
            $_SESSION['staff_department'] = $user['department'];
            $_SESSION['logged_in'] = true;

            // Log successful login
            error_log("Simple login successful: " . $email . " (Role: " . $user['role'] . ")");

            echo json_encode([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => '../public/dashboard.php',
                'user' => [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'department' => $user['department']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password. Try: admin@edlivkyhospital.com / admin']);
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
    }
    
    exit;
}

// Handle regular form submission (fallback)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['ajax_login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $user = simpleLogin($email, $password);

        if ($user) {
            $_SESSION['staff_id'] = $user['id'];
            $_SESSION['staff_email'] = $email;
            $_SESSION['staff_name'] = $user['name'];
            $_SESSION['staff_role'] = $user['role'];
            $_SESSION['staff_department'] = $user['department'];
            $_SESSION['logged_in'] = true;

            header('Location: ../dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password. Try: admin@edlivkyhospital.com / admin';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

// If not logged in and not a POST request, show login form
if (!isset($_SESSION['logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Staff Login - EdlivkyHospital</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hospital text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Staff Login</h1>
                <p class="text-gray-600">EdlivkyHospital Management System</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                           placeholder="Enter your email">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-cyan-500 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:shadow-lg transition duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="../../www/index.php" class="text-cyan-600 hover:text-cyan-700 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Website
                </a>
            </div>
            
            <div class="mt-4 text-center text-sm text-gray-500">
                <p><strong>Simple Login Credentials:</strong></p>
                <p>Admin: admin@edlivkyhospital.com / admin</p>
                <p>Doctor: doctor@edlivkyhospital.com / doctor</p>
                <p>Nurse: nurse@edlivkyhospital.com / nurse</p>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
} else {
    // Already logged in, redirect to dashboard
    header('Location: ../dashboard.php');
    exit;
}
?>
