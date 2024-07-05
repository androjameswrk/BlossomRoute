<?php
// Add this line to start the session
require_once '../server.php';

// Database connection parameters
$host = "localhost";
$user = "root";
$pwd = "";
$dbname = "helloworlddb";

// Establish a database connection
$conn = new mysqli($host, $user, $pwd, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the AJAX request is valid
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["productId"]) && isset($_POST["productSize"])) {
    // Get product ID and size from the request
    $productId = $_POST["productId"];
    $productSize = $_POST["productSize"];

    // TODO: Add the logic to remove the item from the cart_items table
    $stmtRemove = $conn->prepare("DELETE FROM cart_items WHERE Flower_Id = ? AND Size = ?");
    $stmtRemove->bind_param("is", $productId, $productSize);
    $stmtRemove->execute();
    $stmtRemove->close();

    echo "Item removed successfully"; // You can customize the response as needed
} else {
    echo "Invalid request"; // You can customize the response for invalid requests
}

// Close the database connection
$conn->close();
?>
