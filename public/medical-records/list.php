<?php
$pageTitle = "Medical Records";
include '../includes/config.php';

// Handle success message from medical record creation
if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['record_id'])) {
    $record_id = (int)$_GET['record_id'];
    $success_message = "Medical record created successfully! Record ID: " . $record_id;
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $record_id = (int)$_GET['id'];
    $deleted = deleteMedicalRecord($record_id);
    
    if ($deleted) {
        $success_message = "Medical record deleted successfully.";
    } else {
        $error_message = "Error deleting medical record.";
    }
}

// Get search parameters
$search = sanitizeInput($_GET['search'] ?? '');
$patient_filter = $_GET['patient_id'] ?? '';
$staff_filter = $_GET['staff_id'] ?? '';

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(mr.diagnosis LIKE ? OR mr.treatment LIKE ? OR mr.notes LIKE ? OR mr.prescription LIKE ? OR CONCAT(p.first_name, ' ', p.last_name) LIKE ? OR CONCAT(s.first_name, ' ', s.last_name) LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param, $search_param, $search_param]);
}

if (!empty($patient_filter)) {
    $where_conditions[] = "mr.patient_id = ?";
    $params[] = $patient_filter;
}

if (!empty($staff_filter)) {
    $where_conditions[] = "mr.staff_id = ?";
    $params[] = $staff_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get medical records with pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Count total records
$count_query = "SELECT COUNT(*) as total FROM medical_records mr
                JOIN patients p ON mr.patient_id = p.patient_id
                JOIN staff s ON mr.staff_id = s.staff_id
                $where_clause";
$total_result = getSingleRecord($count_query, $params);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $per_page);

// Get medical records for current page
$query = "SELECT mr.*, 
                 CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                 CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
                 p.patient_id as patient_id_ref,
                 s.staff_id as staff_id_ref
          FROM medical_records mr
          JOIN patients p ON mr.patient_id = p.patient_id
          JOIN staff s ON mr.staff_id = s.staff_id
          $where_clause 
          ORDER BY mr.visit_date DESC 
          LIMIT $per_page OFFSET $offset";
$medical_records = getAllRecords($query, $params);

// Get patients and staff for filter dropdowns
$patients = getAllPatients();
$staff = getAllStaff();

