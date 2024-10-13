<?php
session_start();
error_reporting(0);

// Connect to the database
$dbcon = mysqli_connect("localhost", "root", "", "fashion");

if (!$dbcon) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the current user details
$sql = "SELECT * FROM users WHERE USER_ID='$_SESSION[USER_ID]'";
$data = mysqli_query($dbcon, $sql);
if ($data) {
    $user = mysqli_fetch_array($data);
}

// Handle form submission
if (isset($_POST['update'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $profile_picture_url = $user['PROFILE_PICTURE']; // Keep the old picture by default

    // Sanitize the input to avoid SQL injection
    $username = mysqli_real_escape_string($dbcon, $username);
    $email = mysqli_real_escape_string($dbcon, $email);

    // Handle the file upload
    if ($_FILES['profile_picture']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            echo "<script>alert('File is not an image.');</script>";
            $uploadOk = 0;
        }

        // Check file size (limit to 5MB)
        if ($_FILES["profile_picture"]["size"] > 5000000) {
            echo "<script>alert('Sorry, your file is too large.');</script>";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            $uploadOk = 0;
        }

        // Try to upload the file if everything is okay
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture_url = mysqli_real_escape_string($dbcon, $target_file); // Save the new URL
                echo "<script>alert('File uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
            }
        }
    }

    // Check if username or email already exists (excluding current user)
    $check_username = "SELECT * FROM users WHERE USERNAME='$username' AND USER_ID != '$_SESSION[USER_ID]'";
    $res_username = mysqli_query($dbcon, $check_username);

    $check_email = "SELECT * FROM users WHERE EMAIL='$email' AND USER_ID != '$_SESSION[USER_ID]'";
    $res_email = mysqli_query($dbcon, $check_email);

    if (mysqli_num_rows($res_username) > 0) {
        echo "<script>alert('Username is already taken. Please choose another one.');</script>";
    } elseif (mysqli_num_rows($res_email) > 0) {
        echo "<script>alert('Email is already in use. Please use a different email.');</script>";
    } else {
        // Update user data
        $sql = "UPDATE users SET USERNAME='$username', EMAIL='$email', PROFILE_PICTURE='$profile_picture_url' WHERE USER_ID='$_SESSION[USER_ID]'";

        if (mysqli_query($dbcon, $sql)) {
            echo "<script>alert('Profile updated successfully!');</script>";
            $_SESSION['USERNAME'] = $username;
            $_SESSION['EMAIL'] = $email;
        } else {
            echo "<script>alert('There was an error updating your profile.');</script>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Add your CSS styles here */
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

        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid palevioletred;
        }

        .profile-info {
            margin-left: 20px;
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

        .order-card {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            background-color: #f0d9e0;
        }

        .order-card h4 {
            color: #333;
        }

        .order-card p {
            font-size: 14px;
            color: #555;
        }

        .order-card .status {
            color: #28a745;
            font-weight: 600;
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
        .customize-button{
            background-color: #d1477a !important;
        }
        .customize-button:hover{
            color: black !important;
            background-color: rgb(247, 144, 178)!important;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <!-- Display profile picture -->
        <div class="profile-header">
            <?php if (!empty($user['PROFILE_PICTURE'])): ?>
                <img src="<?php echo htmlspecialchars($user['PROFILE_PICTURE']); ?>" alt="Profile Picture" style="width: 120px; height: 120px; border-radius: 50%;">
            <?php else: ?>
                <img src="https://via.placeholder.com/120" alt="No Profile Picture" style="width: 120px; height: 120px; border-radius: 50%;">
            <?php endif; ?>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user['USERNAME']); ?></h2>
                <p>Email: <?php echo htmlspecialchars($user['EMAIL']); ?></p>
                <p>Member since: <?php echo substr($user['CREATED_AT'], 0, 10); ?></p>
                <form action="profile.php" method="POST" enctype="multipart/form-data">
                    <label for="profile_picture">Change Profile Picture:</label>
                    <input type="file" name="profile_picture" id="profile_picture">
                    <button type="submit" name="update" class="btn-edit">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
