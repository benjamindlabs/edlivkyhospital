<?php
$pageTitle = "Patient Registration";
include '../includes/config.php';

// Initialize variables
$success_message = '';
$error_message = '';
$form_data = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log form submission
    error_log("Patient form submitted with data: " . print_r($_POST, true));

    // Sanitize and validate input
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    // Capitalize gender to match database enum values
    if ($gender) {
        $gender = ucfirst(strtolower($gender));
    }
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $blood_type = $_POST['blood_type'] ?? '';
    $emergency_contact_name = sanitizeInput($_POST['emergency_contact_name'] ?? '');
    $emergency_contact_phone = sanitizeInput($_POST['emergency_contact_phone'] ?? '');

    // Store form data for repopulation on error
    $form_data = $_POST;

    // Debug: Log sanitized data
    error_log("Sanitized data - First: $first_name, Last: $last_name, Phone: $phone, Email: $email");

    // Validation
    $errors = [];

    if (empty($first_name)) {
        $errors[] = "First name is required";
    }

    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }

    if (!empty($email) && !validateEmail($email)) {
        $errors[] = "Please enter a valid email address";
    }

    if (!empty($phone) && !validatePhone($phone)) {
        $errors[] = "Please enter a valid phone number";
    }

    if (!empty($emergency_contact_phone) && !validatePhone($emergency_contact_phone)) {
        $errors[] = "Please enter a valid emergency contact phone number";
    }

    if (!empty($date_of_birth)) {
        $date_of_birth = formatDateForDB($date_of_birth);
        if (!$date_of_birth) {
            $errors[] = "Please enter a valid date of birth";
        }
    } else {
        $date_of_birth = null;
    }

    // If no errors, insert into database
    if (empty($errors)) {
        // Debug: Log before database insertion
        error_log("Attempting to insert patient with no validation errors");

        $query = "INSERT INTO patients (first_name, last_name, date_of_birth, gender, phone_number, email, address, blood_type, emergency_contact_name, emergency_contact_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $first_name,
            $last_name,
            $date_of_birth,
            $gender ?: null,
            $phone ?: null,
            $email ?: null,
            $address ?: null,
            $blood_type ?: null,
            $emergency_contact_name ?: null,
            $emergency_contact_phone ?: null
        ];

        // Debug: Log query and params
        error_log("Query: " . $query);
        error_log("Params: " . print_r($params, true));

        $patient_id = insertRecord($query, $params);

        if ($patient_id) {
            // Debug: Log success
            error_log("Patient inserted successfully with ID: " . $patient_id);
            // Redirect to patient list page after successful registration
            header("Location: list.php?success=1&patient_id=" . $patient_id);
            exit();
        } else {
            // Debug: Log failure
            error_log("Failed to insert patient record");
            $error_message = "Error registering patient. Please try again.";
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Patient Registration</h1>
                <p class="text-gray-600 dark:text-gray-400">Register a new patient in the system</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="list.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <i class="fas fa-list mr-2"></i>
                    Patient Records
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
                    <a href="list.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">View all patients</a> |
                    <a href="add.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">Register another patient</a>
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

        <!-- Registration Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <form method="POST" class="space-y-6">
                <!-- Personal Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required
                                   value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="Enter first name">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required
                                   value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="Enter last name">
                        </div>
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth"
                                   value="<?php echo htmlspecialchars($form_data['date_of_birth'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                            <select id="gender" name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Gender</option>
                                <option value="male" <?php echo (strtolower($form_data['gender'] ?? '') === 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo (strtolower($form_data['gender'] ?? '') === 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo (strtolower($form_data['gender'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="Enter phone number">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                            <input type="email" id="email" name="email"
                                   value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="Enter email address">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                            <textarea id="address" name="address" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                      placeholder="Enter full address"><?php echo htmlspecialchars($form_data['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Medical Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Medical Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="blood_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Blood Type</label>
                            <select id="blood_type" name="blood_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Blood Type</option>
                                <option value="A+" <?php echo (($form_data['blood_type'] ?? '') === 'A+') ? 'selected' : ''; ?>>A+</option>
                                <option value="A-" <?php echo (($form_data['blood_type'] ?? '') === 'A-') ? 'selected' : ''; ?>>A-</option>
                                <option value="B+" <?php echo (($form_data['blood_type'] ?? '') === 'B+') ? 'selected' : ''; ?>>B+</option>
                                <option value="B-" <?php echo (($form_data['blood_type'] ?? '') === 'B-') ? 'selected' : ''; ?>>B-</option>
                                <option value="AB+" <?php echo (($form_data['blood_type'] ?? '') === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                <option value="AB-" <?php echo (($form_data['blood_type'] ?? '') === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                                <option value="O+" <?php echo (($form_data['blood_type'] ?? '') === 'O+') ? 'selected' : ''; ?>>O+</option>
                                <option value="O-" <?php echo (($form_data['blood_type'] ?? '') === 'O-') ? 'selected' : ''; ?>>O-</option>
                            </select>
                        </div>
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Name</label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                   value="<?php echo htmlspecialchars($form_data['emergency_contact_name'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="Enter emergency contact name">
                        </div>
                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact Phone</label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone"
                                   value="<?php echo htmlspecialchars($form_data['emergency_contact_phone'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="Enter emergency contact phone">
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
                        <i class="fas fa-user-plus mr-2"></i>
                        Register Patient
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
