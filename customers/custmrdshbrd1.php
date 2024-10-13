<?php
session_start();
error_reporting(0);
// Connect to MySQL database
$_SESSION["KEY"]==null;
$dbcon = mysqli_connect("localhost", "root", "", "fashion");

// Check connection
if (!$dbcon) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Women's Boutique</title>
    <style>
        /* CSS styling as in your HTML code */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
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

        .hero {
            background-image: url('../dress/img3.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
            text-align: center;
            padding: 100px 20px;
            position: relative;
        }

        .hero h1 {
            font-size: 3em;
            margin: 0;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        .container {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
            text-align: center;
        }

        h2 {
            font-size: 2em;
            margin-bottom: 30px;
            color: #333;
        }

        .featured-dresses {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .dress-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin: 15px;
            padding: 20px;
            width: calc(33.333% - 40px);
            box-sizing: border-box;
            transition: transform 0.3s ease;
        }

        .dress-card:hover {
            transform: translateY(-10px);
        }

        .dress-card img {
            width: 100%; /* Adjust width as needed */
            height: 200px; /* Set a fixed height */
            object-fit: contain; /* Ensures the whole image is visible without cropping */
            border-radius: 10px;
            margin-bottom: 15px;
            /* transition: transform 0.001s ease;
            will-change: transform; Optimizes performance for transformations */

        }
/* 
        .dress-card:hover img {
            transform: scale(1.5);} Scale image to 110% of its size */
        

        .dress-card h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #333;
        }

        .dress-card p {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 20px;
        }

        .dress-card button {
            background-color: palevioletred;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .dress-card button:hover {
            background-color: #d1477a;
        }

        .footer {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            font-size: 0.9em;
            margin-top: 40px;
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
    <div class="navbar">
        <a href="custmrdshbrd.php">Home</a>
        <a href="fabric.php">Choose by Fabric</a>
        <a href="abt.php">About</a>
        <a href="contact1.php">Contact</a>

        <?php
        if($_SESSION["USER_ID"]==null){
           echo "<a href='login.php'>Login</a>";
        }else{
            echo "<a href='logout.php'>Logout</a>";
        }
        ?>

        <a href="customize1.php" class="customize-button">Customize Now</a>
    </div>
    
    <div class="hero">
        <h1>Welcome to Our Boutique</h1>
    </div>
    
    <div class="container">
        <h2>Featured Dresses</h2>
        <div class="featured-dresses">
            <?php
            // Fetch dress data from the database
            $sql = "SELECT * FROM dress";
            $result = mysqli_query($dbcon, $sql);
            // Check if there are results
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="dress-card">';
                    echo '<img src="' . (isset($row['IMAGE_URL']) ? $row['IMAGE_URL'] : '../default.jpg') . '" alt="' . (isset($row['NAME']) ? $row['NAME'] : 'No name') . '">';
                    echo '<h3>' . (isset($row['NAME']) ? $row['NAME'] : 'No name') . '</h3>';
                    // echo '<p>' . (isset($row['DESCRIPTION']) ? $row['DESCRIPTION'] : 'No description available') . '</p>';
                    echo '<p class="price">₹' . (isset($row['BASE_PRICE']) ? number_format($row['BASE_PRICE'], 2) : '0.00') . '</p>';
                    echo '<button onclick="window.location.href=\'dress_details.php?id=' . $row['DRESS_ID'] . '\'">View Details</button>';
                    //echo '<button onclick="window.location.href=\'customize1.php?dress_id=' . $row['DRESS_ID'] . '\'">Customize now</button>';

                    echo '</div>';
                    // echo '<button onclick="window.location.href=\'dress_details.php?id=' . $row['DRESS_ID'] . '\'">View Details</button>';
                }
            } else {
                echo '<p>No dresses available</p>';
            }

            ?>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; 2024 Women's Boutique. All Rights Reserved.</p>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($dbcon);
?>
