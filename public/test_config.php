<?php
// Test the current config.php database functions
include 'includes/config.php';

echo "<h2>Testing Current Config.php Functions</h2>";

try {
    // Test basic connection
    $connection = getDBConnection();
    echo "✅ Database connection successful<br><br>";
    
    // Test getAllRecords function
    echo "<h3>Testing getAllRecords function:</h3>";
    $patients = getAllRecords("SELECT * FROM patients LIMIT 3");
    
    if (!empty($patients)) {
        echo "✅ getAllRecords working. Found " . count($patients) . " patients:<br>";
        echo "<pre>";
        print_r($patients);
        echo "</pre>";
    } else {
        echo "❌ No patients found or getAllRecords not working<br>";
    }
    
    // Test insertRecord function with a simple test
    echo "<h3>Testing insertRecord function:</h3>";
    $test_query = "INSERT INTO patients (first_name, last_name) VALUES (?, ?)";
    $test_params = ['Test', 'Patient'];
    
    $insert_id = insertRecord($test_query, $test_params);
    
    if ($insert_id) {
        echo "✅ insertRecord working. New patient ID: $insert_id<br>";
        
        // Clean up test record
        executeUpdate("DELETE FROM patients WHERE id = ?", [$insert_id]);
        echo "Test record cleaned up.<br>";
    } else {
        echo "❌ insertRecord not working<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>
