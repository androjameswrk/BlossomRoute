<?php
// Add this line to start the session
require_once '../server.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page or handle unauthorized access
    header("Location: ../login.php");
    exit();
}

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

// Check if the form for proceeding to checkout is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["selected_products"]) && isset($_POST["save_selected"])) {
    // Get the selected product IDs from the form
    $selectedProductIds = $_POST['selected_products'];

    // Initialize an array to store quantities and total prices
    $selectedProductQuantities = array();
    $selectedProductTotalPrices = array();

   // Inside the loop for selected products
foreach ($selectedProductIds as $index => $selectedProductIdSize) {
    // Split the combined value into product ID and size
    list($selectedProductId, $size) = explode('_', $selectedProductIdSize);

    // Get the quantity for each product
    $quantityKey = 'quantity_' . $selectedProductIdSize;
    $quantity = isset($_POST[$quantityKey]) ? $_POST[$quantityKey] : 0;

    // Check if User_Id is set and not null
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Get the price, Flower_Name, and cart_items_id for each product based on size
    $stmtPrice = $conn->prepare("SELECT Price, Flower_Name, cart_items_id FROM cart_items WHERE Flower_Id = ? AND Size = ?");
    $stmtPrice->bind_param("is", $selectedProductId, $size);
    $stmtPrice->execute();
    $resultPrice = $stmtPrice->get_result();
    $rowPrice = $resultPrice->fetch_assoc();
    $price = $rowPrice['Price'];
    $flowerName = $rowPrice['Flower_Name'];
    $cartItemId = $rowPrice['cart_items_id'];

    // Calculate the total price for each selected item
    $totalPrice = $quantity * $price;

    // Insert selected item into selected_cart_items table
    $insertStmt = $conn->prepare("
    INSERT INTO selected_cart_items (cart_items_id, User_Id, Flower_Id, Flower_Name, Size, Qty, Price, image_filename) 
    VALUES (?, ?, ?, ?, ?, ?, ?, (SELECT image_filename FROM cart_items WHERE Flower_Id = ? AND Size = ? LIMIT 1))
");

// Bind parameters
$insertStmt->bind_param("iiissdiss", $cartItemId, $userId, $selectedProductId, $flowerName, $size, $quantity, $totalPrice, $selectedProductId, $size);

// Execute the statement
if (!$insertStmt->execute()) {
    // Handle the error (e.g., log, display an error message)
    echo "Error: " . $insertStmt->error;
    exit(); // or handle the error as appropriate
}

    // ... (any additional code within the loop)
}


    // Set a session flag to indicate that the order was placed successfully
    $_SESSION['order_placed'] = true;

    // Redirect to the checkout page or any other page as needed
    header("Location: customer_checkout.php");
    exit; // Ensure that the script stops execution after redirection
}

// Fetch items from the cart_items table based on the user's cart
$userId = $_SESSION['user_id'];  // Replace with the actual User_Id
$sql = "SELECT * FROM cart_items WHERE User_Id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the order was placed successfully
if (isset($_SESSION['order_placed']) && $_SESSION['order_placed']) {
    // Delete selected cart items
    $deleteSelectedCartItemsQuery = "DELETE FROM selected_cart_items WHERE User_Id = '$userId'";
    $conn->query($deleteSelectedCartItemsQuery);

    // Unset the order_placed flag
    unset($_SESSION['order_placed']);
}

