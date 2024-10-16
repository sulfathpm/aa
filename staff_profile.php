<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to MySQL database
$dbcon = mysqli_connect("localhost", "root", "", "fashion");

// Check connection
if (!$dbcon) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the current staff details
$staff_id = $_SESSION['USER_ID'];
if (!isset($staff_id)) {
    die("Staff not logged in.");
}

// Use prepared statement to fetch staff details
$stmt = $dbcon->prepare("SELECT * FROM users WHERE USER_ID = ?");
if (!$stmt) {
    die("Prepare failed: " . $dbcon->error);
}
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $staff = $result->fetch_assoc();
} else {
    die("Staff not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .navbar {
            background-color: #333;
            padding: 15px 0;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar a {
            color: #fff;
            padding: 14px 20px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .navbar a:hover, .navbar a.customize-button {
            background-color: palevioletred;
            border-radius: 20px;
        }
        .profile-container {
            max-width: 1000px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        .profile-header img.profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: contain;
            border: 4px solid palevioletred;
            margin-right: 20px;
        }
        .profile-info h2 {
            font-weight: 600;
            margin-bottom: 5px;
            color: palevioletred;
        }
        .profile-info p {
            font-size: 16px;
            color: #666;
        }
        .profile-section {
            margin-bottom: 30px;
        }
        .profile-section h3 {
            margin-bottom: 10px;
            font-size: 20px;
            color: palevioletred;
            border-bottom: 2px solid #ececec;
            padding-bottom: 10px;
        }
        .activity-card {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .activity-card:hover {
            background-color: #f0d9e0;
        }
        .settings-link {
            text-decoration: none;
            color: palevioletred;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        .settings-link:hover {
            text-decoration: underline;
        }
        .btn-edit {
            padding: 10px 20px;
            background-color: palevioletred;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn-edit:hover {
            background-color: #d75a8a;
        }
        .footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="staff_dashboard.php">Home</a>
        <a href="orders.php">Orders</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <?php
        if (!isset($_SESSION["USER_ID"])) {
            echo "<a href='login.php'>Login</a>";
        } else {
            echo "<a href='logout.php'>Logout</a>";
            echo "<a href='staff_profile.php'>Profile</a>";
        }
        ?>
        <a href="customize.php" class="customize-button">Customize Now</a>
    </div>

    <div class="profile-container">
        <div class="profile-header">
            <?php if (!empty($staff['PROFILE_PICTURE'])): ?>
                <img src="<?php echo htmlspecialchars($staff['PROFILE_PICTURE']); ?>" alt="Profile Picture" class="profile-pic">
            <?php else: ?>
                <img src="https://via.placeholder.com/120" alt="Profile Picture" class="profile-pic">
            <?php endif; ?>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($staff['USERNAME']); ?></h2>
                <p>Email: <?php echo htmlspecialchars($staff['EMAIL']); ?></p>
                <p>Joined on: <?php echo substr($staff['CREATED_AT'], 0, 10); ?></p>
                <button class="btn-edit" onclick="window.location.href='edit_staff_profile.php'">Edit Profile</button>
            </div>
        </div>

        <div class="profile-section">
            <h3>Recent Activities</h3>
            <?php
                // Fetch recent activities from the hypothetical activities table
                $stmt = $dbcon->prepare("SELECT * FROM comments WHERE USER_ID = ? ORDER BY created_at DESC");
                if (!$stmt) {
                    die("Prepare failed: " . $dbcon->error . " - SQL: " . "SELECT * FROM comments WHERE USER_ID = " . $staff_id);
                }
                $stmt->bind_param("i", $staff_id);
                $stmt->execute();
                $data2 = $stmt->get_result();

                if ($data2 && $data2->num_rows > 0) {
                    while ($activity = $data2->fetch_assoc()) {
                        echo "<div class='activity-card'>
                                <h4>" . htmlspecialchars($activity['DESCRIPTION']) . "</h4>
                                <p>Date: " . substr($activity['ACTIVITY_DATE'], 0, 10) . "</p>
                              </div>";
                    }
                } else {
                    echo "<p>No recent activities found.</p>";
                }
            ?>
        </div>

        <div class="profile-section">
            <h3>Account Settings</h3>
            <a href="change_staff_password.php" class="settings-link">Change Password</a>
            <br>
            <a href="update_staff_address.php" class="settings-link">Update Address</a>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Fashion Boutique. All Rights Reserved.</p>
    </div>

</body>
</html>
