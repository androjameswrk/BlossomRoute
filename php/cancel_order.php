<?php
// Include your database configuration file
require_once '../db_config.php';

// Check if order_id parameter is present
if (isset($_GET['order_id'])) {
    // Get the order_id from the parameter
    $order_id = $_GET['order_id'];

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sample SQL statements (modify according to your database schema)
    $sqlDeleteOrderStatus = "DELETE FROM order_status WHERE Order_Id = '$order_id'";
    $sqlDeleteOrderDetails = "DELETE FROM order_details WHERE Order_Id = '$order_id'";
    $sqlDeleteOrder = "DELETE FROM orders WHERE Order_Id = '$order_id'";

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Execute SQL statements
        $conn->query($sqlDeleteOrderStatus);
        $conn->query($sqlDeleteOrderDetails);
        $conn->query($sqlDeleteOrder);

        // Commit the transaction if all queries are successful
        $conn->commit();

        // Provide a success message
        echo '<script>
        alert("Order cancelled successfully.");

        // Close the modal using the closeModal function
        closeModal(); // This assumes closeModal() is defined in my_order.php

        // Refresh the current page
        location.reload();
        </script>';

    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $conn->rollback();

        // Handle the error (you might want to log it or show a user-friendly message)
        echo '<script>
                alert("Error cancelling order: ' . $e->getMessage() . '");
                window.location.href = "my_order.php"; // Redirect to your main page
              </script>';
    }

    // Close the database connection
    $conn->close();
} else {
    // Handle case where order_id parameter is not present
    echo '<script>
            alert("Order ID not provided.");
            window.location.href = "my_order.php"; // Redirect to your main page
          </script>';
}
?>