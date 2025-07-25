<?php
// Test script for bed assignment system
include '../includes/config.php';

echo "<h1>Bed Assignment System Test</h1>";

// Test 1: Check if beds table exists and has data
echo "<h2>Test 1: Beds Table</h2>";
try {
    $beds = getAllBeds();
    echo "✅ Beds table accessible. Found " . count($beds) . " beds.<br>";
    if (!empty($beds)) {
        echo "Sample bed: " . htmlspecialchars($beds[0]['bed_number']) . " in " . htmlspecialchars($beds[0]['ward']) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error accessing beds table: " . $e->getMessage() . "<br>";
}

// Test 2: Check available beds
echo "<h2>Test 2: Available Beds</h2>";
try {
    $available_beds = getAvailableBeds();
    echo "✅ Available beds function works. Found " . count($available_beds) . " available beds.<br>";
} catch (Exception $e) {
    echo "❌ Error getting available beds: " . $e->getMessage() . "<br>";
}

// Test 3: Check patients table
echo "<h2>Test 3: Patients Table</h2>";
try {
    $patients = getAllPatients();
    echo "✅ Patients table accessible. Found " . count($patients) . " patients.<br>";
    if (!empty($patients)) {
        echo "Sample patient: " . htmlspecialchars($patients[0]['full_name']) . " (ID: " . $patients[0]['patient_id'] . ")<br>";
    }
} catch (Exception $e) {
    echo "❌ Error accessing patients table: " . $e->getMessage() . "<br>";
}

// Test 4: Check staff table
echo "<h2>Test 4: Staff Table</h2>";
try {
    $staff = getAllStaff();
    echo "✅ Staff table accessible. Found " . count($staff) . " active staff members.<br>";
    if (!empty($staff)) {
        echo "Sample staff: " . htmlspecialchars($staff[0]['full_name']) . " (ID: " . $staff[0]['staff_id'] . ")<br>";
    }
} catch (Exception $e) {
    echo "❌ Error accessing staff table: " . $e->getMessage() . "<br>";
}

// Test 5: Check bed assignments table
echo "<h2>Test 5: Bed Assignments Table</h2>";
try {
    $query = "SELECT COUNT(*) as total FROM bed_assignments";
    $result = getSingleRecord($query);
    echo "✅ Bed assignments table accessible. Found " . $result['total'] . " assignments.<br>";
} catch (Exception $e) {
    echo "❌ Error accessing bed assignments table: " . $e->getMessage() . "<br>";
}

// Test 6: Test date formatting functions
echo "<h2>Test 6: Date Formatting Functions</h2>";
try {
    $test_date = "2024-01-15 14:30:00";
    $formatted = formatDateTimeForDisplay($test_date);
    echo "✅ Date formatting works. Test date: " . $formatted . "<br>";
} catch (Exception $e) {
    echo "❌ Error with date formatting: " . $e->getMessage() . "<br>";
}

echo "<h2>Test Complete</h2>";
echo "<p><a href='add.php'>Go to Bed Assignment Form</a> | <a href='list.php'>Go to Bed Assignment List</a></p>";
?>
