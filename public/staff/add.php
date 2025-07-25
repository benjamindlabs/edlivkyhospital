<?php
$pageTitle = "Staff Registration";
include '../includes/config.php';

// Initialize variables
$success_message = '';
$error_message = '';
$form_data = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    $role = $_POST['role'] ?? '';
    $specialization = sanitizeInput($_POST['specialization'] ?? '');
    $department = sanitizeInput($_POST['department'] ?? '');
    $license_number = sanitizeInput($_POST['license_number'] ?? '');
    $hire_date = $_POST['hire_date'] ?? '';
    $status = $_POST['status'] ?? 'Active';
    $phone_number = sanitizeInput($_POST['phone_number'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');

    // Store form data for repopulation on error
    $form_data = $_POST;

    // Validation
    $errors = [];

    if (empty($first_name)) {
        $errors[] = "First name is required";
    }

    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }

    if (empty($role)) {
        $errors[] = "Role is required";
    }

    if (!empty($email) && !validateEmail($email)) {
        $errors[] = "Please enter a valid email address";
    }

    if (!empty($phone_number) && !validatePhone($phone_number)) {
        $errors[] = "Please enter a valid phone number";
    }

    if (!empty($hire_date)) {
        $hire_date = formatDateForDB($hire_date);
        if (!$hire_date) {
            $errors[] = "Please enter a valid hire date";
        }
    } else {
        $hire_date = null;
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $query = "INSERT INTO staff (first_name, last_name, role, specialization, department, email, phone_number, hire_date, license_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $first_name,
            $last_name,
            $role,
            $specialization ?: null,
            $department ?: null,
            $email ?: null,
            $phone_number ?: null,
            $hire_date,
            $license_number ?: null,
            $status
        ];

        $staff_id = insertRecord($query, $params);

        if ($staff_id) {
            // Redirect to staff list page after successful registration
            header("Location: list.php?success=1&staff_id=" . $staff_id);
            exit();
        } else {
            $error_message = "Error registering staff member. Please try again.";
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Staff Registration</h1>
                <p class="text-gray-600 dark:text-gray-400">Register a new staff member in the system</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="list.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <i class="fas fa-list mr-2"></i>
                    Staff Records
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
                    <a href="list.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">View all staff</a> |
                    <a href="add.php" class="text-green-800 dark:text-green-200 underline hover:no-underline">Register another staff member</a>
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

        <!-- Staff Registration Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <form method="POST" action="" class="space-y-6">

                <!-- Basic Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name <span class="text-red-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" required
                                   value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="Enter first name">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" required
                                   value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="Enter last name">
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role <span class="text-red-500">*</span></label>
                            <select id="role" name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Role</option>
                                <option value="Doctor" <?php echo (($form_data['role'] ?? '') === 'Doctor') ? 'selected' : ''; ?>>Doctor</option>
                                <option value="Nurse" <?php echo (($form_data['role'] ?? '') === 'Nurse') ? 'selected' : ''; ?>>Nurse</option>
                                <option value="Administrator" <?php echo (($form_data['role'] ?? '') === 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
                            </select>
                        </div>
                        <div>
                            <label for="specialization" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specialization</label>
                            <input type="text" id="specialization" name="specialization"
                                   value="<?php echo htmlspecialchars($form_data['specialization'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="e.g., Cardiology, General Nursing, Pediatrics">
                        </div>
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department</label>
                            <input type="text" id="department" name="department"
                                   value="<?php echo htmlspecialchars($form_data['department'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="e.g., Cardiology, Emergency, ICU">
                        </div>
                        <div>
                            <label for="license_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">License Number</label>
                            <input type="text" id="license_number" name="license_number"
                                   value="<?php echo htmlspecialchars($form_data['license_number'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                   placeholder="Professional license number">
                        </div>
                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hire Date</label>
                            <input type="date" id="hire_date" name="hire_date"
                                   value="<?php echo htmlspecialchars($form_data['hire_date'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number"
                                   value="<?php echo htmlspecialchars($form_data['phone_number'] ?? ''); ?>"
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
                    </div>
                </div>

                <!-- Employment Status Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Employment Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Active" <?php echo (($form_data['status'] ?? 'Active') === 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo (($form_data['status'] ?? '') === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
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
                        Register Staff Member
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
