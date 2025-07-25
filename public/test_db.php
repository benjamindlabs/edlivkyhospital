<?php
// Test database connection and table structure using main config
include 'includes/config.php';

echo "<h2>Testing Database Connection</h2>";

try {
    $db_handle = getDBConnection();
    echo "✅ Database connection successful<br><br>";

    // Check if patients table exists using the main config functions
    $table_check_result = executeQuery("SHOW TABLES LIKE 'patients'");

    if ($table_check_result && mysqli_num_rows($table_check_result) > 0) {
        echo "✅ Patients table exists<br><br>";

        // Show table structure
        echo "<h3>Patients Table Structure:</h3>";
        $structure_result = executeQuery("DESCRIBE patients");
        
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
        
        // Show existing data
        echo "<h3>Existing Patient Data:</h3>";
        $data_result = executeQuery("SELECT * FROM patients LIMIT 5");

        if ($data_result && mysqli_num_rows($data_result) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            
            // Get column names
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
            echo "No patient data found.";
        }
        
    } else {
        echo "❌ Patients table does not exist<br>";
    }

} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}
?>
