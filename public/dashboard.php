<?php
$pageTitle = "Dashboard";
include 'includes/header.php';

// Include database configuration
include 'includes/config.php';

// Function to get dashboard statistics
function getDashboardStats() {
    try {
        // Get total patients count
        $total_patients_result = getSingleRecord("SELECT COUNT(*) as count FROM patients");
        $total_patients = $total_patients_result ? $total_patients_result['count'] : 0;

        // Get active staff count
        $active_staff_result = getSingleRecord("SELECT COUNT(*) as count FROM staff WHERE status = 'active'");
        $active_staff = $active_staff_result ? $active_staff_result['count'] : 0;

        // Get today's appointments count
        $today_appointments_result = getSingleRecord("SELECT COUNT(*) as count FROM appointments WHERE appointment_date = CURDATE()");
        $today_appointments = $today_appointments_result ? $today_appointments_result['count'] : 0;

        // Get available beds count
        $available_beds_result = getSingleRecord("SELECT COUNT(*) as count FROM beds WHERE status = 'available'");
        $available_beds = $available_beds_result ? $available_beds_result['count'] : 0;

        return [
            'total_patients' => $total_patients,
            'active_staff' => $active_staff,
            'today_appointments' => $today_appointments,
            'available_beds' => $available_beds
        ];
    } catch (Exception $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
        return [
            'total_patients' => 0,
            'active_staff' => 0,
            'today_appointments' => 0,
            'available_beds' => 0
        ];
    }
}

