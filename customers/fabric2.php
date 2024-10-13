<?php
session_start();
error_reporting(0);
$_SESSION["KEY"]==null;
$_SESSION["DRESS_ID"]=null;
$_SESSION["FABRIC_ID"]=null;

$dbcon = mysqli_connect("localhost", "root", "", "fashion");
if (!$dbcon) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose by Fabric - Women's Boutique</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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
            background-image: url('../fabric\chiffon1.jpg');
            background-size: cover;
            background-position: center;
            /* padding: 100px 20px; */
            text-align: center;
            color: white;
            height: 110px;
        }

        .hero h1 {
            font-size: 2.5em;
            margin: 0;
        }

        .browse-button {
            background-color: palevioletred;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 30px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
        }

        .browse-button:hover {
            background-color: #d06c9f;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
            background-color: white;
            margin-top: -50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: palevioletred;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .dress-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            transition: transform 0.3s ease;
            flex: 1;
        }

        .dress-card img {
            width: 100%; /* Adjust width as needed */
            height: 200px; /* Set a fixed height */
            object-fit: contain; /* Ensures the whole image is visible without cropping */
            border-radius: 10px;
            margin-bottom: 15px;
            transition: transform 0.001s ease;
            will-change: transform; /* Optimizes performance for transformations */

        }

        /* .dress-card:hover img {
            transform: scale(1.5); }Scale image to 110% of its size */
        

        .dress-card h3 {
            margin: 15px 0 10px;
            font-size: 1.2em;
        }

        .dress-card p {
            color: #777;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .dress-card button {
            background-color: palevioletred;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dress-card button:hover {
            background-color: #d06c9f;
        }

        .dress-card:hover {
            transform: translateY(-5px);
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: white;
            margin-top: 40px;
            border-radius: 0 0 10px 10px;
        }

        .footer p {
            margin: 0;
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
        <a href="fabric.php" class="active">Fabric</a>
        <a href="abt.php">About</a>
        <a href="contact1.php">Contact</a>
        <?php
        if($_SESSION["USER_ID"]==null){
           echo "<a href='login.php'>Login</a>";
        }else{
            echo "<a href='logout.php'>Logout</a>";
            echo "<a href='profile.php'>Profile</a>";
        }
        ?>
        <a href="customize1.php" class="customize-button">Customize Now</a>
    </div>
    <div class="hero">
        <!-- <h1>Discover the Perfect Fabric for Your Custom Dress</h1>
        <button class="browse-button">Browse Fabrics</button> -->
    </div>

    <div class="container">
        <h1>Choose Your Fabric</h1>
        <!-- <p>Browse through our selection of high-quality fabrics and choose the perfect one for your custom dress.</p> -->
        
        <div class="card-container">
            <?php
           $fabrics_query = "SELECT * FROM fabrics";
           $result = mysqli_query($dbcon, $fabrics_query);
           if (mysqli_num_rows($result) > 0) {
               while ($row = mysqli_fetch_assoc($result)) {
                   echo "<div class='dress-card'>";
                   echo "<img src='" . $row['IMAGE_URL'] . "' alt='" . $row['DESCRIPTION'] . "'>";
                   echo "<h3>" . $row['NAME'] . "</h3>";
                   // echo "<p>" . $row['DESCRIPTION'] . "</p>";
                   // echo "<button>View Details</button>";
                   echo "<button onclick=\"window.location.href='fabric_details.php?id=" . $row['FABRIC_ID'] . "'\">View Details</button>";
                   echo "</div>";
               }
           } else {
               echo "<p>No fabrics available at the moment.</p>";
           }
           ?>
        </div>
    </div>

<?php
// Close database connection
mysqli_close($dbcon);
?>

    </div>
    
    <div class="footer">
        <p>&copy; 2024 Women's Boutique. All Rights Reserved.</p>
    </div>
</body>
</html>