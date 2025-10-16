<?php
session_start();
require_once '../Configurations/Config_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "Unauthorized access";
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM items WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Listings</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }
        img {
            max-width: 100px;
        }
    </style>
</head>
<body>
    <h2>Your Listings</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Price</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['product_type']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>R <?= number_format($row['price'], 2) ?></td>
                <td>
                    <?php if ($row['product_type'] == 'good' && $row['image_path']) { ?>
                        <img src="../<?= $row['image_path'] ?>" alt="Image">
                    <?php } else { echo 'N/A'; } ?>
                </td>
                <td>
                    <a href="Edit_Listing.php?id=<?= $row['item_id'] ?>">Edit</a> |
                    <a href="Delete_Listing.php?id=<?= $row['item_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>