// Function to calculate percentage change from last month
function getPercentageChange($current_count, $table_name, $date_column = 'created_at') {
    try {
        // For current month vs last month comparison, we need to be more specific
        // Get current month count
        $current_month_query = "SELECT COUNT(*) as count FROM $table_name
                               WHERE MONTH($date_column) = MONTH(CURDATE())
                               AND YEAR($date_column) = YEAR(CURDATE())";

        // Get last month count
        $last_month_query = "SELECT COUNT(*) as count FROM $table_name
                            WHERE MONTH($date_column) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                            AND YEAR($date_column) = YEAR(CURDATE() - INTERVAL 1 MONTH)";

        // Add specific conditions for different tables
        if ($table_name === 'staff') {
            $current_month_query = "SELECT COUNT(*) as count FROM staff
                                   WHERE status = 'active'
                                   AND MONTH(created_at) = MONTH(CURDATE())
                                   AND YEAR(created_at) = YEAR(CURDATE())";
            $last_month_query = "SELECT COUNT(*) as count FROM staff
                                WHERE status = 'active'
                                AND MONTH(created_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                                AND YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 MONTH)";
        } elseif ($table_name === 'appointments') {
            // For appointments, compare this month's appointments vs last month's
            $current_month_query = "SELECT COUNT(*) as count FROM appointments
                                   WHERE MONTH(appointment_date) = MONTH(CURDATE())
                                   AND YEAR(appointment_date) = YEAR(CURDATE())";
            $last_month_query = "SELECT COUNT(*) as count FROM appointments
                                WHERE MONTH(appointment_date) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                                AND YEAR(appointment_date) = YEAR(CURDATE() - INTERVAL 1 MONTH)";
        } elseif ($table_name === 'beds') {
            // For beds, we'll compare total beds created this month vs last month
            $current_month_query = "SELECT COUNT(*) as count FROM beds
                                   WHERE MONTH(created_at) = MONTH(CURDATE())
                                   AND YEAR(created_at) = YEAR(CURDATE())";
            $last_month_query = "SELECT COUNT(*) as count FROM beds
                                WHERE MONTH(created_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                                AND YEAR(created_at) = YEAR(CURDATE() - INTERVAL 1 MONTH)";
        }

        $current_month_result = getSingleRecord($current_month_query);
        $current_month_count = $current_month_result ? $current_month_result['count'] : 0;

        $last_month_result = getSingleRecord($last_month_query);
        $last_month_count = $last_month_result ? $last_month_result['count'] : 0;

        if ($last_month_count == 0) {
            return $current_month_count > 0 ? 100 : 0; // 100% increase if we had 0 last month and have some now
        }

        $percentage = (($current_month_count - $last_month_count) / $last_month_count) * 100;
        return round($percentage, 1);
    } catch (Exception $e) {
        error_log("Percentage calculation error: " . $e->getMessage());
        return 0;
    }
}

// Get dashboard statistics
$stats = getDashboardStats();

// Calculate percentage changes
$patients_change = getPercentageChange($stats['total_patients'], 'patients');
$staff_change = getPercentageChange($stats['active_staff'], 'staff');
$appointments_change = getPercentageChange($stats['today_appointments'], 'appointments', 'appointment_date');
$beds_change = getPercentageChange($stats['available_beds'], 'beds');

// Function to get recent activity
function getRecentActivity() {
    try {
        $activities = [];

        // Get recent patient registrations
        $recent_patients = getAllRecords("
            SELECT 'patient' as type, CONCAT(first_name, ' ', last_name) as description,
                   created_at as activity_time
            FROM patients
            ORDER BY created_at DESC
            LIMIT 3
        ");

        foreach ($recent_patients as $patient) {
            $activities[] = [
                'type' => 'patient',
                'description' => 'New patient registered: ' . $patient['description'],
                'time' => $patient['activity_time'],
                'icon_color' => 'blue'
            ];
        }

        // Get recent appointments
        $recent_appointments = getAllRecords("
            SELECT 'appointment' as type,
                   CONCAT(p.first_name, ' ', p.last_name, ' - ', s.full_name) as description,
                   a.created_at as activity_time
            FROM appointments a
            JOIN patients p ON a.patient_id = p.id
            JOIN staff s ON a.staff_id = s.id
            ORDER BY a.created_at DESC
            LIMIT 3
        ");

        foreach ($recent_appointments as $appointment) {
            $activities[] = [
                'type' => 'appointment',
                'description' => 'Appointment scheduled: ' . $appointment['description'],
                'time' => $appointment['activity_time'],
                'icon_color' => 'green'
            ];
        }

        // Get recent medical records
        $recent_records = getAllRecords("
            SELECT 'medical_record' as type,
                   CONCAT(p.first_name, ' ', p.last_name, ' - ', LEFT(mr.diagnosis, 30)) as description,
                   mr.created_at as activity_time
            FROM medical_records mr
            JOIN patients p ON mr.patient_id = p.id
            ORDER BY mr.created_at DESC
            LIMIT 2
        ");

        foreach ($recent_records as $record) {
            $activities[] = [
                'type' => 'medical_record',
                'description' => 'Medical record updated: ' . $record['description'],
                'time' => $record['activity_time'],
                'icon_color' => 'purple'
            ];
        }

        // Get recent bed assignments
        $recent_beds = getAllRecords("
            SELECT 'bed' as type,
                   CONCAT(p.first_name, ' ', p.last_name, ' - Bed ', b.bed_number) as description,
                   ba.created_at as activity_time
            FROM bed_assignments ba
            JOIN patients p ON ba.patient_id = p.id
            JOIN beds b ON ba.bed_id = b.id
            WHERE ba.status = 'active'
            ORDER BY ba.created_at DESC
            LIMIT 2
        ");

        foreach ($recent_beds as $bed) {
            $activities[] = [
                'type' => 'bed',
                'description' => 'Bed assigned: ' . $bed['description'],
                'time' => $bed['activity_time'],
                'icon_color' => 'orange'
            ];
        }

        // Sort all activities by time (most recent first)
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        // Return only the 8 most recent activities
        return array_slice($activities, 0, 8);

    } catch (Exception $e) {
        error_log("Recent activity error: " . $e->getMessage());
        return [];
    }
}

// Function to format time ago
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);

    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    return date('M j, Y', strtotime($datetime));
}

// Get recent activity data
$recent_activities = getRecentActivity();
?>

<?php include 'includes/sidebar.php'; ?>

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        
        <!-- Dashboard Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Welcome to EdlivkyHospital Administration</p>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-plus-circle me-2"></i>Quick Actions
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Register Patient -->
                <a href="patients/add.php" class="quick-action-btn bg-blue-500 hover:bg-blue-600">
                    <i class="fas fa-user-plus text-2xl mb-2"></i>
                    <span>Register Patient</span>
                </a>
                
                <!-- Schedule Appointment -->
                <a href="appointments/appointment_form.php" class="quick-action-btn bg-green-500 hover:bg-green-600">
                    <i class="fas fa-calendar-plus text-2xl mb-2"></i>
                    <span>Schedule Appointment</span>
                </a>
                
                <!-- Add Medical Record -->
                <a href="medical-records/add.php" class="quick-action-btn bg-purple-500 hover:bg-purple-600">
                    <i class="fas fa-file-medical text-2xl mb-2"></i>
                    <span>Add Medical Record</span>
                </a>
                
                <!-- Assign Bed -->
                <a href="beds/add.php" class="quick-action-btn bg-orange-500 hover:bg-orange-600">
                    <i class="fas fa-bed text-2xl mb-2"></i>
                    <span>Assign Bed</span>
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Patients -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Patients</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['total_patients']); ?></p>
                        <p class="text-sm <?php echo $patients_change >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                            <i class="fas fa-arrow-<?php echo $patients_change >= 0 ? 'up' : 'down'; ?> me-1"></i><?php echo ($patients_change >= 0 ? '+' : '') . $patients_change; ?>% from last month
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <i class="fas fa-users text-blue-600 dark:text-blue-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Staff -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Staff</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['active_staff']); ?></p>
                        <p class="text-sm <?php echo $staff_change >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                            <i class="fas fa-arrow-<?php echo $staff_change >= 0 ? 'up' : 'down'; ?> me-1"></i><?php echo ($staff_change >= 0 ? '+' : '') . $staff_change; ?>% from last month
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <i class="fas fa-user-md text-green-600 dark:text-green-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Appointments -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Appointments</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['today_appointments']); ?></p>
                        <p class="text-sm <?php echo $appointments_change >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                            <i class="fas fa-arrow-<?php echo $appointments_change >= 0 ? 'up' : 'down'; ?> me-1"></i><?php echo ($appointments_change >= 0 ? '+' : '') . $appointments_change; ?>% from last month
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <i class="fas fa-calendar-check text-purple-600 dark:text-purple-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Available Beds -->
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Available Beds</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo number_format($stats['available_beds']); ?></p>
                        <p class="text-sm <?php echo $beds_change >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'; ?>">
                            <i class="fas fa-arrow-<?php echo $beds_change >= 0 ? 'up' : 'down'; ?> me-1"></i><?php echo ($beds_change >= 0 ? '+' : '') . $beds_change; ?>% from last month
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                        <i class="fas fa-bed text-orange-600 dark:text-orange-300 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Hospital Activity Chart -->
            <div class="chart-container">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-bar me-2"></i>Monthly Hospital Activity
                    </h3>
                </div>
                <div class="relative h-80">
                    <canvas id="monthlyActivityChart"></canvas>
                </div>
            </div>

            <!-- Patient Distribution by Department -->
            <div class="chart-container">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-chart-pie me-2"></i>Patient Distribution by Department
                    </h3>
                </div>
                <div class="relative h-80">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="chart-container">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
            <div class="space-y-4">
                <?php if (!empty($recent_activities)): ?>
                    <?php foreach ($recent_activities as $activity): ?>
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-2 h-2 bg-<?php echo $activity['icon_color']; ?>-500 rounded-full me-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($activity['description']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo timeAgo($activity['time']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="w-2 h-2 bg-gray-400 rounded-full me-3"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">No recent activity</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Activity will appear here as records are added</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Charts Script -->
<script src="assets/js/dashboard-charts.js"></script>

<?php include 'includes/footer.php'; ?>
