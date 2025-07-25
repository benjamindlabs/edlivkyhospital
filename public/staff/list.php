<?php
$pageTitle = "Staff Records";
include '../includes/config.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $staff_id = (int)$_GET['id'];
    $delete_query = "DELETE FROM staff WHERE staff_id = ?";
    $deleted = executeUpdate($delete_query, [$staff_id]);

    if ($deleted) {
        $success_message = "Staff record deleted successfully.";
    } else {
        $error_message = "Error deleting staff record.";
    }
}

// Handle success message from registration
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Staff member registered successfully!";
    if (isset($_GET['staff_id'])) {
        $success_message .= " Staff ID: " . htmlspecialchars($_GET['staff_id']);
    }
}

// Get search parameters
$search = sanitizeInput($_GET['search'] ?? '');
$role_filter = $_GET['role'] ?? '';
$department_filter = sanitizeInput($_GET['department'] ?? '');
$status_filter = $_GET['status'] ?? '';

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ? OR phone_number LIKE ? OR license_number LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if (!empty($role_filter)) {
    $where_conditions[] = "role = ?";
    $params[] = $role_filter;
}

if (!empty($department_filter)) {
    $where_conditions[] = "department LIKE ?";
    $params[] = "%$department_filter%";
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get staff with pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Count total records
$count_query = "SELECT COUNT(*) as total FROM staff $where_clause";
$total_result = getSingleRecord($count_query, $params);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $per_page);

// Get staff for current page
$query = "SELECT * FROM staff $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$staff_members = getAllRecords($query, $params);

// Get unique departments for filter dropdown
$dept_query = "SELECT DISTINCT department FROM staff WHERE department IS NOT NULL AND department != '' ORDER BY department";
$departments = getAllRecords($dept_query);

