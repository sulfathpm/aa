<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "fashion");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handling form submission to add new comments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
    $user_id = $_POST["user_id"];
    $order_id = $_POST["order_id"];
    $comments = $_POST["comments"];
    $sender_type = $_POST["sender_type"];

    // Insert new comment into database
    $sql = "INSERT INTO comments (USER_ID, ORDER_ID, COMMENTS, SENDER_TYPE, READ1, CREATED_AT)
            VALUES ('$user_id', '$order_id', '$comments', '$sender_type', 0, NOW())";

    if (mysqli_query($conn, $sql)) {
        echo "New comment added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Handling the mark as read request
if (isset($_GET['mark_as_read'])) {
    $comment_id = $_GET['mark_as_read'];

    // Mark the comment as read
    $sql = "UPDATE comments SET READ1 = 1 WHERE COMMENT_ID = $comment_id";
    if (mysqli_query($conn, $sql)) {
        echo "Comment marked as read.";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// Filtering by sender type
$filter = isset($_GET['sender_type']) ? $_GET['sender_type'] : 'ALL';

if ($filter == 'ALL') {
    $sql = "SELECT COMMENT_ID, USER_ID, ORDER_ID, COMMENTS, SENDER_TYPE, READ1, CREATED_AT 
            FROM comments 
            ORDER BY CREATED_AT DESC";
} else {
    $sql = "SELECT COMMENT_ID, USER_ID, ORDER_ID, COMMENTS, SENDER_TYPE, READ1, CREATED_AT 
            FROM comments 
            WHERE SENDER_TYPE = '$filter' 
            ORDER BY CREATED_AT DESC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Communications</title>
    <link rel="stylesheet" href="admin.css">
    <script>
    function fetchIDs() {
        const userName = document.getElementById('user_name').value;
        const dressName = document.getElementById('dress_name').value;

        if (userName && dressName) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `fetch_ids.php?user_name=${userName}&dress_name=${dressName}`, true);
            xhr.onload = function () {
                if (this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    if (response.success) {
                        document.getElementById('user_id').value = response.user_id;
                        document.getElementById('order_id').value = response.order_id;
                    } else {
                        alert('User or Dress not found.');
                    }
                }
            };
            xhr.send();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('user_name').addEventListener('blur', fetchIDs);
        document.getElementById('dress_name').addEventListener('blur', fetchIDs);
    });
    </script>
</head>
<body>
    <div class="header">
        <h1>Recent Communications</h1>
    </div>
    <div class="admin-dashboard">
        <aside class="sidebar">
            <h3>Menu</h3>
            <a href="admindshbrd.html">Dashboard</a>
            <a href="customers.html">Customer Management</a>
            <a href="staff.html">Staff Management</a>
            <a href="delivery.html">Delivery Management</a>
            <a href="communications.php">Communication</a>
            <a href="manageDesign.html">Manage Designs</a>
            <a href="orderManage.html">Order Management</a>
            <a href="useracc.html">User Accounts</a>
        </aside>

        <main class="content">
            <!-- Filter comments by sender type -->
            <form action="communications.php" method="GET">
                <label for="sender_type">Filter by Sender:</label>
                <select name="sender_type" id="sender_type" onchange="this.form.submit()">
                    <option value="ALL" <?php if($filter == 'ALL') echo 'selected'; ?>>All</option>
                    <option value="ADMIN" <?php if($filter == 'ADMIN') echo 'selected'; ?>>Admin</option>
                    <option value="CUSTOMER" <?php if($filter == 'CUSTOMER') echo 'selected'; ?>>Customer</option>
                    <option value="STAFF" <?php if($filter == 'STAFF') echo 'selected'; ?>>Staff</option>
                </select>
            </form>

            <!-- Display communication logs -->
            <h3>Recent Communications</h3>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<p><strong>" . ucfirst(strtolower($row["SENDER_TYPE"])) . "</strong>: " 
                         . $row["COMMENTS"] . " for Order #" . $row["ORDER_ID"] 
                         . " - Date: " . $row["CREATED_AT"] 
                         . ($row['READ1'] == 0 ? " <a href='?mark_as_read=" . $row['COMMENT_ID'] . "'>Mark as Read</a>" : " (Read)")
                         . "</p>";
                }
            } else {
                echo "<p>No communications found.</p>";
            }
            ?>

            <!-- Add new comment form -->
            <h3>Add New Comment</h3>
            <form action="communications.php" method="POST">
                <label for="user_name">User Name:</label>
                <input type="text" name="user_name" id="user_name" required>

                <label for="dress_name">Dress Name:</label>
                <input type="text" name="dress_name" id="dress_name" required>

                <input type="hidden" name="user_id" id="user_id">
                <input type="hidden" name="order_id" id="order_id">

                <label for="comments">Comment:</label>
                <textarea name="comments" id="comments" required></textarea>
                
                <label for="sender_type">Sender Type:</label>
                <select name="sender_type" id="sender_type" required>
                    <option value="ADMIN">Admin</option>
                    <option value="CUSTOMER">Customer</option>
                    <option value="STAFF">Staff</option>
                </select>

                <button type="submit" name="add_comment">Submit Comment</button>
            </form>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>
