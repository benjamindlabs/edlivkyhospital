<?php
// Determine the correct path to assets based on current directory
$current_dir = dirname($_SERVER['SCRIPT_NAME']);
$base_path = (basename($current_dir) === 'public') ? './' : '../';
?>
<!-- Sidebar -->
<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            <!-- Dashboard -->
            <li>
                <a href="<?php echo $base_path; ?>dashboard.php" class="sidebar-link group <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>

            <!-- Patient Registration -->
            <li>
                <a href="<?php echo $base_path; ?>patients/add.php" class="sidebar-link group <?php echo (strpos($_SERVER['PHP_SELF'], 'patients') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-user-plus w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                    <span class="ms-3">Patient Registration</span>
                </a>
            </li>

            <!-- Staff Registration -->
            <li>
                <a href="<?php echo $base_path; ?>staff/add.php" class="sidebar-link group <?php echo (strpos($_SERVER['PHP_SELF'], 'staff') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-user-md w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                    <span class="ms-3">Staff Registration</span>
                </a>
            </li>

            <!-- Appointment Scheduling -->
            <li>
                <a href="<?php echo $base_path; ?>appointments/appointment_form.php" class="sidebar-link group <?php echo (strpos($_SERVER['PHP_SELF'], 'appointments') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-plus w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                    <span class="ms-3">Appointment Scheduling</span>
                </a>
            </li>

            <!-- Medical Record Entry -->
            <li>
                <a href="<?php echo $base_path; ?>medical-records/add.php" class="sidebar-link group <?php echo (strpos($_SERVER['PHP_SELF'], 'medical-records') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-file-medical w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                    <span class="ms-3">Medical Record Entry</span>
                </a>
            </li>

            <!-- Bed Assignment -->
            <li>
                <a href="<?php echo $base_path; ?>beds/add.php" class="sidebar-link group <?php echo (strpos($_SERVER['PHP_SELF'], 'beds') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-bed w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"></i>
                    <span class="ms-3">Bed Assignment</span>
                </a>
            </li>

        </ul>

        <!-- User Info and Logout Section -->
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center mb-3">
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        <?php echo isset($_SESSION['staff_name']) ? htmlspecialchars($_SESSION['staff_name']) : 'Staff User'; ?>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        <?php echo isset($_SESSION['staff_role']) ? ucfirst($_SESSION['staff_role']) : 'Staff'; ?>
                        <?php if (isset($_SESSION['staff_department'])): ?>
                            • <?php echo htmlspecialchars($_SESSION['staff_department']); ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="flex space-x-2">
                <a href="<?php echo $base_path; ?>auth/logout.php"
                   class="flex-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-2 px-3 rounded-lg transition duration-300 text-center"
                   onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt mr-1"></i>
                    Logout
                </a>
                <a href="../../www/index.php"
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium py-2 px-3 rounded-lg transition duration-300 text-center">
                    <i class="fas fa-home mr-1"></i>
                    Website
                </a>
            </div>
        </div>
    </div>
</aside>
