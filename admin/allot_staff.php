<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'fashion');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order ID from the URL
$order_id = $_GET['id'];

// Fetch order details
$sql = "SELECT * FROM orders WHERE ORDER_ID = $order_id";
$order_result = $conn->query($sql);
$order = $order_result->fetch_assoc();

// Fetch staff members (assuming you have a staff table)
$staff_sql = "SELECT * FROM users where USER_TYPE='STAFF'"; // Adjust this query based on your staff table structure
$staff_result = $conn->query($staff_sql);

// Handle form submission for allotting staff
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff-id'];

    // Update the order with the staff ID
    $update_sql = "UPDATE orders SET USER_ID = $staff_id WHERE ORDER_ID = $order_id"; // Assuming STAFF_ID column exists in orders table

    if ($conn->query($update_sql) === TRUE) {
        echo "Staff allotted successfully.";
    } else {
        echo "Error allotting staff: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allot Staff</title>
    <link rel="stylesheet" href="admin1.css">
</head>
<body>
    <h1>Allot Staff to Order #<?php echo $order['ORDER_ID']; ?></h1>
    <form action="" method="post">
        <label for="staff-id">Select Staff Member:</label>
        <select id="staff-id" name="staff-id" required>
            <option value="">--Select Staff--</option>
            <?php while ($staff = $staff_result->fetch_assoc()): ?>
                <option value="<?php echo $staff['USER_ID']; ?>"><?php echo $staff['NAME']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Allot Staff</button>
    </form>
    <a href="OrderManage.php"><button>Back to Orders</button></a>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
