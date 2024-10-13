<?php
// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "fashion");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$message = "";

// Check if deletion is confirmed via GET parameter
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && isset($_GET['id'])) {
    // Get design ID from the URL
    $design_id = $_GET['id'];

    // Delete design from the database
    $sql = "DELETE FROM dress WHERE DRESS_ID = $design_id";

    if ($conn->query($sql) === TRUE) {
        $message = "Design deleted successfully.";
    } else {
        $message = "Error deleting design: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Design</title>
    <link rel="stylesheet" href="admin.css">
    <script>
        function confirmDeletion(designId) {
            var confirmation = confirm("Are you sure you want to delete this design?");
            if (confirmation) {
                // If confirmed, redirect with confirmation parameter
                window.location.href = "deleteDesign.php?id=" + designId + "&confirm=yes";
            } else {
                // If not confirmed, return to the manage designs page
                window.location.href = "manageDesign.php";
            }
        }
    </script>
</head>
<body>
    <?php if ($message): ?>
        <h1><?php echo $message; ?></h1>
    <?php endif; ?>
    <a href="manageDesign.php"><button>Back</button></a>
</body>
</html>
