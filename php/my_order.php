<?php

function notifyOrderStatusChange($status) {
    // Get the current notification count from the session or initialize it
    $notificationCount = isset($_SESSION['notification_count']) ? $_SESSION['notification_count'] : 0;

    // Increment the notification count
    $notificationCount++;

    // Update the notification badge count and show the indicator
    echo '<script>updateNotificationBadge("' . $status . '", ' . $notificationCount . ');</script>';

    // Update the session with the new notification count
    $_SESSION['notification_count'] = $notificationCount;
}

function getLatestOrderStatus($orderStatusRow) {
    if ($orderStatusRow['Completed']) {
        notifyOrderStatusChange('Completed');
        return 'Completed';
    } elseif ($orderStatusRow['Delivered']) {
        // Check if 24 hours have passed since the order was delivered
        $deliveredTimestamp = strtotime($orderStatusRow['Delivered']);
        $currentTimestamp = time();
        $hoursDifference = ($currentTimestamp - $deliveredTimestamp) / 3600;

        if ($hoursDifference >= 24) {
            // More than 24 hours have passed, and the status is currently 'Delivered'
            if (!$orderStatusRow['Completed']) {
                $orderId = $orderStatusRow['Order_Id'];

                // Update the order status to Completed in the order_status table
              
                $query = "UPDATE order_status SET Completed = NOW() WHERE Order_Id = $orderId";


                // Execute the query (use your database connection here)
                global $conn;

                if ($conn->query($query) === TRUE) {
                    // Query executed successfully
                    notifyOrderStatusChange('Completed');
                    return 'Completed';
                } else {
                    // Handle errors appropriately
                    // Example: echo "Error updating record: " . $conn->error;
                    return 'Unknown';
                }
            }
        } else {
            notifyOrderStatusChange('Delivered');
            return 'Delivered';
        }
    } elseif ($orderStatusRow['Shipped']) {
        notifyOrderStatusChange('Shipped');
        return 'Shipped';
    } elseif ($orderStatusRow['Hold']) {
        notifyOrderStatusChange('On Hold');
        return 'On Hold';
    } elseif ($orderStatusRow['New']) {
        notifyOrderStatusChange('New');
        return 'New';
    } else {
        notifyOrderStatusChange('Unknown');
        return 'Unknown';
    }

     // Notify order status change and update notification count
    // notifyOrderStatusChange($currentStatus);
}


// Function to update the order status to Completed in the order_status table
function updateOrderStatusToCompleted($orderId) {
    // Get the current timestamp
    $completedTimestamp = date('Y-m-d H:i:s');

    // Update the order status to Completed in the order_status table
    $query = "UPDATE order_status SET Completed = '$completedTimestamp' WHERE Order_Id = $orderId";
    
    // Execute the query (use your database connection here)
    // Example: mysqli_query($connection, $query);
    // Make sure to handle errors appropriately in your application
    
    // Return true if the update was successful, otherwise false
    // Example: return mysqli_affected_rows($connection) > 0;
}




// Rest of your PHP code
session_start();
require_once '../db_config.php'; // Include your database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page or handle unauthorized access
    header("Location: ../login.php");
    exit();
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders for the specific user
$sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY Order_Date DESC"; // Adjust the column names as per your database schema
$result = $conn->query($sql);

