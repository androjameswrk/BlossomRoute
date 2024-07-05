<?php
require_once '../server.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: ../login.php");
    exit(); // Ensure that the script stops execution after the redirect
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

// Check if the order_placed flag is set
if (isset($_SESSION['order_placed']) && $_SESSION['order_placed'] === true) {
    // Display the alert
    echo '<script>alert("Order placed successfully!");</script>';

    // Reset the order_placed flag
    unset($_SESSION['order_placed']);
}


// Function to get the cart_id for a user
function getCartId($userId) {
    global $conn;
    $sql = "SELECT Cart_Id FROM cart WHERE User_Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Cart_Id'];
    }

    return null; // Return null if no cart is found
}

function addOrUpdateCartItem($cartId, $productId, $size, $userId) {
    global $conn;

    // Check if the same product with the same size is already in the cart
    $existingProductQuery = "SELECT * FROM cart_items WHERE Cart_Id = ? AND Flower_Id = ? AND Size = ? AND User_Id = ?";
    $existingProductStmt = $conn->prepare($existingProductQuery);
    $existingProductStmt->bind_param("iisi", $cartId, $productId, $size, $userId);
    $existingProductStmt->execute();
    $existingProductResult = $existingProductStmt->get_result();

    if ($existingProductResult->num_rows > 0) {
        // Product already exists in the cart, update the quantity
        $updateQuantitySql = "UPDATE cart_items SET Qty = Qty + 1 WHERE Cart_Id = ? AND Flower_Id = ? AND Size = ? AND User_Id = ?";
        $updateQuantityStmt = $conn->prepare($updateQuantitySql);
        $updateQuantityStmt->bind_param("iisi", $cartId, $productId, $size, $userId);

        if ($updateQuantityStmt->execute()) {
            echo "Product quantity updated in cart!";
        } else {
            echo "Error updating product quantity in cart: " . $updateQuantityStmt->error;
        }
    } else {
        // Product not in the cart, insert a new row
        $productDetailsSql = "SELECT Flower_Name, Price, image_filename FROM flowers f JOIN flower_sizes fs ON f.Flower_Id = fs.Flower_Id WHERE f.Flower_Id = ? AND fs.Size = ?";
        $productDetailsStmt = $conn->prepare($productDetailsSql);
        $productDetailsStmt->bind_param("is", $productId, $size);
        $productDetailsStmt->execute();
        $productDetailsResult = $productDetailsStmt->get_result();

        if ($productDetailsResult->num_rows > 0) {
            $productDetails = $productDetailsResult->fetch_assoc();
            $flowerName = $productDetails['Flower_Name'];
            $price = $productDetails['Price'];
            $imageFilename = $productDetails['image_filename'];

            // Insert a new row into cart_items
            $insertSql = "INSERT INTO cart_items (Cart_Id, Flower_Id, Flower_Name, Size, Qty, Price, image_filename, User_Id) VALUES (?, ?, ?, ?, 1, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("iissdsi", $cartId, $productId, $flowerName, $size, $price, $imageFilename, $userId);

            if ($insertStmt->execute()) {
                echo "Product added to cart successfully!";
            } else {
                echo "Error adding product to cart: " . $insertStmt->error;
            }
        } else {
            echo "Product details not found or size mismatch.";
        }
    }
}

// Function to create a new cart for a user and return the cart_id
function createCart($userId) {
    global $conn;
    $sql = "INSERT INTO cart (User_Id) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        return $conn->insert_id; // Return the last inserted cart_id
    }

    return null; // Return null if the cart creation fails
}

