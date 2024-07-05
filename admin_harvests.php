<?php
require_once 'db_config.php'; 
$db = mysqli_connect($servername, $username, $password, $dbname);

function addHarvest($db, $Order_Id, $Size, $Color, $Order_Qty, $Replace_Qty, $Add_Ons, $Total) {
    $Size = mysqli_real_escape_string($db, $Size);
    $Color = mysqli_real_escape_string($db, $Color);
    $Order_Qty = mysqli_real_escape_string($db, $Order_Qty);
    $Replace_Qty = mysqli_real_escape_string($db, $Replace_Qty);
    $Add_Ons = "0";
    $Total = mysqli_real_escape_string($db, $Total);

    // Insert the data into the database
    $query = "INSERT INTO harvest (Order_Id, Size, Color, Order_Qty, Replace_Qty, Add_Ons, Total) 
              VALUES ('$Order_Id', '$Size', '$Color', '$Order_Qty', '$Replace_Qty', '$Add_Ons', '$Total')";
    mysqli_query($db, $query);

    // Update the 'Hold' column in order_status table
    $currentDateTime = date('Y-m-d H:i:s');
    $queryUpdateOrderStatus = "UPDATE order_status SET Hold = '$currentDateTime' WHERE Order_Id = '$Order_Id'";
    mysqli_query($db, $queryUpdateOrderStatus);

    return $Order_Id;
}

// Check if the add harvest form is submitted
$Order_Id = []; // Array to store Order_Details_Id values

// Check if the add harvest form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_harvest'])) {
    // Loop through the rows and insert data into the database
    foreach ($_POST['Size'] as $index => $size) {
        $color = $_POST['Color'][$index];
        $orderQty = $_POST['Order_Qty'][$index];
        $replaceQty = $_POST['Replace_Qty'][$index];
        $addOn = $_POST['Add_Ons'][$index];
        $total = $_POST['Total'][$index];
    
        // Fetch the Order_Id for each iteration
        $queryGetOrderId = "SELECT Order_Id FROM order_details ORDER BY Order_Id ASC LIMIT 1 OFFSET $index";
        $resultOrderId = mysqli_query($db, $queryGetOrderId);

        if ($resultOrderId) {
            $row = mysqli_fetch_assoc($resultOrderId);
            $Order_Id = $row['Order_Id'];

            // Call the addHarvest function with the obtained Order_Id
            $lastInsertedOrderId = addHarvest($db, $Order_Id, $size, $color, $orderQty, $replaceQty, $addOn, $total);

            // Update the order status for each inserted row
            if (!empty($lastInsertedOrderId)) {
                // Store the Order_Id for later use
                $updatedOrderIds[] = $lastInsertedOrderId;
            }

            // Free the result set
            mysqli_free_result($resultOrderId);
            
            // Check if the inserted row is from Walkins and update its status to 'Completed'
            $walkinUpdateQuery = "UPDATE walkins SET status = 'Completed'";
            mysqli_query($db, $walkinUpdateQuery);
            
            // Similarly, update the status for replacement and orders
            $queryUpdateReplacementStatus = "UPDATE replacement SET status = 'Pending'";
            mysqli_query($db, $queryUpdateReplacementStatus);
            
            $queryUpdateOrderStatus = "UPDATE orders SET status = 'Pending'";
            mysqli_query($db, $queryUpdateOrderStatus);
        }
    }

    if (!empty($updatedOrderIds)) {
        $currentDateTime = date('Y-m-d H:i:s');
        $updatedOrderIdsString = implode(',', $updatedOrderIds);
    
        $queryUpdateOrderStatus = "UPDATE order_status SET Hold = '$currentDateTime' WHERE Order_Id IN ($updatedOrderIdsString)";
        mysqli_query($db, $queryUpdateOrderStatus);
    }
    // Redirect to the user management page after successful user addition
    header('location: admin_harvests.php');
}

require_once 'db_config.php'; 

