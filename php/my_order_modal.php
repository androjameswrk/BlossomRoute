<?php
// my_order_modal.php

// Check if order_id parameter is present
if (isset($_GET['order_id'])) {
    // Get the order_id from the parameter
    $order_id = $_GET['order_id'];

    // Include your database configuration file
    require_once '../db_config.php';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

 // Fetch order details along with replacement and refund quantity
$sql = "SELECT orders.*, flowers.Flower_Name, users.firstName, users.lastName,
order_details.Qty, order_details.Price, order_details.Flower_Size_Id,
flower_sizes.Size,
order_status.New, order_status.Hold, order_status.Shipped,
order_status.Delivered, order_status.Completed,
IFNULL(replacement.Qty, 0) AS replacement_qty,
IFNULL(refunds.Qty, 0) AS refund_qty
FROM orders 
JOIN order_details ON orders.Order_Id = order_details.Order_Id
JOIN flowers ON order_details.Flower_Id = flowers.Flower_Id
JOIN users ON orders.User_Id = users.id
LEFT JOIN flower_sizes ON order_details.Flower_Size_Id = flower_sizes.Flower_Size_Id
                    AND flowers.Flower_Id = flower_sizes.Flower_Id
LEFT JOIN order_status ON orders.Order_Id = order_status.Order_Id
LEFT JOIN replacement ON order_details.Order_Details_Id = replacement.Order_Details_Id
LEFT JOIN refunds ON order_details.Order_Details_Id = refunds.Order_Details_Id
WHERE orders.Order_Id = '$order_id'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
$order_data = $result->fetch_assoc();

// Fetch order details
$order_details = [];

// Move the result pointer to the beginning
mysqli_data_seek($result, 0);

while ($row = $result->fetch_assoc()) {
$size = $row['Size']; // Instantiate the size here
$order_details[] = [
    'product_name' => $row['Flower_Name'],
    'quantity' => $row['Qty'],
    'price' => $row['Price'],
    'size' => $size, // Include the size in the order details
    'replacement_qty' => $row['replacement_qty'], // Include the replacement_qty in the order details
    'refund_qty' => $row['refund_qty'], // Include the refund_qty in the order details
];
}
        

        // Use the fetched data to populate the modal content
        $order_date = $order_data['Order_Date'];
        $order_number = $order_data['Order_Id'];
        $customer_name = $order_data['Name'];
        $payment_method = $order_data['PaymentMethod'];
        $order_status = $order_data['Order_Status_Id'];
        $customer_address = $order_data['Address'];

        // Fetch total price from the orders table
        $total_price = $order_data['TotalPrice'];

        
    }
      
    
    else {
        // Handle case where no order is found
        echo "Order not found.";
    }

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_order']) && !isset($_SESSION['order_confirmed'])) {
        // Update the order_status table with the current date in the Completed field
        $orderId = $order_data['Order_Id'];
        $currentDate = date('Y-m-d H:i:s');
        $sql = "UPDATE order_status SET Completed = '$currentDate' WHERE Order_Id = $orderId";

        if ($conn->query($sql) === TRUE) {
            // Set session variable to mark the order as confirmed
            $_SESSION['order_confirmed'] = true;

            // Display JavaScript alert instead of echoing in PHP
            echo '<script>
                    alert("Order confirmed successfully, please re-open the receipt to see changes.");
                    
                    // Update tracking progress in the modal
                    var step4 = document.getElementById("step4");
                    if (step4) {
                        step4.classList.add("active");

                        // Reload or reopen the modal after a delay (adjust the delay as needed)
                        setTimeout(function() {
                            // Reload the content of the modal
                            var modalIframe = document.getElementById("modalIframe");
                            if (modalIframe) {
                                modalIframe.contentWindow.location.reload();
                            }
                        }, 1000); // 1000 milliseconds (1 second) delay
                    }
                  </script>';
        } else {
            echo '<script>alert("Error updating order status: ' . $conn->error . '");</script>';
        }
    }
}



function cancelOrder($conn, $order_id)
{
    // Display a confirmation dialog using JavaScript
    echo '<script>
            var confirmCancel = confirm("Are you sure you want to cancel the order?");
            if (confirmCancel) {
                window.location.href = "cancel_order.php?order_id=' . $order_id . '";
            } else {
                alert("Order cancellation canceled.");
            }
          </script>';
}


// Check if cancel_order button is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_order'])) {
    // Call the cancelOrder function
    cancelOrder($conn, $order_id);
}
    
} else {
    // Handle case where order_id parameter is not present
    echo "Order ID not provided.";
}
// Close the database connection
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="index.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Order Purchase History</title>
</head>

<body>
 

 
    <div class="card">
    <div class="title">
    Purchase Receipt


