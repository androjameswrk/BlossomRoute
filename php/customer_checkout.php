<?php
require_once '../server.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page or handle unauthorized access
    header("Location: ../login.php");
    exit();
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Database connection parameters
$host = "localhost";
$user = "root";
$pwd = "";
$dbname = "helloworlddb";

// Establish a database connection
$conn = new mysqli($host, $user, $pwd, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the user's first name and last name from the users table
$userNameQuery = "SELECT firstName, lastName FROM users WHERE id = '$user_id'";
$userNameResult = $conn->query($userNameQuery);

// Fetch the most recent location information from the location table
$locationQuery = "SELECT location FROM location WHERE User_Id = '$user_id' ORDER BY location_id DESC LIMIT 1";
$locationResult = $conn->query($locationQuery);

// Check if the query was successful
if ($locationResult->num_rows > 0) {
    $locationRow = $locationResult->fetch_assoc();
    $location = $locationRow['location'];
} else {
    // Set a default location if not found
    $location = 'Default Location';
}


// Check if the query was successful
if ($userNameResult->num_rows > 0) {
    $userNameRow = $userNameResult->fetch_assoc();
    $firstName = $userNameRow['firstName'];
    $lastName = $userNameRow['lastName'];
    // Concatenate first name and last name
    $fullName = $firstName . ' ' . $lastName;
} else {
    // Default to an empty string if the name is not found
    $fullName = '';
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get customer information from the form
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $area = isset($_POST["area"]) ? $_POST["area"] : "";
    $city = isset($_POST["city"]) ? $_POST["city"] : "";
    $state = isset($_POST["state"]) ? $_POST["state"] : "";
    $street = isset($_POST["street"]) ? $_POST["street"] : "";
    $barangay = isset($_POST["barangay"]) ? $_POST["barangay"] : "";
    $postCode = isset($_POST["post-code"]) ? $_POST["post-code"] : "";
    $contactnumber = isset($_POST["contact-number"]) ? $_POST["contact-number"] : "";
    $paymentMethod = isset($_POST["payment_method"]) ? $_POST["payment_method"] : "";

    // Check if all required information is provided
    if (!empty($area) && !empty($city) && !empty($state) && !empty($street) && !empty($barangay) && !empty($postCode) && !empty($paymentMethod) && !empty($contactnumber)) {
        // Check if the user has selected cart items
        $selectedCartItemsQuery = "SELECT * FROM selected_cart_items WHERE User_Id = '$user_id'";
        $selectedCartItemsResult = $conn->query($selectedCartItemsQuery);

        // Check if there are items in the selected_cart_items
        if ($selectedCartItemsResult->num_rows > 0) {
            // Calculate TotalPrice
            $totalPriceQuery = "SELECT SUM(Price) AS TotalPrice FROM selected_cart_items WHERE User_Id = '$user_id'";
            $totalPriceResult = $conn->query($totalPriceQuery);

            if ($totalPriceResult->num_rows > 0) {
                $totalPriceRow = $totalPriceResult->fetch_assoc();
                $totalPrice = $totalPriceRow['TotalPrice'];

                // Concatenate address components
                $address = "$street, $barangay, $area, $city, $state, $postCode";

                // Automatically record the timestamp
                date_default_timezone_set('Asia/Manila');
              // Automatically record the timestamp
$orderDate = date('Y-m-d H:i:s');

// Set the time zone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Set the order date for the order_status table
$orderDateS = date('Y-m-d H:i:s'); // Format: 'YYYY-MM-DD HH:MM:SS'

// Check if the location is set to the default
if ($location === 'Default Location') {
    // Display an alert message
    echo '<script>alert("Error: Please set your location before proceeding to checkout.");</script>';
    
    // Go back to the previous page after displaying the alert
    echo '<script>window.history.back();</script>';
    
    // Highlight the "Get Pin Point Location" button with red color
    echo '<script>
        var getLocationButton = document.getElementById("get_location_button");
        if (getLocationButton) {
            getLocationButton.style.backgroundColor = "#FF0000"; // Set to red
        }
    </script>';
    
    exit();
}

                // Check if the location is set to the default
                if ($location === 'Default Location') {
                    // Display an alert message
                    echo '<script>alert("Error: Please set your location before proceeding to checkout.");</script>';
                    
                    // Go back to the previous page after displaying the alert
                    echo '<script>window.history.back();</script>';
                    
                    // Highlight the "Get Pin Point Location" button with red color
                    echo '<script>
                        var getLocationButton = document.getElementById("get_location_button");
                        if (getLocationButton) {
                            getLocationButton.style.backgroundColor = "#FF0000"; // Set to red
                        }
                    </script>';
                    
                    exit();
                }



                // Insert customer information into the orders table without specifying Order_Status_Id and TotalPrice
                $insertOrderQuery = "INSERT INTO orders (Order_Date, Name, Address, PaymentMethod, User_Id, Order_Status_Id, TotalPrice, Location, Contact) 
                    VALUES ('$orderDate', '$name', '$address', '$paymentMethod', '$user_id', 0, '$totalPrice', '$location', '$contactnumber')";

                if ($conn->query($insertOrderQuery) === TRUE) {
                    $orderId = $conn->insert_id; // Get the ID of the inserted order

                    // Update the order_status record with the obtained Order_Id
                 

                    $updateOrderStatusQuery = "INSERT INTO order_status (Order_Id, New) 
                                            VALUES ('$orderId', '$orderDateS')";
                    

                    if ($conn->query($updateOrderStatusQuery) === TRUE) {
                        // Fetch Order_Status_Id from order_status table based on Order_Id
                        $orderStatusQuery = "SELECT Order_Status_Id FROM order_status WHERE Order_Id = '$orderId'";
                        $orderStatusResult = $conn->query($orderStatusQuery);

                        if ($orderStatusResult->num_rows > 0) {
                            $orderStatusRow = $orderStatusResult->fetch_assoc();
                            $orderStatusId = $orderStatusRow['Order_Status_Id'];

                            // Update Order_Status_Id in the orders table
                            $updateOrderStatusIdQuery = "UPDATE orders SET Order_Status_Id = '$orderStatusId' WHERE Order_Id = '$orderId'";

                            if ($conn->query($updateOrderStatusIdQuery) === TRUE) {
                                // Continue with the rest of the code or redirect

                                // Array to store cart item ids to be deleted from selected_cart_items
                                $cartItemIdsToDelete = [];

                                // Prepare a statement to insert into order_details
                                $insertOrderDetailsStmt = $conn->prepare("INSERT INTO order_details (Order_Id, Flower_Id, Price, Qty, Flower_Size_Id) VALUES (?, ?, ?, ?, ?)");

                                while ($selectedCartItem = $selectedCartItemsResult->fetch_assoc()) {
                                    // Save Flower_Id, Price, Qty, and Flower_Size_Id to order_details
                                    $flowerId = $selectedCartItem['Flower_Id'];
                                    $price = $selectedCartItem['Price'];
                                    $size = $selectedCartItem['Size'];

                                    // Retrieve quantity and flower size id from the selected_cart_items table
                                    $quantityQuery = "
                                        SELECT sc.Qty, fs.Flower_Size_Id 
                                        FROM selected_cart_items sc
                                        JOIN flower_sizes fs ON sc.Flower_Id = fs.Flower_Id AND sc.Size = fs.Size
                                        WHERE sc.User_Id = '$user_id' AND sc.Flower_Id = '$flowerId' AND sc.Size = '$size'
                                    ";

                                    $quantityResult = $conn->query($quantityQuery);

                                    if ($quantityResult->num_rows > 0) {
                                        $quantityRow = $quantityResult->fetch_assoc();
                                        $quantity = $quantityRow['Qty'];
                                        $flowerSizeId = $quantityRow['Flower_Size_Id'];
                                    } else {
                                        // Handle the case when quantity is not found (you may set a default value or show an error)
                                        echo "Error: Quantity not found for User_Id=$user_id, Flower_Id=$flowerId, and Size=$size";
                                        continue; // Skip this iteration and move to the next item
                                    }

                                    // Insert into order_details using prepared statement
                                    $insertOrderDetailsStmt->bind_param("sssss", $orderId, $flowerId, $price, $quantity, $flowerSizeId);
                                    if ($insertOrderDetailsStmt->execute() !== TRUE) {
                                        echo "Error: " . $insertOrderDetailsStmt->error;
                                    }

                                    // Add cart item id to the list
                                    $cartItemIdsToDelete[] = $selectedCartItem['Selected_cart_items_id'];
                                }

                                // Close the prepared statement
                                $insertOrderDetailsStmt->close();

                                // Array to store cart item ids to be deleted from cart_items
                                $cartItemsToDelete = [];

                                // Fetch Flower_Id and Size for each selected cart item
                                foreach ($cartItemIdsToDelete as $selectedCartItemID) {
                                    $fetchCartItemQuery = "SELECT Flower_Id, Size FROM selected_cart_items WHERE Selected_cart_items_id = $selectedCartItemID";
                                    $fetchCartItemResult = $conn->query($fetchCartItemQuery);

                                    if ($fetchCartItemResult->num_rows > 0) {
                                        $cartItemData = $fetchCartItemResult->fetch_assoc();
                                        $flowerId = $cartItemData['Flower_Id'];
                                        $size = $cartItemData['Size'];

                                        // Store Flower_Id and Size in the array for deletion
                                        $cartItemsToDelete[] = [
                                            'Flower_Id' => $flowerId,
                                            'Size' => $size,
                                        ];
                                    }
                                }

                                // Delete items from cart_items based on Flower_Id and Size
                                if (!empty($cartItemsToDelete)) {
                                    foreach ($cartItemsToDelete as $cartItem) {
                                        $deleteCartItemQuery = "DELETE FROM cart_items WHERE User_Id = '$user_id' AND Flower_Id = '{$cartItem['Flower_Id']}' AND Size = '{$cartItem['Size']}'";
                                        $conn->query($deleteCartItemQuery);

                                        // Check for errors in the delete operation
                                        if ($conn->error) {
                                            echo "Error deleting cart items: " . $conn->error;
                                        }
                                    }

                                    // Delete items from selected_cart_items
                                    if (!empty($cartItemIdsToDelete)) {
                                        $deleteSelectedCartItemsQuery = "DELETE FROM selected_cart_items WHERE Selected_cart_items_id IN (" . implode(',', $cartItemIdsToDelete) . ")";
                                        $conn->query($deleteSelectedCartItemsQuery);

                                        // Check for errors in the delete operation
                                        if ($conn->error) {
                                            echo "Error deleting selected cart items: " . $conn->error;
                                        }
                                    }
                                }

                                // Redirect to customer_order.php after processing the order
                                if ($paymentMethod === "Paypal") {
                                    // Set a flag to indicate that the order was placed successfully
                                    $_SESSION['order_placed'] = true;
                                    // Redirect to PayPal checkout
                                    echo '<script>window.location.href = "../index.php";</script>';
                                    exit();
                                } else {
                                    // Set a flag to indicate that the order was placed successfully
                                    $_SESSION['order_placed'] = true;
                                    header("Location: customer_order.php");
                                }
                                exit();
                            } else {
                                echo "Error updating Order_Status_Id in orders table: " . $conn->error;
                            }
                        } else {
                            echo "Error: No matching order status found for Order_Id $orderId";
                        }
                    } else {
                        echo "Error updating order status: " . $conn->error;
                    }
                } else {
                    echo "Error inserting order: " . $conn->error;
                }
            } else {
                echo "Error: Failed to fetch TotalPrice";
            }
        } else {
            echo '<p>No items in the selected cart.</p>';
        }
    } else {
        echo '<p>Please fill up all required information.</p>';
    }
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BlossomRoute - Find your roses</title>

    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="../cs/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
</head>


<body>
    <header class="header" data-header>
        <div style=" background-color: white; border-bottom: 15px solid #C1AEFF; /* Add a purple line at the bottom */" class="header-bottom">
            <div class="container">

                <a href="../php/index.php" class="logo"  style="margin-top: 110px; margin-bottom: -95px;">
                    <img src="../img/homelogo.png" alt="Image Failed to Load" width="240" height="240" >
                </a>

                <nav class="navbar" data-navbar>

                    <div class="navbar-top">
                        <a href="#" class="logo">
                            <img src="./images/logo.png" alt="Image Failed to Load">
                        </a>
                        <button class="nav-close-btn" data-nav-close-btn aria-label="Close Menu">
                            <ion-icon name="close-outline"></ion-icon>
                        </button>
                    </div>

                    <div class="navbar-bottom">
    
                    </div>

                </nav>

                <div class="header-bottom-actions home icon-container custom-icon ">
                    <button aria-label="Search">
                        <ion-icon name="search-outline" class="icongap" ></ion-icon>
                        <span></span>
                    </button>

                    
                    <!-- Your button with notification badge and red circle indicator -->
                    <button id="orders-button" aria-label="Orders" class="notification-icon">
                        <ion-icon name="bag-check-outline" class="icongap"></ion-icon>
                        <span id="notification-badge"></span>
                        <div id="notification-indicator"></div>
                    </button>
                    
                    <button id="cart-button" aria-label="Cart">
                        <ion-icon name="cart-outline" class="icongap"></ion-icon>
                        <span></span>
                    </button>

                    <style>
                        .notification-icon {
                            position: relative;
                        }

                        #notification-indicator {
                            position: absolute;
                            top: 5px; /* Adjust the distance from the top */
                            right: 5px; /* Adjust the distance from the right */
                            width: 12px;
                            height: 12px;
                            background-color: red;
                            border-radius: 50%;
                            display: none; /* Initially hidden */
                        }
/* Style for the dropdown container */
.dropdown {
    position: relative;
    display: inline-block;
}

/* Style for the dropdown content */
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
}