// Establish a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch distinct order dates for dropdown
$orderDatesResult = $conn->query("SELECT DISTINCT DATE(order_Date) as orderDate FROM Orders");
$orderDates = [];
while ($row = $orderDatesResult->fetch_assoc()) {
    $orderDates[] = $row['orderDate'];
}

// Define the number of records to display per page
$recordsPerPage = 10;

// Get the current page number from the URL
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = intval($_GET['page']);
} else {
    $currentPage = 1;
}

// Initialize the search query
$searchQuery = "";

// Check if a search query is provided
if (isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
}

// Calculate the starting record for the current page
$offset = ($currentPage - 1) * $recordsPerPage;

// Construct the SQL query with pagination and search


$sql = " 

SELECT
    FS.Size,
    F.Flower_Name,
    DATE(O.Order_Date) AS OrderDate,
    O.Order_Id,
    SUM(IFNULL(OD.Qty, 0)) AS Qty1,
    0 AS Qty2,
    SUM(IFNULL(OD.Qty, 0)) AS Total,
    'Order' AS Current_Status
FROM
    order_details AS OD
JOIN
    flowers AS F ON OD.Flower_Id = F.Flower_Id
JOIN
    `Orders` AS O ON OD.Order_Id = O.Order_Id
JOIN
    flower_sizes AS FS ON OD.Flower_Size_Id = FS.Flower_Size_Id
WHERE
    (O.status IS NULL OR O.status = '')
    AND (
        (DATE(O.Order_Date) = CURDATE() - INTERVAL 1 DAY AND TIME(O.Order_Date) >= '10:01:00')
        OR (DATE(O.Order_Date) = CURDATE() AND TIME(O.Order_Date) <= '10:00:00')
    )
GROUP BY
    FS.Size, F.Flower_Name, OrderDate, O.Order_Id

UNION

SELECT
    FS.Size,
    F.Flower_Name,
    DATE(W.Order_Date) AS OrderDate,
    W.Walkin_Id,
    SUM(WD.Qty) AS Qty1,
    0 AS Qty2,
    SUM(WD.Qty) AS Total,
    'Walkins' AS Current_Status
FROM
    walkins AS W
JOIN
    walkin_details AS WD ON W.Walkin_Id = WD.Walkin_Id
JOIN
    flower_sizes AS FS ON WD.Flower_Size_Id = FS.Flower_Size_Id
JOIN
    flowers AS F ON FS.Flower_Id = F.Flower_Id
WHERE
    (W.status IS NULL OR W.status = '')
    AND (
        (DATE(W.Order_Date) = CURDATE() - INTERVAL 1 DAY AND TIME(W.Order_Date) >= '10:01:00')
        OR (DATE(W.Order_Date) = CURDATE() AND TIME(W.Order_Date) <= '10:00:00')
    )
GROUP BY
    FS.Size, F.Flower_Name, OrderDate, W.Walkin_Id

UNION

SELECT
    FS.Size,
    F.Flower_Name,
    DATE(R.Replace_Date) AS OrderDate,
    OD.Order_Id,  -- Include Order_Id in the select list
    0 AS Qty1,
    SUM(R.Qty) AS Qty2,
    SUM(R.Qty) AS Total,
    'Replace' AS Current_Status
FROM
    `replacement` AS R
JOIN
    order_details AS OD ON R.Order_Details_Id = OD.Order_Details_Id
JOIN
    flowers AS F ON OD.Flower_Id = F.Flower_Id
JOIN
    flower_sizes AS FS ON OD.Flower_Size_Id = FS.Flower_Size_Id
WHERE
    (R.status IS NULL OR R.status = '')
    AND (
        (DATE(R.Replace_Date) = CURDATE() - INTERVAL 1 DAY AND TIME(R.Replace_Date) >= '10:01:00')
        OR (DATE(R.Replace_Date) = CURDATE() AND TIME(R.Replace_Date) <= '10:00:00')
    )
GROUP BY
    FS.Size, F.Flower_Name, OrderDate, OD.Order_Id

";

if (!empty($searchQuery)) {
    $sql .= " HAVING OrderDate LIKE '%$searchQuery%' ";
}

