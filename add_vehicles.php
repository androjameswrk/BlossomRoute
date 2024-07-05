<?php
require_once 'db_config.php'; 
session_start();

// connect to the database
$db = mysqli_connect($servername, $username, $password, $dbname);

// Function to add a user to the database
function addVehicles($db, $Vehicle_Name, $Plate_No, $User_Id, $Capacity, $Vehicle_Status) {
    $Vehicle_Name = mysqli_real_escape_string($db, $Vehicle_Name);
    $Plate_No = mysqli_real_escape_string($db, $Plate_No);
    $User_Id = "0";
    $Capacity = mysqli_real_escape_string($db, $Capacity);
    $Vehicle_Status = "Available";

    // Check if the plate number already exists in the database
    $checkPlateNoQuery = "SELECT * FROM vehicles WHERE Plate_No='$Plate_No'";
    $checkPlateNoResult = mysqli_query($db, $checkPlateNoQuery);

    if (mysqli_num_rows($checkPlateNoResult) > 0) {
        // Plate number already exists, display an error message
        echo "<script>alert('Error: Plate number \"$Plate_No\" already exists in the database.'); window.location.href = 'add_vehicles.php';</script>";
        exit;
    }

    // Insert the user into the database
    $query = "INSERT INTO vehicles (Vehicle_Name, Plate_No, User_Id, Capacity, Vehicle_Status) 
              VALUES ('$Vehicle_Name','$Plate_No','$User_Id', '$Capacity', '$Vehicle_Status')";
    mysqli_query($db, $query);

    // Redirect to the user management page after successful user addition
    header('location: admin_vehicles.php');
}

// Check if the add user form is submitted
if (isset($_POST['add_vehicles'])) {
    // Retrieve the form input values
    $Vehicle_Name = $_POST['Vehicle_Name'];
    $Plate_No = $_POST['Plate_No'];
    $User_Id = $_POST['User_Id'];
    $Capacity = $_POST['Capacity'];
    $Vehicle_Status = $_POST['Vehicle_Status'];
  
    // Call the addUser() function to add the user to the database
    addVehicles($db, $Vehicle_Name, $Plate_No,$User_Id, $Capacity, $Vehicle_Status);
}
?>
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Vehicle</title>
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
                <img src="./images/dp.png"
                    class="nav-img" alt="dp">
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
					<div class="nav-option option5">
						<img src="./images/flower.png" class="nav-img" alt="browse">
						<h4>Flowers</h4>
					</div>
					</a>

                    <a href="admin_orders.php" class="link">
					<div class="nav-option option4">
                        <img src="./images/orders.png" class="nav-img" alt="edit profile">
						<h4>Orders</h4>
					</div>
                    </a>

                    <a href="admin_vehicles.php" class="link">
					<div class="nav-option option1">
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
					<div class="nav-option option4">
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
                    <h1 class="recent-Articles">Add Vehicle</h1>
                  
                    

                </div>
    <style>
        .recent-Articles {
    color: #C1AEFF !important;
}

    </style>
                       
                
                <form method="post" class="the-form" action="add_vehicles.php">
                 
                <div class="txt-field input-wrapper">
          <input  type="text" name="Vehicle_Name" required>
          <label for="Vehicle_Name">Vehicle Name</label>
          
        </div>
        <div class="txt-field input-wrapper">
          <input  type="text" name="Plate_No" required>
          <label for="Plate_No">Plate No</label>
          
        </div>
        
       


        <div class="txt-field input-wrapper">
    <input type="number" name="Capacity" required oninput="validateCapacity(this)">
    <label for="Capacity">Capacity</label>
</div>
        <script>
    function validateCapacity(input) {
        // Get the entered value
        var value = input.value;

        // Check if the value is a valid positive integer
        if (!/^[1-9]\d*$/.test(value)) {
            // If not valid, set the input value to an empty string
            input.value = '';
        }
    }
</script>
        
        <div>
       <button class = "view" type="submit" name="add_vehicles" style="color: white; border-radius: 10px; text-align: center; font-size: 15px; line-height: 15px; margin-right: 10px;" >Save</button>
       <button class="view" style="color: white;" onclick="window.location.href='admin_vehicles.php'">Cancel</button>
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

    <script src="./js/admin.js"></script>
</body>

</html>

