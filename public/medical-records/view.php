<?php
include '../includes/config.php';

// Get record ID from URL
$record_id = (int)($_GET['id'] ?? 0);

if ($record_id <= 0) {
    echo '<div class="text-red-600">Invalid record ID</div>';
    exit;
}

// Get medical record details
$record = getMedicalRecordById($record_id);

if (!$record) {
    echo '<div class="text-red-600">Medical record not found</div>';
    exit;
}
?>

<div class="space-y-4">
    <!-- Patient Information -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
            <i class="fas fa-user mr-2"></i>Patient Information
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Name:</span>
                <span class="text-gray-900 dark:text-white ml-2"><?php echo htmlspecialchars($record['patient_name']); ?></span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Patient ID:</span>
                <span class="text-gray-900 dark:text-white ml-2"><?php echo $record['patient_id']; ?></span>
            </div>
        </div>
    </div>

    <!-- Staff Information -->
    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
        <h4 class="font-semibold text-purple-900 dark:text-purple-100 mb-2">
            <i class="fas fa-user-md mr-2"></i>Attending Staff
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Name:</span>
                <span class="text-gray-900 dark:text-white ml-2"><?php echo htmlspecialchars($record['staff_name']); ?></span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Staff ID:</span>
                <span class="text-gray-900 dark:text-white ml-2"><?php echo $record['staff_id']; ?></span>
            </div>
        </div>
    </div>

    <!-- Visit Information -->
    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
        <h4 class="font-semibold text-green-900 dark:text-green-100 mb-2">
            <i class="fas fa-calendar-alt mr-2"></i>Visit Information
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Visit Date:</span>
                <span class="text-gray-900 dark:text-white ml-2"><?php echo date('M d, Y g:i A', strtotime($record['visit_date'])); ?></span>
            </div>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Record ID:</span>
                <span class="text-gray-900 dark:text-white ml-2"><?php echo $record['record_id']; ?></span>
            </div>
            <?php if ($record['follow_up_date']): ?>
            <div class="md:col-span-2">
                <span class="font-medium text-gray-700 dark:text-gray-300">Follow-up Date:</span>
                <span class="text-orange-600 dark:text-orange-400 ml-2 font-medium">
                    <i class="fas fa-clock mr-1"></i><?php echo date('M d, Y', strtotime($record['follow_up_date'])); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Medical Information -->
    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
        <h4 class="font-semibold text-yellow-900 dark:text-yellow-100 mb-3">
            <i class="fas fa-stethoscope mr-2"></i>Medical Information
        </h4>
        
        <div class="space-y-3">
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300 block mb-1">Diagnosis:</span>
                <div class="bg-white dark:bg-gray-700 rounded p-3 text-sm text-gray-900 dark:text-white">
                    <?php echo nl2br(htmlspecialchars($record['diagnosis'])); ?>
                </div>
            </div>
            
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300 block mb-1">Treatment:</span>
                <div class="bg-white dark:bg-gray-700 rounded p-3 text-sm text-gray-900 dark:text-white">
                    <?php echo nl2br(htmlspecialchars($record['treatment'])); ?>
                </div>
            </div>
            
            <?php if ($record['prescription']): ?>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300 block mb-1">Prescription:</span>
                <div class="bg-white dark:bg-gray-700 rounded p-3 text-sm text-gray-900 dark:text-white">
                    <?php echo nl2br(htmlspecialchars($record['prescription'])); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($record['notes']): ?>
            <div>
                <span class="font-medium text-gray-700 dark:text-gray-300 block mb-1">Additional Notes:</span>
                <div class="bg-white dark:bg-gray-700 rounded p-3 text-sm text-gray-900 dark:text-white">
                    <?php echo nl2br(htmlspecialchars($record['notes'])); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-600">
        <a href="edit.php?id=<?php echo $record['record_id']; ?>" 
           class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-edit mr-2"></i>
            Edit Record
        </a>
        <button onclick="window.print()" 
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
            <i class="fas fa-print mr-2"></i>
            Print
        </button>
    </div>
</div>
