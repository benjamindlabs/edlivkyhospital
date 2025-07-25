<?php
$pageTitle = "Edit Appointment";
include '../includes/config.php';

$id = $_GET['id'] ?? null;
$success_message = '';
$error_message = '';

if (!$id) {
    $error_message = "Invalid appointment ID";
} else {
    // Get appointment details
    $query = "SELECT a.*,
                     CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                     CONCAT(s.first_name, ' ', s.last_name) AS staff_name
              FROM appointments a
              JOIN patients p ON a.patient_id = p.patient_id
              JOIN staff s ON a.staff_id = s.staff_id
              WHERE a.id = ?";
    $appointment = getSingleRecord($query, [$id]);

    if (!$appointment) {
        $error_message = "Appointment not found";
    }
}

// Get patients and staff for dropdowns
$patients_query = "SELECT patient_id, CONCAT(first_name, ' ', last_name) AS full_name FROM patients ORDER BY first_name, last_name";
$patients = getAllRecords($patients_query);

$staff_query = "SELECT staff_id, CONCAT(first_name, ' ', last_name) AS full_name, department FROM staff ORDER BY first_name, last_name";
$staff_members = getAllRecords($staff_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($appointment)) {
    $patient_id = trim($_POST['patient_id']);
    $staff_id = trim($_POST['staff_id']);
    $appointment_date = trim($_POST['appointment_date']);
    $appointment_time = trim($_POST['appointment_time']);
    $reason = trim($_POST['reason']);
    $status = trim($_POST['status']);

    // Validation
    $errors = [];

    if (empty($patient_id)) {
        $errors[] = "Patient is required";
    }

    if (empty($staff_id)) {
        $errors[] = "Staff member is required";
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

    if (empty($status)) {
        $errors[] = "Status is required";
    }

    if (empty($errors)) {
        try {
            $update_query = "UPDATE appointments SET
                            patient_id = ?,
                            staff_id = ?,
                            appointment_date = ?,
                            appointment_time = ?,
                            reason = ?,
                            status = ?
                            WHERE id = ?";

            $result = updateRecord($update_query, [
                $patient_id,
                $staff_id,
                $appointment_date,
                $appointment_time,
                $reason,
                $status,
                $id
            ]);

            if ($result) {
                $success_message = "Appointment updated successfully!";
                // Refresh appointment data
                $appointment = getSingleRecord($query, [$id]);
            } else {
                $error_message = "Failed to update appointment. Please try again.";
            }
        } catch (Exception $e) {
            $error_message = "Error updating appointment: " . $e->getMessage();
        }
    } else {
        $error_message = implode(", ", $errors);
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Appointment</h1>
                <p class="text-gray-600 dark:text-gray-400">Appointment ID: #<?php echo $appointment['id'] ?? 'N/A'; ?></p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="view_appointment.php?id=<?php echo $id; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <i class="fas fa-eye mr-2"></i>
                    View Details
                </a>
                <a href="appointments_list.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <i class="fas fa-list mr-2"></i>
                    Back to List
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

        <?php if (!empty($appointment)): ?>
        <!-- Edit Appointment Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <form method="POST" class="p-6">
                <!-- Appointment Information Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                        Appointment Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Patient Selection -->
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Patient <span class="text-red-500">*</span>
                            </label>
                            <select name="patient_id" id="patient_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                                <option value="">Select a patient...</option>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?php echo $patient['patient_id']; ?>"
                                        <?php echo ($appointment['patient_id'] == $patient['patient_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($patient['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Staff Selection -->
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Staff Member <span class="text-red-500">*</span>
                            </label>
                            <select name="staff_id" id="staff_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                                <option value="">Select a staff member...</option>
                                <?php foreach ($staff_members as $staff): ?>
                                    <option value="<?php echo $staff['staff_id']; ?>"
                                        <?php echo ($appointment['staff_id'] == $staff['staff_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($staff['full_name']); ?>
                                        <?php if (!empty($staff['department'])): ?>
                                            - <?php echo htmlspecialchars($staff['department']); ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Appointment Date -->
                        <div>
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Appointment Date <span class="text-red-500">*</span>
                            </label>
                            <input
                            type="date"
                                   id="appointment_date"
                                   name="appointment_date"
                                   required
                                   value="<?php echo $appointment['appointment_date']; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <!-- Appointment Time -->
                        <div>
                            <label for="appointment_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Appointment Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time"
                                   id="appointment_time"
                                   name="appointment_time"
                                   required
                                   value="<?php echo $appointment['appointment_time']; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Scheduled" <?php echo ($appointment['status'] === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                <option value="Completed" <?php echo ($appointment['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="Cancelled" <?php echo ($appointment['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                <option value="No Show" <?php echo ($appointment['status'] === 'No Show') ? 'selected' : ''; ?>>No Show</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Reason Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-notes-medical mr-2 text-green-600"></i>
                        Appointment Details
                    </h3>
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reason for Appointment <span class="text-red-500">*</span>
                        </label>
                        <textarea id="reason"
                                  name="reason"
                                  rows="4"
                                  required
                                  placeholder="Describe the reason for this appointment..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"><?php echo htmlspecialchars($appointment['reason']); ?></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex space-x-3">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            Update Appointment
                        </button>
                        <a href="view_appointment.php?id=<?php echo $id; ?>"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="text-red-500">*</span> Required fields
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>