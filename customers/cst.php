<?php
session_start();
error_reporting(0);

// Connect to MySQL database
$dbcon = mysqli_connect("localhost", "root", "", "fashion");

// Check connection
if (!$dbcon) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables
$searchQuery = "";
$categoryCondition = $styleCondition = $fabricCondition = $colorCondition = $sizeCondition = $sortCondition = "";

// Handle search query
if (isset($_POST['search'])) {
    $searchQuery = mysqli_real_escape_string($dbcon, trim($_POST['searchQuery']));
}

// Handle filtering
if (isset($_POST['filter'])) {
    $filters = [
        'category' => 'CATEGORY',
        'style' => 'STYLE',
        'fabric' => 'FABRIC',
        'color' => 'COLOR',
        'size' => 'SIZE',
    ];

    foreach ($filters as $field => $dbField) {
        if (!empty($_POST[$field])) {
            $value = mysqli_real_escape_string($dbcon, $_POST[$field]);
            ${$field . 'Condition'} = " AND $dbField = '$value'";
        }
    }

    // Sorting
    if (!empty($_POST['sort'])) {
        switch ($_POST['sort']) {
            case 'price_asc':
                $sortCondition = " ORDER BY BASE_PRICE ASC";
                break;
            case 'price_desc':
                $sortCondition = " ORDER BY BASE_PRICE DESC";
                break;
            case 'popularity':
                $sortCondition = " ORDER BY POPULARITY DESC"; // Assuming a POPULARITY field exists
                break;
            case 'new_arrivals':
                $sortCondition = " ORDER BY CREATED_AT DESC"; // Assuming CREATED_AT for new arrivals
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Women's Boutique</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f5f5f5; color: #333; }
        .hero { background-image: url(''); background-size: cover; background-position: center; color: #de4b7b !important; text-align: center; padding: 100px 20px; }
        .hero h1 { font-size: 3em; margin: 0; text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7); }
        .dress-card { border-radius: 10px; box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); margin: 15px; padding: 20px; transition: transform 0.3s ease; }
        .dress-card:hover { transform: translateY(-10px); }
        .dress-card img { width: 100%; height: 200px; object-fit: contain; border-radius: 10px; margin-bottom: 15px; }
        .footer { background-color: #333; color: #fff; padding: 20px 0; text-align: center; font-size: 0.9em; margin-top: 40px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="custmrdshbrd.php">Home</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="fabric.php">Fabric</a></li>
                <li class="nav-item"><a class="nav-link" href="abt.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="contact1.php">Contact</a></li>
            </ul>
            <div class="navbar-nav">
                <?php
                if (empty($_SESSION["USER_ID"])) {
                    echo "<a class='nav-link' href='login.php'>Login</a>";
                } else {
                    echo "<a class='nav-link' href='logout.php'>Logout</a>";
                    echo "<a class='nav-link' href='profile.php'>Profile</a>";
                }
                ?>
                <a class="nav-link btn btn-danger" href="customize1.php">Customize Now</a>
            </div>
        </div>
    </nav>

    <div class="hero">
        <h1>Welcome to Our Boutique</h1>
    </div>

    <div class="container my-4">
        <h1 class="text-center">Featured Dresses</h1>

        <!-- Filter Bar -->
        <div class="filter-bar my-3">
            <form method="POST" action="" class="form-inline justify-content-center">
                <select name="category" class="form-control mx-2">
                    <option value="">Category</option>
                    <option value="traditional">Traditional</option>
                    <option value="western">Western</option>
                    <option value="party-wear">Party-Wear</option>
                    <option value="casual">Casual</option>
                </select>

                <select name="style" class="form-control mx-2">
                    <option value="">Style</option>
                    <!-- Add style options here -->
                </select>

                <select name="fabric" class="form-control mx-2">
                    <option value="">Fabric</option>
                    <!-- Add fabric options here -->
                </select>

                <select name="color" class="form-control mx-2">
                    <option value="">Color</option>
                    <!-- Add color options here -->
                </select>

                <select name="size" class="form-control mx-2">
                    <option value="">Size</option>
                    <!-- Add size options here -->
                </select>

                <select name="sort" class="form-control mx-2">
                    <option value="price_asc">Sort by Price: Low to High</option>
                    <option value="price_desc">Sort by Price: High to Low</option>
                    <option value="popularity">Sort by Popularity</option>
                    <option value="new_arrivals">Sort by New Arrivals</option>
                </select>

                <button type="submit" name="filter" class="btn btn-primary mx-2">Filter</button>
            </form>
        </div>

        <!-- Featured Dresses Section -->
        <div class="row">
            <?php
            // Combine search query and filter conditions
            $sql = "SELECT * FROM dress WHERE 1=1";

            // Search condition
            if (!empty($searchQuery)) {
                $sql .= " AND DRESS_NAME LIKE '%$searchQuery%'";
            }

            // Add the filtering and sorting conditions
            $sql .= $categoryCondition . $styleCondition . $fabricCondition . $colorCondition . $sizeCondition . $sortCondition;

            // Execute query
            $result = mysqli_query($dbcon, $sql);

            // Display dresses
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='col-md-4'>";
                    echo "<div class='dress-card bg-white p-3'>";
                    echo "<img src='" . $row['IMAGE'] . "' alt='" . $row['DRESS_NAME'] . "' class='img-fluid'>";
                    echo "<h3 class='h5'>" . $row['DRESS_NAME'] . "</h3>";
                    echo "<p>Price: ₹" . $row['BASE_PRICE'] . "</p>";
                    echo "<button class='btn btn-danger' onclick=\"window.location.href='dress_details.php?id=" . $row['DRESS_ID'] . "'\">View Details</button>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-center'>No dresses found matching your criteria.</p>";
            }
            ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 Women's Boutique. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
