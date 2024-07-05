<?php
require_once 'db_config.php'; 
session_start();

$db = mysqli_connect($servername, $username, $password, $dbname);

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

function addWalkins($db, $Name, $PaymentMethod) {
    $Name = mysqli_real_escape_string($db, $Name);
    $PaymentMethod = mysqli_real_escape_string($db, $PaymentMethod);

    $query = "INSERT INTO walkins (Name, PaymentMethod) 
              VALUES ('$Name', '$PaymentMethod')";
    
    mysqli_query($db, $query);
    
    return mysqli_insert_id($db);  // Return the auto-generated Walkin_Id
}

function addWalkinDetails($db, $Walkin_Id, $Flower_Id, $Qty, $Price) {
    $Walkin_Id = mysqli_real_escape_string($db, $Walkin_Id);
    $Flower_Id = mysqli_real_escape_string($db, $Flower_Id);
    $Qty = mysqli_real_escape_string($db, $Qty);
    $Price = mysqli_real_escape_string($db, $Price);

    $query = "INSERT INTO walkin_details (Walkin_Id, Flower_Id, Qty, Price) 
              VALUES ('$Walkin_Id', '$Flower_Id', '$Qty', '$Price')";
    
    mysqli_query($db, $query);

    return mysqli_insert_id($db);  // Return the auto-generated Walkin_Details_Id
}

if (isset($_POST['add_walkins'])) {
    // Start a transaction
    mysqli_begin_transaction($db);

    try {
        // Retrieve the form input values
        $Name = $_POST['Name'];
        $PaymentMethod = $_POST['PaymentMethod'];

        // Call the addWalkins function and get the Walkin_Id
        $Walkin_Id = addWalkins($db, $Name, $PaymentMethod);

        // Retrieve additional form input values
        $Flower_Id = $_POST['Flower_Id']; // Ensure this field is correctly set in your HTML form
        $Qty = $_POST['Qty'];
        $Price = $_POST['Price'];

        // Call the addWalkinDetails function and get the Walkin_Details_Id
        $Walkin_Details_Id = addWalkinDetails($db, $Walkin_Id, $Flower_Id, $Qty, $Price);

        // Commit the transaction
        mysqli_commit($db);

        header('location: admin_orders.php');
        //echo "Transaction committed successfully.";
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        mysqli_rollback($db);
        echo "Transaction rolled back. Error: " . $e->getMessage();
    }
}

mysqli_close($db);
?>






    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Walk-in Order</title>
    <link rel="stylesheet" href="./css/edit-style.css">
</head>

<body>

    <!-- for header part -->
    <header>

    <div class="logosec">
			<div class="logo">
                <img src="./img/finalhome.png" alt="Image Failed to Load" width="240" height="50" style="margin-top: 22px;">
            </div>
		</div>

        <div class="searchbar">
            <input type="text" placeholder="Search">
            <div class="searchbtn">
                <img src="./images/search.png" class="nav-img" alt="search">
            </div>
        </div>

        <div class="message">
			<div class="circle"></div>
			<img src="./images/notif.png" class="nav-img" alt="notif">
			<div class="message">
    <a href="update.php">
        <img src="./images/dp.png" class="nav-img" alt="dp">
    </a>
