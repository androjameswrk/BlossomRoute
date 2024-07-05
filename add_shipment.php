<?php
require_once 'server.php';
require_once 'db_config.php';

// Establish a database connection
$db = mysqli_connect($servername, $username, $password, $dbname);
$selectedOrders = [];

// Fetch vehicles and orders here
$vehicles = getAllVehiclesAvailable($db);
$drivers = getAllDriversAvailable($db); // Fixed typo in variable name
$items = getItems();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_shipment'])) {
    // Validate selected vehicle and driver
    $selectedVehicleId = mysqli_real_escape_string($db, $_POST['selectedVehicle']);
    $selectedDriverId = mysqli_real_escape_string($db, $_POST['selectedDriver']);

    // Check if selectedOrders are not blank
    if (isset($_POST['selectedOrders']) && is_array($_POST['selectedOrders']) && !empty($_POST['selectedOrders'])) {
        // Insert into shipment table
        $shipmentInsertQuery = "INSERT INTO shipment (Vehicle_Id, User_Id) VALUES ('$selectedVehicleId', '$selectedDriverId')";
        mysqli_query($db, $shipmentInsertQuery);
        $shipmentId = mysqli_insert_id($db);

        // Iterate through selected orders and insert into shipment_details table
        foreach ($_POST['selectedOrders'] as $selectedOrder) {
            list($orderId, $sourceId) = explode('_', $selectedOrder);

            // Determine the appropriate column to insert based on source_id
            $orderColumn = ($sourceId === 'orders') ? 'Order_Id' : (($sourceId === 'refunds') ? 'Refund_Id' : 'Replace_Id');

            // Insert into shipment_details table
            $shipmentDetailsInsertQuery = "INSERT INTO shipment_details (Shipment_Id, $orderColumn) VALUES ('$shipmentId', '$orderId')";
            mysqli_query($db, $shipmentDetailsInsertQuery);

            // Update order_status table based on the source_id
            $orderStatusColumn = ($sourceId === 'orders') ? 'Shipped' : 'Shipped';
            $updateOrderStatusQuery = "UPDATE order_status SET $orderStatusColumn = NOW() WHERE Order_Id = '$orderId'";
            mysqli_query($db, $updateOrderStatusQuery);

            $updateOrdersQuery = "UPDATE orders SET Status = 'Shipped' WHERE Order_Id = '$orderId'";
            mysqli_query($db, $updateOrdersQuery);

            // Update status for refunds and replacements
            if ($sourceId === 'refunds') {
                $updateRefundStatusQuery = "UPDATE refunds SET Status = 'Shipped' WHERE Refund_Id = '$orderId'";
                mysqli_query($db, $updateRefundStatusQuery);
            } elseif ($sourceId === 'replacement') {
                $updateReplacementStatusQuery = "UPDATE replacement SET Status = 'Shipped' WHERE Replace_Id = '$orderId'";
                mysqli_query($db, $updateReplacementStatusQuery);
            }
        }

        // Update vehicle status to Unavailable
        $updateVehicleQuery = "UPDATE vehicles SET Vehicle_Status = 'Unavailable', user_id = '$selectedDriverId' WHERE Vehicle_Id = '$selectedVehicleId'";
mysqli_query($db, $updateVehicleQuery);

        // Update driver status to Inactive
        $updateDriverQuery = "UPDATE users SET driver_status = 'Unavailable' WHERE id = '$selectedDriverId'";
        mysqli_query($db, $updateDriverQuery);

        

        // Additional processing or redirect as needed
        // ...

        echo '<script>window.location.href = "add_shipment.php";</script>';
        
    } else {
        // Handle the case when no selected orders are provided
        // You can redirect or display an error message, as needed
        echo '<script>alert("No selected orders provided. Please select orders before adding a shipment.");</script>';
        echo '<script>window.location.href = "add_shipment.php";</script>';
        exit; 
    }

    // Close the database connection
    mysqli_close($db);
}
?>






    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Shipment</title>
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
					<div class="nav-option option3">
						<img src="./images/flower.png" class="nav-img" alt="browse">
						<h4>Flowers</h4>
					</div>
					</a>
                    <a href="admin_orders.php" class="link">
					<div class="nav-option option5">
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
					<div class="nav-option option1">
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
                    <h1 class="recent-Articles">Loading</h1>
                  
                    

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
    flex-direction: row; /* Arrange elements side by side */
    justify-content: space-between; /* Add space between elements */
}

