<?php
require_once 'db_config.php';
require 'vendor/autoload.php'; // Include the Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

session_start();

$firstName = "";
$lastName = "";
$email = "";
$errors = array();


$db = mysqli_connect($servername, $username, $password, $dbname);

// CHECK IF THE VERIFICATION FORM IS SUBMITTED
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CHECK IF THE VERIFICATION CODE IS SET AND NOT EMPTY
    if (isset($_POST['verification_code']) && !empty($_POST['verification_code'])) {
        // GET THE ENTERED VERIFICATION CODE
        $enteredCode = strtoupper($_POST['verification_code']);
        $enteredCode = strtolower($_POST['verification_code']);
        // CHECK IF THE ENTERED CODE MATCHES THE ONE STORED IN THE SESSION
        if (isset($_SESSION['confirmation_code']) && $_SESSION['confirmation_code'] === $enteredCode) {
            // VERIFICATION SUCCESSFUL

            // CHECK IF USER REGISTRATION DATA IS STORED IN THE SESSION
            if (isset($_SESSION['registration_data'])) {
                $registrationData = $_SESSION['registration_data'];

                // EXTRACT USER DETAILS
                $firstName = $registrationData['firstName'];
                $lastName = $registrationData['lastName'];
                $email = $registrationData['email'];
                $password = $registrationData['password'];

                // Hash the password using password_hash
                $hashedPassword = md5($password);

                // Set the role to "Customer"
                $role = "Customer";
                $status = "Active";

                // INSERT USER INTO THE DATABASE
                $query = "INSERT INTO users (email, firstName, lastName, password, role, status) 
                          VALUES('$email','$firstName','$lastName','$hashedPassword', '$role', '$status')";
                mysqli_query($db, $query);

                // SET USER SESSION DATA
                $_SESSION['email'] = $email;
                $_SESSION['firstName'] = $firstName;
                $_SESSION['lastName'] = $lastName;
                $_SESSION['role'] = $role; 
                $_SESSION['status'] = $status; // Set the user role in the session
              //  $_SESSION['success'] = "You are now logged in";


                header('location: ./php/customer_order.php');
                exit();
            } else {
                echo 'User registration data not found.';
            }
        } else {
            // VERIFICATION FAILED
            echo 'Verification failed. Please make sure you entered the correct code.';
        }
    } else {
        echo 'Verification code is missing.';
    }
}
?>
<!-- Your HTML form goes here -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        body {
            font-family: Poppins;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #E1D8FF;
            color: #623672;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-weight: bold;
        }

        input {
            width: 90%;
    height: 50px; 
 
    padding: 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 25px;
   
}

input:hover,
input:focus {
    border: none;
}

        form {
    margin-top: 20px;
}

button {
    width: 90%;
    height: 50px; /* Set the width to 100% */
    background-color:  #455D48;
    color: #fff;
    padding: 10px 20px;
    border: none;
    font-size: medium;
    cursor: pointer;
    border-radius: 25px;
    box-sizing: border-box; /* Ensure padding and border are included in the width */
    margin-top: 10px;
}

button:hover {
    background-color:#3E5340;
}

#resend-btn {
    width: 90%; /* Set the width to 100% */
    background-color: #343C74 ;
    font-size: medium;
    border-radius: 25px;
    color: #fff;
    padding: 10px 20px;
    border: none;
   
    cursor: pointer;
    box-sizing: border-box; /* Ensure padding and border are included in the width */
    margin-top: 10px;
    margin-bottom: 20px;
}

#resend-btn:hover {
    background-color: #2E3668;
}
    </style>

</head>
<body style="background-image: url('img/purple.jpg'); background-size: cover; background-position: center;">
    <div class="container" tyle = "font-family: Poppins">
    <div class="centered-div"><img src="./img/biggerlogo.png" alt="Image Failed to Load" width="150" height="150"></div>
     
        <form method="post" action="verification.php">
        <label style="font-family: 'Poppins', sans-serif; font-weight: normal; text-align: right; font-size: medium;" for="verification_code">Verification Code:</label>


            <input tyle = "font-family: Poppins font-size: medium;" type="text" name="verification_code" required>
            <button  tyle = "font-family: Poppins"type="submit">Verify</button>
        </form>

        <!-- Resend button and script -->
        <button tyle = "font-family: Poppins" id="resend-btn" onclick="handleResendClick()">Resend Code</button>

    </div>

    <script>
    let resendCount = 0;
    let countdownTimer;

    function handleResendClick() {
        if (resendCount === 0) {
            resendCount++;

            // Disable the button
            const resendBtn = document.getElementById('resend-btn');
            resendBtn.disabled = true;

            // Add logic to resend the verification code

            // You can use AJAX to send a request to the server to trigger the resend logic
            // For simplicity, this example uses the Fetch API

            fetch('resend_verification.php', {
                method: 'POST',
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response from the server
                alert(data.message);
            })
            .catch(error => {
                console.error('Error:', error);
            });

            let timeLeft = 60; // Set the initial countdown time in seconds

            // Update the button text with the countdown
            countdownTimer = setInterval(() => {
                resendBtn.innerHTML = `Resend Code (${timeLeft}s)`;

                if (timeLeft <= 0) {
                    // Enable the button after the timer expires
                    clearInterval(countdownTimer);
                    resendBtn.innerHTML = 'Resend Code';
                    resendBtn.disabled = false;
                    resendCount = 0; // Reset the resend count
                }

                timeLeft--;
            }, 1000); // Update every 1 second
        } else {
            alert('You can only resend the code once every 60 seconds.');
        }
    }
</script>

</body>
</html>