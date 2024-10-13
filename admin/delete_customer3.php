<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'fashion');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get customer ID from URL
$customer_id = $_GET['id'];

// Fetch customer details
$sql = "SELECT * FROM users WHERE USER_ID = $customer_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
} else {
    echo "Customer not found.";
    exit;
}

// Delete customer if confirmed
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delete_sql = "DELETE FROM users WHERE USER_ID = $customer_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "Customer deleted successfully.";
        exit; // Redirect or stop further script execution after deletion
    } else {
        echo "Error deleting customer: " . $conn->error;
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
    <title>Delete Customer</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Delete Customer</h1>
    <p>Are you sure you want to delete this customer?</p>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <td><?php echo $customer['USER_ID']; ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?php echo $customer['USERNAME']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $customer['EMAIL']; ?></td>
        </tr>
    </table>

    <br>
    <form method="POST">
        <button type="submit">Yes, Delete</button>
        <a href="customers.php"><button type="button">No, Go Back</button></a>
        <!-- <br>
        <a href="customers.php"><button>Back to Customer Management</button></a> -->
    </form>
</body>
</html>
