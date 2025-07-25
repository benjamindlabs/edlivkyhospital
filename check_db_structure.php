<?php
include 'public/includes/config.php';

echo '<h2>APPOINTMENTS TABLE STRUCTURE:</h2>';
$result = executeQuery('DESCRIBE appointments');
echo '<table border="1"><tr><th>Field</th><th>Type</th><th>Key</th><th>Default</th></tr>';
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr><td>' . $row['Field'] . '</td><td>' . $row['Type'] . '</td><td>' . $row['Key'] . '</td><td>' . $row['Default'] . '</td></tr>';
    }
}
echo '</table>';

echo '<h2>PATIENTS TABLE STRUCTURE:</h2>';
$result = executeQuery('DESCRIBE patients');
echo '<table border="1"><tr><th>Field</th><th>Type</th><th>Key</th><th>Default</th></tr>';
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr><td>' . $row['Field'] . '</td><td>' . $row['Type'] . '</td><td>' . $row['Key'] . '</td><td>' . $row['Default'] . '</td></tr>';
    }
}
echo '</table>';

echo '<h2>STAFF TABLE STRUCTURE:</h2>';
$result = executeQuery('DESCRIBE staff');
echo '<table border="1"><tr><th>Field</th><th>Type</th><th>Key</th><th>Default</th></tr>';
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr><td>' . $row['Field'] . '</td><td>' . $row['Type'] . '</td><td>' . $row['Key'] . '</td><td>' . $row['Default'] . '</td></tr>';
    }
}
echo '</table>';

echo '<h2>SAMPLE APPOINTMENT DATA:</h2>';
$appointments = getAllRecords('SELECT * FROM appointments LIMIT 3');
echo '<table border="1">';
if (!empty($appointments)) {
    // Show headers
    echo '<tr>';
    foreach (array_keys($appointments[0]) as $key) {
        echo '<th>' . $key . '</th>';
    }
    echo '</tr>';

    // Show data
    foreach ($appointments as $row) {
        echo '<tr>';
        foreach ($row as $value) {
            echo '<td>' . $value . '</td>';
        }
        echo '</tr>';
    }
}
echo '</table>';
?>
