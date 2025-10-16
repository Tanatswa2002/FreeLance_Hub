<?php
session_start();
require_once 'config_db.php';

// Handle report upload
if (isset($_POST['upload_report'])) {
    $admin_id = $_POST['admin_id'];

    if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['report_file']['tmp_name'];
        $file_content = file_get_contents($file_tmp);

        $stmt = $conn->prepare("INSERT INTO report (report_file, admin_id, report_date) VALUES (?, ?, NOW())");
        $null = NULL; // For blob binding
        $stmt->bind_param("bi", $null, $admin_id);
        $stmt->send_long_data(0, $file_content);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['msg'] = "Report uploaded successfully!";
        } else {
            $_SESSION['msg'] = "Failed to upload report.";
        }

        $stmt->close();
    } else {
        $_SESSION['msg'] = "Error uploading file.";
    }

    header("Location: Generate_Reports.php");
    exit();
}

// Fetch all reports
$result = $conn->query("SELECT report_id, report_date FROM report ORDER BY report_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reports</title>
</head>
<body>
    <h2>Admin Reports</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <p><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></p>
    <?php endif; ?>

    <h3>Add New Report</h3>
    <form action="Generate_Reports.php" method="post" enctype="multipart/form-data">
        <input type="file" name="report_file" required />
        <input type="hidden" name="admin_id" value="<?= $_SESSION['admin_id'] ?? 1; ?>" />
        <button type="submit" name="upload_report">Upload Report</button>
    </form>

    <h3>Available Reports</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>Report ID</th>
            <th>Date</th>
            <th>Download</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['report_id']) ?></td>
            <td><?= htmlspecialchars($row['report_date']) ?></td>
            <td>
                <a href="download_report.php?report_id=<?= $row['report_id'] ?>">Download</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>