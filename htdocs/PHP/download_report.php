<?php
require_once 'config_db.php';

if (isset($_GET['report_id'])) {
    $report_id = intval($_GET['report_id']);
    $stmt = $conn->prepare("SELECT report_file FROM report WHERE report_id = ?");
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($report_file);
        $stmt->fetch();
        
        // Output headers for file download
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=report_$report_id.pdf"); // assuming pdf, adjust if needed
        header("Content-Length: " . strlen($report_file));
        echo $report_file;
        exit();
    } else {
        echo "Report not found.";
    }
} else {
    echo "No report specified.";
}
?>