$sql .= " ORDER BY OrderDate DESC LIMIT $offset, $recordsPerPage";

$result = $conn->query($sql);

$summary = array();

// Gather data for summary
foreach ($result as $row) {
    $flowerName = $row['Flower_Name'];
    $size = $row['Size'];
    $totalQty = $row['Qty1'] + $row['Qty2'];

    // Update total quantity for the combination of Flower_Name and Size
    if (!isset($summary[$flowerName][$size])) {
        $summary[$flowerName][$size] = 0;
    }

    $summary[$flowerName][$size] += $totalQty;
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
    <title>Admin - Harvest</title>
    <link rel="stylesheet" href="./css/admin-style.css">
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
					<div class="nav-option option5">
                        <img src="./images/orders.png" class="nav-img" alt="edit profile">
						<h4>Orders</h4>
					</div>
                    </a>

                    <a href="admin_vehicles.php" class="link">
					<div class="nav-option option6">
						<img src="./images/vehicles.png" class="nav-img" alt="settings">
						<h4>Vehicles</h4>
					</div>
                    </a>

                    <a href="admin_harvests.php" class="link">
					<div class="nav-option option1">
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
					<div class="nav-option option7">
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
                <h1 class="recent-Articles" style="color: #C1AEFF; white-space: nowrap;">For Harvest</h1>

                    <?php
echo "<form id='searchForm' method='get' style='margin-left: 550px;'>";
echo "<input hidden type='date' name='search' value='$searchQuery' onchange='document.getElementById(\"searchForm\").submit()' style='color: black; border: none; outline: none;'>";
echo "</form>";
?>



                </div>
                
                    <style>
                        
                    table { 
                        width: 100%;
                        border-collapse: collapse;
                    
                    }

                    th, td {
                        text-align: left;
                        padding: 10px; /* Add padding for spacing */
                        
                    }

                    .t-op {
                        font-size: 15px;
                    }

                    /* Match the width of thead cells with tbody cells */
                    thead th {
                        
                        width: 15%; /* 100% / 6 columns  16.67%*/
                        
                    }
                    
                    @media print {
    body {
        background-color: white;
    }

    @page {
        size: landscape;
    }

    /* Define print styles here */
    body * {
        visibility: hidden;
    }

    #printableTable, #printableTable * {
        visibility: visible;
    }

    #printableTable {
        position: absolute;
        left: 0;
        top: 0;
    }

    /* Hide the footer in print */
    footer {
        display: none;
    }
}


                    </style>