</div>

		</div>

    </header>

    <div class="main-container">
    <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">

                <a href="admin_home.php" class="link">
					<div class="nav-option option2">
						<img src="./images/dashboard.jpg" class="nav-img" alt="dashboard">
						<h4>Dashboard</h4>
					</div>
                </a>

					<a href="user_management.php" class="link">
                        <div class="nav-option option3">
                            <img src="./images/users.png" class="nav-img" alt="users">
                            <h4>Users</h4>
                        </div>
                    </a>
					
					<a href="admin_flowers.php" class="link">
					<div class="nav-option option4">
						<img src="./images/flower.png" class="nav-img" alt="browse">
						<h4>Flowers</h4>
					</div>
					</a>

                    <a href="admin_orders.php" class="link"> 
					<div class="nav-option option1">
                        <img src="./images/orders.png" class="nav-img" alt="edit profile">
						<h4>Orders</h4>
					</div>
                    </a>

                    <a href="admin_vehicles.php" class="link">
					<div class="nav-option option5">
						<img src="./images/vehicles.png" class="nav-img" alt="settings">
						<h4>Vehicles</h4>
					</div>
                    </a>

                    <a href="admin_harvests.php" class="link">
					<div class="nav-option option5">
						<img src="./images/delivered.png" class="nav-img" alt="settings">
						<h4>Harvests</h4>
					</div>
                    </a>
                    <a href="admin_shipment.php" class="link">
					<div class="nav-option option4">
						<img src="./images/ship.png" class="nav-img" alt="browse">
						<h4>Shipment </h4>
					</div>
					</a>
                    <a href="admin_returns.php" class="link">
					<div class="nav-option option5">
						<img src="./images/return.png" class="nav-img" alt="settings">
						<h4>Return Items</h4>
					</div>
                    </a>
                    <div></div>
					<div></div>
					<a href="logout.php" class="link">
                        <br>
						<br>
						<br>
						
                    <div class="nav-option logout">                       
                        <img src="./images/out.png" class="nav-img" alt="logout">
                        <h4>Logout</h4>
                    </div>
				</a>
            </nav>
        </div>
        <div class="main">

            <div class="searchbar2">
                <input type="text" name="" id="" placeholder="Search">
                <div class="searchbtn">
                    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180758/Untitled-design-(28).png"
                        class="icn srchicn" alt="search-button">
                </div>
            </div>


            <div class="report-container">
                <div class="report-header">
                    <h1 class="recent-Articles">Add Walk-in Order</h1>
                  
                    

                </div>
    <style>
        .recent-Articles {
    color: #C1AEFF !important;
}
.div1, .div2 {
    display: flex;
    width: 50%; /* You can adjust the width as needed */
    margin-bottom: -5px !important; 
}
.flex-container {

            display: flex;
            justify-content: space-between;
        }

        .flex-col {
            flex: 1;
            margin-right: 20px; /* Adjust margin as needed */
        }

    </style>
    
                       
                
    <form method="post" class="flex flex-row the-form" action="add_walkin_order.php" enctype="multipart/form-data">
    <div class="flex-container">
        <div class="flex-col" style = "margin-top:25px; margin-left: 20px;">
        <div class="txt-field input-wrapper" style="margin-left: 20px; margin-bottom: 10px; position: relative; text-align: center;">
    <label for="Name" style="position: absolute; top: -10px; left: 0; pointer-events: none; transition: 0.3s ease-out;">Name</label>
    <input type="text" name="Name"  required >
</div>

<div class="formbold-mb-5" style="margin-top: 30px; color: #C1AEFF; margin-left: 20px;"> <!-- Adjust margin-left as needed -->

    <label class="formbold-form-label" style="margin-top: 30px; color: #C1AEFF;">Payment Method:</label>
    <br>
    <br>
    <label style="font-weight: normal;" for="credit_card">Paypal</label>
    <input style="color: #C1AEFF;" type="radio" id="credit_card" name="PaymentMethod" value="Paypal" required>
    <br>
    <label for="cash_on_delivery" style="font-weight: normal;">Cash on Delivery</label>
    <input type="radio" id="cash_on_delivery" name="PaymentMethod" value="COD" required>
    <br><br>
</div>

           

<script>
    document.getElementById('Order_Date').addEventListener('change', function () {
        var selectedDate = new Date(this.value);
        var today = new Date();

        // Check if the selected date is not today  
        if (selectedDate.toDateString() !== today.toDateString()) {
            alert('Please select today\'s date.');
            this.value = ''; // Clear the input
        }
    });
