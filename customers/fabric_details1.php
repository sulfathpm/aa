<?php
session_start();
error_reporting(0);
// Connect to MySQL database
$dbcon = mysqli_connect("localhost", "root", "", "fashion");

// Check connection
if (!$dbcon) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the fabric ID from the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $fabric_id = intval($_GET['id']); // Sanitize input

    // Fetch fabric details from the database
    $sql = "SELECT * FROM fabrics WHERE FABRIC_ID = $fabric_id";
    $result = mysqli_query($dbcon, $sql);

    // Check if the fabric exists
    if ($result->num_rows > 0) {
        $fabric = $result->fetch_assoc();
        $price=$fabric['PRICE_PER_UNIT'];
        $_SESSION["FABRIC_ID"]=$fabric['FABRIC_ID'];
        $_SESSION["BASE_PRICE"]=$fabric['PRICE_PER_UNIT'];
    } else {
        echo "Fabric not found.";
        exit();
    }
} else {
    echo "Invalid fabric ID.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($fabric['NAME']); ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            padding: 40px;
            max-width: 1200px;
            margin: auto;
        }

        .fabric-details {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .fabric-details img {
            width: 40%;
            height: auto;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .fabric-info {
            width: 55%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .fabric-info h2 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #333;
        }

        .fabric-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .fabric-info table th, .fabric-info table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .fabric-info table th {
            background-color: #f9f9f9;
            color: #333;
        }

        .price {
            font-size: 1.5em;
            color: palevioletred;
            margin-bottom: 20px;
        }

        .buy-button {
            background-color: palevioletred;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .buy-button:hover {
            background-color: #d1477a;
        }
    </style>
</head>
<body>
    <form action="" method="post">
    <div class="container">
        <h1><?php echo htmlspecialchars($fabric['NAME']); ?></h1>
        <div class="fabric-details">
            <img src="<?php echo htmlspecialchars($fabric['IMAGE_URL']); ?>" alt="<?php echo htmlspecialchars($fabric['NAME']); ?>">
            <div class="fabric-info">
                <h2>Fabric Details</h2>
                <table>
                    <tr>
                        <th>Description</th>
                        <td><?php echo htmlspecialchars($fabric['DESCRIPTION']); ?></td>
                    </tr>
                    <tr>
                        <th>Fabric Type</th>
                        <td><?php echo htmlspecialchars($fabric['NAME']); ?></td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td class="price">₹<?php echo number_format($fabric['PRICE_PER_UNIT'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Enter Quantity in meter</th>
                        <!-- <td><input type="text" id="meter" name="meter" placeholder="Enter how much meter you want" onchange="updatePrice()"></td> -->
                        <td>
                            <select id="meter" name="meter" onchange="updatePrice()">
                                <option value="0" >Select how much meter you want</option>
                                <option value="1" >1 meter</option>
                                <option value="2" >2 meter</option>
                                <option value="3" >3 meter </option>
                                <option value="4" >4 meter</option>
                                <option value="5" >5 meter</option>
                                <option value="6" >6 meter</option>
                                <option value="7" >7 meter</option>
                                <option value="8" >8 meter</option>
                                <option value="9">9 meter</option>
                                <option value="10">10 meter</option>
                            </select>
                        </td>
                    </tr>
                    <tr></tr>
                    <tr>
                        
                        <th>Total Price:</th>
                        <td><h3><span id="total-price">0</span></h3></td>
                    </tr>
                </table>
                <br>
                <button type="submit"class="buy-button" name="cart-fabric">Add cart</button>
                <button type="submit"class="buy-button" name="buy-fabric">Buy Now</button>
            </div>
        </div>
    </div>
    </form>

    <?php
    if (isset($_POST['buy-fabric'])) {
        $meter = $_POST['meter'];
        if($meter==0){
            
        }else{

        
            $totalprice = $price * $meter;

            if ($_SESSION["USER_ID"] == null) {
                echo "<script>alert('You need to login to customise dress'); window.location.href='login.php';</script>";
            } else {
                $current_date = date('Y-m-d');
                $estimate_date = date('Y-m-d', strtotime($current_date . ' + 3 days'));
                $actual_date = date('Y-m-d', strtotime($current_date . ' + 5 days'));

                // Now place order
                $sql = "INSERT INTO orders(USER_ID, FABRIC_ID, STATUSES, SSIZE, TOTAL_PRICE, ESTIMATED_DELIVERY_DATE, ACTUAL_DELIVERY_DATE) 
                        VALUES ('{$_SESSION['USER_ID']}', '{$_SESSION['FABRIC_ID']}', 'PENDING', '$meter', '$totalprice', '$estimate_date', '$actual_date')";
                
                $data = mysqli_query($dbcon, $sql);

                if ($data) {
                    echo "<script>alert('Fabric ordered successfully!'); window.location.href='fabric.php';</script>";
                }
            }
        }
    }else if(isset($_POST['cart-fabric'])){
        $meter = $_POST['meter'];
        if($meter==0){
            
        }else{

        
            $totalprice = $price * $meter;

            if ($_SESSION["USER_ID"] == null) {
                echo "<script>alert('You need to login to customise dress'); window.location.href='login.php';</script>";
            } else {
                $current_date = date('Y-m-d');
                $estimate_date = date('Y-m-d', strtotime($current_date . ' + 3 days'));
                $actual_date = date('Y-m-d', strtotime($current_date . ' + 5 days'));

                // Now place order
                $sql = "INSERT INTO orders(USER_ID, FABRIC_ID, STATUSES, SSIZE, TOTAL_PRICE) 
                        VALUES ('{$_SESSION['USER_ID']}', '{$_SESSION['FABRIC_ID']}', 'CART', '$meter', '$totalprice')";
                
                $data = mysqli_query($dbcon, $sql);

                if ($data) {
                    echo "<script>alert('Fabric added to cart!'); window.location.href='fabric.php';</script>";
                }
            }
        }
    }
    ?>

    <script>
        function updatePrice() {
            const meter = document.getElementById('meter').value;
            const pricePerUnit = <?php echo $price; ?>;  // PHP price value
            const totalPrice = meter * pricePerUnit;

            // Update total price in HTML
            if (!isNaN(totalPrice) && totalPrice > 0) {
                document.getElementById('total-price').innerText = '₹' + totalPrice.toFixed(2);
            } else {
                document.getElementById('total-price').innerText = '₹0';
            }
        }
    </script>
</body>
</html>

<?php
// Close the database connection
mysqli_close($dbcon);
?>

<?php
// Close the database connection
mysqli_close($dbcon);
?>
