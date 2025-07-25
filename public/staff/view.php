<?php
include '../includes/config.php';

// Get staff ID from URL
$staff_id = (int)($_GET['id'] ?? 0);

if ($staff_id <= 0) {
    echo '<div class="text-red-600">Invalid staff ID</div>';
    exit;
}

// Get staff details
$query = "SELECT * FROM staff WHERE id = ?";
$staff = getSingleRecord($query, [$staff_id]);

if (!$staff) {
    echo '<div class="text-red-600">Staff member not found</div>';
    exit;
}

// Calculate employment duration if hire date is available
$employment_duration = '';
if ($staff['hire_date']) {
    $hire_date = new DateTime($staff['hire_date']);
    $now = new DateTime();
    $diff = $now->diff($hire_date);
    
    if ($diff->y > 0) {
        $employment_duration = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
        if ($diff->m > 0) {
            $employment_duration .= ', ' . $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
        }
    } elseif ($diff->m > 0) {
        $employment_duration = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
    } else {
        $employment_duration = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
    }
}
?>

<div class="space-y-6">
    <!-- Staff Header -->
    <div class="flex items-center space-x-4">
        <div class="h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
            <span class="text-xl font-medium text-indigo-600 dark:text-indigo-400">
                <?php 
                $name_parts = explode(' ', $staff['full_name']);
                $initials = strtoupper(substr($name_parts[0], 0, 1));
                if (count($name_parts) > 1) {
                    $initials .= strtoupper(substr(end($name_parts), 0, 1));
                }
                echo $initials;
                ?>
            </span>
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                <?php echo htmlspecialchars($staff['full_name']); ?>
            </h2>
            <p class="text-gray-600 dark:text-gray-400">Staff ID: <?php echo $staff['id']; ?></p>
            <p class="text-gray-600 dark:text-gray-400"><?php echo ucfirst($staff['role']); ?></p>
        </div>
        <div class="ml-auto">
            <?php
            $status_colors = [
                'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
                'on_leave' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'terminated' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ];
            $status_class = $status_colors[$staff['status']] ?? $status_colors['inactive'];
            ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $status_class; ?>">
                <?php echo ucfirst(str_replace('_', ' ', $staff['status'])); ?>
            </span>
        </div>
    </div>

    <!-- Staff Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Professional Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Professional Information</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Role:</span>
                    <span class="text-gray-900 dark:text-white font-medium"><?php echo ucfirst($staff['role']); ?></span>
                </div>
                <?php if ($staff['department']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Department:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($staff['department']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($staff['license_number']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">License Number:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($staff['license_number']); ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                    <span class="text-gray-900 dark:text-white font-medium"><?php echo ucfirst(str_replace('_', ' ', $staff['status'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Contact Information</h3>
            <div class="space-y-2">
                <?php if ($staff['phone']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($staff['phone']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($staff['email']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($staff['email']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!$staff['phone'] && !$staff['email']): ?>
                    <div class="text-gray-500 dark:text-gray-400">No contact information provided</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Employment Information</h3>
            <div class="space-y-2">
                <?php if ($staff['hire_date']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Hire Date:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo date('F j, Y', strtotime($staff['hire_date'])); ?></span>
                    </div>
                    <?php if ($employment_duration): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Employment Duration:</span>
                            <span class="text-gray-900 dark:text-white font-medium"><?php echo $employment_duration; ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Registered:</span>
                    <span class="text-gray-900 dark:text-white font-medium"><?php echo date('F j, Y \a\t g:i A', strtotime($staff['created_at'])); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                    <span class="text-gray-900 dark:text-white font-medium"><?php echo date('F j, Y \a\t g:i A', strtotime($staff['updated_at'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Quick Stats</h3>
            <div class="space-y-2">
                <?php
                // Get appointment count for this staff member (if they are a doctor)
                if ($staff['role'] === 'doctor') {
                    $appointment_count_query = "SELECT COUNT(*) as total FROM appointments WHERE staff_id = ?";
                    $appointment_result = getSingleRecord($appointment_count_query, [$staff['id']]);
                    $appointment_count = $appointment_result['total'] ?? 0;
                ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Appointments:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo number_format($appointment_count); ?></span>
                    </div>
                <?php } ?>
                
                <?php
                // Get medical records count for this staff member (if they are a doctor)
                if ($staff['role'] === 'doctor') {
                    $medical_records_count_query = "SELECT COUNT(*) as total FROM medical_records WHERE staff_id = ?";
                    $medical_records_result = getSingleRecord($medical_records_count_query, [$staff['id']]);
                    $medical_records_count = $medical_records_result['total'] ?? 0;
                ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Medical Records:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo number_format($medical_records_count); ?></span>
                    </div>
                <?php } ?>
                
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Account Status:</span>
                    <span class="text-gray-900 dark:text-white font-medium">
                        <?php echo ($staff['status'] === 'active') ? 'Active' : 'Inactive'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
        <a href="edit.php?id=<?php echo $staff['id']; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-edit mr-2"></i>
            Edit Staff
        </a>
        <?php if ($staff['role'] === 'doctor'): ?>
            <a href="../appointments/appointment_form.php?staff_id=<?php echo $staff['id']; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-calendar-plus mr-2"></i>
                Schedule Appointment
            </a>
        <?php endif; ?>
        <a href="../medical-records/add.php?staff_id=<?php echo $staff['id']; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
            <i class="fas fa-file-medical mr-2"></i>
            Add Medical Record
        </a>
    </div>
</div>