include '../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">

        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Staff Records</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage all registered staff members</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <!-- Statistics Cards - Compact Top-Right -->
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-black dark:text-white px-4 py-2 rounded-lg shadow-lg">
                        <div class="flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            <div>
                                <p class="text-xs opacity-90">Total Staff</p>
                                <p class="text-lg font-bold"><?php echo number_format($total_records); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="add.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-plus mr-2"></i>
                    Register New Staff
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
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Staff</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Search by name, email, phone, or license..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                    <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Roles</option>
                        <option value="Doctor" <?php echo ($role_filter === 'Doctor') ? 'selected' : ''; ?>>Doctor</option>
                        <option value="Nurse" <?php echo ($role_filter === 'Nurse') ? 'selected' : ''; ?>>Nurse</option>
                        <option value="Administrator" <?php echo ($role_filter === 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Status</option>
                        <option value="Active" <?php echo ($status_filter === 'Active') ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo ($status_filter === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="lg:col-span-4 flex items-end gap-2">
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

        <!-- Staff Records Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <?php if (empty($staff_members)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-user-md text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No staff members found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <?php if (!empty($search) || !empty($role_filter) || !empty($status_filter)): ?>
                            No staff members match your search criteria. Try adjusting your filters.
                        <?php else: ?>
                            Get started by registering your first staff member.
                        <?php endif; ?>
                    </p>
                    <a href="add.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-plus mr-2"></i>
                        Register First Staff Member
                    </a>
                </div>
            <?php else: ?>
                <!-- Desktop Table View -->
                <div class="staff-table-responsive">
                    <div class="staff-table-container">
                        <table class="staff-table w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Staff Member</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registered</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($staff_members as $staff): ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center shadow-lg">
                                                    <span class="text-lg font-bold text-white">
                                                        <?php
                                                        $initials = strtoupper(substr($staff['first_name'], 0, 1) . substr($staff['last_name'], 0, 1));
                                                        echo $initials;
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    ID: <?php echo $staff['staff_id']; ?>
                                                    <?php if ($staff['license_number']): ?>
                                                        | License: <?php echo htmlspecialchars($staff['license_number']); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700 shadow-lg">
                                            <i class="fas fa-user-tie mr-2"></i>
                                            <?php echo htmlspecialchars($staff['role']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($staff['department']): ?>
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-purple-50 text-purple-800 border border-purple-200 dark:bg-purple-900 dark:text-purple-300 dark:border-purple-700 shadow-lg">
                                                <i class="fas fa-building mr-2"></i>
                                                <?php echo htmlspecialchars($staff['department']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400 dark:text-gray-500 italic">Not specified</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            <?php if ($staff['phone_number']): ?>
                                                <div class="flex items-center mb-1">
                                                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                                                    <?php echo htmlspecialchars($staff['phone_number']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($staff['email']): ?>
                                                <div class="flex items-center">
                                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                                    <span class="truncate max-w-xs"><?php echo htmlspecialchars($staff['email']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $status_colors = [
                                            'Active' => 'bg-green-50 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-700',
                                            'Inactive' => 'bg-gray-50 text-gray-800 border-gray-200 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700'
                                        ];
                                        $status_class = $status_colors[$staff['status']] ?? $status_colors['Inactive'];
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold border shadow-lg <?php echo $status_class; ?>">
                                            <i class="fas fa-circle mr-2 text-xs"></i>
                                            <?php echo htmlspecialchars($staff['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <div class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-black dark:text-white shadow-lg">
                                                <i class="fas fa-calendar-alt mr-2"></i>
                                                <?php echo date('M d, Y', strtotime($staff['created_at'])); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button onclick="viewStaff(<?php echo $staff['staff_id']; ?>)"
                                                    class="table-action-btn bg-blue-100 text-blue-600 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-400 dark:hover:bg-blue-800"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="edit.php?id=<?php echo $staff['staff_id']; ?>"
                                               class="table-action-btn bg-green-100 text-green-600 hover:bg-green-200 dark:bg-green-900 dark:text-green-400 dark:hover:bg-green-800"
                                               title="Edit Staff">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="deleteStaff(<?php echo $staff['staff_id']; ?>, '<?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>')"
                                                    class="table-action-btn bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900 dark:text-red-400 dark:hover:bg-red-800"
                                                    title="Delete Staff">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Mobile Card View -->
                <div class="staff-cards-mobile">
                    <?php foreach ($staff_members as $staff): ?>
                        <div class="staff-card">
                            <!-- Card Header with Avatar and Name -->
                            <div class="staff-card-header">
                                <div class="staff-card-avatar">
                                    <?php echo strtoupper(substr($staff['first_name'], 0, 1) . substr($staff['last_name'], 0, 1)); ?>
                                </div>
                                <div class="staff-card-info">
                                    <div class="staff-card-name">
                                        <?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>
                                    </div>
                                    <div class="staff-card-id">
                                        ID: <?php echo $staff['staff_id']; ?>
                                        <?php if ($staff['license_number']): ?>
                                            | License: <?php echo htmlspecialchars($staff['license_number']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Content -->
                            <div class="staff-card-content">
                                <div class="staff-card-detail">
                                    <span class="staff-card-label">Role:</span>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700 shadow-lg">
                                        <i class="fas fa-user-tie mr-2"></i>
                                        <?php echo htmlspecialchars($staff['role']); ?>
                                    </span>
                                </div>

                                <?php if ($staff['department']): ?>
                                <div class="staff-card-detail">
                                    <span class="staff-card-label">Department:</span>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-purple-50 text-purple-800 border border-purple-200 dark:bg-purple-900 dark:text-purple-300 dark:border-purple-700 shadow-lg">
                                        <i class="fas fa-building mr-2"></i>
                                        <?php echo htmlspecialchars($staff['department']); ?>
                                    </span>
                                </div>
                                <?php endif; ?>

                                <?php if ($staff['phone_number']): ?>
                                <div class="staff-card-detail">
                                    <span class="staff-card-label">Phone:</span>
                                    <span class="staff-card-value"><?php echo htmlspecialchars($staff['phone_number']); ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if ($staff['email']): ?>
                                <div class="staff-card-detail">
                                    <span class="staff-card-label">Email:</span>
                                    <span class="staff-card-value"><?php echo htmlspecialchars($staff['email']); ?></span>
                                </div>
                                <?php endif; ?>

                                <div class="staff-card-detail">
                                    <span class="staff-card-label">Status:</span>
                                    <?php
                                    $status_colors = [
                                        'Active' => 'bg-green-50 text-green-800 border-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-700',
                                        'Inactive' => 'bg-gray-50 text-gray-800 border-gray-200 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700'
                                    ];
                                    $status_class = $status_colors[$staff['status']] ?? $status_colors['Inactive'];
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold border shadow-lg <?php echo $status_class; ?>">
                                        <i class="fas fa-circle mr-2 text-xs"></i>
                                        <?php echo htmlspecialchars($staff['status']); ?>
                                    </span>
                                </div>

                                <div class="staff-card-detail">
                                    <span class="staff-card-label">Registered:</span>
                                    <span class="staff-card-value"><?php echo date('M d, Y', strtotime($staff['created_at'])); ?></span>
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="staff-card-actions">
                                <button onclick="viewStaff(<?php echo $staff['staff_id']; ?>)"
                                        class="staff-card-action-btn view"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="edit.php?id=<?php echo $staff['staff_id']; ?>"
                                   class="staff-card-action-btn edit"
                                   title="Edit Staff">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteStaff(<?php echo $staff['staff_id']; ?>, '<?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>')"
                                        class="staff-card-action-btn delete"
                                        title="Delete Staff">
                                    <i class="fas fa-trash"></i>
                                </button>
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
                                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Previous
                                    </a>
                                <?php endif; ?>
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Next
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing <span class="font-medium"><?php echo (($page - 1) * $per_page) + 1; ?></span> to
                                        <span class="font-medium"><?php echo min($page * $per_page, $total_records); ?></span> of
                                        <span class="font-medium"><?php echo $total_records; ?></span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <?php if ($page > 1): ?>
                                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                            <?php if ($i == $page): ?>
                                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-300 dark:border-gray-600">
                                                    <?php echo $i; ?>
                                                </span>
                                            <?php else: ?>
                                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                                    <?php echo $i; ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
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

<!-- Staff Details Modal -->
<div id="staffModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Staff Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="staffDetails" class="text-sm text-gray-600 dark:text-gray-400">
                <!-- Staff details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewStaff(staffId) {
    // Show modal with staff details
    fetch(`view.php?id=${staffId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('staffDetails').innerHTML = data;
            document.getElementById('staffModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading staff details');
        });
}

function closeModal() {
    document.getElementById('staffModal').classList.add('hidden');
}

function deleteStaff(staffId, staffName) {
    if (confirm(`Are you sure you want to delete the record for ${staffName}? This action cannot be undone.`)) {
        window.location.href = `list.php?action=delete&id=${staffId}`;
    }
}

// Close modal when clicking outside
document.getElementById('staffModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