// Close the database connection
$conn->close();
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
                        <!-- <ul class="navbar-list home">
                            <li>
                                <a href="index.php" class="navbar-link" data-nav-link>Home</a>
                            </li>
                            <li>
                                <a href="#about" class="navbar-link" data-nav-link>About</a>
                            </li>
                            <li>
                                <a href="#service" class="navbar-link" data-nav-link>Features</a>
                            </li>
                            <li>
                                <a href="#property" class="navbar-link" data-nav-link>Products</a>
                            </li>                   
                            <li>
                                <a href="#contact" class="navbar-link" data-nav-link>Contact</a>
                            </li>

                        </ul> -->
                    </div>

                </nav>

                <div class="header-bottom-actions home icon-container custom-icon ">
                   

                   

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

      <section>
      <form id="checkout-form" method="POST" action="customer_cart.php">
    <div class="order-card-container" id="cart-items">
        <?php
        while ($row = $result->fetch_assoc()) {
            // Fetch data from the current row
            $productId = $row['Flower_Id'];
            $productName = $row['Flower_Name'];
            $productSize = $row['Size'];
            $productPrice = $row['Price'];
            $productImage = base64_encode($row['image_filename']);  // Assuming the image is stored as binary data

            ?>
            <!-- Inside the loop for displaying cart items -->
            <div class="order-card" style="margin-top: 50px">
                <!-- Checkbox for Product Selection -->
                <input style="margin-left: 35px;" type="checkbox" id="checkbox_<?php echo $productId . '_' . $productSize; ?>" name="selected_products[]" value="<?php echo $productId . '_' . $productSize; ?>">

                <!-- Product Image -->
                <img src="data:image/jpeg;base64,<?php echo $productImage; ?>" alt="Product Image" class="order-image">

                <div class="order-content">
                    <!-- Product Details -->
                    <h3 class="order-title"><?php echo $productName; ?></h3>
                    <p class="order-details">
                        <span class="order-price">Price: <?= $productPrice ?></span>,
                        <span class="order-size">Size: <?= $productSize ?></span>
                    </p>

                    <!-- Quantity input field with + and - buttons -->
                    <div class="quantity-control view" style="font-size: 13px">
                        <button type="button" class="quantity-button decrement" onclick="adjustQuantity('<?php echo $productId; ?>', '<?php echo $productSize; ?>', -1, event)">-</button>
                        <input type="number" id="quantity_<?php echo $productId . '_' . $productSize; ?>" name="quantity_<?php echo $productId . '_' . $productSize; ?>" value="<?php echo $row['Qty']; ?>" min="1" readonly>
                        <button type="button" class="quantity-button increment" onclick="adjustQuantity('<?php echo $productId; ?>', '<?php echo $productSize; ?>', 1, event)">+</button>
                    </div>

                    <!-- Move the Remove button below the quantity input -->
                    <div class="order-buttons">
                        <button style="font-size: 13px; margin-top: 10px; margin-left: 20px; background-color:#F97C7C; padding-left:20px; padding-right:20px; padding-top:10px; padding-bottom:10px"
                            class="remove-from-cart-btn" onclick="removeFromCart('<?php echo $productId; ?>', '<?php echo $productSize; ?>')">
                            <img src="../img/delete.png" alt="Remove" style="width: 16px; height: 16px;">
                        </button>
                    </div>

                    <!-- Hidden input fields for size and price -->
                    <input type="hidden" id="size_<?php echo $productId; ?>" value="<?php echo $productSize; ?>">
                    <input type="hidden" id="price_<?php echo $productId . '_' . $productSize; ?>" value="<?php echo $productPrice; ?>">
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <div class="side-form">
        <label for="order-summary"></label>
        <br>

        <!-- Display order summary information here -->
        <p style="margin-top:80px; text-align:left; display: inline;">Number of items: <span id="item-count"></span></p>
        <p style="text-align:left; margin-top:10px; color: #F97C7C; display: block;">Total Price: <span id="total-price"></span></p>

        <br>

        <br>
        <br>
        <br>
        <br>

        <!-- Add this button after the loop of displaying cart items -->
        <button style = "  background-color: #C1AEFF; margin-left:10px" type="submit" class="checkout-cart-btn" name="save_selected">Proceed to Checkout</button>
    </div>


<script>
   // Function to update the order summary based on checked items
function updateOrderSummary() {
    // Get all checkboxes that are checked
    var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');

    // Calculate and display order summary
    var itemCount = checkboxes.length;
    var totalPrice = 0;

    checkboxes.forEach(checkbox => {
        var productIdAndSize = checkbox.id.replace('checkbox_', '');
        var [productId, selectedSize] = productIdAndSize.split('_');
        var quantity = document.getElementById('quantity_' + productId + '_' + selectedSize).value; // Updated line

        // Retrieve the price based on both product ID and size
        var price = parseFloat(document.getElementById('price_' + productId + '_' + selectedSize).value);

        totalPrice += quantity * price;
    });

    // Display order summary
    document.getElementById('item-count').style.display = 'inline';
    document.getElementById('item-count').style.marginBottom = '100px'; // Adjust the margin value as needed

    document.getElementById('total-price').style.display = 'inline';
    document.getElementById('item-count').innerText = '' + itemCount;
    document.getElementById('total-price').innerText = '₱' + totalPrice.toFixed(2);
}

// Add the event listener for checkbox changes
document.querySelectorAll('input[type="checkbox"]').forEach(function (checkbox) {
    checkbox.addEventListener('change', updateOrderSummary);
});

// Update the order summary when the page loads
updateOrderSummary();

function adjustQuantity(productId, size, change, event) {
    var quantityInput = document.getElementById("quantity_" + productId + '_' + size); // Updated line
    var currentQuantity = parseInt(quantityInput.value);

    if (!isNaN(currentQuantity)) {
        var newQuantity = currentQuantity + change;
        if (newQuantity >= 1) {
            quantityInput.value = newQuantity;

            // You can add logic here to update the server with the new quantity using AJAX if needed

            // Prevent the default behavior of the button click
            event.preventDefault();

            // Update the order summary after adjusting the quantity
            updateOrderSummary();
        }
    }
}
function removeFromCart(productId, productSize) {
        // Send an AJAX request to the server to remove the item
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'remove_from_cart.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Update the page or handle the response as needed
                console.log(xhr.responseText);
                // For example, you can reload the page after removal
                location.reload();
            }
        };
        xhr.send('productId=' + productId + '&productSize=' + productSize);
    }