<div class="user-name">
    <?php echo "<span style='font-weight: bold; color: black; font-size: 1.2em;'>" . $customer_name . "</span>"; ?>
</div>
</div>
<div class="info">
    <div class="row">
        <div class="col-7">
            <span id="heading">Date</span><br>
            <span id="details"><?php echo $order_date; ?></span>
        </div>
        <div class="col-5 pull-right">
            <span id="heading">Order No.</span><br>
            <span id="details"><?php echo $order_number; ?></span>
        </div>
        <div class="col-12">
            <span id="heading">Address</span><br>
            <span id="details"><?php echo $customer_address; ?></span>
        </div>
    </div>
</div>

<!-- Inside the HTML section, display order details with refund and replacement quantity -->
<div class="pricing">
    <?php foreach ($order_details as $item) : ?>
        <div class="row">
            <div class="col-9">
                <span id="name"><?php echo $item['product_name'];
                    if (!empty($item['size'])) {
                        echo ' - ' . $item['size'];
                    }
                    if ($item['replacement_qty'] > 0) {
                        echo ' <span style="color: red;">(' . $item['replacement_qty'] . 'x to replace)</span>';
                    }
                    if ($item['refund_qty'] > 0) {
                        echo ' <span style="color: red;">(' . $item['refund_qty'] . 'x to refund)</span>';
                    }
                ?></span>
            </div>
            <div class="col-3">
                <span id="price"><?php echo 'Qty: ' . $item['quantity'] . ' - ₱' . $item['price']; ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>



        <div class="total">
            <div class="row">
                <div class="col-9"></div>
                <div class="col-3"><big><?php echo 'Total: ₱' . $total_price; ?></big></div>
            </div>
            <br>
        </div>

<br>
      <div class="tracking">
    <div class="title">Tracking Order</div>
</div>
<div class="progress-track">
    <ul id="progressbar">
        <li class="step0 <?php echo (!empty($order_data['New'])) ? 'active' : ''; ?>" id="step1">Ordered</li>
        <li class="step0 <?php echo (!empty($order_data['Shipped'])) ? 'active' : ''; ?> text-center" id="step2">Shipped</li>
        <li class="step0 <?php echo (!empty($order_data['Delivered'])) ? 'active' : ''; ?> text-right" id="step3">Delivered</li>
        <li class="step0 <?php echo (!empty($order_data['Completed'])) ? 'active' : ''; ?> text-right" id="step4">Completed</li>
    </ul>
</div>

<?php
// Determine if there are replacements or refunds
$hasReplacements = false;
$hasRefunds = false;

foreach ($order_details as $item) {
    if ($item['replacement_qty'] > 0) {
        $hasReplacements = true;
    }

    if ($item['refund_qty'] > 0) {
        $hasRefunds = true;
    }
}

