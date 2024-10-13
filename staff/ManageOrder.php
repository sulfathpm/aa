<?php
// Database connection settings
$host = "localhost";  // Your database host
$dbname = "fashion";  // Your database name
$username = "root";  // Your database username
$password = "";  // Your database password

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted for updating the status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // SQL query to update the order status
    $sql = "UPDATE orders SET STATUSES = ? WHERE ORDER_ID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $status, $order_id);
        if ($stmt->execute()) {
            echo "<p>Order status updated successfully!</p>";
        } else {
            echo "<p>Error updating record: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Fetch orders from the database
$sql = "SELECT orders.ORDER_ID, users.USER_ID, orders.SSIZE, orders.STATUSES, orders.ESTIMATED_DELIVERY_DATE 
        FROM orders
        INNER JOIN users ON orders.USER_ID = users.USER_ID";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="staff1.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        .status-update {
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Manage Orders</h1>
    </div>

    <div class="staff-dashboard">
        <aside class="sidebar">
            <h3>Menu</h3>
            <a href="ManageOrder.php">Manage Orders</a>
            <a href="measurement.php">Measurements</a>
        </aside>

        <main class="main-content">
            <section id="manage-orders-section" class="card">
                <h2>Manage Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer ID</th>
                            <th>Size</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['ORDER_ID'] . "</td>";
                                echo "<td>" . $row['USER_ID'] . "</td>";
                                echo "<td>" . $row['SSIZE'] . "</td>";
                                echo "<td id='status-display-" . $row['ORDER_ID'] . "'>" . ucfirst($row['STATUSES']) . "</td>";
                                echo "<td>" . $row['ESTIMATED_DELIVERY_DATE'] . "</td>";

                                // Form for updating status
                                echo "<td>";
                                echo "<form action='' method='post'>";
                                echo "<input type='hidden' name='order_id' value='" . $row['ORDER_ID'] . "'>";
                                echo "<select name='status' required>
                                        <option value='pending'" . ($row['STATUSES'] == 'pending' ? " selected" : "") . ">Pending</option>
                                        <option value='in-progress'" . ($row['STATUSES'] == 'in-progress' ? " selected" : "") . ">In Progress</option>
                                        <option value='completed'" . ($row['STATUSES'] == 'completed' ? " selected" : "") . ">Completed</option>
                                        <option value='delivered'" . ($row['STATUSES'] == 'delivered' ? " selected" : "") . ">Delivered</option>
                                        <option value='cancelled'" . ($row['STATUSES'] == 'cancelled' ? " selected" : "") . ">Cancelled</option>
                                    </select>";
                                echo "<button type='submit' name='update'>Update</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No orders found</td></tr>";
                        }

                        // Close connection
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
