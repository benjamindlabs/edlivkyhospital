<?php
// Database configuration - Using simple approach like example.php
$db_config = [
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => 'edlivkyhospital'
];

// Create database connection - Simple approach
function getDBConnection() {
    global $db_config;

    $connection = mysqli_connect(
        $db_config['host'],
        $db_config['username'],
        $db_config['password']
    );

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $db_found = mysqli_select_db($connection, $db_config['database']);
    if (!$db_found) {
        die("Database not found: " . $db_config['database']);
    }

    // Set charset to utf8
    mysqli_set_charset($connection, "utf8");

    return $connection;
}

// Function to execute query and return result - Simplified approach
function executeQuery($query, $params = []) {
    $connection = getDBConnection();

    if (!empty($params)) {
        // Use prepared statements for safety
        $stmt = mysqli_prepare($connection, $query);
        if ($stmt) {
            $types = getParameterTypes($params);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            $success = mysqli_stmt_execute($stmt);
            if ($success) {
                $result = mysqli_stmt_get_result($stmt);
            } else {
                $result = false;
                error_log("Query execution failed: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            $result = false;
            error_log("Query preparation failed: " . mysqli_error($connection));
        }
    } else {
        $result = mysqli_query($connection, $query);
        if (!$result) {
            error_log("Query failed: " . mysqli_error($connection));
        }
    }

    mysqli_close($connection);
    return $result;
}

// Function to get single record
function getSingleRecord($query, $params = []) {
    $result = executeQuery($query, $params);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

// Function to get all records
function getAllRecords($query, $params = []) {
    $result = executeQuery($query, $params);
    $records = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
    }
    return $records;
}

// Function to insert record and return ID - Enhanced with better error handling
function insertRecord($query, $params = []) {
    $connection = getDBConnection();

    if (!empty($params)) {
        $stmt = mysqli_prepare($connection, $query);
        if ($stmt) {
            $types = getParameterTypes($params);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            $success = mysqli_stmt_execute($stmt);
            if ($success) {
                $insert_id = mysqli_insert_id($connection);
            } else {
                $insert_id = 0;
                error_log("Insert failed: " . mysqli_stmt_error($stmt));
                error_log("Query: " . $query);
                error_log("Params: " . print_r($params, true));
            }
            mysqli_stmt_close($stmt);
        } else {
            $success = false;
            $insert_id = 0;
            error_log("Insert preparation failed: " . mysqli_error($connection));
            error_log("Query: " . $query);
        }
    } else {
        $success = mysqli_query($connection, $query);
        if ($success) {
            $insert_id = mysqli_insert_id($connection);
        } else {
            $insert_id = 0;
            error_log("Insert query failed: " . mysqli_error($connection));
            error_log("Query: " . $query);
        }
    }

    mysqli_close($connection);
    return $success ? $insert_id : false;
}

// Function to update/delete records
function executeUpdate($query, $params = []) {
    $connection = getDBConnection();
    
    if (!empty($params)) {
        $stmt = mysqli_prepare($connection, $query);
        if ($stmt) {
            $types = getParameterTypes($params);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            $success = mysqli_stmt_execute($stmt);
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
        } else {
            $success = false;
            $affected_rows = 0;
        }
    } else {
        $success = mysqli_query($connection, $query);
        $affected_rows = mysqli_affected_rows($connection);
    }
    
    mysqli_close($connection);
    return $success ? $affected_rows : false;
}

// Helper function to determine parameter types for prepared statements
function getParameterTypes($params) {
    $types = '';
    foreach ($params as $param) {
        if (is_int($param)) {
            $types .= 'i';
        } elseif (is_float($param)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }
    return $types;
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone number
function validatePhone($phone) {
    // Remove all non-digit characters except + for country code
    $cleaned_phone = preg_replace('/[^\d+]/', '', $phone);

    // Check if phone number is reasonable length (7-15 digits, optionally with + prefix)
    // Accepts formats like: +1234567890, 123-456-7890, (123) 456-7890, 123.456.7890, etc.
    return preg_match('/^[\+]?[\d]{7,15}$/', $cleaned_phone);
}

// Function to format date for database
function formatDateForDB($date) {
    if (empty($date)) return null;
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    return $dateTime ? $dateTime->format('Y-m-d') : null;
}

// Function to format datetime for database
function formatDateTimeForDB($datetime) {
    if (empty($datetime)) return null;

    // Handle HTML5 datetime-local format (Y-m-d\TH:i)
    $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $datetime);

    // If that fails, try the old format (Y-m-d H:i)
    if (!$dateTime) {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i', $datetime);
    }

    // If that fails, try with seconds (Y-m-d\TH:i:s)
    if (!$dateTime) {
        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i:s', $datetime);
    }

    return $dateTime ? $dateTime->format('Y-m-d H:i:s') : null;
}

// ======================================================================
// MEDICAL RECORDS FUNCTIONS
// ======================================================================

// Get all medical records with patient and staff names
function getMedicalRecords() {
    $sql = "SELECT mr.*, 
                   CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                   CONCAT(s.first_name, ' ', s.last_name) AS staff_name
            FROM medical_records mr
            JOIN patients p ON mr.patient_id = p.patient_id
            JOIN staff s ON mr.staff_id = s.staff_id";
    return getAllRecords($sql);
}

// Get single medical record by ID
function getMedicalRecordById($id) {
    $sql = "SELECT mr.*, 
                   CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                   CONCAT(s.first_name, ' ', s.last_name) AS staff_name
            FROM medical_records mr
            JOIN patients p ON mr.patient_id = p.patient_id
            JOIN staff s ON mr.staff_id = s.staff_id
            WHERE mr.record_id = ?";
    return getSingleRecord($sql, [$id]);
}

// Create a new medical record
function createMedicalRecord($data) {
    // Convert associative array to indexed array in correct order
    $params = [
        $data['patient_id'],
        $data['staff_id'],
        $data['visit_date'],
        $data['diagnosis'],
        $data['treatment'],
        $data['notes'] ?? null,
        $data['prescription'] ?? null,
        $data['follow_up_date'] ?? null
    ];
    
    $sql = "INSERT INTO medical_records 
            (patient_id, staff_id, visit_date, diagnosis, treatment, notes, prescription, follow_up_date) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?)";
    
    return insertRecord($sql, $params);
}

// Update an existing medical record
function updateMedicalRecord($data) {
    $params = [
        $data['patient_id'],
        $data['staff_id'],
        $data['visit_date'],
        $data['diagnosis'],
        $data['treatment'],
        $data['notes'] ?? null,
        $data['prescription'] ?? null,
        $data['follow_up_date'] ?? null,
        $data['record_id']
    ];
    
    $sql = "UPDATE medical_records SET 
            patient_id = ?, 
            staff_id = ?, 
            visit_date = ?, 
            diagnosis = ?, 
            treatment = ?, 
            notes = ?, 
            prescription = ?, 
            follow_up_date = ? 
            WHERE record_id = ?";
    
    return executeUpdate($sql, $params) > 0;
}

// Delete a medical record
function deleteMedicalRecord($id) {
    $sql = "DELETE FROM medical_records WHERE record_id = ?";
    return executeUpdate($sql, [$id]) > 0;
}

// ======================================================================
// PATIENT/STAFF FUNCTIONS FOR MEDICAL RECORDS (IF NOT ALREADY PRESENT)
// ======================================================================

// Get all patients for dropdowns
function getAllPatients() {
    return getAllRecords("SELECT patient_id, CONCAT(first_name, ' ', last_name) AS full_name FROM patients");
}

// Get all staff for dropdowns
function getAllStaff() {
    return getAllRecords("SELECT staff_id, CONCAT(first_name, ' ', last_name) AS full_name FROM staff WHERE status = 'Active'");
}

// ======================================================================
// BED ASSIGNMENT FUNCTIONS
// ======================================================================

// Function to get all available beds for dropdowns
function getAvailableBeds() {
    $query = "SELECT id, bed_number, ward, bed_type FROM beds WHERE status = 'available' ORDER BY ward, bed_number";
    return getAllRecords($query);
}

// Function to get all beds (for management purposes)
function getAllBeds() {
    $query = "SELECT id, bed_number, ward, bed_type, status FROM beds ORDER BY ward, bed_number";
    return getAllRecords($query);
}

// Function to get bed assignment details with patient and bed info
function getBedAssignmentDetails($assignment_id) {
    $query = "SELECT ba.*,
                     CONCAT(p.first_name, ' ', p.last_name) as patient_name,
                     p.patient_id,
                     b.bed_number,
                     b.ward,
                     b.bed_type
              FROM bed_assignments ba
              JOIN patients p ON ba.patient_id = p.patient_id
              JOIN beds b ON ba.bed_id = b.id
              WHERE ba.id = ?";
    return getSingleRecord($query, [$assignment_id]);
}

// Function to check if a bed is available
function isBedAvailable($bed_id) {
    $query = "SELECT status FROM beds WHERE id = ?";
    $bed = getSingleRecord($query, [$bed_id]);
    return $bed && $bed['status'] === 'available';
}

// Function to update bed status
function updateBedStatus($bed_id, $status) {
    $query = "UPDATE beds SET status = ? WHERE id = ?";
    return executeUpdate($query, [$status, $bed_id]);
}

// Function to create bed assignment with transaction
function createBedAssignment($patient_id, $bed_id, $admission_date, $reason = null) {
    $connection = getDBConnection();

    // Start transaction
    mysqli_begin_transaction($connection);

    try {
        // Check if bed is still available
        if (!isBedAvailable($bed_id)) {
            throw new Exception("Bed is no longer available");
        }

        // Insert bed assignment
        $insert_assignment = "INSERT INTO bed_assignments (patient_id, bed_id, admission_date, reason, status) VALUES (?, ?, ?, ?, 'active')";
        $stmt1 = mysqli_prepare($connection, $insert_assignment);
        mysqli_stmt_bind_param($stmt1, 'iiss', $patient_id, $bed_id, $admission_date, $reason);
        $success1 = mysqli_stmt_execute($stmt1);
        $assignment_id = mysqli_insert_id($connection);
        mysqli_stmt_close($stmt1);

        if (!$success1) {
            throw new Exception("Failed to create assignment");
        }

        // Update bed status to occupied
        $update_bed = "UPDATE beds SET status = 'occupied' WHERE id = ?";
        $stmt2 = mysqli_prepare($connection, $update_bed);
        mysqli_stmt_bind_param($stmt2, 'i', $bed_id);
        $success2 = mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        if (!$success2) {
            throw new Exception("Failed to update bed status");
        }

        // Commit transaction
        mysqli_commit($connection);
        mysqli_close($connection);
        return $assignment_id;

    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($connection);
        mysqli_close($connection);
        error_log("Bed assignment failed: " . $e->getMessage());
        return false;
    }
}

// Function to discharge patient from bed
function dischargeBedAssignment($assignment_id) {
    $connection = getDBConnection();

    // Start transaction
    mysqli_begin_transaction($connection);

    try {
        // Get assignment details
        $assignment = getBedAssignmentDetails($assignment_id);
        if (!$assignment) {
            throw new Exception("Assignment not found");
        }

        // Update assignment status and discharge date
        $update_assignment = "UPDATE bed_assignments SET status = 'discharged', discharge_date = NOW() WHERE id = ?";
        $stmt1 = mysqli_prepare($connection, $update_assignment);
        mysqli_stmt_bind_param($stmt1, 'i', $assignment_id);
        $success1 = mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);

        if (!$success1) {
            throw new Exception("Failed to update assignment");
        }

        // Update bed status to available
        $update_bed = "UPDATE beds SET status = 'available' WHERE id = ?";
        $stmt2 = mysqli_prepare($connection, $update_bed);
        mysqli_stmt_bind_param($stmt2, 'i', $assignment['bed_id']);
        $success2 = mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        if (!$success2) {
            throw new Exception("Failed to update bed status");
        }

        // Commit transaction
        mysqli_commit($connection);
        mysqli_close($connection);
        return true;

    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($connection);
        mysqli_close($connection);
        error_log("Discharge failed: " . $e->getMessage());
        return false;
    }
}