/* Show the dropdown content when the dropdown button is hovered over */
.dropdown:hover .dropdown-content {
    display: block;
}

/* Style for the logout label */
.logout-label {
    margin: 0;
    padding: 10px;
    background-color: #333;
    color: #fff;
    text-align: center;
}

/* Style for the logout button */
.logout-btn {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #333;
    text-align: center;
}

/* Add more styles as needed */

                        
                    </style>


                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            // Find the "Cart" button by its id
                            const cartButton = document.getElementById("cart-button");

                            // Add a click event listener to the "Cart" button
                            cartButton.addEventListener("click", function () {
                                // Redirect to customer_cart.php when the "Cart" button is clicked
                                window.location.href = "customer_cart.php";
                            });

                            // Find the "Orders" button by its id
                            const ordersButton = document.getElementById("orders-button");

                            // Add a click event listener to the "Orders" button
                            ordersButton.addEventListener("click", function () {
                                // Redirect to my_order.php when the "Orders" button is clicked
                                window.location.href = "my_order.php";
                            });
                        });
                    </script>

                    


<div class="dropdown">
    <button cdata-nav-open-btn aria-label="Open Menu">
        <ion-icon name="menu-outline" class="icongap"></ion-icon>
        <span></span>
    </button>
    <div class="dropdown-content">
        <p class="logout-label">Logout</p>
        <a href="../logout.php" class="logout-btn" aria-label="Logout">
            <ion-icon name="log-out-outline"></ion-icon>
        </a>
    </div>
