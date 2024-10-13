<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "fashion");
error_reporting(0);

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

// Handling feedback retrieval
$feedback = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['show_feedback'])) {
    // Correct table name
    $feedback_sql = "SELECT * FROM contact_messages ORDER BY created_at DESC"; 
    $feedback_result = mysqli_query($conn, $feedback_sql);
    
    if ($feedback_result) {
        while ($row = mysqli_fetch_assoc($feedback_result)) {
            $feedback[] = $row;
        }
    } else {
        // Output any errors for debugging
        echo "Error retrieving feedback: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Communications</title>
    <link rel="stylesheet" href="admin1.css">
    <style>
        .message-tile, .feedback-tile {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 10px auto;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 80%;
            background-color: #f9f9f9;
        }

        .message-tile p, .feedback-tile p {
            margin: 10px 0;
        }

        .actions {
            margin-top: 15px;
        }

        .actions > a, .show-feedback {
            padding: 8px 16px;
            border: none;
            text-decoration: none;
            background-color: #3498db;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .actions > a:hover, .show-feedback:hover {
            background-color: #2980b9;
        }
        
    </style>
</head>
<body>
    <div class="header">
        <h1>Recent Communications</h1>
    </div>
    <div class="admin-dashboard">
        <aside class="sidebar">
            <h3>Menu</h3>
            <a href="customers.php">Customer Management</a>
            <a href="staff.php">Staff Management</a>
            <a href="communications.php">Communication</a>
            <a href="manageDesign.php">Manage Designs</a>
            <a href="manageFabric.php">Manage Fabric</a>
            <a href="OrderManage.php">Order Management</a>
        </aside>

        <main class="content">
            <form action="" method="post">
                <!-- Existing message tiles -->
                <?php
                // Display comments
                $sql = "SELECT * FROM comments WHERE READ1='SEND'";
                $data = mysqli_query($conn, $sql);
                if ($data) {
                    $count = mysqli_num_rows($data);
                    for ($i = 0; $i < $count; $i++) {
                        $message = mysqli_fetch_array($data);
                        echo "<div class='message-tile'>
                                    <input type='number' name='order_id' value='" . $message['ORDER_ID'] . "' hidden>
                                    <input type='number' name='comment_id' value='" . $message['COMMENT_ID'] . "' hidden>
                                    <p><strong>Order:</strong> #" . $message['ORDER_ID'] . "</p>
                                    <p><strong>Message:</strong> " . htmlspecialchars($message['COMMENTS']) . "</p>
                                    
                                    <div class='actions'>
                                        <a href='acceptReject.php?key=accept&id=" . $message['COMMENT_ID'] . "'>Accept</a>
                                        <a href='acceptReject.php?key=reject&id=" . $message['COMMENT_ID'] . "'>Reject</a>
                                    </div>
                                </div>";
                    }
                }
                ?>
                <div class="actions">
                    <a href="?view_history=true" class="view_history">View History</a>
                    <button type="submit" name="show_feedback" class="show-feedback">Show Feedback</button>
                </div>
            </form>

            <?php
            // Display feedback if requested
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['show_feedback'])) {
                echo "<h2>Feedback</h2>";
                if (!empty($feedback)) {
                    foreach ($feedback as $fb) {
                        echo "<div class='feedback-tile'>
                                <p><strong>Name:</strong> " . htmlspecialchars($fb['name']) . "</p>
                                <p><strong>Email:</strong> " . htmlspecialchars($fb['email']) . "</p>
                                <p><strong>Subject:</strong> " . htmlspecialchars($fb['subject']) . "</p>
                                <p><strong>Message:</strong> " . htmlspecialchars($fb['message']) . "</p>
                                <p><strong>Submitted At:</strong> " . htmlspecialchars($fb['created_at']) . "</p>
                              </div>";
                    }
                } else {
                    echo "<p>No feedback found.</p>";
                }
            }

            // Check if view history is requested
            if (isset($_GET['view_history'])) {
                // Query to fetch all comments
                $history_sql = "SELECT COMMENT_ID, USER_ID, ORDER_ID, COMMENTS, SENDER_TYPE, READ1, CREATED_AT 
                                FROM comments 
                                ORDER BY CREATED_AT DESC";
                $history_result = mysqli_query($conn, $history_sql);

                if ($history_result && mysqli_num_rows($history_result) > 0) {
                    echo "<h2>Communication History</h2>";
                    while ($history = mysqli_fetch_assoc($history_result)) {
                        echo "<div class='message-tile'>
                                <p><strong>Order:</strong> #" . $history['ORDER_ID'] . "</p>
                                <p><strong>Message:</strong> " . htmlspecialchars($history['COMMENTS']) . "</p>
                                <p><strong>Sender:</strong> " . htmlspecialchars($history['SENDER_TYPE']) . "</p>
                                <p><strong>Status:</strong> " . ($history['READ1'] ? "Read" : "Unread") . "</p>
                                <p><strong>Created At:</strong> " . htmlspecialchars($history['CREATED_AT']) . "</p>
                              </div>";
                    }
                } else {
                    echo "<p>No communication history found.</p>";
                }
            }
            ?>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>
