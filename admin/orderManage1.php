<?php
// Database connection
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "fashion"; // replace with your database name

$conn =  mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for filtering and searching
$filter_status = $_POST['filter-status'] ?? 'all';
$search_order = $_POST['search-order'] ?? '';

// Construct the SQL query based on filters and search criteria
$sql = "
    SELECT 
        o.ORDER_ID, 
        o.USER_ID, 
        d.NAME as DRESS_NAME, 
        f.NAME as FABRIC_NAME, 
        o.TOTAL_PRICE, 
        o.STATUSES, 
        o.SSIZE, 
        o.ESTIMATED_DELIVERY_DATE, 
        o.ACTUAL_DELIVERY_DATE, 
        o.CREATED_AT 
    FROM orders o
    LEFT JOIN dress d ON o.DRESS_ID = d.DRESS_ID
    LEFT JOIN fabrics f ON o.FABRIC_ID = f.FABRIC_ID
    WHERE 1=1
";

if ($filter_status != 'all') {
    $sql .= " AND o.STATUSES = '$filter_status'";
}

if ($search_order != '') {
    $sql .= " AND (o.ORDER_ID LIKE '%$search_order%' OR d.NAME LIKE '%$search_order%')";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="header">
        <h1>Order Management</h1>
    </div>
    <div class="admin-dashboard">
        <aside class="sidebar">
            <h3>Menu</h3>
            <a href="admindshbrd.html">Dashboard</a>
            <a href="customers.html">Customer Management</a>
            <a href="staff.html">Staff Management</a>
            <a href="delivery.html">Delivery Management</a>
            <a href="communications.html">Communication</a>
            <a href="manageDesign.html">Manage Designs</a>
            <a href="orderManage.php">Order Management</a>
            <a href="useracc.html">User Accounts</a>
        </aside>

        <main class="content">
            <h3>Order Management</h3>
            <a href="orderManage.php"><button>Back</button></a>

            <div class="filters">
                <form method="POST" action="orderManage.php">
                    <label for="filter-status">Filter by Status:</label>
                    <select id="filter-status" name="filter-status">
                        <option value="all" <?php if ($filter_status == 'all') echo 'selected'; ?>>All</option>
                        <option value="PENDING" <?php if ($filter_status == 'PENDING') echo 'selected'; ?>>Pending</option>
                        <option value="IN-PROGRESS" <?php if ($filter_status == 'IN-PROGRESS') echo 'selected'; ?>>In Progress</option>
                        <option value="COMPLETED" <?php if ($filter_status == 'COMPLETED') echo 'selected'; ?>>Completed</option>
                        <option value="SHIPPED" <?php if ($filter_status == 'SHIPPED') echo 'selected'; ?>>Shipped</option>
                        <option value="DELIVERED" <?php if ($filter_status == 'DELIVERED') echo 'selected'; ?>>Delivered</option>
                        <option value="CANCELLED" <?php if ($filter_status == 'CANCELLED') echo 'selected'; ?>>Cancelled</option>
                    </select>

                    <label for="search-order">Search Order:</label>
                    <input type="text" id="search-order" name="search-order" placeholder="Enter Order ID or Dress Name" value="<?php echo htmlspecialchars($search_order); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Dress Name</th>
                        <th>Fabric</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Size</th>
                        <th>Estimated Delivery</th>
                        <th>Actual Delivery</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['ORDER_ID']}</td>
                                    <td>{$row['DRESS_NAME']}</td>
                                    <td>{$row['FABRIC_NAME']}</td>
                                    <td>₹{$row['TOTAL_PRICE']}</td>
                                    <td>{$row['STATUSES']}</td>
                                    <td>{$row['SSIZE']}</td>
                                    <td>{$row['ESTIMATED_DELIVERY_DATE']}</td>
                                    <td>{$row['ACTUAL_DELIVERY_DATE']}</td>
                                    <td>{$row['CREATED_AT']}</td>
                                    <td>
                                        <a href='viewOrder.php?order_id={$row['ORDER_ID']}'><button>View</button></a>
                                        <a href='editOrder.php?order_id={$row['ORDER_ID']}'><button>Edit</button></a>
                                        <a href='deleteOrder.php?order_id={$row['ORDER_ID']}' onclick=\"return confirm('Are you sure?')\"><button>Delete</button></a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