.flex-col {
    display: flex;
    flex-direction: column;
}


    </style>
  <style>
    #selectedVehicle {
        background-color: white;
        color: black; /* Set text color to contrast with the red background */
        border-radius: 10px; /* Adjust the border-radius to control the degree of rounding */
        padding: 5px; /* Add padding for better appearance */
        font-weight: normal;
        margin-bottom: 10px;
        border: 1px solid white; /* Add a border to define the edges of the select element */
    }

    #selectedVehicle option {
        background-color: white; /* Set background color for options */
        color: black; /* Set text color for options */
    }
    #selectedDriver {
        background-color: white;
        color: black; /* Set text color to contrast with the red background */
        border-radius: 10px; /* Adjust the border-radius to control the degree of rounding */
        padding: 5px; /* Add padding for better appearance */
        font-weight: normal;
        margin-bottom: 10px;
        border: 1px solid white; /* Add a border to define the edges of the select element */
    }

    #selectedDriver option {
        background-color: white; /* Set background color for options */
        color: black; /* Set text color for options */
    }
</style>

    <form method="post" class="flex flex-row the-form" id="theForm" action="add_shipment.php" enctype="multipart/form-data" onsubmit="return validateCapacity();">
    <div class="flex-container">
        <div class="flex-col">
        
        <div style="margin-top: 10px; color: black; font-weight: normal; font-size:16px;">
        <label for="selectedVehicle" style="font-weight: bold; color:#C1AEFF ">Select Vehicle:</label>
<br >
        <select id="selectedVehicle" name="selectedVehicle" style="margin-bottom: 10px; font-weight: normal; font-size:16px; margin-top:10px">
    <?php foreach ($vehicles as $vehicle) : ?>
        <option value="<?php echo $vehicle['Vehicle_Id']; ?>" data-capacity="<?php echo $vehicle['Capacity']; ?>">
            <?php echo $vehicle['Vehicle_Name'] . ' - ' . $vehicle['Plate_No'] . ' (Capacity: ' . $vehicle['Capacity'] . ')'; ?>
        </option>
    <?php endforeach; ?>
</select>


<input hidden type="date" id="selectedDate" name="selectedDate" style="color: black; border: none; outline: none; margin-left:10px; font-size:16px; ">

    </div> 

    <div style="margin-top: -10px; color: black; font-weight: normal; font-size:16px;">
    <label for="selectedDriver" style="font-weight: bold; color:#C1AEFF ">Select Driver:</label>
    <br>
    <select id="selectedDriver" name="selectedDriver" style="margin-bottom: 10px; font-weight: normal; font-size:16px; margin-top:10px">
        <?php 
        $drivers = getAllDriversAvailable($db);
        foreach ($drivers as $driver) : ?>
            <option value="<?php echo $driver['id']; ?>">
                <?php echo $driver['firstName'] . ' ' . $driver['lastName'] ; ?>
            </option>
        <?php endforeach; ?>
    </select>
   
</div>
<br>
    
    <?php
// Include your database connection and functions
require_once 'db_config.php';  // Assuming this file contains your database connection
require_once 'server.php';     // Assuming this file contains your functions

// Establish a database connection
$db = mysqli_connect($servername, $username, $password, $dbname);