</script>


        </div>

        <div class="flex-col upload-form" style="margin-top:45px; margin-right:100px">
        <label style="color: #C1AEFF; font-size: 16px; margin-top: 100px; margin-right: 10px;">Flowers</label>
        <div style="margin-top: 10px; width: 100%; position: relative;">
        <input type="text" id="searchInput" placeholder="Search" style="border: 1.5px solid #C1AEFF; outline: none; width: 100%; padding-right: 30px; background-color: transparent;">


    <img src="./images/search.png" class="nav-img" alt="search" style="width: 30px; height: 30px; cursor: pointer; position: absolute; top: 38%; transform: translateY(-50%); right: 5px; filter: grayscale(100%);">

    </div>
   

        <style>
            
            .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        padding: 10px;
    }
    .card.selected {
    border: 2px solid #C1AEFF; /* Add a border to highlight the selected card */
}


    .card {
      
        width: 300px;
        margin: 10px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s;
        display: flex;
    }

    .flower-details-container {
        flex: 1;
        padding-left: 10px;
      
    }
    .flower-details {
        font-size: 16px;
        color: black;
        margin-top: 45px;
    }

    .flower-image {
        width: 40%;
        height: auto;
        border-radius: 10px;
    }
    .card {
    /* ... your existing styles ... */
    display: flex; /* Make sure the card is initially visible */
}

.card.hide {
    display: none; /* Add a class to hide cards */
}


        .sizes-container {
            display: none; /* Hide sizes container by default */
            margin-top: 10px; /* Adjusted to provide space between image and sizes container */
        }

        .size-option {
    cursor: pointer;
    padding: 3px; /* Adjust padding to make the buttons smaller */
    margin: 5px;
    position: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 10px;
}


        .quantity-input {
            margin-top: 10px;
        }

        #summary {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php
require_once 'db_config.php'; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT
flowers.Flower_Id,
flowers.Flower_Name,
flowers.image_filename,
GROUP_CONCAT(flower_sizes.Size ORDER BY flower_sizes.Size DESC SEPARATOR ', ') AS available_sizes,
GROUP_CONCAT(flower_sizes.Price ORDER BY flower_sizes.Size DESC SEPARATOR ', ') AS available_prices
FROM
flowers
INNER JOIN
flower_sizes ON flowers.Flower_Id = flower_sizes.Flower_Id
GROUP BY
flowers.Flower_Id, flowers.Flower_Name, flowers.image_filename;
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Display flower information and available sizes
        // Inside the while loop where you display flowers
echo "<div class='card' data-flower-id='{$row['Flower_Id']}' data-flower-name='{$row['Flower_Name']}' onclick='showSizes(event, this)'>";

     
        $imageBase64 = base64_encode($row['image_filename']);
        echo '<img src="data:image/jpeg;base64,' . $imageBase64 . '" alt="Flower Image" class="flower-image">';
        echo "<p style='font-size: 14px; margin-top:45px; margin-left:10px'>{$row['Flower_Name']}</p>";
        
        // Check if the keys exist before using them
        if (isset($row['Flower_Id']) && isset($row['available_sizes']) && isset($row['available_prices'])) {
            echo "<div class='sizes-container'>";
            
            // Split sizes and prices into arrays
            $sizes = explode(', ', $row['available_sizes']);
            $prices = explode(', ', $row['available_prices']);

            // Display size options
            foreach ($sizes as $key => $size) {
                echo "<div class='size-option' onclick='setSizeAndPrice(\"{$size}\", \"{$prices[$key]}\", \"{$row['Flower_Id']}\")'>$size</div>";
            }

            echo "</div>";
        } else {
            echo "<div class='sizes-container'>Data not available</div>";
        }

        echo "</div>";
    }
} else {
    echo "No flowers found.";
}

$conn->close();
?>

<!-- Summary outside of cards -->


