<?php
include '../includes/config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid appointment ID");
}

// Use the main config's executeUpdate function instead of PDO directly
$delete_query = "DELETE FROM appointments WHERE id = ?";
$deleted = executeUpdate($delete_query, [$id]);

if ($deleted) {
    header("Location: appointments_list.php?deleted=1");
    exit;
} else {
    die("Appointment not found or already deleted");
}
?>