</script>


</form>

<style>
     @import url(
"https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");
    .side-form {
    position: fixed;
    font-family:Poppins;
    top: 50%;
    right: 150px;
    bottom: auto; /* Resetting 'bottom' to 'auto' to allow 'top' to take effect */
    transform: translateY(-50%);
    background-color: #fff;
    padding-left: 100px; 
    padding-right: 100px;
    padding-top: 10px;
    padding-bottom: 30px; /* In /* Increased padding for all sides */
    border: 1px solid #ddd;
    border-radius: 5px;
   
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
   margin-top: 55px;
}

.side-form label {
    display: block;
    margin-bottom: 10px;
}

.side-form button {
    background-color: #623672;
    color: #fff;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.quantity-control input {
    font-size: 12px;
    width: 6%;
    text-align: center;
    border: none; /* Center the text in the input */
}




    /* Container style for the card */
    .card-container {
        background-color: White;
      
        border-radius: 10px; /* Rounded corners for the card */
        padding: 20px;
        margin: 20px; /* Adjust margin for spacing */
    }

    /* Styles for the existing card elements */
    .order-card {
        font-family:Poppins;
        display: flex;
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
    .order-details {
    display: flex;
   font-size: 14px;
    margin-top: 5px; /* Adjust the margin as needed */
}



    .order-title {
        font-size: 16px;
    }

    .order-description {
        font-size: 13px;
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
        background-color: #C1AEFF;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 15px;
    }
</style>
<style>


.card button:hover {
  opacity: 0.7;
}
               
.product-quantity {
  border: none;
  outline: none;
  text-align: center;
  font-size: 14px;
  padding: 4px;
  margin: 0;
  width: 30px;
  display: inline-block; /* Display the input as an inline-block element */
}

/* Style the decrement button */
.quantity-button.decrement {
  background: none;
  border: none;
  padding: 0;
  margin: 0;
  cursor: pointer;
  font-size: 16px;
  margin-right: 10px;
  display: inline-block; /* Display the decrement button as an inline-block element */
}

/* Style the increment button */
.quantity-button.increment {
  background: none;
  border: none;
  padding: 0;
  margin: 0;
  cursor: pointer;
  font-size: 16px;
  margin-left: -6px;
  display: inline-block; /* Display the increment button as an inline-block element */
}

/* Optionally, you can further adjust styles as needed */


.checkout-cart-btn {
  background-color: #C1AEFF;
  color: white;
  float: right; /* Float the button to the right */
  margin-right: 10px; /* Add margin for spacing */
}
.footer {
  position: fixed;
  left: 0;
  bottom: 0;
  padding-top:10px;
  padding-right:50px;
  width: 100%;
  height: 40px;
  background-color: #C1AEFF;
  color: white;
  text-align: right;
  font-size: 16px;
  
}
/* Add this CSS to remove the outline and adjust positioning */
/* Add this CSS to remove the outline and adjust positioning */
.order-quantity .quantity-input {
    outline: none; /* Remove the outline from the input */
    width: 30px; /* Adjust the width as needed */
    text-align: center; /* Center-align the text in the input */
    margin: 0 2px; /* Reduce margin for spacing */
    border: none;
    font-size: 13px !important;
    font-family: Poppins;
}

/* Optionally, you can adjust the styles for the plus and minus buttons */
.quantity-button {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    font-size: 13px; /* Make the buttons smaller */
    margin: 0;
    font-family: Poppins;
}

/* Adjust the styles for the minus button */
.quantity-button.decrement {
    margin-right: 2px;
    font-family: Poppins; /* Reduce the margin between buttons */
}

/* Adjust the styles for the plus button */
.quantity-button.increment {
    margin-left: -2px; 
    font-family: Poppins;/* Reduce the margin between buttons */
}


</style>




<style>
    /* Add or modify the following styles in your existing style section */
    .quantity-control {
        display: flex;
        align-items: center;
    }

    .quantity-button {
        background: none;
        border: none;
        padding: 5px;
        cursor: pointer;
        font-size: 16px;
        margin: 0;
    }

    .quantity-input {
        width: 20px; /* Adjust the width as needed */
        text-align: center;
        margin: 0 5px; /* Adjust the margin for spacing */
        border: 1px solid #ddd;
        font-size: 14px;
    }
</style>








</div>

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

    <script>
        function incrementQuantity(input) {
            var currentValue = parseInt(input.nextElementSibling.value);
            if (!isNaN(currentValue) && currentValue < 99) {
                input.nextElementSibling.value = currentValue + 1;
            }
        }

        function decrementQuantity(input) {
            var currentValue = parseInt(input.previousElementSibling.value);
            if (!isNaN(currentValue) && currentValue > 1) {
                input.previousElementSibling.value = currentValue - 1;
            }
        }
    </script>

</body>

</html>