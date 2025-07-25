<?php
$pageTitle = "Appointment Records";
include '../includes/config.php';

// Handle success message from appointment scheduling
if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['appointment_id'])) {
    $appointment_id = (int)$_GET['appointment_id'];
    $success_message = "Appointment scheduled successfully! Appointment ID: " . $appointment_id;
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $appointment_id = (int)$_GET['id'];
    $delete_query = "DELETE FROM appointments WHERE id = ?";
    $deleted = executeUpdate($delete_query, [$appointment_id]);

    if ($deleted) {
        $success_message = "Appointment deleted successfully.";
    } else {
        $error_message = "Error deleting appointment.";
    }
}

// Search and filter functionality
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';

$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(CONCAT(p.first_name, ' ', p.last_name) LIKE ? OR CONCAT(s.first_name, ' ', s.last_name) LIKE ? OR a.reason LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($status_filter)) {
    $where_conditions[] = "a.status = ?";
    $params[] = $status_filter;
}

if (!empty($date_filter)) {
    $where_conditions[] = "a.appointment_date = ?";
    $params[] = $date_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get appointments with pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Count total records
$count_query = "SELECT COUNT(*) as total FROM appointments a
                JOIN patients p ON a.patient_id = p.patient_id
                JOIN staff s ON a.staff_id = s.staff_id
                $where_clause";
$total_result = getSingleRecord($count_query, $params);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $per_page);

// Get appointments for current page
$query = "SELECT a.*,
                 CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                 CONCAT(s.first_name, ' ', s.last_name) AS staff_name
          FROM appointments a
          JOIN patients p ON a.patient_id = p.patient_id
          JOIN staff s ON a.staff_id = s.staff_id
          $where_clause
          ORDER BY a.appointment_date DESC, a.appointment_time DESC
          LIMIT $per_page OFFSET $offset";
$appointments = getAllRecords($query, $params);

