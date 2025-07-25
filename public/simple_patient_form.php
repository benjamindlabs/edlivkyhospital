<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Patient Registration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f9; }
        .form-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); max-width: 600px; margin: 0 auto; }
        .form-container h2 { margin-bottom: 20px; text-align: center; color: #333; }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; }
        .form-group { flex: 1; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-group textarea { height: 80px; resize: vertical; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { text-align: left; padding: 10px; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Patient Registration</h2>
        
        <?php
        include 'includes/config.php';

        try {
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $first_name = sanitizeInput($_POST['first_name'] ?? '');
                $last_name = sanitizeInput($_POST['last_name'] ?? '');
                $date_of_birth = $_POST['date_of_birth'] ?? '';
                $gender = $_POST['gender'] ?? '';
                $phone_number = sanitizeInput($_POST['phone_number'] ?? '');
                $email = sanitizeInput($_POST['email'] ?? '');
                $address = sanitizeInput($_POST['address'] ?? '');
                $blood_type = $_POST['blood_type'] ?? '';
                $emergency_contact_name = sanitizeInput($_POST['emergency_contact_name'] ?? '');
                $emergency_contact_phone = sanitizeInput($_POST['emergency_contact_phone'] ?? '');
                
                // Validation
                $errors = [];
                if (empty($first_name)) $errors[] = "First name is required";
                if (empty($last_name)) $errors[] = "Last name is required";
                
                if (empty($errors)) {
                    $sql = "INSERT INTO patients (first_name, last_name, date_of_birth, gender, phone_number, email, address, blood_type, emergency_contact_name, emergency_contact_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $params = [$first_name, $last_name, $date_of_birth, $gender, $phone_number, $email, $address, $blood_type, $emergency_contact_name, $emergency_contact_phone];

                    $insert_id = insertRecord($sql, $params);

                    if ($insert_id) {
                        echo "<div class='success'>✅ Patient registered successfully! Patient ID: $insert_id</div>";
                        // Clear form data
                        $_POST = [];
                    } else {
                        echo "<div class='error'>❌ Error: Failed to register patient</div>";
                    }
                } else {
                    echo "<div class='error'>❌ " . implode('<br>', $errors) . "</div>";
                }
            }
            
            // Show form
            ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo (($_POST['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (($_POST['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo (($_POST['gender'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="blood_type">Blood Type</label>
                        <select id="blood_type" name="blood_type">
                            <option value="">Select Blood Type</option>
                            <option value="A+" <?php echo (($_POST['blood_type'] ?? '') === 'A+') ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo (($_POST['blood_type'] ?? '') === 'A-') ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo (($_POST['blood_type'] ?? '') === 'B+') ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo (($_POST['blood_type'] ?? '') === 'B-') ? 'selected' : ''; ?>>B-</option>
                            <option value="AB+" <?php echo (($_POST['blood_type'] ?? '') === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo (($_POST['blood_type'] ?? '') === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                            <option value="O+" <?php echo (($_POST['blood_type'] ?? '') === 'O+') ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo (($_POST['blood_type'] ?? '') === 'O-') ? 'selected' : ''; ?>>O-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="emergency_contact_name">Emergency Contact Name</label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo htmlspecialchars($_POST['emergency_contact_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="emergency_contact_phone">Emergency Contact Phone</label>
                    <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo htmlspecialchars($_POST['emergency_contact_phone'] ?? ''); ?>">
                </div>
                
                <button type="submit">Register Patient</button>
            </form>
            
            <?php
            // Show recent patients
            $sql_patients = "SELECT * FROM patients ORDER BY registration_date DESC LIMIT 5";
            $result_patients = mysqli_query($db_handle, $sql_patients);
            
            if ($result_patients && mysqli_num_rows($result_patients) > 0) {
                echo '<h3>Recent Patients</h3>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Blood Type</th><th>Registered</th></tr>';
                
                while ($patient = mysqli_fetch_array($result_patients)) {
                    echo '<tr>';
                    echo '<td>' . $patient['patient_id'] . '</td>';
                    echo '<td>' . htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($patient['phone_number'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($patient['email'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($patient['blood_type'] ?? '') . '</td>';
                    echo '<td>' . date('M d, Y', strtotime($patient['registration_date'])) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ Database connection failed: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="patients/list.php" style="color: #007bff; text-decoration: none;">View All Patients</a>
        </div>
    </div>
</body>
</html>