<!-- JavaScript to handle size selection and display -->
<script>
    function showSizes(event, card) {
        // Hide all sizes containers
        var allSizesContainers = document.querySelectorAll('.sizes-container');
        allSizesContainers.forEach(function(container) {
            container.style.display = 'none';
        });

        // Show sizes container for the clicked card
        var sizesContainer = card.querySelector('.sizes-container');
        sizesContainer.style.display = 'block';
    }

    function setSizeAndPrice(size, price, flowerId, flowerName) {
    // Set the selected size to the input field
    var sizeInput = document.getElementById('selectedSize');
    sizeInput.innerText = size;

    // Set the corresponding price to the input field
    var priceInput = document.getElementById('flowerPrice');
    priceInput.innerText = price;

    // Set the selected flower name to the input field
    var flowerNameInput = document.getElementById('hiddenSelectedFlowerName');
    flowerNameInput.value = flowerName;

    // Set the selected flower ID to the input field
    var flowerIdInput = document.getElementById('hiddenSelectedFlowerId');
    flowerIdInput.value = flowerId;

    // Display selected flower name and ID in the summary
    var flowerInfoDisplay = document.getElementById('selectedFlowerInfoDisplay');
    flowerInfoDisplay.textContent = `(${flowerId}) ${flowerName}`;

    // Calculate total when size and price are set
    calculateTotal();
}