// Check if there are orders
if ($result->num_rows > 0) {
    // HTML starts here
    ?>

    
   <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="index.css" />

    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+WyAq6ZC7UtW8OEGk5KTf9I8ZjBpkL" crossorigin="anonymous">

    <!-- Include jQuery, Popper.js, and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js" integrity="sha384-TEiHxqdDoI6Zg7S0FpfM7f0ewI06qDh4GaKfAe4h5Y9p8g3bCWf+atGI7aH4iS9N" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+WyAq6ZC7UtW8OEGk5KTf9I8ZjBpkL" crossorigin="anonymous"></script>
    
    <title>Order Purchase History</title>
</head>



<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

    body {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-family: Poppins, sans-serif;
    }

    .history-page {
        background-color: #fff;
        width: 100%;
        height: 100%;
    }

    .history-page__title {
        font-size: x-large; /* Increase the font size */
        text-underline-offset: 6px; /* Adjust the offset for better visibility */
        color: #c1aeff;
    }

    hr {
        color: #b4b3b3;
    }

    .purchase {
        display: flex;
    }

    .purchase__icon-background {
        align-self: center;
        margin: 10px;
        border-radius: 35px;
        display: flex;
        background-color: #c0df85;
    }

    .purchase__icon {
        padding: 10px;
        font-size: 50px;
        color: #fff;
    }

    .purchase__info {
        margin: auto 0;
    }

    .purchase__title,
    .purchase__date {
        margin: 0;
    }

    .purchase__date {
        padding: 3px 0 10px 0;
        font-size: small;
        color: #808080;
    }

    .purchase__price {
        align-self: center;
        margin-left: auto;
    }

    .purchase__details-btn {
        background-color: #c1aeff;
        padding: 5px 15px;
        border: solid 1px #ffffff;
        transition: ease-in-out 0.2s;
        font-family: Poppins;
        border-radius: 10px;
        color: white;
    }

    .purchase__details-btn:hover {
        background-color: #c1aeff;
        color: #fff;
        cursor: pointer;
        border: solid 1px #c1aeff;
    }

    @media (min-width: 750px) {
        .history-page {
            width: 750px;
        }
    }
    hr {
        margin-top:-10px;
        margin-bottom: 20px;
        color: #c1aeff;
        border: none; /* Remove default border */
        height: 3px; /* Set the height of the line */
        background-color: #c1aeff; /* Set the color of the line */
    }
</style>

<body>

    
<div class="history-page">
    <h1 class="history-page__title">Order Purchase History</h1>
    <hr />
    <div class="history-page__purchases">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <?php
                // Fetch order status from the order_status table
                $orderStatusQuery = "SELECT * FROM order_status WHERE Order_Id = " . $row['Order_Id'];
                $orderStatusResult = $conn->query($orderStatusQuery);
                $orderStatusRow = $orderStatusResult->fetch_assoc();
                $currentStatus = getLatestOrderStatus($orderStatusRow);
            ?>
            <div class="purchase">
                <?= createIcon($row['PaymentMethod']) ?>
                <div class="purchase__info">
                    <p class="purchase__title"><?= $row['Name'] ?></p>
                    
                    <!-- Display the order status -->
                    <p class="purchase__date"><?= $currentStatus ?></p>

                    <p class="purchase__date"><?= $row['Order_Date'] ?></p>
                    <!-- Pass the order ID to openModal -->
                    <button class="purchase__details-btn" data-order-id="<?= $row['Order_Id'] ?>" onclick="openModal(this)">Details</button>
                </div>
                <p class="purchase__price" style="color:#F97C7C;">₱<?= $row['TotalPrice'] ?></p>
            </div>
            <br>
        <?php endwhile; ?>
    </div>
</div>

<style>
    .purchase {
    display: flex;
    justify-content: space-between;
    /* Add any other styles as needed */
}

.purchase__status-container {
    text-align: right;
    /* Add any other styles as needed */
}
    </style>




  <!-- The Modal -->
  <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <!-- Close button -->
            <span class="close" onclick="closeModal()">&times;</span>
            <!-- Iframe for loading my_order_modal.php -->
            <iframe id="modalIframe" width="100%" height="100%" style="border: none;"></iframe>
        </div>
    </div>


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
  width: 50%;
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
  right: 20px;
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


<?php
// Close the database connection
$conn->close();
} else {
echo "No orders found.";
}
?>

<?php
// Additional PHP functions and logic can be placed here
function createIcon($paymentMethod)
{
// Place your logic to create an icon based on the payment method
// For example, return an icon based on $paymentMethod
// ...

// Sample logic (modify based on your requirements)
$iconClass = '';

switch ($paymentMethod) {
    case 'CreditCard':
        $iconClass = 'credit-card-icon';
        break;
    case 'CashOnDelivery':
        $iconClass = 'cash-icon';
        break;
    // Add more cases as needed

    default:
        $iconClass = 'default-icon';
        break;
}

return '<div class="purchase__icon-background ' . $iconClass . '"></div>';
}
?>

    <script src="https://code.iconify.design/1/1.0.3/iconify.min.js"></script>
    <script src="index.js"></script>

    <script>
        // Function to open the modal and load content based on order ID
        function openModal(button) {
            // Extract order ID from the data-order-id attribute
            var order_id = button.getAttribute('data-order-id');

            // Set the iframe source dynamically with the order ID
            document.getElementById('modalIframe').src = 'my_order_modal.php?order_id=' + order_id;

            // Display the modal
            document.getElementById('myModal').style.display = 'block';
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('myModal').style.display = 'none';
        }
    </script>


    </body>


</html>