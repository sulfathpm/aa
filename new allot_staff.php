<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'fashion');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ORDER_ID and STAFF_ID are set
if (isset($_GET['id']) && isset($_POST['staff_id'])) {
    $orderId = $_GET['id'];
    $staffId = $_POST['staff_id']; // Assume you get this from a form or dropdown

    // Insert the assignment into the database
    $sql = "INSERT INTO order_assignments (ORDER_ID, STAFF_ID) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $orderId, $staffId);
    
    if ($stmt->execute()) {
        echo "Staff assigned successfully.";
    } else {
        echo "Error assigning staff: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
