<?php
$pageTitle = "Patient Records";
include '../includes/config.php';

// Handle success message from patient registration
if (isset($_GET['success']) && $_GET['success'] == '1' && isset($_GET['patient_id'])) {
    $patient_id = (int)$_GET['patient_id'];
    $success_message = "Patient registered successfully! Patient ID: " . $patient_id;
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $patient_id = (int)$_GET['id'];
    $delete_query = "DELETE FROM patients WHERE patient_id = ?";
    $deleted = executeUpdate($delete_query, [$patient_id]);
    
    if ($deleted) {
        $success_message = "Patient record deleted successfully.";
    } else {
        $error_message = "Error deleting patient record.";
    }
}

// Get search parameters
$search = sanitizeInput($_GET['search'] ?? '');
$blood_type_filter = $_GET['blood_type'] ?? '';

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if (!empty($blood_type_filter)) {
    $where_conditions[] = "blood_type = ?";
    $params[] = $blood_type_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get patients with pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Count total records
$count_query = "SELECT COUNT(*) as total FROM patients $where_clause";
$total_result = getSingleRecord($count_query, $params);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $per_page);

// Get patients for current page
$query = "SELECT * FROM patients $where_clause ORDER BY registration_date DESC LIMIT $per_page OFFSET $offset";
$patients = getAllRecords($query, $params);

include '../includes/header.php';
?>

<?php include '../includes/sidebar.php'; ?>

