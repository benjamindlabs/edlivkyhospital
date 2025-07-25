<?php
$pageTitle = "Appointment Scheduling";
include '../includes/config.php';

// Initialize variables
$success_message = '';
$error_message = '';
$form_data = [];

// Get patients and staff for dropdowns
$patients_query = "SELECT patient_id, CONCAT(first_name, ' ', last_name) AS full_name FROM patients ORDER BY first_name, last_name";
$patients = getAllRecords($patients_query);

$staff_query = "SELECT staff_id, CONCAT(first_name, ' ', last_name) AS full_name FROM staff ORDER BY first_name, last_name";
$staff_members = getAllRecords($staff_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $staff_id = (int)($_POST['staff_id'] ?? 0);
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $reason = sanitizeInput($_POST['reason'] ?? '');
    $status = $_POST['status'] ?? 'Scheduled';

    // Store form data for repopulation on error
    $form_data = $_POST;

    // Validation
    $errors = [];

    if ($patient_id <= 0) {
        $errors[] = "Please select a patient";
    }

    if ($staff_id <= 0) {
        $errors[] = "Please select a staff member";
    }

    if (empty($appointment_date)) {
        $errors[] = "Appointment date is required";
    }

    if (empty($appointment_time)) {
        $errors[] = "Appointment time is required";
    }

    if (empty($reason)) {
        $errors[] = "Reason for appointment is required";
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $query = "INSERT INTO appointments (patient_id, staff_id, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, ?)";

        $params = [
            $patient_id,
            $staff_id,
            $appointment_date,
            $appointment_time,
            $reason,
            $status
        ];

        $appointment_id = insertRecord($query, $params);

        if ($appointment_id) {
            // Redirect to appointment list page after successful scheduling
            header("Location: appointments_list.php?success=1&appointment_id=" . $appointment_id);
            exit();
        } else {
            $error_message = "Error scheduling appointment. Please try again.";
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

include '../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">

        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Appointment Scheduling</h1>
                <p class="text-gray-600 dark:text-gray-400">Schedule a new appointment in the system</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="appointments_list.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <i class="fas fa-list mr-2"></i>
                    Appointment Records
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg dark:bg-green-900 dark:border-green-700 dark:text-green-300">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span><?php echo $success_message; ?></span>
                </div>
                <div class="mt-2">
                    <a href="appointments_list.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">View all appointments</a> |
                    <a href="appointment_form.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">Schedule another appointment</a>
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
        <!-- Appointment Scheduling Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <form method="POST" class="space-y-6">
                <!-- Appointment Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Appointment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Patient *</label>
                            <select id="patient_id" name="patient_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select a patient...</option>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?php echo $patient['patient_id']; ?>"
                                        <?php echo (($form_data['patient_id'] ?? '') == $patient['patient_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($patient['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Staff Member *</label>
                            <select id="staff_id" name="staff_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select a staff member...</option>
                                <?php foreach ($staff_members as $staff): ?>
                                    <option value="<?php echo $staff['staff_id']; ?>"
                                        <?php echo (($form_data['staff_id'] ?? '') == $staff['staff_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($staff['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Appointment Date *</label>
                            <input type="date" id="appointment_date" name="appointment_date" required
                                   min="<?php echo date('Y-m-d'); ?>"
                                   value="<?php echo htmlspecialchars($form_data['appointment_date'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label for="appointment_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Appointment Time *</label>
                            <input type="time" id="appointment_time" name="appointment_time" required
                                   value="<?php echo htmlspecialchars($form_data['appointment_time'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Appointment Details Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Appointment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Scheduled" <?php echo (($form_data['status'] ?? 'Scheduled') === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                <option value="Completed" <?php echo (($form_data['status'] ?? '') === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo (($form_data['status'] ?? '') === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                <option value="No Show" <?php echo (($form_data['status'] ?? '') === 'No Show') ? 'selected' : ''; ?>>No Show</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Appointment *</label>
                            <textarea id="reason" name="reason" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                      placeholder="Enter the reason for this appointment"><?php echo htmlspecialchars($form_data['reason'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="../dashboard.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                    <button type="button" onclick="clearForm()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Clear Form
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Schedule Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function clearForm() {
    if (confirm('Are you sure you want to clear all form data?')) {
        document.querySelector('form').reset();
    }
}
</script>

<?php include '../includes/footer.php'; ?>