function calculateTotal() {
    var quantity = parseInt(document.getElementById('quantityInput').value) || 1; // Default quantity to 1 if not provided
    var flowerPrice = parseFloat(document.getElementById('flowerPrice').innerText) || 0;

    // Calculate total amount
    var total = quantity * flowerPrice;
  // Get the flower price from the flowerPrice span
  var flowerPrice = parseFloat(document.getElementById('flowerPrice').innerText) || 0;

// Set the corresponding hidden input value for flower price
var hiddenFlowerPriceInput = document.getElementById('hiddenFlowerPrice');
hiddenFlowerPriceInput.value = flowerPrice.toFixed(2);
    // Display total amount in the Total Price input
    var totalPriceInput = document.getElementById('totalPrice');
    totalPriceInput.innerText = total.toFixed(2);

    // Set the corresponding hidden input value for total price
    var hiddenTotalPriceInput = document.getElementById('hiddenTotalPrice');
    hiddenTotalPriceInput.value = total.toFixed(2);

    // Set the corresponding hidden input values for selected size, flower ID, and flower name
    var selectedSize = document.getElementById('selectedSize').innerText.trim();
    var hiddenSelectedSizeInput = document.getElementById('hiddenSelectedSize');
    hiddenSelectedSizeInput.value = selectedSize;

    var hiddenSelectedFlowerIdInput = document.getElementById('hiddenSelectedFlowerId');
    hiddenSelectedFlowerIdInput.value = flowerInfoDisplay.textContent.split(' ')[0].replace('(', '');

    var hiddenSelectedFlowerNameInput = document.getElementById('hiddenSelectedFlowerName');
    hiddenSelectedFlowerNameInput.value = flowerInfoDisplay.textContent.split(' ')[1].replace(')', '');

    
}

    $(document).ready(function () {
        $('#searchInput').on('input', function () {
            var searchTerm = $(this).val().toLowerCase();

            $('.card').each(function () {
                var flowerName = $(this).find('p').text().toLowerCase();

                // Show or hide the card based on the search term
                if (flowerName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>


<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
  $(document).ready(function () {
    $('#searchInput').on('input', function () {
        var searchTerm = $(this).val().toLowerCase();

        $('.card').each(function () {
            var flowerName = $(this).find('p').text().toLowerCase();
            // Toggle the hide class based on the search term
            $(this).toggleClass('hide', !flowerName.includes(searchTerm));
        });
    });
});


</script>

            <!-- Add an onchange event to the quantity input -->
<!-- Add an oninput event to the quantity input -->



        </div>


        
        <div class="flex-col" style = "margin-top:25px">
        <div class="txt-field input-wrapper quantity-input" style="margin-bottom: 10px;">
<input name="Qty" type='number' id='quantityInput' name='quantity' min='1' value='1' oninput='calculateTotal()'>

    <label for="Qty" style="position: absolute; top: -10px; left: 0; pointer-events: none; transition: 0.3s ease-out;">Quantity</label>
</div>




    <!-- Summary section -->
    <div id="summary">
   
    

        <h2 style="color: #C1AEFF;">Order Summary</h2>
       <div>
    <strong style="color: #C1AEFF;">Selected Size:</strong>
    <span id="selectedSize" oninput="updateSize(this)"></span>
</div>
<div>
    <strong style="color: #C1AEFF;">Price:</strong> ₱
    <span id="flowerPrice" oninput="updatePrice(this)"></span>
</div>
<div>
    <strong style="color: #C1AEFF;">Total:</strong> ₱
    <span id="totalPrice" oninput="updateTotal(this)"></span>
</div>

    </div>
    <input  type="hidden" name="Total_Price" id="hiddenTotalPrice">
    <input type="hidden" name="Flower_Id" id="hiddenSelectedFlowerId">
    <input type="hidden" name="Flower_Name" id="hiddenSelectedFlowerName">
    <input  type="hidden" name="Size" id="hiddenSelectedSize">
    <input type="hidden" name="Price" id="hiddenFlowerPrice" >
    </div>

       <!-- <div class="input-wrapper" style="margin-top: 400px; margin-right: 20px;">
                <label for="Price" name="Price" style = "color: #C1AEFF;">Amount</label>
            </div> -->
    </div>
  
    <div>
        <button class="view" type="submit" name="add_walkins" style="margin-left: 20px; color: white; border-radius: 10px; text-align: center; font-size: 15px; line-height: 15px; margin-right: 10px;">Save</button>
        <button class="view" style="color: white;" onclick="window.location.href='admin_orders.php'">Cancel</button>
       
    </div>
</form>


            </div>
        </div>
    </div>
  
    <style>
         .txt-field input[type="date"] {
        color: #C1AEFF; /* Default font color */
    }

    /* Style for the calendar picker indicator */
    .txt-field input[type="date"]::-webkit-calendar-picker-indicator {
        color: #C1AEFF;; /* Color for min and max attributes */
    }
        .the-form div {
 
    margin-bottom: 10px; /* Adjust the gap between form elements as needed */
}
        .input-wrapper {
    position: relative;
    width: 200px; /* Set a width for the input field */
}

/* Style the input field */
.input-wrapper input[type="text"] {
    border: none; /* Remove the border */
    background-color: transparent; /* Set the initial background color to transparent */
    width: 100%;
    padding: 5px; /* Add padding for better appearance */
    transition: background-color 0.3s; /* Smooth transition for background color */
}

/* Change the background color when the input is in focus */
.input-wrapper input[type="text"]:focus {
    background-color: white; /* Change the background color to white on focus */
    outline: none; /* Remove the default focus outline (optional) */
}

        form .txt-field{
    position: relative;
    border-bottom: 2px solid #E1D8FF;
    margin: 40px 0;
}
.txt-field input{
    width: 100%;
    padding: 0 10px;
    height: 40px;
    font-size: 16px;
    border: none;
    background: none;
    outline: none;
}
.txt-field label{
    position: absolute;
    top: 50%;
    left: 5px;
    color: #C1AEFF;
    transform: translateY(-50%);
    font-size: 16px;
    pointer-events: none;
    transition: .5s;
}
.txt-field span::before{
    content: '';
    position: absolute;
    top: 40px;
    left: 0;
    width: 100%;
    height: 2px;
    background: #000000;
    transition: .5s;
}
.txt-field input:focus ~ label,
.txt-field input:valid ~ label{
    top: -5px;
    color: #C1AEFF;
}
/* Set the default text color to black */
input[type="text"] {
    color: black !important;
}

/* Prevent the text color from changing to gray on focus loss */
input[type="text"]:focus {
    color: black !important;
}

.txt-field input:focus ~ span::before,
.txt-field input:valid ~ span:before{
    width: 100%;
}
.pass{
    margin: -5px 0 60px 5px;
    color: #623672;
    cursor: pointer;
}
.color{
    color: #623672;
}
.pass:hover{
    text-decoration: underline;
}
        .small-button {
            font-size: 15px !important; /* You can adjust the font size as needed */  
        }
        .recent-Articles {
            color: #000000;
        }
        .view {
            background-color: #C1AEFF;
            color:#000000
        }
       
        .the-form [type="text"],
        .the-form [type="password"]   {      
            background-color: #C1AEFF;
  
        }
        .report-header {

border-bottom: 2px solid #C1AEFF;
}


    </style>


    <script src="./js/admin.js"></script>
</body>

</html>

