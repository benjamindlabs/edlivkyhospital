<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page
    header('Location: auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>EdlivkyHospital</title>
    
    <!-- Tailwind CSS -->
    <?php
    // Determine the correct path to assets based on current directory
    $current_dir = dirname($_SERVER['SCRIPT_NAME']);
    $base_path = (basename($current_dir) === 'public') ? './' : '../';
    ?>
    <link href="<?php echo $base_path; ?>assets/css/style.css" rel="stylesheet">
    
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Flowbite -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.2.0/dist/flowbite.min.js"></script>
    
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Custom CSS for Patient List Layout -->
    <style>
        /* Ensure statistics cards display horizontally */
        .stats-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
            gap: 1rem !important;
        }

        /* Desktop table container - allow horizontal scroll when needed */
        .patient-table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }

        /* Add scroll indicator shadow */
        .patient-table-container::-webkit-scrollbar {
            height: 8px;
        }

        .patient-table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .patient-table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .patient-table-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .patient-table {
            width: 100%;
            min-width: 1200px; /* Minimum width to ensure all columns are readable */
            table-layout: fixed;
            border-collapse: collapse;
        }

        /* Column width distribution for better layout */
        .patient-table th:nth-child(1), .patient-table td:nth-child(1) { width: 25%; } /* Patient */
        .patient-table th:nth-child(2), .patient-table td:nth-child(2) { width: 20%; } /* Contact */
        .patient-table th:nth-child(3), .patient-table td:nth-child(3) { width: 12%; } /* Blood Type */
        .patient-table th:nth-child(4), .patient-table td:nth-child(4) { width: 20%; } /* Emergency Contact */
        .patient-table th:nth-child(5), .patient-table td:nth-child(5) { width: 13%; } /* Registered */
        .patient-table th:nth-child(6), .patient-table td:nth-child(6) { width: 10%; } /* Actions */

        /* Responsive adjustments for statistics cards */
        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }

        /* Ensure cards don't stack on larger screens */
        @media (min-width: 640px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }

        /* Override any conflicting Tailwind classes */
        .grid.grid-cols-1.md\\:grid-cols-3 {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
        }

        @media (max-width: 767px) {
            .grid.grid-cols-1.md\\:grid-cols-3 {
                grid-template-columns: 1fr !important;
            }
        }

        /* Responsive table padding */
        .patient-table th,
        .patient-table td {
            padding: 0.75rem 1rem;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .patient-table th:first-child,
        .patient-table td:first-child {
            padding-left: 1.5rem;
        }

        .patient-table th:last-child,
        .patient-table td:last-child {
            padding-right: 1.5rem;
        }

        /* Mobile-responsive patient table */
        @media (max-width: 768px) {
            .patient-table-responsive {
                display: none;
            }

            .patient-cards-mobile {
                display: block;
                padding: 1rem;
            }

            /* Ensure the main container doesn't have horizontal scroll */
            .bg-white.dark\\:bg-gray-800.shadow.rounded-lg {
                overflow: visible !important;
            }

            .patient-card {
                background: white;
                border-radius: 0.5rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
                margin-bottom: 1rem;
                padding: 1rem;
                border: 1px solid #e5e7eb;
            }

            .dark .patient-card {
                background: #1f2937;
                border-color: #374151;
            }

            .patient-card-header {
                display: flex;
                align-items: center;
                margin-bottom: 0.75rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px solid #e5e7eb;
            }

            .dark .patient-card-header {
                border-bottom-color: #374151;
            }

            .patient-card-avatar {
                width: 3rem;
                height: 3rem;
                border-radius: 50%;
                background: #dbeafe;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 0.75rem;
                font-weight: 600;
                color: #2563eb;
            }

            .dark .patient-card-avatar {
                background: #1e3a8a;
                color: #93c5fd;
            }

            .patient-card-info {
                flex: 1;
            }

            .patient-card-name {
                font-weight: 600;
                color: #111827;
                margin-bottom: 0.25rem;
            }

            .dark .patient-card-name {
                color: white;
            }

            .patient-card-id {
                font-size: 0.875rem;
                color: #6b7280;
            }

            .dark .patient-card-id {
                color: #9ca3af;
            }

            .patient-card-details {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.5rem;
                margin-bottom: 1rem;
            }

            .patient-card-detail {
                display: flex;
                align-items: center;
                font-size: 0.875rem;
            }

            .patient-card-detail-label {
                font-weight: 500;
                color: #374151;
                margin-right: 0.5rem;
                min-width: 4rem;
            }

            .dark .patient-card-detail-label {
                color: #d1d5db;
            }

            .patient-card-detail-value {
                color: #111827;
                font-weight: 500;
            }

            .dark .patient-card-detail-value {
                color: white;
            }

            /* Special styling for blood type in mobile cards */
            .patient-card-detail-value.blood-type {
                color: #dc2626;
                font-weight: 600;
                display: flex;
                align-items: center;
            }

            .dark .patient-card-detail-value.blood-type {
                color: #f87171;
            }

            .patient-card-actions {
                display: flex;
                justify-content: flex-end;
                gap: 0.75rem;
                padding-top: 0.75rem;
                border-top: 1px solid #e5e7eb;
            }

            .dark .patient-card-actions {
                border-top-color: #374151;
            }

            .patient-card-action-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 0.375rem;
                font-size: 0.875rem;
                transition: all 0.2s;
                border: none;
                cursor: pointer;
            }

            .patient-card-action-btn.view {
                background: #dbeafe;
                color: #2563eb;
            }

            .patient-card-action-btn.view:hover {
                background: #bfdbfe;
            }

            .patient-card-action-btn.edit {
                background: #dcfce7;
                color: #16a34a;
            }

            .patient-card-action-btn.edit:hover {
                background: #bbf7d0;
            }

            .patient-card-action-btn.delete {
                background: #fee2e2;
                color: #dc2626;
            }

            .patient-card-action-btn.delete:hover {
                background: #fecaca;
            }

            .dark .patient-card-action-btn.view {
                background: #1e3a8a;
                color: #93c5fd;
            }

            .dark .patient-card-action-btn.edit {
                background: #14532d;
                color: #4ade80;
            }

            .dark .patient-card-action-btn.delete {
                background: #7f1d1d;
                color: #f87171;
            }
        }

        @media (min-width: 769px) {
            .patient-table-responsive {
                display: block;
            }

            .patient-cards-mobile {
                display: none;
            }
        }

        /* Enhanced table styling for desktop */
        .patient-table-responsive .patient-table {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-table-responsive .patient-table th {
            background: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            color: #374151;
            padding: 0.75rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .patient-table-responsive .patient-table th {
            background: #374151;
            color: #d1d5db;
            border-bottom-color: #4b5563;
        }

        .patient-table-responsive .patient-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .dark .patient-table-responsive .patient-table td {
            border-bottom-color: #374151;
        }

        .patient-table-responsive .patient-table tr:hover {
            background: #f9fafb;
        }

        .dark .patient-table-responsive .patient-table tr:hover {
            background: #374151;
        }

        /* Enhanced action buttons for desktop */
        .table-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
            margin: 0 0.125rem;
        }

        .table-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Ensure Font Awesome icons display properly */
        .fas, .fa {
            font-family: "Font Awesome 5 Free" !important;
            font-weight: 900 !important;
            display: inline-block !important;
        }

        /* Enhanced blood type styling for desktop table */
        .patient-table .bg-red-50 {
            background-color: #fef2f2 !important;
            border-color: #fecaca !important;
        }

        .dark .patient-table .bg-red-50 {
            background-color: #7f1d1d !important;
            border-color: #991b1b !important;
        }

        /* Ensure blood type icon and text are consistently red */
        .patient-table .fas.fa-tint {
            color: #dc2626 !important;
            margin-right: 0.5rem !important;
            font-size: 0.875rem !important;
            vertical-align: middle !important;
        }

        .dark .patient-table .fas.fa-tint {
            color: #f87171 !important;
        }

        /* Enhanced responsive breakpoints */
        @media (max-width: 640px) {
            .patient-cards-mobile {
                padding: 0.5rem;
            }

            .patient-card {
                margin-bottom: 0.75rem;
                padding: 0.75rem;
            }

            .patient-card-header {
                margin-bottom: 0.5rem;
                padding-bottom: 0.5rem;
            }

            .patient-card-avatar {
                width: 2.5rem;
                height: 2.5rem;
                margin-right: 0.5rem;
            }

            .patient-card-actions {
                gap: 0.5rem;
            }

            .patient-card-action-btn {
                width: 2.25rem;
                height: 2.25rem;
            }
        }

        /* Tablet optimizations */
        @media (min-width: 641px) and (max-width: 768px) {
            .patient-cards-mobile {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                padding: 1rem;
            }

            .patient-card {
                margin-bottom: 0;
            }
        }

        /* Staff Table Responsive Styling - Match Patient Table */
        .staff-table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }

        .staff-table-container::-webkit-scrollbar {
            height: 8px;
        }

        .staff-table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .staff-table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .dark .staff-table-container::-webkit-scrollbar-track {
            background: #374151;
        }

        .dark .staff-table-container::-webkit-scrollbar-thumb {
            background: #6b7280;
        }

        /* Mobile-responsive staff table */
        @media (max-width: 768px) {
            .staff-table-responsive {
                display: none;
            }

            .staff-cards-mobile {
                display: block;
                padding: 1rem;
            }

            .staff-card {
                background: white;
                border-radius: 0.5rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
                margin-bottom: 1rem;
                padding: 1rem;
                border: 1px solid #e5e7eb;
            }

            .dark .staff-card {
                background: #1f2937;
                border-color: #374151;
            }

            .staff-card-header {
                display: flex;
                align-items: center;
                margin-bottom: 0.75rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px solid #e5e7eb;
            }

            .dark .staff-card-header {
                border-bottom-color: #374151;
            }

            .staff-card-avatar {
                width: 3rem;
                height: 3rem;
                border-radius: 50%;
                background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 1rem;
                margin-right: 0.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .staff-card-info {
                flex: 1;
            }

            .staff-card-name {
                font-weight: 600;
                color: #1f2937;
                font-size: 1rem;
                margin-bottom: 0.25rem;
            }

            .dark .staff-card-name {
                color: #f9fafb;
            }

            .staff-card-id {
                font-size: 0.875rem;
                color: #6b7280;
            }

            .dark .staff-card-id {
                color: #9ca3af;
            }

            .staff-card-content {
                margin-bottom: 1rem;
            }

            .staff-card-detail {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.5rem;
                padding: 0.5rem 0;
            }

            .staff-card-label {
                font-weight: 500;
                color: #374151;
                font-size: 0.875rem;
                min-width: 80px;
            }

            .dark .staff-card-label {
                color: #d1d5db;
            }

            .staff-card-value {
                color: #6b7280;
                font-size: 0.875rem;
                text-align: right;
                flex: 1;
                margin-left: 1rem;
            }

            .dark .staff-card-value {
                color: #9ca3af;
            }

            .staff-card-actions {
                display: flex;
                justify-content: flex-end;
                gap: 0.75rem;
                padding-top: 0.75rem;
                border-top: 1px solid #e5e7eb;
            }

            .dark .staff-card-actions {
                border-top-color: #374151;
            }

            .staff-card-action-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 0.375rem;
                font-size: 0.875rem;
                transition: all 0.2s;
                border: none;
                cursor: pointer;
            }

            .staff-card-action-btn.view {
                background: #dbeafe;
                color: #2563eb;
            }

            .staff-card-action-btn.view:hover {
                background: #bfdbfe;
            }

            .staff-card-action-btn.edit {
                background: #dcfce7;
                color: #16a34a;
            }

            .staff-card-action-btn.edit:hover {
                background: #bbf7d0;
            }

            .staff-card-action-btn.delete {
                background: #fee2e2;
                color: #dc2626;
            }

            .staff-card-action-btn.delete:hover {
                background: #fecaca;
            }

            .dark .staff-card-action-btn.view {
                background: #1e3a8a;
                color: #93c5fd;
            }

            .dark .staff-card-action-btn.edit {
                background: #14532d;
                color: #4ade80;
            }

            .dark .staff-card-action-btn.delete {
                background: #7f1d1d;
                color: #f87171;
            }
        }

        @media (min-width: 769px) {
            .staff-table-responsive {
                display: block;
            }

            .staff-cards-mobile {
                display: none;
            }
        }

        /* Enhanced table styling for staff desktop */
        .staff-table-responsive .staff-table {
            width: 100%;
            border-collapse: collapse;
        }

        .staff-table-responsive .staff-table th {
            background: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            color: #374151;
            padding: 0.75rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .staff-table-responsive .staff-table th {
            background: #374151;
            color: #d1d5db;
            border-bottom-color: #4b5563;
        }

        .staff-table-responsive .staff-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .dark .staff-table-responsive .staff-table td {
            border-bottom-color: #374151;
        }

        .staff-table-responsive .staff-table tr:hover {
            background: #f9fafb;
        }

        .dark .staff-table-responsive .staff-table tr:hover {
            background: #374151;
        }

        /* Appointment Table Responsive Styling - Match Patient/Staff Table */
        .appointment-table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }

        .appointment-table-container::-webkit-scrollbar {
            height: 8px;
        }

        .appointment-table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .appointment-table-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .dark .appointment-table-container::-webkit-scrollbar-track {
            background: #374151;
        }

        .dark .appointment-table-container::-webkit-scrollbar-thumb {
            background: #6b7280;
        }

        /* Mobile-responsive appointment table */
        @media (max-width: 768px) {
            .appointment-table-responsive {
                display: none;
            }

            .appointment-cards-mobile {
                display: block;
                padding: 1rem;
            }

            .appointment-card {
                background: white;
                border-radius: 0.5rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
                margin-bottom: 1rem;
                padding: 1rem;
                border: 1px solid #e5e7eb;
            }

            .dark .appointment-card {
                background: #1f2937;
                border-color: #374151;
            }

            .appointment-card-header {
                display: flex;
                align-items: center;
                margin-bottom: 0.75rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px solid #e5e7eb;
            }

            .dark .appointment-card-header {
                border-bottom-color: #374151;
            }

            .appointment-card-avatar {
                width: 3rem;
                height: 3rem;
                border-radius: 50%;
                background: linear-gradient(135deg, #3b82f6, #8b5cf6);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
                font-size: 1rem;
                margin-right: 0.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .appointment-card-info {
                flex: 1;
            }

            .appointment-card-name {
                font-weight: 600;
                color: #1f2937;
                font-size: 1rem;
                margin-bottom: 0.25rem;
            }

            .dark .appointment-card-name {
                color: #f9fafb;
            }

            .appointment-card-id {
                font-size: 0.875rem;
                color: #6b7280;
            }

            .dark .appointment-card-id {
                color: #9ca3af;
            }

            .appointment-card-content {
                margin-bottom: 1rem;
            }

            .appointment-card-detail {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.5rem;
                padding: 0.5rem 0;
            }

            .appointment-card-label {
                font-weight: 500;
                color: #374151;
                font-size: 0.875rem;
                min-width: 80px;
            }

            .dark .appointment-card-label {
                color: #d1d5db;
            }

            .appointment-card-value {
                color: #6b7280;
                font-size: 0.875rem;
                text-align: right;
                flex: 1;
                margin-left: 1rem;
            }

            .dark .appointment-card-value {
                color: #9ca3af;
            }

            .appointment-card-actions {
                display: flex;
                justify-content: flex-end;
                gap: 0.75rem;
                padding-top: 0.75rem;
                border-top: 1px solid #e5e7eb;
            }

            .dark .appointment-card-actions {
                border-top-color: #374151;
            }

            .appointment-card-action-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 0.375rem;
                font-size: 0.875rem;
                transition: all 0.2s;
                border: none;
                cursor: pointer;
                text-decoration: none;
            }

            .appointment-card-action-btn.view {
                background: #dbeafe;
                color: #2563eb;
            }

            .appointment-card-action-btn.view:hover {
                background: #bfdbfe;
            }

            .appointment-card-action-btn.edit {
                background: #dcfce7;
                color: #16a34a;
            }

            .appointment-card-action-btn.edit:hover {
                background: #bbf7d0;
            }

            .appointment-card-action-btn.delete {
                background: #fee2e2;
                color: #dc2626;
            }

            .appointment-card-action-btn.delete:hover {
                background: #fecaca;
            }

            .dark .appointment-card-action-btn.view {
                background: #1e3a8a;
                color: #93c5fd;
            }

            .dark .appointment-card-action-btn.edit {
                background: #14532d;
                color: #4ade80;
            }

            .dark .appointment-card-action-btn.delete {
                background: #7f1d1d;
                color: #f87171;
            }
        }

        @media (min-width: 769px) {
            .appointment-table-responsive {
                display: block;
            }

            .appointment-cards-mobile {
                display: none;
            }
        }

        /* Enhanced table styling for appointment desktop */
        .appointment-table-responsive .appointment-table {
            width: 100%;
            border-collapse: collapse;
        }

        .appointment-table-responsive .appointment-table th {
            background: #f9fafb;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            color: #374151;
            padding: 0.75rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .dark .appointment-table-responsive .appointment-table th {
            background: #374151;
            color: #d1d5db;
            border-bottom-color: #4b5563;
        }

        .appointment-table-responsive .appointment-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .dark .appointment-table-responsive .appointment-table td {
            border-bottom-color: #374151;
        }

        .appointment-table-responsive .appointment-table tr:hover {
            background: #f9fafb;
        }

        .dark .appointment-table-responsive .appointment-table tr:hover {
            background: #374151;
        }

        /* Large tablet and small desktop */
        @media (min-width: 769px) and (max-width: 1024px) {
            .patient-table-responsive .patient-table th,
            .patient-table-responsive .patient-table td {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }
        }

        /* Animation enhancements */
        .patient-card {
            transition: all 0.3s ease;
        }

        .patient-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.15);
        }

        .patient-table-responsive .patient-table tr {
            transition: background-color 0.2s ease;
        }

        /* Loading states and micro-interactions */
        .table-action-btn,
        .patient-card-action-btn {
            transition: all 0.2s ease;
        }

        .table-action-btn:active,
        .patient-card-action-btn:active {
            transform: scale(0.95);
        }
    </style>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-body">
    
    <!-- Top Navigation Bar -->
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <!-- Sidebar Toggle Button -->
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>
                    
                    <!-- Logo -->
                    <a href="<?php echo $base_path; ?>dashboard.php" class="flex ms-2 md:me-24">
                        <i class="fas fa-hospital text-blue-600 text-2xl me-3"></i>
                        <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">EdlivkyHospital</span>
                    </a>
                </div>
                
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 me-3">
                            <i x-show="!darkMode" class="fas fa-moon"></i>
                            <i x-show="darkMode" class="fas fa-sun"></i>
                        </button>
                        
                        <!-- Last Updated Time -->
                        <div class="hidden sm:flex items-center text-sm text-gray-500 dark:text-gray-400 me-4">
                            <i class="fas fa-clock me-2"></i>
                            <span>Last updated: <span id="lastUpdated"><?php echo date('g:i:s A'); ?></span></span>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                            </button>
                            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                                <div class="px-4 py-3">
                                    <p class="text-sm text-gray-900 dark:text-white">Admin User</p>
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300">admin@edlivky.com</p>
                                </div>
                                <ul class="py-1">
                                    <li><a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Settings</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Sign out</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
