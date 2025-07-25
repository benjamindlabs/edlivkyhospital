<?php
header('Content-Type: application/json');
include '../includes/config.php';

// Get count of available beds
$query = "SELECT COUNT(*) as available_count FROM beds WHERE status = 'available'";
$result = getSingleRecord($query);

echo json_encode([
    'available_count' => $result['available_count'] ?? 0
]);
?>