// Fetch replacement orders from the database
function getItems() {
    $pdo = new PDO("mysql:host=localhost;dbname=helloworlddb", "root", "");

    $stmt = $pdo->prepare("
        SELECT
            o.Order_Id,
            CONCAT(u.firstName, ' ', u.lastName) AS Name,
            o.Address,
            SUM(od.Qty) AS Qty,
            'orders' AS source_id,
            CASE
                WHEN COALESCE(STR_TO_DATE(os.New, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
                    COALESCE(STR_TO_DATE(os.New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
                ) THEN 'New'
                WHEN COALESCE(STR_TO_DATE(os.Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
                    COALESCE(STR_TO_DATE(os.New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
                ) THEN 'Hold'
                WHEN COALESCE(STR_TO_DATE(os.Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
                    COALESCE(STR_TO_DATE(os.New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
                ) THEN 'Shipped'
                WHEN COALESCE(STR_TO_DATE(os.Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
                    COALESCE(STR_TO_DATE(os.New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
                ) THEN 'Delivered'
                WHEN COALESCE(STR_TO_DATE(os.Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
                    COALESCE(STR_TO_DATE(os.New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
                    COALESCE(STR_TO_DATE(os.Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
                ) THEN 'Completed'
                END AS Status
                FROM
                    orders o
                JOIN
                    users u ON o.User_Id = u.id
                JOIN
                    order_details od ON o.Order_Id = od.Order_Id
                JOIN
                    order_status os ON o.Order_Id = os.Order_Id
                GROUP BY
                    o.Order_Id, u.firstName, u.lastName, o.Address
                HAVING
                    Status = 'Hold'
                
                UNION
                
                SELECT
                    r.Refund_Id,
                    CONCAT(u.firstName, ' ', u.lastName) AS Name,
                    r.Address,
                    r.Refund_Amount AS Qty,
                    'refunds' AS source_id,
                    '' AS Status
                FROM
                    refunds r
                JOIN
                    users u ON r.User_Id = u.id
                WHERE
                    r.Status = ''
                
                UNION
                
                SELECT
                    rp.Replace_Id,
                    CONCAT(u.firstName, ' ', u.lastName) AS Name,
                    rp.Address,
                    rp.Qty,
                    'replacement' AS source_id,
                    '' AS Status
                FROM
                    replacement rp
                JOIN
                    users u ON rp.User_Id = u.id
                WHERE
                    rp.Status = 'Pending';
    ");

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$items = getItems();
?>
  <label for="totalQuantity" style="margin-top: -30px; font-weight: bold; color: #C1AEFF">Select Items to Load:</label>
<?php foreach ($items as $index => $item) : ?>
    <label style="display: block; margin-bottom: 5px; font-weight: normal; font-size: 16px">
        <input type="checkbox" 
               value="<?php echo $item['Order_Id'] . '_' . $item['source_id']; ?>"
               data-index="<?php echo $index; ?>"
               data-quantity="<?php echo isset($item['Qty']) ? $item['Qty'] : ''; ?>"
               onclick="updateTotalQty(this)"
               <?php echo isset($item['isSelected']) && $item['isSelected'] ? 'checked' : ''; ?>>
        <?php
        echo $item['Name'] . ', ' . $item['Address'] . ', (';
        if ($item['source_id'] === 'refunds' && isset($item['Qty'])) {
            echo 'Refund Amount: ' . $item['Qty'];
        } elseif (isset($item['Qty'])) {
            echo 'Quantity: ' . $item['Qty'];
        }
        echo ')';
        ?>
    </label>
<?php endforeach; ?>

<div style="margin-top: 5px; color: black; font-weight: normal; font-size: 16px;">
    <p style="margin-top: 20px; font-weight: bold; color: #C1AEFF ">Selected Items Summary</p>
    <ol id="selectedItemsSummary" name="selectedOrders[]" style="margin-left: 30px; margin-bottom: 20px;"></ol>


    <!-- Labels for Total Quantity and Total Amount -->
    <label for="totalQuantity" style="margin-top: 20px; font-weight: bold; color: #F97C7C ">Total Quantity:</label>
    <span id="totalQuantity">0</span>

    <br>

    <label for="totalAmount" style="font-weight: bold; color: #F97C7C ">Refund Amount:</label>
    <span id="totalAmount">₱0.00</span>
</div>

<!-- ... (your existing HTML code) -->

<script>
    // Initialize an array to store selected items
    var selectedItems = <?php echo json_encode($items); ?>;
    // Array to store the order of selection
    var selectionOrder = [];

    // Function to update the selected items summary
    function updateSelectedItemsSummary() {
        var selectedItemsSummary = document.getElementById('selectedItemsSummary');
        var form = document.getElementById('theForm');

        // Clear existing content and remove previously added hidden input fields
        selectedItemsSummary.innerHTML = '';
        var existingHiddenInputs = form.querySelectorAll('input[type="hidden"][name^="selectedOrders"]');
        existingHiddenInputs.forEach(function (input) {
            input.parentNode.removeChild(input);
        });

        // Iterate through the selection order and add selected items to the summary
        selectionOrder.forEach(function (index) {
            var item = selectedItems[index];
            var listItem = document.createElement('li');
            listItem.textContent = item.Name + ', ' + item.Address + ', ';
            if (item.source_id === 'refunds' && typeof item.Qty !== 'undefined') {
                listItem.textContent += 'Refund Amount: ' + item.Qty;
            } else if (typeof item.Qty !== 'undefined') {
                listItem.textContent += 'Quantity: ' + item.Qty;
            }
            selectedItemsSummary.appendChild(listItem);

            // Add hidden input fields for each selected item
            var hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'selectedOrders[]';
            hiddenInput.value = item.Order_Id + '_' + item.source_id;
            form.appendChild(hiddenInput);
        });

        // Update total quantity and total amount
        updateTotals();
    }

    // Function to calculate total quantity and update the UI (excluding refunds)
    function calculateTotalQuantity() {
        var totalQty = 0;
        selectionOrder.forEach(function (index) {
            var item = selectedItems[index];
            // Only include quantities for items that are not refunds
            if (item.source_id !== 'refunds' && typeof item.Qty !== 'undefined') {
                totalQty += parseInt(item.Qty);
            }
        });
        return totalQty;
    }

    // Function to calculate total amount and update the UI (considering only refunds)
    function calculateTotalAmount() {
        var totalAmount = 0;
        selectionOrder.forEach(function (index) {
            var item = selectedItems[index];
            if (item.source_id === 'refunds' && typeof item.Qty !== 'undefined') {
                totalAmount += parseFloat(item.Qty);
            }
        });
        return totalAmount.toFixed(2);
    }

    // Function to update total quantity and total amount in the UI
    function updateTotals() {
        var totalQty = calculateTotalQuantity();
        var totalAmount = calculateTotalAmount();

        // Update the label for Total Quantity
        document.getElementById('totalQuantity').textContent = "" + totalQty;

        // Update the label for Total Amount
        document.getElementById('totalAmount').textContent = "₱" + totalAmount;
    }

    // Function to update the isSelected property and call the summary update function
    function updateTotalQty(checkbox) {
        var index = checkbox.dataset.index;

        // If the checkbox is checked, add the index to the selection order
        if (checkbox.checked) {
            selectionOrder.push(index);
        } else {
            // If the checkbox is unchecked, remove the index from the selection order
            var indexOfUnchecked = selectionOrder.indexOf(index);
            if (indexOfUnchecked !== -1) {
                selectionOrder.splice(indexOfUnchecked, 1);
            }
        }

        // Call the function to update the selected items summary
        updateSelectedItemsSummary();
    }

    // Call the function to update the selected items summary when the page loads
    window.onload = function () {
        updateSelectedItemsSummary();
    };
</script>


<!-- ... (rest of your HTML code) -->














       
            

           
        </div>
        <style>
   table {
    border-collapse: collapse;
    width: 100%;
   
    overflow: hidden;
}

th, td {
    padding: 13px;
    font-size: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    font-weight: bold;
        background-color: #C1AEFF;
        text-transform: none; 
   
    background-color: #C1AEFF;
}

thead th {
    width: 25%; /* Equal distribution for 5 columns */
}

</style>
        

        
        </div>
       
        <div>
        <button class="view" type="submit" name="add_shipment" style="color: white; border-radius: 10px; text-align: center; font-size: 15px; line-height: 15px; margin-right: 10px;">Save</button>
        <button class="view"><a href="admin_shipment.php"  style="color: white; text-decoration: none; ">Cancel</a></button>
    </div>
        
    </div>
    
</form>                   
                
            </div>
        </div>
    </div>

    <style>
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
<script>
    document.getElementById('selectedVehicle').addEventListener('change', function () {
        var selectedVehicle = this.options[this.selectedIndex];
        var plateNo = selectedVehicle.getAttribute('data-plate');
        var username = selectedVehicle.getAttribute('data-username');
        var capacity = selectedVehicle.getAttribute('data-capacity');

        // Update the content below the combo box
        document.getElementById('vehicleInfo').innerHTML = '<strong>Plate No:</strong> ' + plateNo + '<br>' +
                                                              '<strong>Vehicle Driver:</strong> ' + username + '<br>' +
                                                              '<strong>Capacity:</strong> ' + capacity;
    });
</script>

<script>
    function validateCapacity() {
        // Calculate total quantity based on the selected items
        var totalQty = calculateTotalQuantity();

        // Update the label
        document.getElementById('totalQuantity').textContent = "" + totalQty;

        // Check if total quantity exceeds the capacity of the selected vehicle
        var selectedVehicle = document.getElementById('selectedVehicle');
        var selectedCapacity = parseInt(selectedVehicle.options[selectedVehicle.selectedIndex].getAttribute('data-capacity'));

        if (totalQty > selectedCapacity) {
            alert('Total quantity exceeds the capacity of the chosen vehicle.');
            // Clear the selection to prevent form submission
            selectedVehicle.selectedIndex = -1;
            return false; // Prevent form submission
        } else {
            return true; // Allow form submission
        }
    }


</script>


    <script src="./js/admin.js"></script>
</body>

</html>

