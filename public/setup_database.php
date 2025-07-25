<?php
// Database setup page - Run this once to initialize the database
$pageTitle = "Database Setup";

// Database configuration
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'edlivkyhospital';

$setup_complete = false;
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_database'])) {
    try {
        // Connect to MySQL server (without selecting database)
        $connection = mysqli_connect($host, $username, $password);
        
        if (!$connection) {
            throw new Exception("Connection failed: " . mysqli_connect_error());
        }
        
        // Create database if it doesn't exist
        $create_db_query = "CREATE DATABASE IF NOT EXISTS `$database`";
        if (!mysqli_query($connection, $create_db_query)) {
            throw new Exception("Error creating database: " . mysqli_error($connection));
        }
        
        // Select the database
        if (!mysqli_select_db($connection, $database)) {
            throw new Exception("Error selecting database: " . mysqli_error($connection));
        }
        
        // Read and execute SQL file
        $sql_content = file_get_contents('../database_setup.sql');
        if ($sql_content === false) {
            throw new Exception("Could not read database_setup.sql file");
        }
        
        // Split SQL content into individual queries
        $queries = explode(';', $sql_content);
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query) && !preg_match('/^--/', $query)) {
                if (!mysqli_query($connection, $query)) {
                    throw new Exception("Error executing query: " . mysqli_error($connection) . "\nQuery: " . $query);
                }
            }
        }
        
        mysqli_close($connection);
        $setup_complete = true;
        $success_message = "Database setup completed successfully!";
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EdlivkyHospital</title>
    <link href="./assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="fas fa-database text-blue-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    Database Setup
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    Initialize the EdlivkyHospital database
                </p>
            </div>
            
            <?php if ($setup_complete): ?>
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">
                                Setup Complete!
                            </h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p><?php echo $success_message; ?></p>
                                <p class="mt-2">You can now access the dashboard:</p>
                                <a href="dashboard.php" class="font-medium underline">Go to Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif (!empty($error_message)): ?>
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Setup Error
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p><?php echo htmlspecialchars($error_message); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!$setup_complete): ?>
                <form class="mt-8 space-y-6" method="POST">
                    <div class="rounded-md shadow-sm space-y-4">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Database Configuration</h3>
                            <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex justify-between">
                                    <span>Host:</span>
                                    <span class="font-mono"><?php echo htmlspecialchars($host); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Username:</span>
                                    <span class="font-mono"><?php echo htmlspecialchars($username); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Database:</span>
                                    <span class="font-mono"><?php echo htmlspecialchars($database); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                        Important Notice
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        <p>This will create the database and all required tables. Make sure your MySQL server is running.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" name="setup_database" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-play text-blue-500 group-hover:text-blue-400"></i>
                            </span>
                            Initialize Database
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
