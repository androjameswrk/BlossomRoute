<?php
require_once 'db_config.php'; 
session_start();

// connect to the database
$db = mysqli_connect($servername, $username, $password, $dbname);

// Function to add a user to the database
function addUser($db, $firstName, $lastName, $email, $password, $contact, $role, $status) {
    $firstName = mysqli_real_escape_string($db, $firstName);
    $lastName = mysqli_real_escape_string($db, $lastName);
    $email = mysqli_real_escape_string($db, $email);
    $password = mysqli_real_escape_string($db, $password);
    $contact = mysqli_real_escape_string($db, $contact);
    $role = mysqli_real_escape_string($db, $role);
    $status = "Active";

    // Check if the email contains the "@" symbol
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Invalid email format. Please enter a valid email address.");</script>';
        return; // Exit the function if email format is invalid
    }


// Check if the email already exists in the users table
$check_query = "SELECT id FROM users WHERE email = '$email'";
$check_result = mysqli_query($db, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    echo '<script>alert("Email already exists. Please choose a different email.");</script>';
    return;
 // Exit the function if email already exists
}
    // Encrypt the password before saving in the database
    $password = md5($password);

    // Insert the user into the database
    $query = "INSERT INTO users (email, firstName, lastName, role, password, contact,status) 
              VALUES ('$email', '$firstName', '$lastName', '$role', '$password', '$contact','$status')";
    mysqli_query($db, $query);
    header('location: user_management.php');
}

// Check if the add user form is submitted
if (isset($_POST['add_user'])) {
    // Retrieve the form input values
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contact = $_POST['contact'];
    $role = $_POST['role'];
    $status = "Active";

    // Call the addUser() function to add the user to the database
    addUser($db, $firstName, $lastName, $email, $password, $contact, $role, $status);
}

function generateRoleOptions($selectedRole) {
    $userOptionSelected = ($selectedRole == 1) ? 'selected' : '';
    $adminOptionSelected = ($selectedRole == 2) ? 'selected' : '';
    $vehicleOptionSelected = ($selectedRole == 3) ? 'selected' : '';


    $html = '<select name="role" required>';
    $html .= '<option value="1" ' . $userOptionSelected . '>User</option>';
    $html .= '<option value="2" ' . $adminOptionSelected . '>Admin</option>';
    $html .= '<option value="3" ' . $vehicleOptionSelected . '>Vehicle</option>';
    $html .= '</select>';

    return $html;
}
?>
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add User</title>
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
                        <div class="nav-option option1">
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
						<div class="nav-option option4">
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
				</div>
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
                    <h1 class="recent-Articles">Add User</h1>
                    <style>
        .recent-Articles {
    color: #C1AEFF !important;
}
.div1, .div2 {
    display: inline-block;
    width: 50%; /* You can adjust the width as needed */
    margin-bottom: -5px !important; 
}

    </style>                 
                    

                </div>
                
                <form method="post" class="the-form" action="add_user.php">
                <div class="txt-field input-wrapper">
          <input  type="text" name="email" required>
          <label>Email</label>
        </div>
        <div class="txt-field input-wrapper">
          <input  type="text" name="firstName" required>
          <label>First Name</label>
        </div>
        <div class="txt-field input-wrapper">
          <input  type="text" name="lastName" required>
          <label>Last Name</label>
        </div>
        <div class="txt-field input-wrapper">
          <input  type="text" name="password" required>
          <label>Password</label>
        </div>
        <div class="txt-field input-wrapper">
          <input  type="text" name="contact" required>
          <label>Contact No</label>
        </div>

        <script>
    document.addEventListener("DOMContentLoaded", function() {
        var inputElement = document.querySelector('input[name="contact"]');
        inputElement.addEventListener('input', function() {
            // Remove non-numeric characters
            var sanitizedValue = this.value.replace(/[^0-9]/g, '');

            // Limit the length to 12 digits
            if (sanitizedValue.length > 11) {
                sanitizedValue = sanitizedValue.slice(0, 11);
            }

            // Update the input value
            this.value = sanitizedValue;
        });
    });
</script>
        
           
                    <label style="margin-left: 7px; margin-right: 12px; color: #C1AEFF">Role</label>
                    <select name="role" style="color: white; width: 145px; background-color: #C1AEFF; border: 1px solid #C1AEFF; border-radius: 10px; text-align: center; font-size: 15px; line-height: 60px; padding: 5px; margin-right: 50px;">
    <?php
    $selectedRole = 1; // Set the selected role value here based on your logic
    $roles = [
      //  "Admin" => "Admin",
        "Customer" => "Customer",
        "Vehicle" => "Vehicle Rider"
    ];

    foreach ($roles as $roleValue => $roleLabel) {
        $selected = ($selectedRole === $roleValue) ? 'selected' : '';
        echo "<option value='$roleValue' style='color: white; background-color: #C1AEFF; $selected'>$roleLabel</option>";
    }
    ?>
</select>

            <br>
            <br>
    
        <button type="submit" style="color: white; border-radius: 10px; text-align: center; font-size: 15px; line-height: 15px; margin-right: 10px;" name="add_user">Add User</button>
        <button class="view" style="color: white; " onclick="window.location.href='user_management.php'">Cancel</button>
    </form>
    
            </div>
        </div>
    </div>
    <script>
        const flowerImageInput = document.getElementById("flower_image");
        const imageDisplay = document.getElementById("imageDisplay");

        flowerImageInput.addEventListener("change", function() {
            const file = flowerImageInput.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imageDisplay.src = e.target.result;
                    imageDisplay.style.display = "block";
                };

                reader.readAsDataURL(file);
            } else {
                imageDisplay.src = "";
                imageDisplay.style.display = "none";
            }
        });
    </script>
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
        .view1 {
            background-color: #9AD9DB;
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