<form method="post" action="admin_harvests.php"> 
<table id="printableTable">
    <thead>
        <tr>
            <th><h3 class="t-op">Size</h3></th>
            <th><h3 class="t-op">Flower Name</h3></th>
            <th><h3 class="t-op">Order Quantity</h3></th>
            <th><h3 class="t-op">Status</h3></th>
            <th><h3 class="t-op">Replace</h3></th>
            <th><h3 class="t-op">Total</h3></th>
        </tr>
    </thead>

    <tbody>
        <?php
        $summary = array();

        // Calculate the summary of harvested data
        foreach ($result as $row) {
            $flowerName = $row['Flower_Name'];
            $size = $row['Size'];
            $totalQty = $row['Qty1'] + $row['Qty2'];

            // Update total quantity for the combination of Flower_Name and Size
            if (!isset($summary[$flowerName][$size])) {
                $summary[$flowerName][$size] = 0;
            }

            $summary[$flowerName][$size] += $totalQty;
        }

        // Display the summary rows and corresponding individual rows
        foreach ($summary as $flowerName => $sizes) {
            foreach ($sizes as $size => $totalQty) {
                echo "<tr>";
                echo "<td colspan='6' style='font-size: 15px; background-color: #e6e1f9;'>{$flowerName} ({$size}) = <span style='color: red;'>{$totalQty}</span></td>";
                echo "</tr>";

                // Display the individual rows for the corresponding Flower_Name and Size
                foreach ($result as $row) {
                    if ($row['Flower_Name'] == $flowerName && $row['Size'] == $size) {
                        echo "<tr>";
                        echo "<td style='font-size: 15px'><input type='text' name='Size[]' value='{$row['Size']}' readonly style='border: none; background-color: transparent; width: 60px; height: 35px; font-size: 15px;'></td>";
                        echo "<td style='font-size: 15px'><input type='text' name='Flower_Name[]' value='{$row['Flower_Name']}' readonly style='border: none; background-color: transparent; width: 100px; font-size: 15px;'></td>";
                        echo "<td style='font-size: 15px'><input type='text' name='Order_Qty[]' value='{$row['Qty1']}' readonly style='border: none; background-color: transparent; width: 50px; font-size: 15px;'></td>";
                        echo "<td style='font-size: 15px'>";
                        
                        // Status display logic here
                        $statusEmoji = getStatusEmoji($row['Current_Status']);
                        echo "<input type='text' value='$statusEmoji {$row['Current_Status']}' readonly style='border: none; background-color: transparent; width: 90px; font-size: 15px;'>";
                        
                        echo "</td>";
                        echo "<td style='font-size: 15px'><input type='text' name='Replace_Qty[]' value='{$row['Qty2']}' readonly style='border: none; background-color: transparent; width: 50px; font-size: 15px;'></td>";
                        echo "<td style='font-size: 15px'><input type='text' name='Total[]' value='{$row['Total']}' readonly style='border: none; background-color: transparent; width: 50px; font-size: 15px;'></td>";
                        echo "</tr>";
                    }
                }
            }
        }

        // Function to get status emoji
        function getStatusEmoji($status)
        {
            switch ($status) {
                case 'Order':
                    return '🔴';
                case 'Replace':
                    return '🟡';
                case 'Shipped':
                    return '🔵';
                case 'Walkins':
                    return '🟢';
                case 'Delivered':
                    return '🟣';
                case 'Completed':
                    return '🟢';
                default:
                    return '❓'; // Handle unknown status
            }
        }
        ?>
    </tbody>
</table>


    <script>
    function validateAddOnsInput(inputElement) {
        // Get the entered value
        var inputValue = inputElement.value;

        // Remove non-digit characters
        var numericValue = inputValue.replace(/\D/g, '');

        // Ensure the value is not negative
        numericValue = Math.max(parseInt(numericValue), 0);

        // Update the input field with the sanitized value
        inputElement.value = numericValue;

        // Call calculateTotal with the sanitized value
        calculateTotal(inputElement);
    }
</script>

    <br>
    <button class="view" type="submit" name="add_harvest" style="color:white; margin-left: 850px; ">Save</button>
    <button class="view view1" onclick="window.print()" style="color:white; ">Print</button>
    <button id="cancelButton" class="view view2" style="color:white; ">Cancel</button>



</form>

            </div>
            
        </div>
           
           
    </div>
    <script>
    function calculateTotal(inputElement) {
        // Get the parent row of the input element
        var row = inputElement.closest('tr');

        // Get the values of Add_Ons and perform the calculation
        var addOnsValue = parseInt(inputElement.value) || 0;
        var totalValue = calculateTotalFromRow(row, addOnsValue);

        // Update the Total field in the same row
        row.querySelector('[name="Total[]"]').value = totalValue;
    }

    function calculateTotalFromRow(row, addOnsValue) {
        // Get other relevant values from the row
        var orderQtyValue = parseInt(row.querySelector('[name="Order_Qty[]"]').value) || 0;
        var replaceQtyValue = parseInt(row.querySelector('[name="Replace_Qty[]"]').value) || 0;

        // Perform the calculation and return the result
        return orderQtyValue + replaceQtyValue + addOnsValue;
    }
</script>


    <style>
     
    input[readonly]:hover {
        border-color: white;
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
input[readonly]:focus {
            border: none;
            outline: none;
        }
        input[name="Add_Ons"]:focus {
    border: none;
    outline: none;
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
    font-size: 15px;
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
    font-size: 15px;
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
        .view1 {
            background-color: #9AD9DB;
            color:#000000
        }
        .view2 {
            background-color: #F97C7C;
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