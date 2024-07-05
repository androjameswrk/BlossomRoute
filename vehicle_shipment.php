<?php
require_once 'db_config.php'; 
require_once 'server.php';



$conn = new mysqli($servername, $username, $password, $dbname);

$userId = $_SESSION['user_id'];  


$todayDate = date('Y-m-d');

$sql = "
SELECT 
    sd.Shipment_Details_Id, 
    CASE
        WHEN o.Order_Id IS NOT NULL THEN 'Orders'
        WHEN r.Refund_Id IS NOT NULL THEN 'Refund'
        WHEN rp.Replace_Id IS NOT NULL THEN 'Replacements'
        ELSE 'Unknown Category'
    END AS Category,
    COALESCE(CONCAT(ou.firstName, ' ', ou.lastName), CONCAT(ru.firstName, ' ', ru.lastName), CONCAT(rpu.firstName, ' ', rpu.lastName)) AS Customer_Name, 
    COALESCE(ou.contact, ru.contact, rpu.contact) AS Contact,
    CASE
        WHEN o.Order_Id IS NOT NULL THEN (
            SELECT GROUP_CONCAT(CONCAT(f.Flower_Name, ' - Size: ', COALESCE(fs.Size, 'Unknown Size'), ' - Qty: ', od.Qty) SEPARATOR ', ')
            FROM order_details od
            JOIN flowers f ON od.Flower_Id = f.Flower_Id
            JOIN flower_sizes fs ON od.Flower_Size_Id = fs.Flower_Size_Id
            WHERE od.Order_Id = o.Order_Id
        )
        WHEN r.Refund_Id IS NOT NULL THEN CONCAT('Refund Amount: ₱', r.Refund_Amount)
         WHEN rp.Replace_Id IS NOT NULL THEN (
            SELECT GROUP_CONCAT(CONCAT(f.Flower_Name, ' - Qty: ', od.Qty) SEPARATOR ', ')
            FROM order_details od
            JOIN flowers f ON od.Flower_Id = f.Flower_Id
            WHERE od.Order_Details_Id = rp.Order_Details_Id
        )
        ELSE NULL
    END AS Information,
    CASE
        WHEN o.Order_Id IS NOT NULL AND o.PaymentMethod = 'COD' THEN (
            SELECT SUM(od.Price * od.Qty) AS TotalPrice
            FROM order_details od
            WHERE od.Order_Id = o.Order_Id
        )
        ELSE NULL
    END AS TotalPrice
FROM shipment s
JOIN vehicles v ON s.Vehicle_Id = v.Vehicle_Id
JOIN shipment_details sd ON s.Shipment_Id = sd.Shipment_Id
LEFT JOIN orders o ON sd.Order_Id = o.Order_Id
LEFT JOIN users ou ON o.User_Id = ou.id
LEFT JOIN refunds r ON sd.Refund_Id = r.Refund_Id
LEFT JOIN replacement rp ON sd.Replace_Id = rp.Replace_Id
LEFT JOIN users ru ON r.User_Id = ru.id
LEFT JOIN users rpu ON rp.User_Id = rpu.id
WHERE v.User_Id = ? AND DATE(sd.Shipment_Date) = CURRENT_DATE
    AND (sd.delivery_image IS NULL OR sd.delivery_image = '')
ORDER BY sd.Shipment_Details_Id ASC;
"
;


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);

$stmt->execute();
$result = $stmt->get_result();

// Check for errors
if (!$result) {
    die('Error in query: ' . $conn->error);
}

// Fetch the data into $users
$users = $result->fetch_all(MYSQLI_ASSOC);

