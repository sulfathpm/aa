<?php
$conn = new mysqli("localhost", "root", "", "fashion");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$message = "";
$order_id = $_GET['id'];

// Fetch the order details before deletion using prepared statement
$stmt = $conn->prepare("SELECT * FROM orders WHERE ORDER_ID=?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === FALSE) {
    die("SQL error: " . $stmt->error); // Show any SQL errors
}

$row = $result->fetch_assoc(); // Fetch the order details

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Delete query
    $sql_delete = "DELETE FROM orders WHERE ORDER_ID=?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("s", $order_id);
    if ($stmt_delete->execute()) {
        $message = "Order deleted successfully!";
    } else {
        $message = "Error deleting record: " . $stmt_delete->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Order</title>
    <link rel="stylesheet" href="admin.css">
    <script>
        function confirmDeletion(hasOrders) {
            if (hasOrders) {
                return confirm("This Order has existing orders. Are you sure you want to delete the account?");
            }
            return true; // No orders, proceed with deletion
        }
    </script>
</head>
<body>
    <h1>Delete Order</h1>
    <p>Are you sure you want to delete this Order?</p>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Order ID</th>
            <td><?php echo isset($row['ORDER_ID']) ? $row['ORDER_ID'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <th>User ID</th>
            <td><?php echo isset($row['USER_ID']) ? $row['USER_ID'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <th>Dress ID</th>
            <td><?php echo isset($row['DRESS_ID']) ? $row['DRESS_ID'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <th>Dress Name</th>
            <td><?php echo isset($row['NAME']) ? $row['NAME'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <th>Dress Size</th>
            <td><?php echo isset($row['SSIZE']) ? $row['SSIZE'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo isset($row['STATUSES']) ? $row['STATUSES'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <th>Total Price</th>
            <td><?php echo isset($row['TOTAL_PRICE']) ? $row['TOTAL_PRICE'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <th>Ordered Date</th>
            <td><?php echo isset($row['CREATED_AT']) ? $row['CREATED_AT'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <th>Estimated Order Date</th>
            <td><?php echo isset($row['ESTIMATED_DELIVERY_DATE']) ? $row['ESTIMATED_DELIVERY_DATE'] : 'N/A'; ?></td>
        </tr>
        <tr>
            <th>Actual Order Date</th>
            <td><?php echo isset($row['ACTUAL_DELIVERY_DATE']) ? $row['ACTUAL_DELIVERY_DATE'] : 'N/A'; ?></td>
        </tr>
    </table>
    <br>
    <form method="POST" onsubmit="return confirmDeletion(false);">
        <button type="submit">Yes, Delete</button>
        <a href="OrderManage.php"><button type="button">No, Go Back</button></a>
    </form>

    <!-- Display message here after form submission -->
    <?php if (!empty($message)): ?>
        <p style="color: <?php echo ($message == 'Order deleted successfully!') ? 'green' : 'red'; ?>;">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

</body>
</html>