<div class="p-4 sm:ml-64">
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        
        <!-- Page Header -->
        <div class="mb-6 flex flex-col lg:flex-row lg:items-start lg:justify-between">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Patient Records</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage all registered patients</p>
            </div>
            <div class="mt-4 lg:mt-0 flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Compact Statistics Cards -->
                <div class="flex gap-3">
                    <!-- Total Patients Card -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 min-w-[120px]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users text-blue-600 dark:text-blue-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider">Total</p>
                                <p class="text-lg font-semibold text-blue-900 dark:text-blue-100"><?php
                                    // Get total patients count (without filters)
                                    $total_query = "SELECT COUNT(*) as total FROM patients";
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

                <!-- Register Button -->
                <a href="add.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-black dark:text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Register New Patient
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
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Patients</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search by name, email, or phone..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                </div>
                <div class="sm:w-48">
                    <label for="blood_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Blood Type</label>
                    <select id="blood_type" name="blood_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">All Blood Types</option>
                        <option value="A+" <?php echo ($blood_type_filter === 'A+') ? 'selected' : ''; ?>>A+</option>
                        <option value="A-" <?php echo ($blood_type_filter === 'A-') ? 'selected' : ''; ?>>A-</option>
                        <option value="B+" <?php echo ($blood_type_filter === 'B+') ? 'selected' : ''; ?>>B+</option>
                        <option value="B-" <?php echo ($blood_type_filter === 'B-') ? 'selected' : ''; ?>>B-</option>
                        <option value="AB+" <?php echo ($blood_type_filter === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                        <option value="AB-" <?php echo ($blood_type_filter === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                        <option value="O+" <?php echo ($blood_type_filter === 'O+') ? 'selected' : ''; ?>>O+</option>
                        <option value="O-" <?php echo ($blood_type_filter === 'O-') ? 'selected' : ''; ?>>O-</option>
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



        <!-- Patients Table -->
        <div class="w-full bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <?php if (empty($patients)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No patients found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <?php if (!empty($search) || !empty($blood_type_filter)): ?>
                            No patients match your search criteria. Try adjusting your filters.
                        <?php else: ?>
                            Get started by registering your first patient.
                        <?php endif; ?>
                    </p>
                    <a href="add.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Register First Patient
                    </a>
                </div>
            <?php else: ?>

                <!-- Desktop Table View -->
                <div class="patient-table-responsive">
                    <div class="patient-table-container">
                        <table class="patient-table w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Patient</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Blood Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Emergency Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Registered</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($patients as $patient): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                                                        <span class="text-sm font-bold text-white">
                                                            <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                        <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            ID: <?php echo $patient['patient_id']; ?>
                                                        </span>
                                                        <?php if ($patient['date_of_birth']): ?>
                                                            <span class="ml-2 text-xs">DOB: <?php echo date('M d, Y', strtotime($patient['date_of_birth'])); ?></span>
                                                        <?php endif; ?>
                                                        <?php if ($patient['gender']): ?>
                                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                                <?php echo $patient['gender'] === 'Male' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                                                    ($patient['gender'] === 'Female' ? 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300' :
                                                                    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'); ?>">
                                                                <i class="fas fa-<?php echo $patient['gender'] === 'Male' ? 'mars' : ($patient['gender'] === 'Female' ? 'venus' : 'genderless'); ?> mr-1"></i>
                                                                <?php echo ucfirst($patient['gender']); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <?php if ($patient['phone_number']): ?>
                                                    <div class="flex items-center mb-2 p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-phone text-green-600 dark:text-green-400 text-xs"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($patient['phone_number']); ?></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($patient['email']): ?>
                                                    <div class="flex items-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-envelope text-blue-600 dark:text-blue-400 text-xs"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-32"><?php echo htmlspecialchars($patient['email']); ?></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!$patient['phone_number'] && !$patient['email']): ?>
                                                    <span class="text-gray-500 dark:text-gray-400 italic">No contact info</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <?php if ($patient['blood_type']): ?>
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-red-50 border border-red-200 dark:bg-red-900 dark:border-red-700 shadow-lg" style="color: #dc2626;">
                                                        <i class="fas fa-tint mr-2" style="color: #dc2626;"></i>
                                                        <span class="dark:text-red-300"><?php echo htmlspecialchars($patient['blood_type']); ?></span>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                        <i class="fas fa-question mr-2"></i>
                                                        Unknown
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <?php if ($patient['emergency_contact_name']): ?>
                                                    <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                                        <div class="font-semibold text-gray-900 dark:text-white flex items-center">
                                                            <i class="fas fa-user-shield text-orange-600 dark:text-orange-400 mr-2"></i>
                                                            <?php echo htmlspecialchars($patient['emergency_contact_name']); ?>
                                                        </div>
                                                        <?php if ($patient['emergency_contact_phone']): ?>
                                                            <div class="text-gray-600 dark:text-gray-400 mt-1 flex items-center">
                                                                <i class="fas fa-phone text-orange-500 mr-2 text-xs"></i>
                                                                <?php echo htmlspecialchars($patient['emergency_contact_phone']); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-gray-500 dark:text-gray-400 italic">Not provided</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <div class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-black dark:text-white shadow-lg">
                                                    <i class="fas fa-calendar-alt mr-2"></i>
                                                    <?php echo date('M d, Y', strtotime($patient['registration_date'])); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button onclick="viewPatient(<?php echo $patient['patient_id']; ?>)"
                                                        class="table-action-btn bg-blue-100 text-blue-600 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-400 dark:hover:bg-blue-800"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="edit.php?id=<?php echo $patient['patient_id']; ?>"
                                                   class="table-action-btn bg-green-100 text-green-600 hover:bg-green-200 dark:bg-green-900 dark:text-green-400 dark:hover:bg-green-800"
                                                   title="Edit Patient">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deletePatient(<?php echo $patient['patient_id']; ?>, '<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>')"
                                                        class="table-action-btn bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900 dark:text-red-400 dark:hover:bg-red-800"
                                                        title="Delete Patient">
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
                <div class="patient-cards-mobile">
                    <?php foreach ($patients as $patient): ?>
                        <div class="patient-card">
                            <!-- Card Header with Avatar and Name -->
                            <div class="patient-card-header">
                                <div class="patient-card-avatar">
                                    <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                                </div>
                                <div class="patient-card-info">
                                    <div class="patient-card-name">
                                        <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                    </div>
                                    <div class="patient-card-id">
                                        ID: <?php echo $patient['patient_id']; ?>
                                        <?php if ($patient['gender']): ?>
                                            | <?php echo ucfirst($patient['gender']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Details -->
                            <div class="patient-card-details">
                                <?php if ($patient['date_of_birth']): ?>
                                    <div class="patient-card-detail">
                                        <span class="patient-card-detail-label">DOB:</span>
                                        <span class="patient-card-detail-value"><?php echo date('M d, Y', strtotime($patient['date_of_birth'])); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($patient['phone_number']): ?>
                                    <div class="patient-card-detail">
                                        <span class="patient-card-detail-label">Phone:</span>
                                        <span class="patient-card-detail-value"><?php echo htmlspecialchars($patient['phone_number']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($patient['email']): ?>
                                    <div class="patient-card-detail">
                                        <span class="patient-card-detail-label">Email:</span>
                                        <span class="patient-card-detail-value"><?php echo htmlspecialchars($patient['email']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($patient['blood_type']): ?>
                                    <div class="patient-card-detail">
                                        <span class="patient-card-detail-label">Blood:</span>
                                        <span class="patient-card-detail-value blood-type">
                                            <i class="fas fa-tint mr-1"></i><?php echo htmlspecialchars($patient['blood_type']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($patient['emergency_contact_name']): ?>
                                    <div class="patient-card-detail">
                                        <span class="patient-card-detail-label">Emergency:</span>
                                        <span class="patient-card-detail-value">
                                            <?php echo htmlspecialchars($patient['emergency_contact_name']); ?>
                                            <?php if ($patient['emergency_contact_phone']): ?>
                                                <br><small><?php echo htmlspecialchars($patient['emergency_contact_phone']); ?></small>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <div class="patient-card-detail">
                                    <span class="patient-card-detail-label">Registered:</span>
                                    <span class="patient-card-detail-value"><?php echo date('M d, Y', strtotime($patient['registration_date'])); ?></span>
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="patient-card-actions">
                                <button onclick="viewPatient(<?php echo $patient['patient_id']; ?>)"
                                        class="patient-card-action-btn view"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="edit.php?id=<?php echo $patient['patient_id']; ?>"
                                   class="patient-card-action-btn edit"
                                   title="Edit Patient">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deletePatient(<?php echo $patient['patient_id']; ?>, '<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>')"
                                        class="patient-card-action-btn delete"
                                        title="Delete Patient">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Patient Details Modal -->
<div id="patientModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Patient Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="patientDetails" class="text-sm text-gray-600 dark:text-gray-400">
                <!-- Patient details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewPatient(patientId) {
    // Show modal with patient details
    fetch(`view.php?id=${patientId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('patientDetails').innerHTML = data;
            document.getElementById('patientModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading patient details');
        });
}

function closeModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function deletePatient(patientId, patientName) {
    if (confirm(`Are you sure you want to delete the record for ${patientName}? This action cannot be undone.`)) {
        window.location.href = `list.php?action=delete&id=${patientId}`;
    }
}

// Close modal when clicking outside
document.getElementById('patientModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