// Check if the result set is empty
if (empty($users)) {
    // If no records found, update the Vehicle_Status to 'Available'
    $updateVehicleStatusQuery = "UPDATE vehicles SET Vehicle_Status = 'Available' WHERE User_Id = ?";
    
    $stmt = $conn->prepare($updateVehicleStatusQuery);
    $stmt->bind_param("i", $userId);
    
    $stmt->execute();

    // Check for errors
    if ($stmt->error) {
        die('Error in query: ' . $stmt->error);
    }

    // Close the statement
    $stmt->close();

    // Update driver_status to NULL
    $updateDriverStatusQuery = "UPDATE users SET driver_status = NULL WHERE id = ?";
    
    $stmt = $conn->prepare($updateDriverStatusQuery);
    $stmt->bind_param("i", $userId);
    
    $stmt->execute();

    // Check for errors
    if ($stmt->error) {
        die('Error in query: ' . $stmt->error);
    }

    // Close the statement
    $stmt->close();
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Rider - Received</title>
    <link rel="stylesheet" href="./css/admin-style.css">
</head>

<body>


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

                <a href="vehicle_dashboard.php" class="link">
					<div class="nav-option option2">
						<img src="./images/dashboard.jpg" class="nav-img" alt="dashboard">
						<h4>Dashboard</h4>
					</div>
                </a>

				
                    <a href="vehicle_shipment.php" class="link">
					<div class="nav-option option1">
						<img src="./images/ship.png" class="nav-img" alt="browse">
						<h4>Delivery </h4>
					</div>
					</a>
					
			<!--		<a href="vehicle_replaced.php" class="link">
					<div class="nav-option option4">
						<img src="./images/replaced.png" class="nav-img" alt="browse">
						<h4>Replaced </h4>
					</div>
					</a>
                    <a href="vehicle_refund.php" class="link">
					<div class="nav-option option3">
                        <img src="./images/refund.png" class="nav-img" alt="edit profile">
						<h4>Refund </h4>
					</div>
                    </a> -->
                    
                   
                    <div></div>
					<div></div>
					<a href="logout.php" class="link">
                    <br>
						<br>
						<br>
						<br>
                        <br>
						<br>
						<br>
                        <br>
						<br>
						<br>
                        <br>
						<br>
                        <br>
						<br>
						<br>
                        <br>
						<br>
						<br>
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
            <div class="report-header" >
    <h1 class="recent-Articles">Delivery of Orders, Replace, & Refund </h1> 
    
    
  
    <button class="view view2" style ="margin-left:350px;"><a href="location.php"  style="color: white; text-decoration: none;background-color: #F97C7C; ">Map</a></button>
    <button class="view view2" style = "background-color:#9AD9DB;"><a href="location_delivery.php"  style="color: white; text-decoration: none;background-color: #9AD9DB; ">View</a></button>
  
        
</div>
  <div>
        
    </div>


                <style>
                    li{
                      
                        list-style-type: none;
        display: inline-block;
        margin-right: 15px;
        margin-top: 20px;
        margin-bottom: 10px;
                     
                    }
                    
    .dot {
        height: 12px;
        width: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
       
    }

    .red {
        background-color: #F97C7C;
    }

    .green {
        background-color: #3DAC78;
    }

    .violet {
        background-color: #C1AEFF;
    }
</style>

                <style>
                    .view2 {
             background-color: #F97C7C;
             color:#000000
         }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    
                    }

                    th, td {
                        text-align: left;
                        padding: 10px; /* Add padding for spacing */
                        
                    }
                

                    .t-op {
                        font-size: 14px;
                    }

                    /* Match the width of thead cells with tbody cells */
                    thead th {
                        
                        width: 12.5%; /* 100% / 6 columns  16.67%*/
                        
                    }
                    td.replace-info {
        padding-right: 30px; /* Adjust the value to create the desired gap */
    }
                    </style>

<style>
    .red-row {
        color: #F97C7C; /* Set the desired background color for red rows */
    }

    .green-row {
        color: #3DAC78; /* Set the desired background color for green rows */
    }

    .violet-row {
        color: #C1AEFF; /* Set the desired background color for violet rows */
    }
</style>


<table>
    <thead>         
        <tr>
            <th><h3 class="t-op">ID</h3></th>    
            <th><h3 class="t-op">Customer Name</h3></th>   
            <th><h3 class="t-op">Contact</h3></th>  
            <th><h3 class="t-op">Category</h3></th>   
            <th><h3 class="t-op">Information</h3></th>
            <th><h3 class="t-op">Total Price</h3></th>   
            <th><h3 class="t-op">Image</h3></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
   
                <td style="font-size: 15px"><?php echo $user['Shipment_Details_Id']; ?></td>
                <td style="font-size: 15px"><?php echo $user['Customer_Name']; ?></td>
                <td style="font-size: 15px"><?php echo $user['Contact']; ?></td>
                <td style="font-size: 15px"><?php echo $user['Category']; ?></td>
                <td style="font-size: 15px"><?php echo $user['Information']; ?></td>
                <td style="font-size: 15px">
    <?php echo isset($user['TotalPrice']) ? '₱' . $user['TotalPrice'] : ' '; ?>
</td>



                <td style="font-size: 15px">
                <button class="view" onclick="openCamera(<?php echo $user['Shipment_Details_Id']; ?>)">Cam</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!--<div style="margin-left:850px">
<p>
            <li><span class="dot red"></span> Orders</li>
            <li><span class="dot green"></span> Replace</li>
            <li><span class="dot violet"></span> Refund</li>
        </p>
</div> -->

<script>
    function openCamera(shipmentDetailsId) {
        // Redirect to the capture image page with the shipment details ID
        window.location.href = './html/index.html?shipmentDetailsId=' + shipmentDetailsId;
    }
</script>
   
   
            </div>

            
        </div>
           
    </div>
    <script src="./js/admin.js"></script>
</body>

</html>