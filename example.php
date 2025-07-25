
example.php


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <?php 
    $username = 'root';
    $password = '';
    $database = 'address book';
    $server = '127.0.0.1';
    $db_handle = mysqli_connect($server, $username, $password);  
    $db_found = mysqli_select_db($db_handle, $database);        
       if ($db_found) {
            echo "Database Found<br>";
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $address = $_POST['address'];
            $sql = "INSERT INTO tbl_address_book (`First Name`, `Last Name`, `Address`) VALUES ('$first_name', '$last_name', '$address')";
            $result = mysqli_query($db_handle, $sql);

            echo "New record Added Successfullyy<br>";

            $sql_new = "SELECT * FROM tbl_address_book";
            $resultnew = mysqli_query($db_handle, $sql_new);

            // Start the table
            echo '<table>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>First Name</th>';
            echo '<th>Last Name</th>';
            echo '<th>Address</th>';
            echo '</tr>';

            while ($db_fields = mysqli_fetch_array($resultnew)) {
                echo '<tr>';
                echo '<td>' . $db_fields['ID'] . '</td>';
                echo '<td>' . $db_fields['First Name'] . '</td>';
                echo '<td>' . $db_fields['Last Name'] . '</td>';
                echo '<td>' . $db_fields['Address'] . '</td>';
                echo '</tr>';
            }

            // End the table
            echo '</table>';
        } else {
            echo "Database NOT Found<br>";
        }   
        
        mysqli_close($db_handle);
    ?>
</body>
</html>