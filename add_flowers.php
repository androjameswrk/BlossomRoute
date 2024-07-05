<?php
// Check if the user is logged in
require_once 'db_config.php'; 
session_start();

// Connect to the database
$db = mysqli_connect($servername, $username, $password, $dbname);

function isFlowerExists($db, $Flower_Name) {
    $Flower_Name = mysqli_real_escape_string($db, $Flower_Name);
    $query = "SELECT COUNT(*) FROM flowers WHERE Flower_Name = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, 's', $Flower_Name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $count > 0;
}

// Function to add a flower and sizes to the database
function addFlowersAndSizes($db, $Flower_Name, $imageFilename, $imageType, $SizeSmall, $PriceSmall, $SizeMedium, $PriceMedium, $SizeLarge, $PriceLarge) {
    $Flower_Name = mysqli_real_escape_string($db, $Flower_Name);
    $SizeSmall = mysqli_real_escape_string($db, $SizeSmall);
    $PriceSmall = mysqli_real_escape_string($db, $PriceSmall);
    $SizeMedium = mysqli_real_escape_string($db, $SizeMedium);
    $PriceMedium = mysqli_real_escape_string($db, $PriceMedium);
    $SizeLarge = mysqli_real_escape_string($db, $SizeLarge);
    $PriceLarge = mysqli_real_escape_string($db, $PriceLarge);

    // Validate the prices before inserting
    if (
        (!empty($SizeSmall) && !empty($PriceSmall) && floatval($PriceSmall) > 0) ||
        (!empty($SizeMedium) && !empty($PriceMedium) && floatval($PriceMedium) > 0) ||
        (!empty($SizeLarge) && !empty($PriceLarge) && floatval($PriceLarge) > 0)
    ) {
        // Check if the flower already exists
        if (isFlowerExists($db, $Flower_Name)) {
            echo '<script>alert("Flower with the same name already exists. Please choose a different name.");</script>';
            return false;
        }

        // Insert the flower into the "flowers" table
        $query = "INSERT INTO flowers (Flower_Name, image_filename, image_type) 
                  VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, 'sss', $Flower_Name, $imageFilename, $imageType);

        if (mysqli_stmt_execute($stmt)) {
            // Get the Flower_Id of the inserted flower
            $flowerId = mysqli_insert_id($db);

            // Insert the flower sizes into the "flower_sizes" table
            $querySizes = "INSERT INTO flower_sizes (Flower_Id, Size, Price) VALUES (?, ?, ?)";
            $stmtSizes = mysqli_prepare($db, $querySizes);

            if (!empty($SizeSmall) && !empty($PriceSmall) && floatval($PriceSmall) > 0) {
                mysqli_stmt_bind_param($stmtSizes, 'iss', $flowerId, $SizeSmall, $PriceSmall);
                mysqli_stmt_execute($stmtSizes);
            }

            if (!empty($SizeMedium) && !empty($PriceMedium) && floatval($PriceMedium) > 0) {
                mysqli_stmt_bind_param($stmtSizes, 'iss', $flowerId, $SizeMedium, $PriceMedium);
                mysqli_stmt_execute($stmtSizes);
            }

            if (!empty($SizeLarge) && !empty($PriceLarge) && floatval($PriceLarge) > 0) {
                mysqli_stmt_bind_param($stmtSizes, 'iss', $flowerId, $SizeLarge, $PriceLarge);
                mysqli_stmt_execute($stmtSizes);
            }

            mysqli_stmt_close($stmtSizes);

            // Return true if flower and sizes added successfully
            return true;
        } else {
            // Handle the error
            echo "Error adding flower: " . mysqli_error($db);
            return false;
        }
    } else {
       
        return false;
    }
}

