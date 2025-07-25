<?php
$pageTitle = "View Appointment";
include '../includes/config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo '<div class="text-red-600">Invalid appointment ID</div>';
    exit;
}

// Get appointment details with patient and staff information
$query = "SELECT a.*,
                 CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                 CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
                 p.phone_number as patient_phone,
                 p.email as patient_email,
                 s.department as staff_department
          FROM appointments a
          JOIN patients p ON a.patient_id = p.patient_id
          JOIN staff s ON a.staff_id = s.staff_id
          WHERE a.id = ?";
$appointment = getSingleRecord($query, [$id]);

if (!$appointment) {
    echo '<div class="text-red-600">Appointment not found</div>';
    exit;
}

include '../includes/header.php';
?>
<?php include '../includes/sidebar.php'; ?>

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">

        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Appointment Details</h1>
                <p class="text-gray-600 dark:text-gray-400">Appointment ID: #<?php echo $appointment['id']; ?></p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="edit_appointment.php?id=<?php echo $appointment['id']; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Appointment
                </a>
                <a href="appointments_list.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <i class="fas fa-list mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Appointment Information -->
        <div class="space-y-6">
            <!-- Patient and Staff Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Patient Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                            <span class="text-lg font-medium text-blue-600 dark:text-blue-400">
                                <?php echo strtoupper(substr($appointment['patient_name'], 0, 2)); ?>
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Patient Information</h3>
                            <p class="text-gray-600 dark:text-gray-400">Patient ID: <?php echo $appointment['patient_id']; ?></p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                            <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($appointment['patient_name']); ?></p>
                        </div>
                        <?php if (!empty($appointment['patient_phone'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                            <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($appointment['patient_phone']); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($appointment['patient_email'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($appointment['patient_email']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Staff Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                            <span class="text-lg font-medium text-green-600 dark:text-green-400">
                                <?php echo strtoupper(substr($appointment['staff_name'], 0, 2)); ?>
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Staff Information</h3>
                            <p class="text-gray-600 dark:text-gray-400">Staff ID: <?php echo $appointment['staff_id']; ?></p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                            <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($appointment['staff_name']); ?></p>
                        </div>
                        <?php if (!empty($appointment['staff_department'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Department</label>
                            <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($appointment['staff_department']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Appointment Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointment Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Date</label>
                        <p class="text-gray-900 dark:text-white"><?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Time</label>
                        <p class="text-gray-900 dark:text-white"><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <?php
                        $status_colors = [
                            'Scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                            'Completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                            'Cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                            'No Show' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                        ];
                        $status_class = $status_colors[$appointment['status']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                        ?>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?php echo $status_class; ?>">
                            <?php echo $appointment['status']; ?>
                        </span>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Reason for Appointment</label>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-gray-900 dark:text-white"><?php echo htmlspecialchars($appointment['reason']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>