// Check if the add-to-cart button is clicked
if (isset($_POST['add_to_cart'])) {
    // Assuming you have a User_Id (replace with the actual method you use to get the user ID)
    $userId = $_SESSION['user_id']; // Replace with the actual User_Id

    // Check if the user has an existing cart
    $cartId = getCartId($userId);

    //If the user doesn't have a cart, create a new one
    if (!$cartId) {
     $cartId = createCart($userId);
    }

    // Fetch product details from the flowers and flower_sizes tables
    $productId = $_POST['product_id']; // Assuming the form field is 'product_id'
    $size = $_POST['size']; // Assuming the form field is 'size';

    // Add or update the item in the cart
    addOrUpdateCartItem($cartId, $productId, $size, $userId);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Your existing head content -->
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
                    <button aria-label="Search">
                        <ion-icon name="search-outline" class="icongap" ></ion-icon>
                        <span></span>
                    </button>

                    <script>
function updateNotificationBadge(statusUpdates) {
    const badge = document.getElementById('notification-badge');
    const indicator = document.getElementById('notification-indicator');

    if (statusUpdates > 0) {
        badge.textContent = statusUpdates; // Set the badge text to the count of status updates
        indicator.style.display = 'block'; // Show the red circle indicator
    } else {
        badge.textContent = ''; // Clear the badge text
        indicator.style.display = 'none'; // Hide the red circle indicator
    }
}

function checkOrderStatus() {
    // Perform AJAX request to the PHP script
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'check_order_status.php', true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            updateNotificationBadge(response.statusUpdates); // Update the notification badge for status updates
        }
    };

    xhr.send();
}

// Check for updates every 5 seconds (adjust as needed)
setInterval(checkOrderStatus, 5000);
</script>
                    

<button id="orders-button" aria-label="Orders" onclick="checkOrderStatus()">
    <ion-icon name="bag-check-outline" class="icongap"></ion-icon>
    <span id="notification-badge"></span>
    <div id="notification-indicator"></div>
</button>

            
                    
                    
                    <button id="cart-button" aria-label="Cart">
                        <ion-icon name="cart-outline" class="icongap"></ion-icon>
                        <span></span>
                    </button>

                    <style>
                       


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

<style>
    @import url(
"https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

    /* Container style for the card */


    .card-container {
        margin-top:50px;
        margin-left:80px;
        display: flex;
        flex-wrap: wrap;
      
    }

    .order-card {
        border: 1px solid #C1AEFF;
        max-width: 200px; /* Adjust the max-width for the card */
        margin: 10px;
        text-align: center;
        flex: 0 0 calc(33.33% - 20px);
        box-sizing: border-box;
        font-family: Poppins;
        background-color: 	#e6e1f9;
        border-radius: 10px;
        padding: 15px; /* Adjust padding for a smaller card */
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .order-image {
        max-width: 150px; /* Adjust the max-width for the image */
    }

    .order-content {
        flex: 1;
        margin-top: 10px;
        width: 100%;
    }

    .order-title {
        font-size: 14px; /* Adjust the font size for the title */
        margin-bottom: 5px;
    }

    .order-description {
        font-size: 12px; /* Adjust the font size for the description */
        color: grey;
    }

    .order-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
    }

    .add-to-cart-btn,
    .heart-btn {
        background-color: #C1AEFF;
        color: white;
        padding: 5px 8px; /* Adjust padding for smaller buttons */
        border: none;
        border-radius: 5px;
        margin-right: 8px;
        cursor: pointer;
        font-size: 12px; /* Adjust the font size for the buttons */
    }
</style>





<div class="card-container">
    <?php
    $sql = "SELECT f.Flower_Id, f.Flower_Name, fs.Size, fs.Price, f.image_filename
            FROM flowers f
            JOIN flower_sizes fs ON f.Flower_Id = fs.Flower_Id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="order-card">';
            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image_filename']) . '" alt="Product Image" class="order-image">';
            echo '<div class="order-content">';
            echo '<h3 class="order-title">' . $row['Flower_Name'] . '</h3>';
            echo '<p class="order-description">Size: ' . $row['Size'] . ', Price: ₱' . $row['Price'] . '</p>';
            echo '<div class="order-buttons">';
            echo '<form method="post" action="">';
            echo '<input type="hidden" name="product_id" value="' . $row['Flower_Id'] . '">';
            echo '<input type="hidden" name="size" value="' . $row['Size'] . '">';
            echo '<button type="submit" class="add-to-cart-btn" name="add_to_cart">Add to Cart</button>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "0 results";
    }
    ?>
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
    <!-- Add this script tag in the head of your HTML document -->



    <!-- 
    - ionicon link
  -->
  
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <!-- Add this script at the end of your HTML document, right before the closing </body> tag -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>





</body>
</html>