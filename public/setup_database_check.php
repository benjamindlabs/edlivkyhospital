<?php
// Setup and check database using main config
include 'includes/config.php';

echo "<h2>Database Setup and Check</h2>";

try {
    // Test connection using main config
    $connection = getDBConnection();
    echo "✅ Connected to database successfully<br><br>";

    // Database already connected and selected via main config
    echo "✅ Database 'edlivkyhospital' is accessible<br>";

    // Check if patients table exists
    $table_check = executeQuery("SHOW TABLES LIKE 'patients'");
    if ($table_check && mysqli_num_rows($table_check) == 0) {
        echo "❌ Patients table does not exist. Creating it...<br>";

        // Create patients table based on the structure from the images
        $create_table_sql = "
        CREATE TABLE patients (
            patient_id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            date_of_birth DATE,
            gender ENUM('Male', 'Female', 'Other'),
            address TEXT,
            phone_number VARCHAR(20),
            email VARCHAR(100),
            blood_type ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'),
            emergency_contact_name VARCHAR(100),
            emergency_contact_phone VARCHAR(20),
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        if (executeQuery($create_table_sql)) {
            echo "✅ Patients table created successfully<br>";
        } else {
            echo "❌ Error creating patients table<br>";
        }
    } else {
        echo "✅ Patients table exists<br>";
    }

// Show table structure
echo "<h3>Current Patients Table Structure:</h3>";
$structure_result = mysqli_query($connection, "DESCRIBE patients");

if ($structure_result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = mysqli_fetch_array($structure_result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
} else {
    echo "❌ Could not describe patients table: " . mysqli_error($connection) . "<br>";
}

// Test insertion
echo "<h3>Test Patient Insertion:</h3>";
$test_insert = "INSERT INTO patients (first_name, last_name, phone_number, email, blood_type) VALUES ('Test', 'Patient', '1234567890', 'test@example.com', 'O+')";

if (mysqli_query($connection, $test_insert)) {
    $insert_id = mysqli_insert_id($connection);
    echo "✅ Test patient inserted successfully with ID: $insert_id<br>";
    
    // Clean up test record
    mysqli_query($connection, "DELETE FROM patients WHERE patient_id = $insert_id");
    echo "Test record cleaned up.<br>";
} else {
    echo "❌ Test insertion failed: " . mysqli_error($connection) . "<br>";
}

// Show existing data
echo "<h3>Existing Patient Data:</h3>";
$data_result = mysqli_query($connection, "SELECT * FROM patients ORDER BY patient_id DESC LIMIT 5");

if ($data_result && mysqli_num_rows($data_result) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    
    // Get field names
    $fields = mysqli_fetch_fields($data_result);
    echo "<tr>";
    foreach ($fields as $field) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";
    
    // Show data
    while ($row = mysqli_fetch_array($data_result)) {
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<td>" . ($row[$field->name] ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No patient data found.<br>";
}

mysqli_close($connection);
?>