include '../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        
        <!-- Page Header -->
        <div class="mb-6 flex flex-col lg:flex-row lg:items-start lg:justify-between">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Medical Records</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage all patient medical records</p>
            </div>
            <div class="mt-4 lg:mt-0 flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Compact Statistics Cards -->
                <div class="flex gap-3">
                    <!-- Total Records Card -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 min-w-[120px]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-medical text-blue-600 dark:text-blue-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider">Total</p>
                                <p class="text-lg font-semibold text-blue-900 dark:text-blue-100"><?php
                                    // Get total medical records count (without filters)
                                    $total_query = "SELECT COUNT(*) as total FROM medical_records";
                                    $total_result = getSingleRecord($total_query, []);
                                    echo number_format($total_result['total'] ?? 0);
                                ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Filtered Results Card -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 min-w-[120px]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-filter text-green-600 dark:text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">Filtered</p>
                                <p class="text-lg font-semibold text-green-900 dark:text-green-100"><?php echo number_format($total_records); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Button -->
                <a href="add.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-black dark:text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Create Medical Record
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg dark:bg-green-900 dark:border-green-700 dark:text-green-300">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span><?php echo $success_message; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg dark:bg-red-900 dark:border-red-700 dark:text-red-300">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span><?php echo $error_message; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Medical Records</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search by diagnosis, treatment, notes, patient, or staff..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                </div>
                <div class="sm:w-48">
                    <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Patient</label>
                    <select id="patient_id" name="patient_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Patients</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?php echo $patient['patient_id']; ?>" <?php echo ($patient_filter == $patient['patient_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($patient['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sm:w-48">
                    <label for="staff_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Staff</label>
                    <select id="staff_id" name="staff_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Staff</option>
                        <?php foreach ($staff as $staff_member): ?>
                            <option value="<?php echo $staff_member['staff_id']; ?>" <?php echo ($staff_filter == $staff_member['staff_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($staff_member['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                    <a href="list.php" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Medical Records Table -->
        <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <?php if (empty($medical_records)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-file-medical text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No medical records found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <?php if (!empty($search) || !empty($patient_filter) || !empty($staff_filter)): ?>
                            No medical records match your search criteria. Try adjusting your filters.
                        <?php else: ?>
                            Get started by creating your first medical record.
                        <?php endif; ?>
                    </p>
                    <a href="add.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Medical Record
                    </a>
                </div>
            <?php else: ?>

                <!-- Desktop Table View -->
                <div class="medical-records-table-responsive">
                    <div class="medical-records-table-container">
                        <table class="medical-records-table w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Patient</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Staff</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Visit Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diagnosis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Treatment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Follow-up</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($medical_records as $record): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="patient-cell">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-14 w-14">
                                                    <div class="h-14 w-14 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg">
                                                        <span class="text-sm font-bold text-white">
                                                            <?php echo strtoupper(substr($record['patient_name'], 0, 2)); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                                        <?php echo htmlspecialchars($record['patient_name']); ?>
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                            ID: <?php echo $record['patient_id']; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="staff-cell">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg">
                                                        <span class="text-xs font-bold text-white">
                                                            <?php echo strtoupper(substr($record['staff_name'], 0, 2)); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                                        <?php echo htmlspecialchars($record['staff_name']); ?>
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        ID: <?php echo $record['staff_id']; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="visit-date-cell">
                                            <div class="text-center">
                                                <div class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg mb-2">
                                                    <i class="fas fa-calendar-alt mr-2"></i>
                                                    <?php echo date('M d, Y', strtotime($record['visit_date'])); ?>
                                                </div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">
                                                    <i class="fas fa-clock mr-1"></i><?php echo date('g:i A', strtotime($record['visit_date'])); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="diagnosis-cell">
                                            <div class="p-3 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-stethoscope text-yellow-600 dark:text-yellow-400 text-lg"></i>
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Diagnosis</div>
                                                        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                                            <?php echo htmlspecialchars(substr($record['diagnosis'], 0, 80)); ?><?php echo strlen($record['diagnosis']) > 80 ? '...' : ''; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="treatment-cell">
                                            <div class="p-3 bg-gradient-to-br from-green-50 to-green-100 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-pills text-green-600 dark:text-green-400 text-lg"></i>
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Treatment</div>
                                                        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                                            <?php echo htmlspecialchars(substr($record['treatment'], 0, 80)); ?><?php echo strlen($record['treatment']) > 80 ? '...' : ''; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="followup-cell text-center">
                                            <div class="text-sm">
                                                <?php if ($record['follow_up_date']): ?>
                                                    <div class="inline-flex flex-col items-center px-4 py-3 rounded-lg bg-gradient-to-br from-orange-100 to-orange-200 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 border border-orange-300 dark:border-orange-700">
                                                        <i class="fas fa-clock text-lg mb-1"></i>
                                                        <span class="text-xs font-semibold"><?php echo date('M d', strtotime($record['follow_up_date'])); ?></span>
                                                        <span class="text-xs"><?php echo date('Y', strtotime($record['follow_up_date'])); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="inline-flex flex-col items-center px-4 py-3 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border border-gray-300 dark:border-gray-600">
                                                        <i class="fas fa-minus text-lg mb-1"></i>
                                                        <span class="text-xs font-medium">None</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="actions-cell text-center">
                                            <div class="flex items-center justify-center space-x-1">
                                                <button onclick="viewRecord(<?php echo $record['record_id']; ?>)"
                                                        class="table-action-btn bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 hover:from-blue-200 hover:to-blue-300 dark:from-blue-900 dark:to-blue-800 dark:text-blue-300"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="edit.php?id=<?php echo $record['record_id']; ?>"
                                                   class="table-action-btn bg-gradient-to-br from-green-100 to-green-200 text-green-700 hover:from-green-200 hover:to-green-300 dark:from-green-900 dark:to-green-800 dark:text-green-300"
                                                   title="Edit Record">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deleteRecord(<?php echo $record['record_id']; ?>, '<?php echo htmlspecialchars($record['patient_name']); ?>')"
                                                        class="table-action-btn bg-gradient-to-br from-red-100 to-red-200 text-red-700 hover:from-red-200 hover:to-red-300 dark:from-red-900 dark:to-red-800 dark:text-red-300"
                                                        title="Delete Record">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="medical-records-cards-mobile">
                    <?php foreach ($medical_records as $record): ?>
                        <div class="medical-record-card">
                            <!-- Card Header with Patient and Staff -->
                            <div class="medical-record-card-header">
                                <div class="medical-record-card-patient">
                                    <div class="medical-record-card-avatar bg-gradient-to-br from-green-400 to-green-600">
                                        <?php echo strtoupper(substr($record['patient_name'], 0, 2)); ?>
                                    </div>
                                    <div class="medical-record-card-info">
                                        <div class="medical-record-card-name">
                                            <?php echo htmlspecialchars($record['patient_name']); ?>
                                        </div>
                                        <div class="medical-record-card-id">
                                            Patient ID: <?php echo $record['patient_id']; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="medical-record-card-staff">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Attended by:</div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo htmlspecialchars($record['staff_name']); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Details -->
                            <div class="medical-record-card-details">
                                <div class="medical-record-card-detail">
                                    <span class="medical-record-card-detail-label">Visit Date:</span>
                                    <span class="medical-record-card-detail-value">
                                        <?php echo date('M d, Y g:i A', strtotime($record['visit_date'])); ?>
                                    </span>
                                </div>

                                <div class="medical-record-card-detail">
                                    <span class="medical-record-card-detail-label">Diagnosis:</span>
                                    <span class="medical-record-card-detail-value">
                                        <?php echo htmlspecialchars(substr($record['diagnosis'], 0, 100)); ?><?php echo strlen($record['diagnosis']) > 100 ? '...' : ''; ?>
                                    </span>
                                </div>

                                <div class="medical-record-card-detail">
                                    <span class="medical-record-card-detail-label">Treatment:</span>
                                    <span class="medical-record-card-detail-value">
                                        <?php echo htmlspecialchars(substr($record['treatment'], 0, 100)); ?><?php echo strlen($record['treatment']) > 100 ? '...' : ''; ?>
                                    </span>
                                </div>

                                <?php if ($record['prescription']): ?>
                                    <div class="medical-record-card-detail">
                                        <span class="medical-record-card-detail-label">Prescription:</span>
                                        <span class="medical-record-card-detail-value">
                                            <?php echo htmlspecialchars(substr($record['prescription'], 0, 100)); ?><?php echo strlen($record['prescription']) > 100 ? '...' : ''; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($record['follow_up_date']): ?>
                                    <div class="medical-record-card-detail">
                                        <span class="medical-record-card-detail-label">Follow-up:</span>
                                        <span class="medical-record-card-detail-value follow-up-date">
                                            <i class="fas fa-clock mr-1"></i><?php echo date('M d, Y', strtotime($record['follow_up_date'])); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Card Actions -->
                            <div class="medical-record-card-actions">
                                <button onclick="viewRecord(<?php echo $record['record_id']; ?>)"
                                        class="medical-record-card-action-btn view"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="edit.php?id=<?php echo $record['record_id']; ?>"
                                   class="medical-record-card-action-btn edit"
                                   title="Edit Record">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteRecord(<?php echo $record['record_id']; ?>, '<?php echo htmlspecialchars($record['patient_name']); ?>')"
                                        class="medical-record-card-action-btn delete"
                                        title="Delete Record">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <?php echo (($page - 1) * $per_page) + 1; ?> to <?php echo min($page * $per_page, $total_records); ?> of <?php echo $total_records; ?> results
                </div>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&patient_id=<?php echo urlencode($patient_filter); ?>&staff_id=<?php echo urlencode($staff_filter); ?>"
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                            Previous
                        </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&patient_id=<?php echo urlencode($patient_filter); ?>&staff_id=<?php echo urlencode($staff_filter); ?>"
                           class="px-3 py-2 text-sm font-medium <?php echo $i === $page ? 'text-blue-600 bg-blue-50 border-blue-500 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'; ?> border rounded-md">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&patient_id=<?php echo urlencode($patient_filter); ?>&staff_id=<?php echo urlencode($staff_filter); ?>"
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Medical Record Details Modal -->
<div id="recordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Medical Record Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="recordDetails" class="text-sm text-gray-600 dark:text-gray-400">
                <!-- Record details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for medical records table */
.medical-records-table-responsive {
    display: block;
}

.medical-records-table-container {
    overflow-x: auto;
    border-radius: 8px;
}

.medical-records-table {
    min-width: 1400px;
    border-collapse: separate;
    border-spacing: 0;
}

.medical-records-table thead th {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 2px solid #e2e8f0;
    padding: 16px 20px;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.05em;
    position: sticky;
    top: 0;
    z-index: 10;
}

.medical-records-table tbody td {
    padding: 20px;
    vertical-align: top;
    border-bottom: 1px solid #f1f5f9;
}

.medical-records-table tbody tr {
    transition: all 0.2s ease;
}

.medical-records-table tbody tr:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.medical-records-table tbody tr:last-child td {
    border-bottom: none;
}

/* Enhanced column spacing */
.medical-records-table th:first-child,
.medical-records-table td:first-child {
    padding-left: 24px;
}

.medical-records-table th:last-child,
.medical-records-table td:last-child {
    padding-right: 24px;
}

/* Patient column styling */
.medical-records-table .patient-cell {
    min-width: 220px;
}

/* Staff column styling */
.medical-records-table .staff-cell {
    min-width: 180px;
}

/* Visit date column styling */
.medical-records-table .visit-date-cell {
    min-width: 160px;
}

/* Diagnosis column styling */
.medical-records-table .diagnosis-cell {
    min-width: 250px;
    max-width: 300px;
}

/* Treatment column styling */
.medical-records-table .treatment-cell {
    min-width: 250px;
    max-width: 300px;
}

/* Follow-up column styling */
.medical-records-table .followup-cell {
    min-width: 140px;
}

/* Actions column styling */
.medical-records-table .actions-cell {
    min-width: 120px;
}

.table-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    margin: 0 2px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Dark mode enhancements */
@media (prefers-color-scheme: dark) {
    .medical-records-table thead th {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
        border-bottom-color: #374151;
        color: #f9fafb;
    }

    .medical-records-table tbody tr:hover {
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
    }

    .medical-records-table tbody td {
        border-bottom-color: #374151;
    }
}

/* Mobile card styles */
.medical-records-cards-mobile {
    display: none;
}

.medical-record-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.medical-record-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e5e7eb;
}

.medical-record-card-patient {
    display: flex;
    align-items: center;
}

.medical-record-card-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
    margin-right: 12px;
}

.medical-record-card-name {
    font-weight: 600;
    color: #111827;
    font-size: 14px;
}

.medical-record-card-id {
    font-size: 12px;
    color: #6b7280;
}

.medical-record-card-staff {
    text-align: right;
}

.medical-record-card-details {
    margin-bottom: 12px;
}

.medical-record-card-detail {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    align-items: flex-start;
}

.medical-record-card-detail-label {
    font-weight: 500;
    color: #374151;
    font-size: 12px;
    min-width: 80px;
    margin-right: 8px;
}

.medical-record-card-detail-value {
    color: #111827;
    font-size: 12px;
    text-align: right;
    flex: 1;
}

.medical-record-card-detail-value.follow-up-date {
    color: #f59e0b;
    font-weight: 500;
}

.medical-record-card-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
}

.medical-record-card-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    transition: all 0.2s;
    text-decoration: none;
}

.medical-record-card-action-btn.view {
    background-color: #dbeafe;
    color: #2563eb;
}

.medical-record-card-action-btn.view:hover {
    background-color: #bfdbfe;
}

.medical-record-card-action-btn.edit {
    background-color: #dcfce7;
    color: #16a34a;
}

.medical-record-card-action-btn.edit:hover {
    background-color: #bbf7d0;
}

.medical-record-card-action-btn.delete {
    background-color: #fee2e2;
    color: #dc2626;
}

.medical-record-card-action-btn.delete:hover {
    background-color: #fecaca;
}

/* Dark mode styles */
@media (prefers-color-scheme: dark) {
    .medical-record-card {
        background: #1f2937;
        border-color: #374151;
    }

    .medical-record-card-header {
        border-bottom-color: #374151;
    }

    .medical-record-card-name {
        color: #f9fafb;
    }

    .medical-record-card-id {
        color: #9ca3af;
    }

    .medical-record-card-detail-label {
        color: #d1d5db;
    }

    .medical-record-card-detail-value {
        color: #f9fafb;
    }

    .medical-record-card-actions {
        border-top-color: #374151;
    }
}

/* Responsive breakpoints */
@media (max-width: 1024px) {
    .medical-records-table-responsive {
        display: none;
    }

    .medical-records-cards-mobile {
        display: block;
    }
}
</style>

<script>
function viewRecord(recordId) {
    // Show modal with record details
    fetch(`view.php?id=${recordId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('recordDetails').innerHTML = data;
            document.getElementById('recordModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading record details');
        });
}

function closeModal() {
    document.getElementById('recordModal').classList.add('hidden');
}

function deleteRecord(recordId, patientName) {
    if (confirm(`Are you sure you want to delete the medical record for ${patientName}? This action cannot be undone.`)) {
        window.location.href = `list.php?action=delete&id=${recordId}`;
    }
}

// Close modal when clicking outside
document.getElementById('recordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