// Show the buttons based on order status and replacements/refunds
if (!empty($order_data['New']) && empty($order_data['Shipped']) && empty($order_data['Delivered']) && empty($order_data['Completed'])) {
    // If order is new and there are no replacements or refunds, show "Cancel Order" button
    if (!$hasReplacements && !$hasRefunds) {
        echo '<form method="post">
            <button type="submit" name="cancel_order" style="font-family: Poppins; margin-left: 100px; padding: 10px; color: white; align-items: center; justify-content: center; background-color: #F97C7C; border-radius: 10px; border: none; text-align: center; font-size: 13px; line-height: 15px; margin-right: 10px;">Cancel Order</button>
        </form>';
    }
} elseif (!empty($order_data['Delivered']) && empty($order_data['Completed'])) {
    // Check if 24 hours have passed since the order was delivered
    $deliveredTimestamp = strtotime($order_data['Delivered']);
    $currentTimestamp = time();
    $hoursDifference = ($currentTimestamp - $deliveredTimestamp) / 3600;

    if ($hoursDifference >= 24) {
        // More than 24 hours have passed, hide "Confirm Order" and "Request Refund & Replace" buttons
        echo '<p style="color: #9AD9DB; margin-left: 95px;">Order confirmation <br>period has passed.</p>';
    } else {
        // If order is delivered (not completed) and there are no replacements or refunds, show "Confirm Order" button
        if (!$hasReplacements && !$hasRefunds) {
            echo '<form method="post" style="display: inline-block; margin-right: 10px; margin-left: 95px; margin-bottom:10px;">
                    <button type="submit" name="confirm_order" style="font-family: Poppins; padding: 10px; padding-left:13px;padding-right:13px; color: white; align-items: center; justify-content: center; background-color: #9AD9DB; border-radius: 10px; border: none; text-align: center; font-size: 13px; line-height: 15px;">Confirm Order</button>
                </form>';
        }
        
        // Check if less than 24 hours have passed since the order was delivered, then show "Request Refund & Replace" button
        if ($hoursDifference < 24) {
            echo '<form method="post" style="display: inline-block; margin-left: 20px;">
                    <button type="submit" name="request_return" style="margin-left: 75px; font-family: Poppins; padding: 10px; color: white; align-items: center; justify-content: center; background-color: #F97C7C; border-radius: 10px; border: none; text-align: center; font-size: 13px; line-height: 15px;">
                        <a href="customer_return.php?order_id=' . (isset($order_data['Order_Id']) ? $order_data['Order_Id'] : '') . '" style="text-decoration: none; color: white;">Request Return</a>
                    </button>
                </form>';
        }
    }
}
?>




            

            <div class="footer">
                <div class="row">
                    <div class="col-2"><img src="../img/redroses.png"></div>
                    <div class="col-10">Want any help? Please &nbsp;<a>contact us</a></div>
                </div>
                
               
            </div>
        </div>

        </body>
        </html>


        <style>
                   @import url(
"https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");
  
            body{
    background: #ddd3;
    height: 100vh;
    vertical-align: middle;
    display: flex;
    font-family: Poppins, sans-serif;
    font-size: 14px;    
}
.card{
    margin: auto;
    width: 38%;
    max-width:600px;
    padding: 4vh 0;
    box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-top: 3px solid #c1aeff;
    border-bottom: 3px solid #c1aeff;
    border-left: none;
    border-right: none;
}
@media(max-width:768px){
    .card{
        width: 90%;
    }
}
.title{
    color: #c1aeff;
    font-weight: 600;
    margin-bottom: 2vh;
    padding: 0 8%;
    font-size: initial;
}
#details{
    font-weight: 400;
}
.info{
    padding: 5% 8%;
}
.info .col-5{
    padding: 0;
}
#heading{
    color: grey;
    line-height: 6vh;
}
.pricing{
    background-color: #ddd3;
    padding: 2vh 8%;
    font-weight: 400;
    line-height: 2.5;
}
.pricing .col-3{
    padding: 0;
}
.total{
    padding: 2vh 8%;
    background-color: #ddd3;
    font-weight: bold;
}
.total .col-3{
    padding: 0;
}
.footer{
    padding: 0 8%;
    font-size: x-small;
    color: black;
}
.footer img{
    height: 5vh;
    opacity: 0.2;
}
.footer a{
    color: black;
}
.footer .col-10, .col-2{
    display: flex;
    padding: 3vh 0 0;
    align-items: center;
}
.footer .row{
    margin: 0;
}
#progressbar {
    margin-bottom: 3vh;
    overflow: hidden;
    color: rgb(252, 103, 49);
    padding-left: 0px;
    margin-top: 3vh
}

#progressbar li {
    list-style-type: none;
    font-size: x-small;
    width: 25%;
    float: left;
    position: relative;
    font-weight: 400;
    color: rgb(160, 159, 159);
}

#progressbar #step1:before {
    content: "";
    color: #c1aeff;
    width: 5px;
    height: 5px;
    margin-left: 0px !important;
    /* padding-left: 11px !important */
}

#progressbar #step2:before {
    content: "";
    color: #c1aeff;
    width: 5px;
    height: 5px;
    margin-left: 32%;
}

#progressbar #step3:before {
    content: "";
    color: #c1aeff;
    width: 5px;
    height: 5px;
    margin-right: 32% ; 
    /* padding-right: 11px !important */
}

#progressbar #step4:before {
    content: "";
    color: #c1aeff;
    width: 5px;
    height: 5px;
    margin-right: 0px !important;
    /* padding-right: 11px !important */
}

#progressbar li:before {
    line-height: 29px;
    display: block;
    font-size: 12px;
    background: #ddd;
    border-radius: 50%;
    margin: auto;
    z-index: -1;
    margin-bottom: 1vh;
}

#progressbar li:after {
    content: '';
    height: 2px;
    background: #ddd;
    position: absolute;
    left: 0%;
    right: 0%;
    margin-bottom: 2vh;
    top: 1px;
    z-index: 1;
}
.progress-track{
    padding: 0 8%;
}
#progressbar li:nth-child(2):after {
    margin-right: auto;
}

#progressbar li:nth-child(1):after {
    margin: auto;
}

#progressbar li:nth-child(3):after {
    float: left;
    width: 68%;
}
#progressbar li:nth-child(4):after {
    margin-left: auto;
    width: 132%;
}

#progressbar  li.active{
    color: black;
}

#progressbar li.active:before,
#progressbar li.active:after {
    background: #c1aeff;
}
        </style>