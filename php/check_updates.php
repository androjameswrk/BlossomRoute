<?php
// check_updates.php

// Include your database configuration file
require_once 'db_config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Implement your query logic to count orders with updated tracking status
$sql = "SELECT COUNT(*) as count FROM order_status WHERE New IS NOT NULL OR Shipped IS NOT NULL OR Delivered IS NOT NULL OR Completed IS NOT NULL";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $count = $row['count'];

    // Return the count as a response
    echo $count;
} else {
    // Handle the case where no orders are found
    echo 0;
}

// Close the database connection
$conn->close();
?>
