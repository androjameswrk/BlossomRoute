<?php
require_once 'server.php';
// Check if user is logged in and is an admin, otherwise redirect to login page

// Fetch all users from the database
$users = getAllVehicleReplaced($db);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Rider - Replaced</title>
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

					
					<a href="vehicle_Shipment.php" class="link">
					<div class="nav-option option4">
						<img src="./images/ship.png" class="nav-img" alt="browse">
						<h4>Shipment </h4>
					</div>
					</a>
					<a href="vehicle_replaced.php" class="link">
					<div class="nav-option option1">
						<img src="./images/replaced.png" class="nav-img" alt="browse">
						<h4>Replaced </h4>
					</div>
					</a>
                    <a href="vehicle_refund.php" class="link">
					<div class="nav-option option3">
                        <img src="./images/refund.png" class="nav-img" alt="edit profile">
						<h4>Refund </h4>
					</div>
                    </a>
                    
                   
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
            <h1 class="recent-Articles">Replacement Orders</h1>
          
        </div>
        
            <style>
            table {
                width: 100%;
                border-collapse: collapse;
            
            }

            th, td {
                text-align: left;
                padding: 10px; 
                
            }

            .t-op {
                font-size: 15px;
            }

           
            thead th {
                
                width: 20%; 
                
            }
            </style>




        <table>
        <thead>         
        <tr>
                <th><h3 class="t-op">Replacement ID</h3></th>                      
                <th><h3 class="t-op">User Name</h3></th>
              
                <th><h3 class="t-op">Quantity</h3></th>
                <th><h3 class="t-op">Replace Date</h3></th>
                
                
            </div>
        </tr>
        <style>
            
        </style>
        </thead>

             <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
<td style="font-size: 15px"><?php echo $user['replace_id']; ?></td>
<td style="font-size: 15px"><?php echo $user['username']; ?></td>

<td style="font-size: 15px"><?php echo $user['qty']; ?></td>
<td style="font-size: 15px"><?php echo $user['replace_date']; ?></td>


</tr>

        <?php endforeach; ?>
    </tbody>
</table>

    </div>
</div>
   
</div>

<script src="./js/admin.js"></script>
</body>

</html>