// Check if the add flower form is submitted
if (isset($_POST['add_flowers'])) {
    // Retrieve the form input values
    $Flower_Name = $_POST['Flower_Name'];

    // Check if an image file was uploaded
    if (isset($_FILES['flower_image']) && $_FILES['flower_image']['error'] === UPLOAD_ERR_OK) {
        $imageFilename = file_get_contents($_FILES['flower_image']['tmp_name']);
        $imageType = $_FILES['flower_image']['type'];

        // Add flower and sizes to "flowers" and "flower_sizes" tables
        if (addFlowersAndSizes($db, $Flower_Name, $imageFilename, $imageType, $_POST['SizeSmall'], $_POST['PriceSmall'], $_POST['SizeMedium'], $_POST['PriceMedium'], $_POST['SizeLarge'], $_POST['PriceLarge'])) {
            // Redirect to the user management page after a successful flower addition
            echo '<script>alert("Roses added successfully.");</script>';
            header('location: admin_flowers.php');
            exit(); // Make sure to exit after redirecting
        } else {
            echo '<script>alert("Error adding flower. Please try again.");</script>'; 
        }
    } else {
        echo '<script>alert("Please upload an image.");</script>';
    }
}
?>






    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Flower</title>
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
					<div class="nav-option option1">
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
                    <h1 class="recent-Articles">Add Flower</h1>
                  
                    

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
    
                       
                
    <form method="post" class="flex flex-row the-form" action="add_flowers.php" enctype="multipart/form-data">
    <div class="flex-container">
        <div class="flex-col">
            <div class="txt-field input-wrapper" style="margin-bottom: 10px; margin-left: 25px;">
                <input type="text" name="Flower_Name" required>
                <label for="Flower_Name">Flower Name</label>
            </div>
            <br>
         
            
            <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
            <div class="file-upload">
  <button class="file-upload-btn" type="button" onclick="$('.file-upload-input').trigger( 'click' )" style = "font-size: 16px; text-transform: Normal;">Upload Image</button>

  <div class="image-upload-wrap">
    <input class="file-upload-input" name="flower_image" id="flower_image"  type='file' onchange="readURL(this);" accept="image/*" />
    <div class="drag-text">
      <h3>Drag and drop a file or select add Image</h3>
    </div>
  </div>
  <div class="file-upload-content">
    <img class="file-upload-image" src="#" alt="your image" />
    <div class="image-title-wrap">
      
    </div>
  </div>
</div>

<style>
body {
  font-family: sans-serif;
  background-color: #eeeeee;
}

