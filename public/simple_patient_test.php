<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Patient Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 10px; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
        .form-container { background: #f9f9f9; padding: 20px; margin-bottom: 20px; }
        input, select { padding: 8px; margin: 5px; width: 200px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Simple Patient Registration Test</h1>
    
    <?php 
    $username = 'root';
    $password = '';
    $database = 'edlivkyhospital';
    $server = '127.0.0.1';
    
    $db_handle = mysqli_connect($server, $username, $password);  
    $db_found = mysqli_select_db($db_handle, $database);        
    
    if ($db_found) {
        echo "<p>✅ Database Connected</p>";
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = mysqli_real_escape_string($db_handle, $_POST['first_name']);
            $last_name = mysqli_real_escape_string($db_handle, $_POST['last_name']);
            $phone = mysqli_real_escape_string($db_handle, $_POST['phone']);
            $email = mysqli_real_escape_string($db_handle, $_POST['email']);
            $blood_type = mysqli_real_escape_string($db_handle, $_POST['blood_type']);
            
            // Try to insert using the exact field names from the database
            $sql = "INSERT INTO patients (first_name, last_name, phone, email, blood_type) VALUES ('$first_name', '$last_name', '$phone', '$email', '$blood_type')";
            
            echo "<p><strong>SQL Query:</strong> $sql</p>";
            
            $result = mysqli_query($db_handle, $sql);
            
            if ($result) {
                $insert_id = mysqli_insert_id($db_handle);
                echo "<p>✅ Patient registered successfully! ID: $insert_id</p>";
            } else {
                echo "<p>❌ Error: " . mysqli_error($db_handle) . "</p>";
            }
        }
        
        // Show form
        echo '<div class="form-container">';
        echo '<h3>Register New Patient</h3>';
        echo '<form method="POST">';
        echo '<input type="text" name="first_name" placeholder="First Name" required><br>';
        echo '<input type="text" name="last_name" placeholder="Last Name" required><br>';
        echo '<input type="text" name="phone" placeholder="Phone"><br>';
        echo '<input type="email" name="email" placeholder="Email"><br>';
        echo '<select name="blood_type">';
        echo '<option value="">Select Blood Type</option>';
        echo '<option value="A+">A+</option>';
        echo '<option value="A-">A-</option>';
        echo '<option value="B+">B+</option>';
        echo '<option value="B-">B-</option>';
        echo '<option value="AB+">AB+</option>';
        echo '<option value="AB-">AB-</option>';
        echo '<option value="O+">O+</option>';
        echo '<option value="O-">O-</option>';
        echo '</select><br>';
        echo '<button type="submit">Register Patient</button>';
        echo '</form>';
        echo '</div>';
        
        // Show existing patients
        $sql_new = "SELECT * FROM patients ORDER BY id DESC LIMIT 10";
        $resultnew = mysqli_query($db_handle, $sql_new);
        
        if ($resultnew && mysqli_num_rows($resultnew) > 0) {
            echo '<h3>Recent Patients</h3>';
            echo '<table>';
            echo '<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Phone</th><th>Email</th><th>Blood Type</th><th>Created</th></tr>';
            
            while ($db_fields = mysqli_fetch_array($resultnew)) {
                echo '<tr>';
                echo '<td>' . ($db_fields['id'] ?? $db_fields['patient_id'] ?? 'N/A') . '</td>';
                echo '<td>' . ($db_fields['first_name'] ?? 'N/A') . '</td>';
                echo '<td>' . ($db_fields['last_name'] ?? 'N/A') . '</td>';
                echo '<td>' . ($db_fields['phone'] ?? $db_fields['phone_number'] ?? 'N/A') . '</td>';
                echo '<td>' . ($db_fields['email'] ?? 'N/A') . '</td>';
                echo '<td>' . ($db_fields['blood_type'] ?? 'N/A') . '</td>';
                echo '<td>' . ($db_fields['created_at'] ?? $db_fields['registration_date'] ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No patients found.</p>';
        }
        
    } else {
        echo "<p>❌ Database NOT Found</p>";
        echo "<p>Error: " . mysqli_connect_error() . "</p>";
    }   
    
    mysqli_close($db_handle);
    ?>
</body>
</html>
