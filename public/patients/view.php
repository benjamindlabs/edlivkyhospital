<?php
include '../includes/config.php';

// Get patient ID from URL
$patient_id = (int)($_GET['id'] ?? 0);

if ($patient_id <= 0) {
    echo '<div class="text-red-600">Invalid patient ID</div>';
    exit;
}

// Get patient details
$query = "SELECT * FROM patients WHERE id = ?";
$patient = getSingleRecord($query, [$patient_id]);

if (!$patient) {
    echo '<div class="text-red-600">Patient not found</div>';
    exit;
}

// Calculate age if date of birth is available
$age = '';
if ($patient['date_of_birth']) {
    $dob = new DateTime($patient['date_of_birth']);
    $now = new DateTime();
    $age = $now->diff($dob)->y . ' years old';
}
?>

<div class="space-y-6">
    <!-- Patient Header -->
    <div class="flex items-center space-x-4">
        <div class="h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
            <span class="text-xl font-medium text-blue-600 dark:text-blue-400">
                <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
            </span>
        </div>
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
            </h2>
            <p class="text-gray-600 dark:text-gray-400">Patient ID: <?php echo $patient['id']; ?></p>
            <?php if ($age): ?>
                <p class="text-gray-600 dark:text-gray-400"><?php echo $age; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Patient Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Personal Information</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">First Name:</span>
                    <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($patient['first_name']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Last Name:</span>
                    <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($patient['last_name']); ?></span>
                </div>
                <?php if ($patient['date_of_birth']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Date of Birth:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo date('F j, Y', strtotime($patient['date_of_birth'])); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($patient['gender']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Gender:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo ucfirst($patient['gender']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Contact Information</h3>
            <div class="space-y-2">
                <?php if ($patient['phone']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($patient['phone']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($patient['email']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($patient['email']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($patient['address']): ?>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Address:</span>
                        <p class="text-gray-900 dark:text-white font-medium mt-1"><?php echo nl2br(htmlspecialchars($patient['address'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Medical Information</h3>
            <div class="space-y-2">
                <?php if ($patient['blood_type']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Blood Type:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            <i class="fas fa-tint mr-1"></i>
                            <?php echo htmlspecialchars($patient['blood_type']); ?>
                        </span>
                    </div>
                <?php else: ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Blood Type:</span>
                        <span class="text-gray-500 dark:text-gray-400">Not specified</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Emergency Contact</h3>
            <div class="space-y-2">
                <?php if ($patient['emergency_contact_name']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Name:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($patient['emergency_contact_name']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($patient['emergency_contact_phone']): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                        <span class="text-gray-900 dark:text-white font-medium"><?php echo htmlspecialchars($patient['emergency_contact_phone']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!$patient['emergency_contact_name'] && !$patient['emergency_contact_phone']): ?>
                    <div class="text-gray-500 dark:text-gray-400">No emergency contact provided</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Registration Information -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Registration Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Registered:</span>
                <span class="text-gray-900 dark:text-white font-medium"><?php echo date('F j, Y \a\t g:i A', strtotime($patient['created_at'])); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                <span class="text-gray-900 dark:text-white font-medium"><?php echo date('F j, Y \a\t g:i A', strtotime($patient['updated_at'])); ?></span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
        <a href="edit.php?id=<?php echo $patient['id']; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-edit mr-2"></i>
            Edit Patient
        </a>
        <a href="../appointments/appointment_form.php?patient_id=<?php echo $patient['id']; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <i class="fas fa-calendar-plus mr-2"></i>
            Schedule Appointment
        </a>
        <a href="../medical-records/add.php?patient_id=<?php echo $patient['id']; ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
            <i class="fas fa-file-medical mr-2"></i>
            Add Medical Record
        </a>
    </div>
</div>
