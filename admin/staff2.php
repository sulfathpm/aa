<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="header">
        <h1>Staff Management</h1>
    </div>
    <div class="admin-dashboard">
        <aside class="sidebar">
            <h3>Menu</h3>
            <!-- <a href="admindshbrd.html">Dashboard</a> -->
            <a href="customers.php">Customer Management</a>
            <a href="staff.php">Staff Management</a>
            <a href="delivery.php">Delivery Management</a>
            <a href="communications.php">Communication</a>
            <a href="manageDesign.php">Manage Designs</a>
            <a href="orderManage.php">Order Management</a>
            <a href="useracc.php">User Accounts</a>
        </aside>
        <div class="header">
        <h1>Add New Staff</h1>
    </div>
    <div class="content">
            <form method="post" action="">
                <label for="username">Name:</label>
                <input type="text" id="username" name="username" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <input type="hidden" name="user_type" value="STAFF">

                <button type="submit">Add Staff</button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "fashion";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Insert new staff data
                $name = $conn->real_escape_string($_POST['username']);
                $email = $conn->real_escape_string($_POST['email']);
                $user_type = $conn->real_escape_string($_POST['user_type']);

                $checkEmailResult = null;
                $checkUsernameResult = null;

                $checkEmailSql = "SELECT * FROM users WHERE EMAIL='$email'";
                $checkResult = $conn->query($checkEmailSql);
            
                $checkUsernameSql = "SELECT * FROM users WHERE USERNAME='$name'";
                $checkUsernameResult = $conn->query($checkUsernameSql);
            
                if ($checkEmailResult->num_rows > 0) {
                    echo "<p>Email already exists. Please use a different email.</p>";
                } elseif ($checkUsernameResult->num_rows > 0) {
                    echo "<p>Username already exists. Please choose a different username.</p>";
                } else {
                $sql = "INSERT INTO users (USERNAME, EMAIL, USER_TYPE) VALUES ('$name', '$email', 'STAFF')";

                if ($conn->query($sql) === TRUE) {
                    echo "<p>New staff added successfully!</p>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            
                $conn->close();
            }
            ?>
        </div>

        <main class="content">
            <h3>Staff Management</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database connection
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "fashion";

                    // Create connection
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch staff data
                    $sql = "SELECT USER_ID, USERNAME, EMAIL FROM users where USER_TYPE='STAFF'"; // Adjust table name as needed
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['USER_ID'] . "</td>";
                            echo "<td>" . $row['USERNAME'] . "</td>";
                            echo "<td>" . $row['EMAIL'] . "</td>";
                            echo "<td>
                                    <button onclick=\"window.location.href='view.php?id=".$row['USER_ID']."'\">View</button>
                                    <button onclick=\"window.location.href='edit.php?id=".$row['USER_ID']."'\">Edit</button>
                                    <button onclick=\"window.location.href='delete.php?id=".$row['USER_ID']."'\">Delete</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No staff found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
