<?php
session_start();

// initializing variables
$firstname = "";
$lastname = "";
$email    = "";
$role = "customer";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'cargodb');

// REGISTER USER
if (isset($_POST['register'])) {

  // receive all input values from the forms
  $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password= mysqli_real_escape_string($db, $_POST['password']);
  $cpassword = mysqli_real_escape_string($db, $_POST['cpassword']);
  

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($firstname)) { array_push($errors, "Firstname is required"); }
  if (empty($lastname)) { array_push($errors, "Lastname is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password)) { array_push($errors, "Password is required"); }
  
  if ($password != $cpassword) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM tbl_users WHERE firstname='$firstname' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['firstname'] === $firstname) {
      array_push($errors, "Firstname already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  	//$password = md5($password_1);//encrypt the password before saving in the database

    $password = ($password_1);
  	$query = "INSERT INTO tbl_users (firstname, lastname, email, password, role) 
  			  VALUES('$firstname', '$lastname','$email', '$cpassword', '$role')";
  	mysqli_query($db, $query);
  	$_SESSION['firstname'] = $firstname;
  	$_SESSION['success'] = "Successfully Registered!";
  	header('location: login.php');
  }
}
?>