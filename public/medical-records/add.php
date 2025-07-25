<?php
$pageTitle = "Medical Record Entry";
include '../includes/config.php';

// Initialize variables
$success_message = '';
$error_message = '';
$form_data = [];

// Get patients and staff for dropdowns
$patients = getAllPatients();
$staff = getAllStaff();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log form submission
    error_log("Medical record form submitted with data: " . print_r($_POST, true));

    // Sanitize and validate input
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $staff_id = (int)($_POST['staff_id'] ?? 0);
    $visit_date = $_POST['visit_date'] ?? '';
    $diagnosis = sanitizeInput($_POST['diagnosis'] ?? '');
    $treatment = sanitizeInput($_POST['treatment'] ?? '');
    $notes = sanitizeInput($_POST['notes'] ?? '');
    $prescription = sanitizeInput($_POST['prescription'] ?? '');
    $follow_up_date = $_POST['follow_up_date'] ?? '';

    // Store form data for repopulation on error
    $form_data = $_POST;

    // Debug: Log sanitized data
    error_log("Sanitized data - Patient ID: $patient_id, Staff ID: $staff_id, Diagnosis: $diagnosis");

    // Validation
    $errors = [];

    if (empty($patient_id)) {
        $errors[] = "Patient selection is required";
    }

    if (empty($staff_id)) {
        $errors[] = "Staff selection is required";
    }

    if (empty($visit_date)) {
        $errors[] = "Visit date is required";
    } else {
        $visit_date = formatDateTimeForDB($visit_date);
        if (!$visit_date) {
            $errors[] = "Please enter a valid visit date and time";
        }
    }

    if (empty($diagnosis)) {
        $errors[] = "Diagnosis is required";
    }

    if (empty($treatment)) {
        $errors[] = "Treatment is required";
    }

    if (!empty($follow_up_date)) {
        $follow_up_date = formatDateForDB($follow_up_date);
        if (!$follow_up_date) {
            $errors[] = "Please enter a valid follow-up date";
        }
    } else {
        $follow_up_date = null;
    }

    // If no errors, insert into database
    if (empty($errors)) {
        // Debug: Log before database insertion
        error_log("Attempting to insert medical record with no validation errors");

        $medical_record_data = [
            'patient_id' => $patient_id,
            'staff_id' => $staff_id,
            'visit_date' => $visit_date,
            'diagnosis' => $diagnosis,
            'treatment' => $treatment,
            'notes' => $notes ?: null,
            'prescription' => $prescription ?: null,
            'follow_up_date' => $follow_up_date
        ];

        // Debug: Log data being inserted
        error_log("Medical record data: " . print_r($medical_record_data, true));

        $record_id = createMedicalRecord($medical_record_data);

        if ($record_id) {
            // Debug: Log success
            error_log("Medical record inserted successfully with ID: " . $record_id);
            // Redirect to medical records list page after successful entry
            header("Location: list.php?success=1&record_id=" . $record_id);
            exit();
        } else {
            // Debug: Log failure
            error_log("Failed to insert medical record");
            $error_message = "Error creating medical record. Please try again.";
        }
    } else {
        // Debug: Log validation errors
        error_log("Validation errors: " . implode(', ', $errors));
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Medical Record Entry</h1>
                <p class="text-gray-600 dark:text-gray-400">Create a new medical record for patient visit</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="list.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <i class="fas fa-list mr-2"></i>
                    Medical Records
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
                    <a href="list.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">View all medical records</a> |
                    <a href="add.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">Create another record</a>
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

        <!-- Medical Record Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <form method="POST" class="space-y-6">
                <!-- Patient and Staff Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Patient and Staff Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Patient *</label>
                            <select id="patient_id" name="patient_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Patient</option>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?php echo $patient['patient_id']; ?>" 
                                            <?php echo (($form_data['patient_id'] ?? '') == $patient['patient_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($patient['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attending Staff *</label>
                            <select id="staff_id" name="staff_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Staff Member</option>
                                <?php foreach ($staff as $staff_member): ?>
                                    <option value="<?php echo $staff_member['staff_id']; ?>" 
                                            <?php echo (($form_data['staff_id'] ?? '') == $staff_member['staff_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($staff_member['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Visit Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Visit Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="visit_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Visit Date & Time *</label>
                            <input type="datetime-local" id="visit_date" name="visit_date" required
                                   value="<?php echo htmlspecialchars($form_data['visit_date'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label for="follow_up_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Follow-up Date</label>
                            <input type="date" id="follow_up_date" name="follow_up_date"
                                   value="<?php echo htmlspecialchars($form_data['follow_up_date'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Medical Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Medical Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="diagnosis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Diagnosis *</label>
                            <textarea id="diagnosis" name="diagnosis" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                      placeholder="Enter patient diagnosis"><?php echo htmlspecialchars($form_data['diagnosis'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label for="treatment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Treatment *</label>
                            <textarea id="treatment" name="treatment" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                      placeholder="Enter treatment provided"><?php echo htmlspecialchars($form_data['treatment'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label for="prescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prescription</label>
                            <textarea id="prescription" name="prescription" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                      placeholder="Enter prescription details"><?php echo htmlspecialchars($form_data['prescription'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                      placeholder="Enter any additional notes"><?php echo htmlspecialchars($form_data['notes'] ?? ''); ?></textarea>
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
                        <i class="fas fa-file-medical mr-2"></i>
                        Create Medical Record
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
