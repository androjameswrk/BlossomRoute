<?php

//customer_return.php
session_start();

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'helloworlddb');

function insertRefund($db, $orderDetailsId, $quantity, $reason) {
    $orderDetailsId = mysqli_real_escape_string($db, $orderDetailsId);
    $quantity = mysqli_real_escape_string($db, $quantity);
    $reason = mysqli_real_escape_string($db, $reason);

    // Fetch order details
    $orderQuery = "SELECT od.Order_Id, od.Flower_Id, od.Qty, od.Price, fs.Size, o.User_Id, o.Address, o.Location
                   FROM order_details od
                   INNER JOIN flower_sizes fs ON od.Flower_Size_Id = fs.Flower_Size_Id
                   INNER JOIN orders o ON od.Order_Id = o.Order_Id
                   WHERE od.Order_Details_Id = '$orderDetailsId'";
    $orderResult = mysqli_query($db, $orderQuery);

    if ($orderResult) {
        $orderInfo = mysqli_fetch_assoc($orderResult);

        // Calculate refund amount
        $flowerId = $orderInfo['Flower_Id'];
        $flowerSize = $orderInfo['Size'];
        
        // Assuming Flower_Price is available in the Flower_Sizes table
        $flowerPriceQuery = "SELECT Price FROM flower_sizes WHERE Flower_Id = '$flowerId' AND Size = '$flowerSize'";
        $flowerPriceResult = mysqli_query($db, $flowerPriceQuery);

        if ($flowerPriceResult) {
            $flowerPrice = mysqli_fetch_assoc($flowerPriceResult)['Price'];
            $refundAmount = $quantity * $flowerPrice;

            // Insert refund data into the refunds table
            $insertRefundQuery = "INSERT INTO refunds (Order_Details_Id, User_Id, Address, Location, Refund_Amount, Qty, Reason, Status)
                                  VALUES ('$orderDetailsId', '{$orderInfo['User_Id']}', '{$orderInfo['Address']}', '{$orderInfo['Location']}', '$refundAmount', '$quantity', '$reason', '')";
            mysqli_query($db, $insertRefundQuery);
        }
    }
}





function insertReplacement($db, $orderDetailsId, $reason, $quantity) {
    $orderDetailsId = mysqli_real_escape_string($db, $orderDetailsId);
    $reason = mysqli_real_escape_string($db, $reason);
    $quantity = mysqli_real_escape_string($db, $quantity);

    // Fetch order details and user information
    $orderQuery = "SELECT o.Order_Id, o.User_Id, o.Address, o.Location, od.Qty, od.Price
                   FROM orders o
                   INNER JOIN order_details od ON o.Order_Id = od.Order_Id
                   WHERE od.Order_Details_Id = '$orderDetailsId'";
    $orderResult = mysqli_query($db, $orderQuery);

    if ($orderResult) {
        $orderInfo = mysqli_fetch_assoc($orderResult);

        // Insert replacement data into the replacement table
        $query = "INSERT INTO replacement (Order_Details_Id, User_Id, Reason, Qty, Address, Location, Status) 
                  VALUES ('$orderDetailsId', '{$orderInfo['User_Id']}', '$reason', '$quantity', '{$orderInfo['Address']}', '{$orderInfo['Location']}', '')";
        mysqli_query($db, $query);
    }
}

