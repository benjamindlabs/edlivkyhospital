<?php
// Debug patient insertion with exact field matching
include 'includes/config.php';

echo "<h2>Debug Patient Insertion</h2>";

// First, let's see the exact table structure
$connection = getDBConnection();

echo "<h3>1. Table Structure:</h3>";
$structure_query = "DESCRIBE patients";
$structure_result = mysqli_query($connection, $structure_query);

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

$fields = [];
while ($row = mysqli_fetch_array($structure_result)) {
    $fields[] = $row['Field'];
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

echo "<h3>2. Available Fields:</h3>";
echo "<p>" . implode(', ', $fields) . "</p>";

// Test insertion with minimal data
echo "<h3>3. Test Insertion:</h3>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    
    // Try simple insertion first
    $query = "INSERT INTO patients (first_name, last_name) VALUES (?, ?)";
    $params = [$first_name, $last_name];
    
    echo "<p><strong>Query:</strong> $query</p>";
    echo "<p><strong>Params:</strong> " . print_r($params, true) . "</p>";
    
    $patient_id = insertRecord($query, $params);
    
    if ($patient_id) {
        echo "<p>✅ Success! Patient ID: $patient_id</p>";
    } else {
        echo "<p>❌ Failed to insert</p>";
        
        // Try direct mysqli approach
        echo "<h4>Trying direct mysqli approach:</h4>";
        $direct_query = "INSERT INTO patients (first_name, last_name) VALUES ('$first_name', '$last_name')";
        echo "<p><strong>Direct Query:</strong> $direct_query</p>";
        
        $direct_result = mysqli_query($connection, $direct_query);
        if ($direct_result) {
            $direct_id = mysqli_insert_id($connection);
            echo "<p>✅ Direct approach worked! Patient ID: $direct_id</p>";
        } else {
            echo "<p>❌ Direct approach failed: " . mysqli_error($connection) . "</p>";
        }
    }
}

echo '<form method="POST">';
echo '<input type="text" name="first_name" placeholder="First Name" required>';
echo '<input type="text" name="last_name" placeholder="Last Name" required>';
echo '<button type="submit">Test Insert</button>';
echo '</form>';

// Show existing data
echo "<h3>4. Existing Data:</h3>";
$data_query = "SELECT * FROM patients ORDER BY " . (in_array('id', $fields) ? 'id' : (in_array('patient_id', $fields) ? 'patient_id' : $fields[0])) . " DESC LIMIT 5";
$data_result = mysqli_query($connection, $data_query);

if ($data_result && mysqli_num_rows($data_result) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    
    // Header
    echo "<tr>";
    foreach ($fields as $field) {
        echo "<th>$field</th>";
    }
    echo "</tr>";
    
    // Data
    while ($row = mysqli_fetch_array($data_result)) {
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<td>" . ($row[$field] ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data found or query failed: " . mysqli_error($connection) . "</p>";
}

mysqli_close($connection);
?>