include '../includes/header.php';
?>
<?php include '../includes/sidebar.php'; ?>

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">

        <!-- Page Header -->
        <div class="mb-6 flex flex-col lg:flex-row lg:items-start lg:justify-between">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Appointment Records</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage and view all appointments</p>
            </div>
            <div class="mt-4 lg:mt-0 flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Compact Statistics Cards -->
                <div class="flex gap-3">
                    <!-- Total Appointments Card -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 min-w-[120px]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-check text-blue-600 dark:text-blue-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Total</p>
                                <p class="text-lg font-semibold text-blue-900 dark:text-blue-100"><?php echo $total_records; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Appointments Card -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 min-w-[120px]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-day text-green-600 dark:text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-900 dark:text-green-100">Today</p>
                                <p class="text-lg font-semibold text-green-900 dark:text-green-100">
                                    <?php
                                    $today_count = 0;
                                    foreach ($appointments as $appointment) {
                                        if (date('Y-m-d', strtotime($appointment['appointment_date'])) === date('Y-m-d')) {
                                            $today_count++;
                                        }
                                    }
                                    echo $today_count;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div>
                    <a href="appointment_form.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Schedule Appointment
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg dark:bg-green-900 dark:border-green-700 dark:text-green-300">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span><?php echo $success_message; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg dark:bg-red-900 dark:border-red-700 dark:text-red-300">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span><?php echo $error_message; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Appointments</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Search by patient, staff, or reason..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                </div>
                <div class="sm:w-48">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="Scheduled" <?php echo ($status_filter === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                        <option value="Completed" <?php echo ($status_filter === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo ($status_filter === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="No Show" <?php echo ($status_filter === 'No Show') ? 'selected' : ''; ?>>No Show</option>
                    </select>
                </div>
                <div class="sm:w-48">
                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                    <a href="appointments_list.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>



        <!-- Appointments Table -->
        <?php if (empty($appointments)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 border border-gray-200 dark:border-gray-700 text-center">
                <div class="text-gray-400 dark:text-gray-500 mb-4">
                    <i class="fas fa-calendar-times text-4xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No appointments found</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    <?php if (!empty($search) || !empty($status_filter) || !empty($date_filter)): ?>
                        No appointments match your search criteria. Try adjusting your filters.
                    <?php else: ?>
                        There are no appointments in the system yet.
                    <?php endif; ?>
                </p>
                <a href="appointment_form.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Schedule First Appointment
                </a>
            </div>
        <?php else: ?>
            <!-- Desktop Table View -->
            <div class="hidden md:block bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Patient</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Staff</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reason</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($appointments as $appointment): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        #<?php echo $appointment['id']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3">
                                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">
                                                    <?php echo strtoupper(substr($appointment['patient_name'], 0, 2)); ?>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <?php echo htmlspecialchars($appointment['patient_name']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($appointment['staff_name']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            <?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                            <?php echo htmlspecialchars($appointment['reason']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $status_colors = [
                                            'Scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                            'Completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'Cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'No Show' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                                        ];
                                        $status_class = $status_colors[$appointment['status']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                                        ?>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $status_class; ?>">
                                            <?php echo $appointment['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="viewAppointment(<?php echo $appointment['id']; ?>)"
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="edit_appointment.php?id=<?php echo $appointment['id']; ?>"
                                               class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deleteAppointment(<?php echo $appointment['id']; ?>, '<?php echo htmlspecialchars($appointment['patient_name']); ?>')"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4">
                <?php foreach ($appointments as $appointment): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center mr-3">
                                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                        <?php echo strtoupper(substr($appointment['patient_name'], 0, 2)); ?>
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($appointment['patient_name']); ?>
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">ID: #<?php echo $appointment['id']; ?></p>
                                </div>
                            </div>
                            <?php
                            $status_colors = [
                                'Scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'Completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                'Cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                'No Show' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                            ];
                            $status_class = $status_colors[$appointment['status']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                            ?>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $status_class; ?>">
                                <?php echo $appointment['status']; ?>
                            </span>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Staff:</span>
                                <span class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($appointment['staff_name']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Date:</span>
                                <span class="text-gray-900 dark:text-white"><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Time:</span>
                                <span class="text-gray-900 dark:text-white"><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Reason:</span>
                                <p class="text-gray-900 dark:text-white mt-1"><?php echo htmlspecialchars($appointment['reason']); ?></p>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <button onclick="viewAppointment(<?php echo $appointment['id']; ?>)"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="edit_appointment.php?id=<?php echo $appointment['id']; ?>"
                               class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deleteAppointment(<?php echo $appointment['id']; ?>, '<?php echo htmlspecialchars($appointment['patient_name']); ?>')"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <?php echo (($page - 1) * $per_page) + 1; ?> to <?php echo min($page * $per_page, $total_records); ?> of <?php echo $total_records; ?> appointments
                </div>
                <div class="flex space-x-1">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date=<?php echo urlencode($date_filter); ?>"
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                            Previous
                        </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date=<?php echo urlencode($date_filter); ?>"
                           class="px-3 py-2 text-sm font-medium <?php echo ($i == $page) ? 'text-blue-600 bg-blue-50 border-blue-500 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'; ?> border rounded-md">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date=<?php echo urlencode($date_filter); ?>"
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Appointment Details Modal -->
<div id="appointmentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Appointment Details</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="appointmentDetails" class="text-gray-700 dark:text-gray-300">
            <!-- Appointment details will be loaded here -->
        </div>
    </div>
</div>

<script>
function viewAppointment(appointmentId) {
    // Show modal with appointment details
    fetch(`view_appointment.php?id=${appointmentId}`)
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

function deleteAppointment(appointmentId, patientName) {
    if (confirm(`Are you sure you want to delete the appointment for ${patientName}?`)) {
        window.location.href = `appointments_list.php?action=delete&id=${appointmentId}`;
    }
}

// Close modal when clicking outside
document.getElementById('appointmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>