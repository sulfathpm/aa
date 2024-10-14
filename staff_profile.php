<?php
// Start session
session_start();
$_SESSION['USER_ID'] = $user_id;  // Set this after a successful login
$_SESSION['USER_TYPE'] = $STAFF;  // Make sure 'STAFF' is set as their user type


// Check if the staff is logged in
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_TYPE'] != 'STAFF') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "fashion");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch logged-in staff's profile details
$user_id = $_SESSION['USER_ID'];
$sql = "SELECT USERNAME, EMAIL, PHONE, PROFILE_PICTURE, ADDRESSS FROM users WHERE USER_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone, $profile_picture, $address);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <link rel="stylesheet" href="staff_profile.css">
</head>
<body>
    <h1>Welcome, <?php echo $username; ?></h1>
    <div class="profile-container">
        <div class="profile-image">
            <?php if ($profile_picture): ?>
                <img src="uploads/<?php echo $profile_picture; ?>" alt="Profile Picture">
            <?php else: ?>
                <img src="default.png" alt="Default Picture">
            <?php endif; ?>
        </div>
        <div class="profile-details">
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Phone:</strong> <?php echo $phone; ?></p>
            <p><strong>Address:</strong> <?php echo $address; ?></p>
        </div>
        <a href="edit_profile.php"><button>Edit Profile</button></a>
    </div>
</body>
</html>
