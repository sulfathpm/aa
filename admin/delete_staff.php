<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'fashion');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$message = "";

// Get Staff ID from URL
$Staff = $_GET['id'];

// Fetch Staff details
$sql = "SELECT * FROM users WHERE USER_ID = $Staff";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $staffDetails = $result->fetch_assoc(); // Use $staffDetails to store the staff details
} else {
    $message = "Staff not found.";
    exit;
}

// Delete Staff if confirmed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delete_sql = "DELETE FROM users WHERE USER_ID = $Staff"; // Use $Staff (the ID) in the query
    if ($conn->query($delete_sql) === TRUE) {
        $message = "Staff deleted successfully.";
    } else {
        $message = "Error deleting Staff: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Staff</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Delete Staff</h1>
    <p>Are you sure you want to delete this Staff?</p>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <td><?php echo $staffDetails['USER_ID']; ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?php echo $staffDetails['USERNAME']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $staffDetails['EMAIL']; ?></td>
        </tr>
    </table>

    <br>
    <form method="POST">
        <button type="submit">Yes, Delete</button>
        <a href="staff.php"><button type="button">No, Go Back</button></a>
        <!-- <br> -->
        <!-- <a href="staff.php"><button>Back to Staff Management</button></a> -->
    </form>

    <!-- Display message here after form submission -->
    <?php if (!empty($message)): ?>
        <p style="color: <?php echo ($message == 'Staff deleted successfully.') ? 'green' : 'red'; ?>;">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

</body>
</html>
