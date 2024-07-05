<?php
require_once 'server.php';
// Check if user is logged in and is an admin, otherwise redirect to login page

// Create a SQL query to retrieve the data
$query = "SELECT SUM(total) AS total_stock
FROM harvest"; 

$result = mysqli_query($db, $query);


$total_stock_display = '';

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_stock = $row["total_stock"];
    $total_stock_display = $total_stock;
} else {
    $total_stock_display = "No results found";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible"content="IE=edge">
	<meta name="viewport"content="width=device-width, initial-scale=1.0">
	<title>Admin - Dashboard</title>
	<link rel="stylesheet" href="./css/admin-style.css">
</head>

<body>
<div class="content">
        <?php if (count($errors) > 0) : ?>
            <div class="error">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach ?>
            </div>
        <?php endif ?>
	<!-- for header part -->
	<header>

		<div class="logosec">
			<div class="logo">
                <img src="./img/finalhome.png" alt="Image Failed to Load" width="240" height="50" style="margin-top: 22px;">
            </div>
		</div>

		<div class="searchbar">
			<input type="text"
				placeholder="Search">
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

					<div class="nav-option option1">
						<img src="./images/dashboard.jpg" class="nav-img" alt="dashboard">
						<h4>Dashboard</h4>
					</div>

					<a href="user_management.php" class="link">
                        <div class="nav-option option2">
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
				<input type="text"
					name=""
					id=""
					placeholder="Search">
				<div class="searchbtn">
				<img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180758/Untitled-design-(28).png" class="icn srchicn" alt="search-button">
				</div>
			</div>
			<script>
        function redirectToHarvest() {
            
            window.location.href = 'report_harvest.php';
        }
		function redirectToFlower() {
            
            window.location.href = 'report_flowers.php';
        }
		function redirectToOrders() {
            
            window.location.href = 'report_orders.php';
        }
		function redirectToEarnings() {
            
            window.location.href = 'report_earnings.php';
        }
    </script>
			<div class="box-container">

				<div class="box box1" onclick="redirectToHarvest()">
					<div class="text">
					<h2 class="topic-heading" data-order-id="<?= htmlspecialchars($total_stock_display) ?>">
    <?php echo $total_stock_display; ?>
</h2>



						<h2 class="topic">Harvested Flowers</h2>
					</div>
                    <img src= "./images/transaction.png" alt="published">
				</div>
				

				<div class="box box2" onclick="redirectToFlower()">
				<?php
require_once 'server.php';

// Create a SQL query to retrieve the total quantity from the order_details table
$quantityQuery = "SELECT SUM(Qty) AS total_quantity FROM order_details";

$quantityResult = mysqli_query($db, $quantityQuery);

$total_quantity_display = '';

if ($quantityResult->num_rows > 0) {
    $quantityRow = $quantityResult->fetch_assoc();
    $total_quantity = $quantityRow["total_quantity"];
    $total_quantity_display = $total_quantity;
} else {
    $total_quantity_display = "No quantity found";
}
?>

					<div class="text">
					<h2 class="topic-heading" data-order-id="<?= htmlspecialchars($total_quantity_display) ?>">
    <?php echo $total_quantity_display; ?>
</h2>

						<h2 class="topic">Flowers Sold</h2>
					</div>
                    <img src= "./images/petals.png" alt="published">
				</div>

				<div class="box box3" onclick="redirectToOrders()">
					<div class="text">
					<?php
					require_once 'server.php';
					// Check if user is logged in and is an admin, otherwise redirect to login page

					// Create a SQL query to retrieve the data
					$query = "SELECT SUM(total) AS total_stock FROM harvest"; 

					$result = mysqli_query($db, $query);

					$total_stock_display = '';

					if ($result->num_rows > 0) {
						$row = $result->fetch_assoc();
						$total_stock = $row["total_stock"];
						$total_stock_display = $total_stock;
					} else {
						$total_stock_display = "No results found";
					}

					// Query to get the total orders count
					$ordersQuery = "SELECT COUNT(*) AS total_orders FROM orders";
					$ordersResult = mysqli_query($db, $ordersQuery);

					$total_orders_display = '';

					if ($ordersResult->num_rows > 0) {
						$ordersRow = $ordersResult->fetch_assoc();
						$total_orders = $ordersRow["total_orders"];
						$total_orders_display = $total_orders;
					} else {
						$total_orders_display = "No orders found";
					}
					?>
						<h2 class="topic-heading"  class="topic-heading" data-order-id="<?= htmlspecialchars($total_orders_display) ?>">
    <?php echo $total_orders_display; ?>
</h2>
</h2>
						<h2 class="topic">Total Orders</h2>
					</div>
                    <img src= "./images/comments.png" alt="published">
				</div>
				<?php
require_once 'server.php';

$query = "
SELECT
O.Order_Id,
CONCAT(users.firstname, ' ', users.lastname) AS User_Name,
O.Address,
O.PaymentMethod,
O.Order_Date,
SUM(OD.Qty * OD.Price) AS TotalPrice,
CASE
	WHEN COALESCE(STR_TO_DATE(New, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
		COALESCE(STR_TO_DATE(New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
	) THEN 'New'
	WHEN COALESCE(STR_TO_DATE(Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
		COALESCE(STR_TO_DATE(New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
	) THEN 'Hold'
	WHEN COALESCE(STR_TO_DATE(Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
		COALESCE(STR_TO_DATE(New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
	) THEN 'Shipped'
	WHEN COALESCE(STR_TO_DATE(Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
		COALESCE(STR_TO_DATE(New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
	) THEN 'Delivered'
	WHEN COALESCE(STR_TO_DATE(Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01') = GREATEST(
		COALESCE(STR_TO_DATE(New, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Hold, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Shipped, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Delivered, '%Y-%m-%d %H:%i:%s'), '1000-01-01'),
		COALESCE(STR_TO_DATE(Completed, '%Y-%m-%d %H:%i:%s'), '1000-01-01')
	) THEN 'Completed'
END AS Current_Status
FROM
orders AS O
JOIN
order_details AS OD ON O.Order_Id = OD.Order_Id
JOIN
users ON O.User_Id = users.id
LEFT JOIN
order_status AS OS ON O.order_id = OS.order_id
GROUP BY
O.Order_Id, User_Name, O.Address, O.PaymentMethod, O.Order_Date
ORDER BY
O.Order_Date DESC;

";

$result = mysqli_query($db, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

// Fetch the combined results into an array
$combinedOrders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $combinedOrders[] = $row;
}

// Calculate the total price of all orders
$totalPrice = 0;
foreach ($combinedOrders as $order) {
    $totalPrice += $order['TotalPrice'];
}
?>
 
				<div class="box box4" onclick="redirectToEarnings()">
					<div class="text">
						
						<h2 class="topic-heading" ><?php echo number_format($totalPrice); ?></h2>
						<h2 class="topic">Earnings</h2>
					</div>
                    <img src= "./images/deal.png" alt="published">
				</div>
			
			</div>

			<div class="report-container">
				<div class="report-header">
					<h1 class="recent-Articles">Recent Transactions</h1>
					<button class="view" onclick="redirectToAdminOrders()">View All</button>

<script>
function redirectToAdminOrders() {
    window.location.href = 'admin_orders.php';
}
</script>

				</div>

				<div class="report-body">
    <div class="report-topic-heading">
        <h3 class="t-op"  style="width: 17%;">Order ID</h3>
		<h3 class="t-op"  style="width: 17%;">Order Date</h3>
        <h3 class="t-op"  style="width: 17%;">Payment Method</h3>
        <h3 class="t-op"  style="width: 17%;">Total Price</h3>
        <h3 class="t-op"  style="width: 10%;">Status</h3>
    </div>

    <div class="items">
        <?php foreach ($combinedOrders as $order) : ?>
            <div class="item1">
                <h3 class="t-op-nextlvl" style="width: 17%;"><?php echo $order['Order_Id']; ?></h3>
				<h3 class="t-op-nextlvl" style="width: 17%;"><?php echo $order['Order_Date']; ?></h3>
                <h3 class="t-op-nextlvl" style="width: 17%;"><?php echo $order['PaymentMethod']; ?></h3>
                <h3 class="t-op-nextlvl" style="width: 17%;"><?php echo number_format($order['TotalPrice']); ?></h3>
				<h3 class="t-op-nextlvl" style="width: 10%;">
    <?php
        $statusEmoji = '';
        switch ($order['Current_Status']) {
            case 'New':
                $statusEmoji = '🔴';
                break;
            case 'Hold':
                $statusEmoji = '🟡';
                break;
            case 'Shipped':
                $statusEmoji = '🔵';
                break;
            case 'Delivered':
                $statusEmoji = '🟣';
                break;
            case 'Completed':
                $statusEmoji = '🟢';
                break;
            default:
                $statusEmoji = '❓'; // Handle unknown status
                break;
        }
        echo $statusEmoji . ' ' . $order['Current_Status'];
    ?>
</h3>
            </div>
        <?php endforeach; ?>
    </div>
</div>



				<style>
					
				</style>
						
							
						</div>


					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="./js/admin.js"></script>
	</div>
</body>
</html>
