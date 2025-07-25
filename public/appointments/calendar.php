<?php
$pageTitle = "Appointment Calendar";
include '../includes/config.php';

// Get current month and year from URL parameters or use current date
$current_month = $_GET['month'] ?? date('n');
$current_year = $_GET['year'] ?? date('Y');

// Ensure valid month and year
$current_month = max(1, min(12, (int)$current_month));
$current_year = max(2020, min(2030, (int)$current_year));

// Get appointments for the current month
$start_date = sprintf('%04d-%02d-01', $current_year, $current_month);
$end_date = date('Y-m-t', strtotime($start_date));

$appointments_query = "SELECT a.*,
                              p.first_name as patient_first_name, p.last_name as patient_last_name,
                              s.full_name as staff_full_name, s.role as staff_role
                       FROM appointments a
                       JOIN patients p ON a.patient_id = p.patient_id
                       JOIN staff s ON a.staff_id = s.staff_id
                       WHERE a.appointment_date BETWEEN ? AND ?
                       ORDER BY a.appointment_date, a.appointment_time";

$appointments = getAllRecords($appointments_query, [$start_date, $end_date]);

// Group appointments by date
$appointments_by_date = [];
foreach ($appointments as $appointment) {
    $date = $appointment['appointment_date'];
    if (!isset($appointments_by_date[$date])) {
        $appointments_by_date[$date] = [];
    }
    $appointments_by_date[$date][] = $appointment;
}

// Calendar helper functions
function getCalendarDays($month, $year) {
    $first_day = mktime(0, 0, 0, $month, 1, $year);
    $days_in_month = date('t', $first_day);
    $first_day_of_week = date('w', $first_day);
    
    $calendar_days = [];
    
    // Add empty cells for days before the first day of the month
    for ($i = 0; $i < $first_day_of_week; $i++) {
        $calendar_days[] = null;
    }
    
    // Add days of the month
    for ($day = 1; $day <= $days_in_month; $day++) {
        $calendar_days[] = $day;
    }
    
    return $calendar_days;
}

$calendar_days = getCalendarDays($current_month, $current_year);
$month_name = date('F Y', mktime(0, 0, 0, $current_month, 1, $current_year));

// Navigation URLs
$prev_month = $current_month - 1;
$prev_year = $current_year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $current_month + 1;
$next_year = $current_year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

include '../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Appointment Calendar</h1>
                <p class="text-gray-600 dark:text-gray-400">View appointments in calendar format</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Add Appointment Button -->
                <a href="appointment_form.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Schedule Appointment
                </a>
            </div>
        </div>

        <!-- Calendar Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <a href="?month=<?php echo $prev_month; ?>&year=<?php echo $prev_year; ?>" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        <i class="fas fa-chevron-left mr-2"></i>
                        Previous
                    </a>
                    
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo $month_name; ?></h2>
                    
                    <a href="?month=<?php echo $next_month; ?>&year=<?php echo $next_year; ?>" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Next
                        <i class="fas fa-chevron-right ml-2"></i>
                    </a>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="p-6">
                <div class="grid grid-cols-7 gap-1 mb-4">
                    <!-- Day headers -->
                    <?php 
                    $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    foreach ($days as $day): 
                    ?>
                        <div class="p-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <?php echo $day; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="grid grid-cols-7 gap-1">
                    <?php foreach ($calendar_days as $day): ?>
                        <div class="min-h-[120px] border border-gray-200 dark:border-gray-600 rounded-lg p-2 <?php echo $day ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'; ?>">
                            <?php if ($day): ?>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?php echo $day; ?></span>
                                    <?php
                                    $current_date = sprintf('%04d-%02d-%02d', $current_year, $current_month, $day);
                                    $is_today = $current_date === date('Y-m-d');
                                    if ($is_today): ?>
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (isset($appointments_by_date[$current_date])): ?>
                                    <div class="space-y-1">
                                        <?php foreach ($appointments_by_date[$current_date] as $appointment): ?>
                                            <div class="text-xs p-1 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 
                                                        <?php 
                                                        switch($appointment['status']) {
                                                            case 'scheduled': echo 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'; break;
                                                            case 'completed': echo 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'; break;
                                                            case 'cancelled': echo 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'; break;
                                                            case 'no_show': echo 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'; break;
                                                        }
                                                        ?>"
                                                 onclick="viewAppointment(<?php echo $appointment['appointment_id']; ?>)"
                                                 title="<?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name'] . ' - ' . $appointment['reason']); ?>">
                                                <div class="font-medium truncate">
                                                    <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?>
                                                </div>
                                                <div class="truncate">
                                                    <?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?>
                                                </div>
                                                <div class="truncate text-xs opacity-75">
                                                    <?php echo htmlspecialchars($appointment['staff_full_name']); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Status Legend</h3>
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-100 border border-blue-200 rounded mr-2"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Scheduled</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-100 border border-green-200 rounded mr-2"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-100 border border-red-200 rounded mr-2"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Cancelled</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-yellow-100 border border-yellow-200 rounded mr-2"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">No Show</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div id="appointmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Appointment Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="appointmentDetails" class="text-sm text-gray-500 dark:text-gray-400">
                <!-- Appointment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewAppointment(appointmentId) {
    // Show modal with appointment details
    fetch(`view.php?id=${appointmentId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('appointmentDetails').innerHTML = data;
            document.getElementById('appointmentModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading appointment details');
        });
}

function closeModal() {
    document.getElementById('appointmentModal').classList.add('hidden');
}
</script>

<?php include '../includes/footer.php'; ?>
