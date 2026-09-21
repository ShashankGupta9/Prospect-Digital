<?php
/**
 * Prospect Digital — CSV Data Export
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$all_enquiries = admin_get_all_enquiries();

// Filter parameters (matches enquiries.php)
$filters = [
    'q'         => $_GET['q'] ?? '',
    'status'    => $_GET['status'] ?? '',
    'service'   => $_GET['service'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to'   => $_GET['date_to'] ?? '',
];

$data = admin_filter_enquiries($all_enquiries, $filters);

$filename = 'prospect-digital-enquiries-' . date('Y-m-d-His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Output UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// CSV Header row
fputcsv($output, [
    'Reference ID',
    'Received At',
    'Name',
    'Email',
    'Phone',
    'Company',
    'Service',
    'Budget',
    'Message',
    'Status',
    'Priority',
    'Internal Notes',
    'Source Page',
    'IP Hash',
]);

foreach ($data as $row) {
    fputcsv($output, [
        $row['reference'] ?? '',
        $row['received_at'] ?? '',
        $row['name'] ?? '',
        $row['email'] ?? '',
        $row['phone'] ?? '',
        $row['company'] ?? '',
        $row['service'] ?? '',
        $row['budget'] ?? '',
        $row['message'] ?? '',
        $row['status'] ?? 'New',
        $row['priority'] ?? 'Normal',
        $row['notes'] ?? '',
        $row['source_page'] ?? '',
        $row['ip_hash'] ?? '',
    ]);
}

fclose($output);
exit;
