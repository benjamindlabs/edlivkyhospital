<?php
$pageTitle = "Bed Assignment";
include '../includes/config.php';

// Initialize variables
$success_message = '';
$error_message = '';
$form_data = [];

// Get patients and available beds for dropdowns
$patients = getAllPatients();
$available_beds = getAvailableBeds();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log form submission
    error_log("Bed assignment form submitted with data: " . print_r($_POST, true));

    // Sanitize and validate input
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $bed_id = (int)($_POST['bed_id'] ?? 0);
    $admission_date = $_POST['admission_date'] ?? '';
    $admission_time = $_POST['admission_time'] ?? '';
    $reason = sanitizeInput($_POST['reason'] ?? '');

    // Store form data for repopulation on error
    $form_data = $_POST;

    // Validation
    $errors = [];

    if (empty($patient_id)) {
        $errors[] = "Please select a patient";
    }

    if (empty($bed_id)) {
        $errors[] = "Please select a bed";
    }

    if (empty($admission_date)) {
        $errors[] = "Admission date is required";
    }

    if (empty($admission_time)) {
        $errors[] = "Admission time is required";
    }

    // Combine date and time for database
    $admission_datetime = null;
    if (!empty($admission_date) && !empty($admission_time)) {
        $admission_datetime = formatDateTimeForDB($admission_date . ' ' . $admission_time);
        if (!$admission_datetime) {
            $errors[] = "Please enter a valid admission date and time";
        }
    }

    // Check if bed is still available
    if (!empty($bed_id) && !isBedAvailable($bed_id)) {
        $errors[] = "Selected bed is no longer available";
        // Refresh available beds
        $available_beds = getAvailableBeds();
    }

    // If no errors, create bed assignment
    if (empty($errors)) {
        // Debug: Log before database insertion
        error_log("Attempting to create bed assignment with no validation errors");

        $assignment_id = createBedAssignment($patient_id, $bed_id, $admission_datetime, $reason);

        if ($assignment_id) {
            // Debug: Log success
            error_log("Bed assignment created successfully with ID: " . $assignment_id);
            // Redirect to bed assignment list page after successful assignment
            header("Location: list.php?success=1&assignment_id=" . $assignment_id);
            exit();
        } else {
            // Debug: Log failure
            error_log("Failed to create bed assignment");
            $error_message = "Error creating bed assignment. Please try again.";
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bed Assignment</h1>
                <p class="text-gray-600 dark:text-gray-400">Assign a bed to a patient</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="list.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <i class="fas fa-list mr-2"></i>
                    Bed Records
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
                    <a href="list.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">View all bed assignments</a> |
                    <a href="add.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">Create another assignment</a>
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

        <!-- Check if beds are available -->
        <?php if (empty($available_beds)): ?>
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-300 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>No beds are currently available for assignment. Please check back later or contact administration.</span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Bed Assignment Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <form method="POST" class="space-y-6">
                <!-- Patient and Bed Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Assignment Information</h3>
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
                            <label for="bed_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available Bed *</label>
                            <select id="bed_id" name="bed_id" required <?php echo empty($available_beds) ? 'disabled' : ''; ?>
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white <?php echo empty($available_beds) ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                                <option value="">Select Bed</option>
                                <?php foreach ($available_beds as $bed): ?>
                                    <option value="<?php echo $bed['id']; ?>"
                                            <?php echo (($form_data['bed_id'] ?? '') == $bed['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($bed['bed_number'] . ' - ' . $bed['ward'] . ' (' . ucfirst($bed['bed_type']) . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Admission Details Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Admission Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="admission_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Admission Date *</label>
                            <input type="date" id="admission_date" name="admission_date" required
                                   value="<?php echo htmlspecialchars($form_data['admission_date'] ?? date('Y-m-d')); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label for="admission_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Admission Time *</label>
                            <input type="time" id="admission_time" name="admission_time" required
                                   value="<?php echo htmlspecialchars($form_data['admission_time'] ?? date('H:i')); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Admission</label>
                            <textarea id="reason" name="reason" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                      placeholder="Enter reason for bed assignment (optional)"><?php echo htmlspecialchars($form_data['reason'] ?? ''); ?></textarea>
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
                    <button type="submit" <?php echo empty($available_beds) ? 'disabled' : ''; ?>
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 <?php echo empty($available_beds) ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                        <i class="fas fa-bed mr-2"></i>
                        Assign Bed
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
        // Reset to current date and time
        document.getElementById('admission_date').value = '<?php echo date('Y-m-d'); ?>';
        document.getElementById('admission_time').value = '<?php echo date('H:i'); ?>';
    }
}

// Auto-refresh available beds every 30 seconds to check for newly available beds
setInterval(function() {
    // Only refresh if no form data has been entered
    const form = document.querySelector('form');
    const formData = new FormData(form);
    let hasData = false;

    for (let [key, value] of formData.entries()) {
        if (value && key !== 'admission_date' && key !== 'admission_time') {
            hasData = true;
            break;
        }
    }

    if (!hasData) {
        // Silently check for bed availability without refreshing the page
        fetch('check_bed_availability.php')
            .then(response => response.json())
            .then(data => {
                if (data.available_count > 0) {
                    const bedSelect = document.getElementById('bed_id');
                    const submitBtn = document.querySelector('button[type="submit"]');

                    if (bedSelect.disabled) {
                        // Show notification that beds are now available
                        const notification = document.createElement('div');
                        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                        notification.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Beds are now available! Please refresh the page.';
                        document.body.appendChild(notification);

                        setTimeout(() => {
                            notification.remove();
                        }, 5000);
                    }
                }
            })
            .catch(error => {
                console.log('Bed availability check failed:', error);
            });
    }
}, 30000);
</script>

<?php include '../includes/footer.php'; ?>