</div>

                </div>

            </div>
        </div>

    </header>

    <main>
        <article>

        <!-- - #Hero -->

            

        <!-- - #About -->

           

            <!--- #Product -->

            
            <!-- 
        - #PROPERTY
      -->
      

      <section>
       



      <div class="formbold-main-wrapper">
    <div class="flex-container">
        <!-- Order Summary -->
        <div class="order-summary">
            <h2 style = "color: #C1AEFF">Order Summary</h2>
            <div class="card-container">
                <?php
                // Fetch items from the selected_cart_items table for the specific user
                $selectedCartItemsQuery = "SELECT * FROM selected_cart_items WHERE User_Id = '$user_id'";
                $selectedCartItemsResult = $conn->query($selectedCartItemsQuery);

                // Check if there are items in the selected_cart_items
                if ($selectedCartItemsResult->num_rows > 0) {
                    // Loop through each item in the selected_cart_items
                    while ($selectedCartItem = $selectedCartItemsResult->fetch_assoc()) {
                        echo '<div class="card">';
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($selectedCartItem['image_filename']) . '" alt="Product Image" class="order-image" style="max-width: 100px; max-height: 100px;">'; // Adjust max-width and max-height as needed
                        echo '<div class="card-content">';
                        echo '<p class="flower-name">' . $selectedCartItem['Flower_Name'] . '</p>';
                        echo '<p class="size">Size: ' . $selectedCartItem['Size'] . '</p>';
                        echo '<p class="size">Quantity: ' . $selectedCartItem['Qty'] . '</p>';
                        echo '<p class="price">Price: ' . $selectedCartItem['Price'] . '</p>'; // Assuming you want to display the price with ? symbol
                        // Add more fields as needed
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No items in the selected cart.</p>';
                }
                ?>
            </div>
        </div>

        <style>
            @import url(
"https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");
    .formbold-main-wrapper {
        display: flex;
        justify-content: space-between;
    }

    .flex-container {
        display: flex;
        
        width: 100%;
    }

    .order-summary,
    .form-section {
        flex: 1;
        font-size:14px;
        font-family: Poppins;
        margin-right: 20px; /* Add margin between Order Summary and Form Section */
    }

    .card {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        background-color: #e6e1f9;
        font-family: Poppins;
        width: 100%; /* Make the card take up the full width */
        margin: 15px 0; /* Add margin between cards */
        text-align: center;
        display: flex;
        align-items: center; /* Center the items horizontally */
    }

    .card img {
        max-width: 100px; /* Adjust the max-width as needed */
        max-height: 100px; /* Adjust the max-height as needed */
        margin-right: 10px; /* Add margin between image and text */
    }

    .card-content {
        flex: 1; /* Take up the remaining space */
        text-align: left; /* Align text to the left */
        padding: 10px;
    }

    /* Style for the formbold-form-wrapper (adjust as needed) */
    .formbold-form-wrapper {
        max-width: 400px; /* Adjust the max-width as needed */
        margin: auto; /* Center the form */
    }
</style>




  <!-- Author: FormBold Team -->
  <!-- Learn More: https://formbold.com -->
 <div class="form-section">
        <div class="formbold-form-wrapper">
            <form action="" method="POST">
                <div class="formbold-mb-5">
                    <label for="name" class="formbold-form-label"> Full Name </label>
                    <input
                        type="hidden"
                        name="actual_name"
                        value="<?php echo $fullName; ?>"
                    />
                    <input
                        type="text"
                        name="name"
                        id="name"
                        placeholder="Full Name"
                        class="formbold-form-input"
                        value="<?php echo isset($_POST['name']) ? $_POST['name'] : $fullName; ?>"
                    />
                </div>
     
      <div class="formbold-mb-5 formbold-pt-3">
        <label class="formbold-form-label">
          Shipping Address
        </label>
        
        <div class="flex flex-wrap formbold--mx-3">
          <div class="w-full sm:w-half formbold-px-3">
            <div class="formbold-mb-5">
              <input
                type="text"
                name="area"
                id="area"
                placeholder="Enter Area"
                class="formbold-form-input"
              />
            </div>
          </div>

          <div class="w-full sm:w-half formbold-px-3">
            <div class="formbold-mb-5">
              <input
                type="text"
                name="city"
                id="city"
                placeholder="Enter City"
                class="formbold-form-input"
              />
            </div>
          </div>

          <div class="w-full sm:w-half formbold-px-3">
            <div class="formbold-mb-5">
              <input
                type="text"
                name="street"
                id="street"
                placeholder="Enter Street"
                class="formbold-form-input"
              />
            </div>
          </div>

          <div class="w-full sm:w-half formbold-px-3">
            <div class="formbold-mb-5">
              <input
                type="text"
                name="barangay"
                id="barangay"
                placeholder="Enter Barangay"
                class="formbold-form-input"
              />
            </div>
          </div>

          <div class="w-full sm:w-half formbold-px-3">
            <div class="formbold-mb-5">
              <input
                type="text"
                name="state"
                id="state"
                placeholder="Enter State"
                class="formbold-form-input"
              />
            </div>
          </div>
          <div class="w-full sm:w-half formbold-px-3">
            <div class="formbold-mb-5">
              <input
                type="text"
                name="post-code"
                id="post-code"
                placeholder="Post Code"
                class="formbold-form-input"
              />
            </div>
          </div>
          <div class="w-full sm:w-half formbold-px-3">
            <div class="formbold-mb-5">
              <input
                type="text"
                name="contact-number"
                id="contact-number"
                placeholder="Contact Number"
                class="formbold-form-input"
              />
            </div>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap formbold--mx-3">
      <div class="w-full sm:w-half formbold-px-3"> 
       <div class="formbold-mb-5">

        <label class="formbold-form-label" style="margin-bottom:-10px">Payment Method:</label>
        <br>
        <label for="credit_card" style = "font-size:15px; color:#9a9a9a;" >Paypal</label>
        <input style = "margin-right: 10px;"type="radio" id="credit_card" name="payment_method" value="Paypal" required>
    
        <label for="cash_on_delivery" style="font-size:15px; color: #9a9a9a">Cash on Delivery</label>
        <input type="radio" id="cash_on_delivery" name="payment_method" value="COD" required>
        <br><br>
    </div>
    </div>
    
    <div class="w-full sm:w-half formbold-px-3"> 
  <!-- Button for Getting Pin Point Location -->
  <button class="formbold-btn2" style="background-color: #C1AEFF;" type="button" id="get_location_button" onclick="openModal()">Get Pin Point Location</button>
</div>

<!-- The Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <!-- Close button -->
    <span class="close" onclick="closeModal()">&times;</span>
    <!-- Iframe for loading current_loc.php -->
    <iframe src="../current_loc.php" width="100%" height="100%" style="border: none;"></iframe>
  </div>
</div>

<script>
    // Function to open the modal
    function openModal() {
        document.getElementById('myModal').style.display = 'block';
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('myModal').style.display = 'none';
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function (event) {
        var modal = document.getElementById('myModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
</script>

<style>
/* Style for the modal */
.modal {
  display: none;
  position: fixed;
  z-index: 9999; /* Set a high z-index value to ensure it appears in front */
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.5);
}

/* Style for the modal content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  margin-top: 5%;
  width: 80%;
  height: 80%;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* Style for the close button */
.close {
  color: #aaa;
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 28px;
  font-weight: bold;
}

/* Close button on hover */
.close:hover {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>




    <!-- Add the following script before </body> -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Add an event listener to the Checkout button
        document.querySelector('.formbold-btn').addEventListener('click', function () {
            // Check if Paypal radio button is selected
            var paypalRadio = document.getElementById('credit_card');

            // Check if the location is set
            var locationIsSet = <?php echo isset($_SESSION['location_set']) && $_SESSION['location_set'] ? 'true' : 'false'; ?>;

            if (!locationIsSet) {
                // Show an error message or handle the case where the location is not set
               
            } else {
                // Continue with the checkout process
                // ...

                // Redirect or submit the form as needed
                document.querySelector('form').submit(); // Change this line based on your form structure
            }
        });
    });
</script>

     

</div>
      <div>
      <button class="formbold-btn" style="background-color: #F97C7C; color: white;">Checkout</button>

      </div>
    </form>
  </div>
</div>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  body {
    font-family: "Inter", Arial, Helvetica, sans-serif;
  }
  .formbold-mb-5 {
    margin-bottom: 20px;
    
  }
  .formbold-pt-3 {
    padding-top: 12px;
  }
  .formbold-main-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px;
  }

  .formbold-form-wrapper {
    margin: 0 auto;
    max-width: 550px; /* Keep the maximum width if needed */
    width: 100%; /* Make it take the whole width */
    background: white;
    padding: 20px; /* Add padding as needed */
}
  .formbold-form-label {
    display: block;
    font-weight: 500;
    font-size: 16px;
    color: #07074d;
    margin-bottom: 12px;
  }
  .formbold-form-label-2 {
    font-weight: 600;
    font-size: 20px;
    margin-bottom: 20px;
  }

  .formbold-form-input {
    width: 100%;
    padding: 12px 24px;
    border-radius: 6px;
    border: 1px solid #e0e0e0;
    background: white;
    font-weight: 500;
    font-size: 16px;
    color: #6b7280;
    outline: none;
    resize: none;
  }
  .formbold-form-input:focus {
    border-color: #6a64f1;
    box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
  }

  .formbold-btn, .formbold-btn2 {
    text-align: center;
    font-size: 16px;
    border-radius: 6px;
    padding: 14px 32px;
    border: none;
    font-weight: 600;
    background-color: #6a64f1;
    color: white;
    width: 100%;
    cursor: pointer;
  }
  .formbold-btn:hover {
    box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
  }

  .formbold--mx-3 {
    margin-left: -12px;
    margin-right: -12px;
  }
  .formbold-px-3 {
    padding-left: 12px;
    padding-right: 12px;
  }
  .flex {
    display: flex;
  }
  .flex-wrap {
    flex-wrap: wrap;
  }
  .w-full {
    width: 100%;
  }
  @media (min-width: 540px) {
    .sm\:w-half {
      width: 50%;
    }
  }
</style>

<style>
    /* Container style for the card */
    .card-container {
        background-color: White;
       
        border-radius: 10px; /* Rounded corners for the card */
        padding: 20px;
        margin: 20px; /* Adjust margin for spacing */
    }

    /* Styles for the existing card elements */
    .order-card {
        
        
        border: 1px solid #C1AEFF;
        display: flex;
        margin-top:30px;
        align-items: center;
        gap: 20px;
        padding: 10px;
    }

    .order-image {
        max-width: 150px; /* Adjust the image size to make it a little bigger */
    }

    .order-content {
        flex: 1;
    }

    .order-title {
        font-size: 18px;
    }

    .order-description {
        font-size: 14px;
    }

    .order-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .add-to-cart-btn,
    .remove-from-cart-btn,
    .checkout-cart-btn,
    .heart-btn {
        background-color: #623672;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>




</section>

          
        </article>
    </main>





    <!-- 
    - #FOOTER
  -->

    





    <!-- 
    - custom js link
  -->
    <script src="./assets/js/script.js"></script>

    <!-- 
    - ionicon link
  -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>