<?php
$pageTitle = "Bed Assignment Records";
include '../includes/config.php';

// Handle success message from bed assignment
if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['assignment_id'])) {
    $assignment_id = (int)$_GET['assignment_id'];
    $success_message = "Bed assignment created successfully! Assignment ID: " . $assignment_id;
}

// Handle discharge action
if (isset($_GET['action']) && $_GET['action'] === 'discharge' && isset($_GET['id'])) {
    $assignment_id = (int)$_GET['id'];
    $discharged = dischargeBedAssignment($assignment_id);
    
    if ($discharged) {
        $success_message = "Patient discharged successfully.";
    } else {
        $error_message = "Error discharging patient.";
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $assignment_id = (int)$_GET['id'];
    $delete_query = "DELETE FROM bed_assignments WHERE id = ?";
    $deleted = executeUpdate($delete_query, [$assignment_id]);
    
    if ($deleted) {
        $success_message = "Bed assignment record deleted successfully.";
    } else {
        $error_message = "Error deleting bed assignment record.";
    }
}

// Get search parameters
$search = sanitizeInput($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? '';
$ward_filter = $_GET['ward'] ?? '';
$bed_type_filter = $_GET['bed_type'] ?? '';

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(CONCAT(p.first_name, ' ', p.last_name) LIKE ? OR b.bed_number LIKE ? OR b.ward LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

if (!empty($status_filter)) {
    $where_conditions[] = "ba.status = ?";
    $params[] = $status_filter;
}

if (!empty($ward_filter)) {
    $where_conditions[] = "b.ward = ?";
    $params[] = $ward_filter;
}

if (!empty($bed_type_filter)) {
    $where_conditions[] = "b.bed_type = ?";
    $params[] = $bed_type_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get bed assignments with pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Count total records
$count_query = "SELECT COUNT(*) as total 
                FROM bed_assignments ba
                JOIN patients p ON ba.patient_id = p.patient_id
                JOIN beds b ON ba.bed_id = b.id
                $where_clause";
$total_result = getSingleRecord($count_query, $params);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $per_page);

// Get bed assignments for current page
$query = "SELECT ba.*, 
                 CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                 p.patient_id,
                 b.bed_number, 
                 b.ward, 
                 b.bed_type
          FROM bed_assignments ba
          JOIN patients p ON ba.patient_id = p.patient_id
          JOIN beds b ON ba.bed_id = b.id
          $where_clause 
          ORDER BY ba.admission_date DESC 
          LIMIT $per_page OFFSET $offset";
$bed_assignments = getAllRecords($query, $params);

// Get unique wards and bed types for filters
$wards = getAllRecords("SELECT DISTINCT ward FROM beds ORDER BY ward");
$bed_types = getAllRecords("SELECT DISTINCT bed_type FROM beds ORDER BY bed_type");

include '../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        
        <!-- Page Header -->
        <div class="mb-6 flex flex-col lg:flex-row lg:items-start lg:justify-between">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bed Assignment Records</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage all bed assignments and patient admissions</p>
            </div>
            <div class="mt-4 lg:mt-0 flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Compact Statistics Cards -->
                <div class="flex gap-3">
                    <!-- Total Assignments Card -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 min-w-[120px]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-bed text-blue-600 dark:text-blue-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider">Total</p>
                                <p class="text-lg font-semibold text-blue-900 dark:text-blue-100"><?php
                                    // Get total assignments count (without filters)
                                    $total_query = "SELECT COUNT(*) as total FROM bed_assignments";
                                    $total_result = getSingleRecord($total_query, []);
                                    echo number_format($total_result['total'] ?? 0);
                                ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Assignments Card -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 min-w-[120px]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-check text-green-600 dark:text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">Active</p>
                                <p class="text-lg font-semibold text-green-900 dark:text-green-100"><?php
                                    $active_query = "SELECT COUNT(*) as total FROM bed_assignments WHERE status = 'active'";
                                    $active_result = getSingleRecord($active_query, []);
                                    echo number_format($active_result['total'] ?? 0);
                                ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Available Beds Card -->
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-3 min-w-[120px]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-hospital text-orange-600 dark:text-orange-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wider">Available</p>
                                <p class="text-lg font-semibold text-orange-900 dark:text-orange-100"><?php
                                    $available_query = "SELECT COUNT(*) as total FROM beds WHERE status = 'available'";
                                    $available_result = getSingleRecord($available_query, []);
                                    echo number_format($available_result['total'] ?? 0);
                                ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="flex-shrink-0">
                    <a href="add.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        New Assignment
                    </a>
                </div>
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

        <!-- Search and Filter Form -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                           placeholder="Patient name, bed number, ward...">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="active" <?php echo ($status_filter === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="discharged" <?php echo ($status_filter === 'discharged') ? 'selected' : ''; ?>>Discharged</option>
                    </select>
                </div>
                <div>
                    <label for="ward" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ward</label>
                    <select id="ward" name="ward" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Wards</option>
                        <?php foreach ($wards as $ward): ?>
                            <option value="<?php echo htmlspecialchars($ward['ward']); ?>" <?php echo ($ward_filter === $ward['ward']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ward['ward']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="bed_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bed Type</label>
                    <select id="bed_type" name="bed_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Types</option>
                        <?php foreach ($bed_types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type['bed_type']); ?>" <?php echo ($bed_type_filter === $type['bed_type']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst($type['bed_type'])); ?>
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

        <!-- Bed Assignments Table -->
        <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <?php if (empty($bed_assignments)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-bed text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No bed assignments found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <?php if (!empty($search) || !empty($status_filter) || !empty($ward_filter) || !empty($bed_type_filter)): ?>
                            No bed assignments match your search criteria. Try adjusting your filters.
                        <?php else: ?>
                            Get started by creating your first bed assignment.
                        <?php endif; ?>
                    </p>
                    <a href="add.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Assignment
                    </a>
                </div>
            <?php else: ?>

                <!-- Desktop Table View -->
                <div class="bed-assignment-table-responsive">
                    <div class="bed-assignment-table-container">
                        <table class="bed-assignment-table w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">Patient</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">Bed Details</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">Admission</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">Reason</th>
                                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($bed_assignments as $assignment): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                                                        <span class="text-sm font-bold text-white">
                                                            <?php
                                                            $names = explode(' ', $assignment['patient_name']);
                                                            echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                                            ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        <?php echo htmlspecialchars($assignment['patient_name']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            Patient ID: <?php echo $assignment['patient_id']; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <div class="flex items-center mb-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-bed text-blue-600 dark:text-blue-400 text-xs"></i>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars($assignment['bed_number']); ?></div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($assignment['ward']); ?></div>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    <?php
                                                    switch($assignment['bed_type']) {
                                                        case 'icu': echo 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'; break;
                                                        case 'private': echo 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'; break;
                                                        case 'emergency': echo 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300'; break;
                                                        default: echo 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                                                    }
                                                    ?>">
                                                    <?php echo htmlspecialchars(ucfirst($assignment['bed_type'])); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <div class="font-semibold text-gray-900 dark:text-white">
                                                    <?php echo formatDateTimeForDisplay($assignment['admission_date']); ?>
                                                </div>
                                                <?php if ($assignment['discharge_date']): ?>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                                        Discharged: <?php echo formatDateTimeForDisplay($assignment['discharge_date']); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        <?php
                                                        $admission_time = strtotime($assignment['admission_date']);
                                                        $current_time = time();
                                                        $duration = $current_time - $admission_time;
                                                        $days = floor($duration / (24 * 60 * 60));
                                                        echo $days > 0 ? $days . ' day' . ($days > 1 ? 's' : '') : 'Today';
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold
                                                <?php echo $assignment['status'] === 'active' ?
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'; ?>">
                                                <i class="fas fa-<?php echo $assignment['status'] === 'active' ? 'check-circle' : 'times-circle'; ?> mr-2"></i>
                                                <?php echo htmlspecialchars(ucfirst($assignment['status'])); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white max-w-xs">
                                                <?php if ($assignment['reason']): ?>
                                                    <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                        <?php echo htmlspecialchars($assignment['reason']); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-gray-500 dark:text-gray-400 italic">No reason specified</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <?php if ($assignment['status'] === 'active'): ?>
                                                    <a href="?action=discharge&id=<?php echo $assignment['id']; ?>"
                                                       onclick="return confirm('Are you sure you want to discharge this patient?')"
                                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                                        Discharge
                                                    </a>
                                                <?php endif; ?>
                                                <a href="?action=delete&id=<?php echo $assignment['id']; ?>"
                                                   onclick="return confirm('Are you sure you want to delete this bed assignment record?')"
                                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="bed-assignment-mobile-view">
                    <?php foreach ($bed_assignments as $assignment): ?>
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4 shadow-sm">
                            <!-- Patient Header -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                                            <span class="text-xs font-bold text-white">
                                                <?php
                                                $names = explode(' ', $assignment['patient_name']);
                                                echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($assignment['patient_name']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Patient ID: <?php echo $assignment['patient_id']; ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                    <?php echo $assignment['status'] === 'active' ?
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($assignment['status'])); ?>
                                </span>
                            </div>

                            <!-- Bed Information -->
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                                    <div class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Bed</div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars($assignment['bed_number']); ?></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($assignment['ward']); ?></div>
                                </div>
                                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3">
                                    <div class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wider mb-1">Type</div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo htmlspecialchars(ucfirst($assignment['bed_type'])); ?></div>
                                </div>
                            </div>

                            <!-- Admission Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-3">
                                <div class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Admission</div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                    <?php echo formatDateTimeForDisplay($assignment['admission_date']); ?>
                                </div>
                                <?php if ($assignment['discharge_date']): ?>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Discharged: <?php echo formatDateTimeForDisplay($assignment['discharge_date']); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                        <?php
                                        $admission_time = strtotime($assignment['admission_date']);
                                        $current_time = time();
                                        $duration = $current_time - $admission_time;
                                        $days = floor($duration / (24 * 60 * 60));
                                        echo 'Duration: ' . ($days > 0 ? $days . ' day' . ($days > 1 ? 's' : '') : 'Today');
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Reason -->
                            <?php if ($assignment['reason']): ?>
                                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 mb-3">
                                    <div class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wider mb-1">Reason</div>
                                    <div class="text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($assignment['reason']); ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Actions -->
                            <div class="flex justify-end space-x-2 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <?php if ($assignment['status'] === 'active'): ?>
                                    <a href="?action=discharge&id=<?php echo $assignment['id']; ?>"
                                       onclick="return confirm('Are you sure you want to discharge this patient?')"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700">
                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                        Discharge
                                    </a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?php echo $assignment['id']; ?>"
                                   onclick="return confirm('Are you sure you want to delete this bed assignment record?')"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                                    <i class="fas fa-trash mr-1"></i>
                                    Delete
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&ward=<?php echo urlencode($ward_filter); ?>&bed_type=<?php echo urlencode($bed_type_filter); ?>"
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Previous
                                    </a>
                                <?php endif; ?>
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&ward=<?php echo urlencode($ward_filter); ?>&bed_type=<?php echo urlencode($bed_type_filter); ?>"
                                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Next
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing <span class="font-medium"><?php echo ($page - 1) * $per_page + 1; ?></span> to
                                        <span class="font-medium"><?php echo min($page * $per_page, $total_records); ?></span> of
                                        <span class="font-medium"><?php echo $total_records; ?></span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <?php if ($page > 1): ?>
                                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&ward=<?php echo urlencode($ward_filter); ?>&bed_type=<?php echo urlencode($bed_type_filter); ?>"
                                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&ward=<?php echo urlencode($ward_filter); ?>&bed_type=<?php echo urlencode($bed_type_filter); ?>"
                                               class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?php echo $i === $page ? 'z-10 bg-blue-50 border-blue-500 text-blue-600 dark:bg-blue-900 dark:border-blue-600 dark:text-blue-300' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600'; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&ward=<?php echo urlencode($ward_filter); ?>&bed_type=<?php echo urlencode($bed_type_filter); ?>"
                                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Enhanced table styling */
.bed-assignment-table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.bed-assignment-table-container {
    min-width: 100%;
}

.bed-assignment-table {
    border-collapse: separate;
    border-spacing: 0;
}

.bed-assignment-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    border-bottom: 2px solid #e5e7eb;
    font-weight: 600;
    letter-spacing: 0.05em;
    padding: 1rem 1.5rem;
}

.bed-assignment-table tbody td {
    padding: 1rem 1.5rem;
    vertical-align: top;
    border-bottom: 1px solid #f3f4f6;
}

.bed-assignment-table tbody tr:hover {
    background-color: #f9fafb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.dark .bed-assignment-table tbody tr:hover {
    background-color: #374151;
}

.bed-assignment-table tbody tr:last-child td {
    border-bottom: none;
}

/* Mobile view styling */
.bed-assignment-mobile-view {
    display: none;
}

@media (max-width: 768px) {
    .bed-assignment-table-responsive {
        display: none;
    }

    .bed-assignment-mobile-view {
        display: block;
    }
}

/* Dark mode adjustments */
.dark .bed-assignment-table thead th {
    border-bottom-color: #4b5563;
}

.dark .bed-assignment-table tbody td {
    border-bottom-color: #374151;
}
</style>

<?php include '../includes/footer.php'; ?>