.file-upload {
    
  background-color: #ffffff;
  width: 300px; /* Adjust width to your preference */
  height: 300px; /* Set height to match the width */
  margin: 0 auto;
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.file-upload-btn {
  width: 100%;
  margin: 0;
  color: #fff;
  background: #C1AEFF;
  border: none;
  padding: 10px;
  border-radius: 4px;
  border-bottom: 4px solid #C1AEFF;
  transition: all .2s ease;
  outline: none;
  
  font-weight: 700;
}

.file-upload-btn:hover {
  background: #C1AEFF;
  color: #ffffff;
  transition: all .2s ease;
  cursor: pointer;
}

.file-upload-btn:active {
  border: 0;
  transition: all .2s ease;
}

.file-upload-content {
  display: none;
  text-align: center;
}

.file-upload-input {
  position: absolute;
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100%;
  outline: none;
  opacity: 0;
  cursor: pointer;
}

.image-upload-wrap {
  margin-top: 20px;
  border: 4px dashed #C1AEFF;
  position: relative;
}

.image-dropping,
.image-upload-wrap:hover {
  background-color: #C1AEFF;
  color: #ffffff;
  border: 4px dashed #ffffff;
}
.image-upload-wrap:hover .drag-text h3 {
    color: white;
  }

.image-title-wrap {
  padding: 0 15px 15px 15px;
  color: #222;
}

.drag-text {
  text-align: center;
}

.drag-text h3 {
  font-weight: 100;
  text-transform: uppercase;
  color: #C1AEFF;
  padding: 60px 0;
}


.file-upload-image {
  max-height: 200px;
  max-width: 200px;
  margin: auto;
  padding: 20px;
}

.remove-image {
  width: 200px;
  margin: 0;
  color: #fff;
  background: #cd4535;
  border: none;
  padding: 10px;
  border-radius: 4px;
  border-bottom: 4px solid #b02818;
  transition: all .2s ease;
  outline: none;
  text-transform: uppercase;
  font-weight: 700;
}

.remove-image:hover {
  background: #c13b2a;
  color: #ffffff;
  transition: all .2s ease;
  cursor: pointer;
}

.remove-image:active {
  border: 0;
  transition: all .2s ease;
}

.square-button {
        background: none;
        border: 2px solid #C1AEFF; /* Add borders with color */
        padding-left: 7px;
        padding-right: 7px;
        margin: 10px;
        cursor: pointer;
        font-size: 15px;
        color: #C1AEFF;
    }
    .summary-label {
        color: #C1AEFF;
        padding-bottom: 5px;
        font-weight: normal; /* Make it non-bold */
    }

</style>

<script>
    function readURL(input) {
  if (input.files && input.files[0]) {

    var reader = new FileReader();

    reader.onload = function(e) {
      $('.image-upload-wrap').hide();

      $('.file-upload-image').attr('src', e.target.result);
      $('.file-upload-content').show();

      $('.image-title').html(input.files[0].name);
    };

    reader.readAsDataURL(input.files[0]);

  } else {
    removeUpload();
  }
}



</script>
           
        </div>
        <div class="flex-col upload-form" style="margin-right: 280px;">
        <label for="Color" style="margin-bottom: 10px; color:#C1AEFF; margin-top:59px; font-size:16px">Sizes & Prizes</label>

        <div style="margin-bottom: 10px; font-weight: normal; font-size: 16px; margin-top: 10px; display: flex; align-items: center;">
           
        <div style="display: flex; align-items: center;">
    <label for="Size" style="display: block; margin-top: 5px; margin-right: 10px; color:#C1AEFF; margin-top: 10px;">Size</label>
    <input type="text" id="size" name="SizeSmall" value="Small" readonly  style="width: 50%; border: none; border-bottom: 2px solid #C1AEFF; background: none; outline: none; padding: 5px; color: #C1AEFF; margin-bottom: 10px; border-radius: 0px;">
</div>

            <div style="display: flex; align-items: center;">
                <label for="Price" style="display: block; margin-top: 5px; margin-right: 10px; color:#C1AEFF; margin-top: 10px;">Price</label>
                <input type="text" id="price" name="PriceSmall"  style="width: 50%; border: none; border-bottom: 2px solid #C1AEFF; background: none; outline: none; padding: 5px; color: #C1AEFF; margin-bottom: 10px; border-radius: 0px;">
             
            </div>
        </div>
        <div style="margin-bottom: 10px; font-weight: normal; font-size: 16px; margin-top: 10px; display: flex; align-items: center;">
           
           <div style="display: flex; align-items: center;">
       <label for="Size" style="display: block; margin-top: 5px; margin-right: 10px; color:#C1AEFF; margin-top: 10px;">Size</label>
       <input type="text" id="size" name="SizeMedium" value="Medium" readonly  style="width: 50%; border: none; border-bottom: 2px solid #C1AEFF; background: none; outline: none; padding: 5px; color: #C1AEFF; margin-bottom: 10px; border-radius: 0px;">
   </div>
   
               <div style="display: flex; align-items: center;">
                   <label for="Price" style="display: block; margin-top: 5px; margin-right: 10px; color:#C1AEFF; margin-top: 10px;">Price</label>
                   <input type="text" id="price" name="PriceMedium"  style="width: 50%; border: none; border-bottom: 2px solid #C1AEFF; background: none; outline: none; padding: 5px; color: #C1AEFF; margin-bottom: 10px; border-radius: 0px;">
                
               </div>
           </div>
           <div style="margin-bottom: 10px; font-weight: normal; font-size: 16px; margin-top: 10px; display: flex; align-items: center;">
           
           <div style="display: flex; align-items: center;">
       <label for="Size" style="display: block; margin-top: 5px; margin-right: 10px; color:#C1AEFF; margin-top: 10px;">Size</label>
       <input type="text" id="size" name="SizeLarge" value="Large" readonly  style="width: 50%; border: none; border-bottom: 2px solid #C1AEFF; background: none; outline: none; padding: 5px; color: #C1AEFF; margin-bottom: 10px; border-radius: 0px;">
   </div>
   
               <div style="display: flex; align-items: center;">
                   <label for="Price" style="display: block; margin-top: 5px; margin-right: 10px; color:#C1AEFF; margin-top: 10px;">Price</label>
                   <input type="text" id="price" name="PriceLarge"  style="width: 50%; border: none; border-bottom: 2px solid #C1AEFF; background: none; outline: none; padding: 5px; color: #C1AEFF; margin-bottom: 10px; border-radius: 0px;">
                  
               </div>
           </div>
      <!--  <label for="Size" style="color: #C1AEFF; padding-bottom: 5px; ">Summary</label> -->
          <!--  <div id="summary" class="summary-label"></div> -->

        <!-- Save Button -->
      
    </div>
  
    <script>
document.addEventListener("DOMContentLoaded", function() {
    var priceInputs = document.querySelectorAll('input[id^="price"]');

    priceInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            // Remove non-numeric characters
            var sanitizedValue = this.value.replace(/[^0-9]/g, '');

            // Ensure the value is not 0 or negative
            if (sanitizedValue <= 0) {
                sanitizedValue = '';
            }

            // Update the input value
            this.value = sanitizedValue;
        });
    });
});
</script>

        
        
    </div>
    <div>
        <button class="view" type="submit" name="add_flowers" style="color: white; border-radius: 10px; text-align: center; font-size: 15px; line-height: 15px; margin-right: 10px;" >Save</button>
        <button class="view" style="color: white;" onclick="window.location.href='admin_flowers.php'">Cancel</button>
    </div>
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