// Check if the refund/replace form is submitted
if (isset($_POST['add_return'])) {
    // Check if the cancel button is clicked
    if (isset($_POST['cancel'])) {
        // Handle cancellation (you can redirect or perform other actions)
        header("Location: customer_return.php");
        exit();
    }

    // Retrieve the form input values
    $orderDetailsId = $_POST['orderDetailsId'];
    $quantity = $_POST['quantity'];
    $reason = $_POST['reason'];

    // Check if the payment method is set
    if (isset($_POST['payment_method'])) {
        $paymentMethod = $_POST['payment_method'];

        if ($paymentMethod == 'Refund') {
            insertRefund($db, $orderDetailsId, $quantity, $reason);

            // Display an alert for Refund using JavaScript
            echo '<script>alert("Refund processed successfully!");</script>';
        } elseif ($paymentMethod == 'Replacement') {
            insertReplacement($db, $orderDetailsId, $reason, $quantity);

            // Display an alert for Replacement using JavaScript
            echo '<script>alert("Replacement processed successfully!");</script>';
        }

        // Delay the redirection by a few seconds (adjust the timeout as needed)
        echo '<script>
                setTimeout(function() {
                    window.location.href = "customer_return.php";
                }, 3000); // 3000 milliseconds (3 seconds)
              </script>';
        exit();
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
 

    <main>
        <article>

      <section>
        <div class="formbold-main-wrapper">

 
  <div class="formbold-form-wrapper">
  <form action="customer_return.php" method="post" style="padding-top: 30px;" enctype="multipart/form-data">
    <div class="formbold-mb-5">
        <label for="name" class="formbold-form-label">Full Name</label>
        <input
            type="text"
            name="name"
            id="name"
            placeholder="Full Name"
            class="formbold-form-input"
        />
    </div>

    <div class="flex flex-wrap formbold--mx-3">
    <div class="w-full sm:w-half formbold-px-3">
    <div class="formbold-mb-5 w-full">


    <label for="order" class="formbold-form-label">Order</label>
<select name="orderDetailsId" id="orderDetailsId" class="formbold-form-input">
    <?php
    $selectedOrderId = $_GET['order_id'] ?? '';

    // Fetch flower details from the database based on the selected Order_Id
    $orderDetailsQuery = "SELECT od.order_details_id, f.Flower_Name, fs.Size
                        FROM order_details od
                        INNER JOIN flowers f ON od.Flower_Id = f.Flower_Id
                        INNER JOIN flower_sizes fs ON od.Flower_Size_Id = fs.Flower_Size_Id
                        WHERE od.Order_Id = '$selectedOrderId'"; // Filter by Order_Id

    $orderDetailsResult = mysqli_query($db, $orderDetailsQuery);

    // Check if the query was successful
    if ($orderDetailsResult) {
        // Loop through the results and populate the dropdown
        while ($row = mysqli_fetch_assoc($orderDetailsResult)) {
            $orderDetailsId = $row['order_details_id'];
            $flowerName = $row['Flower_Name'];
            $flowerSize = $row['Size'];

            // Display concatenated flower name and size as the dropdown option
            echo "<option value='$orderDetailsId'>$flowerName - $flowerSize</option>";
        }
    }
    ?>
</select>

        </select>
    </div>
</div>
<div class="w-full sm:w-half formbold-px-3">
    <div class="formbold-mb-5">
        
    <label for="quantity" class="formbold-form-label">Quantity</label>
<input
    type="number"
    name="quantity"
    id="quantity"
    placeholder="Quantity"
    class="formbold-form-input"
    oninput="validateQuantity(this)"
    <?php
    // Retrieve the order_id from the URL
    $order_id = $_GET['order_id'] ?? '';

    // Fetch the maximum quantity for the specific order_id from the database
    $maxQuantityQuery = "SELECT MAX(Qty) AS max_quantity FROM order_details WHERE Order_Id = '$order_id'";
    $maxQuantityResult = mysqli_query($db, $maxQuantityQuery);

    // Check if the query was successful
    if ($maxQuantityResult) {
        $maxQuantityRow = mysqli_fetch_assoc($maxQuantityResult);
        $maxQuantity = $maxQuantityRow['max_quantity'];

        // Set the maximum attribute for the input field
        echo "max='$maxQuantity'";
    }
    ?>
/>
    </div>

<script>
    function validateQuantity(input) {
        // Get the entered value
        let quantity = input.value;

        // Remove leading zeros
        quantity = quantity.replace(/^0+/, '');

        // Check if the value is a positive integer
        if (!/^[1-9]\d*$/.test(quantity)) {
            // If not a positive integer, set the value to an empty string
            input.value = '';
        }
    }
</script>

        </div>
    </div>
    <div class="formbold-mb-5">
        <label for="reason" class="formbold-form-label">Reason of Return</label>
        <input
            type="text"
            name="reason"
            id="reason"
            placeholder="Reason of Return"
            class="formbold-form-input"
        />
    </div>

    <div class="w-full sm:w-half formbold-px-3" style="margin-left: -10px">
        <div class="formbold-mb-5">
            <label class="formbold-form-label">Return Method:</label>
            <br>
            <label style="margin-left: 5px" for="replacement">Replacement</label>
            <input type="radio" id="replacement" name="payment_method" value="Replacement" required>

            <label for="refund">Refund (COD)</label>
            <input type="radio" id="refund" name="payment_method" value="Refund" required>
            <br><br>
        </div>
    </div>

    <div>
    <button class="formbold-btn" style="background-color: #C1AEFF; color: white;" type="submit" name="add_return">Save</button>
        <button class="formbold-btn" style="background-color: #F97C7C; color: white; margin-top: 10px" name="cancel">Cancel</button>
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
    max-width: 550px;
    width: 100%;
    background: white;
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

  .formbold-btn {
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
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 10px;
    }

    .order-image {
        max-width: 180px; /* Adjust the image size to make it a little bigger */
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Retrieve cart data from localStorage
        const cartData = JSON.parse(localStorage.getItem("cart")) || [];

        // Select the cart container
        const cartContainer = document.querySelector(".card-container");

        // Function to update the quantity in the cart and local storage
        function updateQuantity(productId, change) {
            const productIndex = cartData.findIndex((product) => product.id === productId);
            if (productIndex !== -1) {
                const currentQuantity = cartData[productIndex].quantity || 1;
                const newQuantity = currentQuantity + change;
                if (newQuantity > 0) {
                    cartData[productIndex].quantity = newQuantity;
                    localStorage.setItem("cart", JSON.stringify(cartData));
                    renderCart();
                }
            }
        }

        // Function to render the cart
        function renderCart() {
            cartContainer.innerHTML = ""; // Clear the cart container

            // Loop through the cart data and create card elements
            cartData.forEach((product) => {
                const card = document.createElement("div");
                card.classList.add("order-card");

                // Create card content using the product information
                card.innerHTML = `
                    <img src="${product.image}" alt="Product Image" class="order-image">
                    <div class="order-content">
                        <h3 class "order-title">${product.name}</h3>
                        <p class="order-description">${product.description}</p>
                        <div class="order-quantity">
                            <button class="quantity-button decrement" data-product-id="${product.id}">-</button>
                            <input type="number" class="product-quantity" value="${product.quantity || 1}" readonly>
                            <button class="quantity-button increment" data-product-id="${product.id}">+</button>
                        </div>
                        <div class="order-buttons">
                            <button class="remove-from-cart-btn" data-product-id="${product.id}">Remove from Cart</button>
                        </div>
                    </div>
                `;

                // Append the card to the cart container
                cartContainer.appendChild(card);
            });

            // Attach event listeners to quantity buttons
            const decrementButtons = document.querySelectorAll(".quantity-button.decrement");
            const incrementButtons = document.querySelectorAll(".quantity-button.increment");

            decrementButtons.forEach((button) => {
                button.addEventListener("click", function () {
                    const productId = button.getAttribute("data-product-id");
                    updateQuantity(productId, -1); // Decrease quantity by 1
                });
            });

            incrementButtons.forEach((button) => {
                button.addEventListener("click", function () {
                    const productId = button.getAttribute("data-product-id");
                    updateQuantity(productId, 1); // Increase quantity by 1
                });
            });
        }

        // Initial rendering of the cart
        renderCart();
    });

    // Add an event listener for removing items from the cart
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("remove-from-cart-btn")) {
            const productId = event.target.getAttribute("data-product-id");
            removeProductFromCart(productId);
        }
    });

    // Function to remove a product from the cart
    function removeProductFromCart(productId) {
        let cartData = JSON.parse(localStorage.getItem("cart")) || [];
        const productIndex = cartData.findIndex((product) => product.id === productId);
        if (productIndex !== -1) {
            cartData.splice(productIndex, 1);
            localStorage.setItem("cart", JSON.stringify(cartData));
            location.reload(); // Update the cart display
        }
    }
</script>

<div class="card-container" id="cart-items">
                    <!-- Cart items will be dynamically added here -->
                </div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cartItems = document.getElementById("cart-items");
        const addToCartButtons = document.querySelectorAll(".add-to-cart-btn");

        addToCartButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const productId = button.getAttribute("data-product-id");
                const productName = button.parentElement.previousElementSibling.querySelector(".order-title").textContent;
                const productDescription = button.parentElement.previousElementSibling.querySelector(".order-description").textContent;

                // Create a list item for the cart
                const cartItem = document.createElement("li");
                cartItem.innerHTML = `${productName} - ${productDescription}`;

                cartItems.appendChild(cartItem);
            });
        });
    });
</script>



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