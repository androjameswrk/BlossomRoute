<?php
require_once 'server.php';
require_once 'db_config.php';

// Database connection parameters
// Establish a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$flowersOnPage = [];

// Define the number of records to display per page
$recordsPerPage = 5;

// Get the current page number from the URL
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = intval($_GET['page']);
} else {
    $currentPage = 1;
}

// Calculate the starting record for the current page
$offset = ($currentPage - 1) * $recordsPerPage;

// Initialize the search query
$searchQuery = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT flowers.image_filename,
        flowers.Flower_Id,
        flowers.Flower_Name,
        GROUP_CONCAT(flower_sizes.Size ORDER BY flower_sizes.Size DESC SEPARATOR ', ') AS available_sizes,
        GROUP_CONCAT(flower_sizes.Price ORDER BY flower_sizes.Size DESC SEPARATOR ', ') AS available_prices
        FROM flowers
        INNER JOIN flower_sizes ON flowers.Flower_Id = flower_sizes.Flower_Id";

// Check if a search query is provided
if (!empty($searchQuery)) {
    $sql .= " WHERE flowers.Flower_Name LIKE '%$searchQuery%'";
}

$sql .= " GROUP BY flowers.Flower_Id, flowers.Flower_Name
         LIMIT $offset, $recordsPerPage";

$result = $conn->query($sql);

// Fetch the filtered flowers
while ($row = $result->fetch_assoc()) {
    $flowersOnPage[] = $row;
}

// Calculate the total number of records
$totalRecords = $conn->query("SELECT COUNT(*) as count FROM flowers")->fetch_assoc()['count'];

// Calculate the total number of pages
$totalPages = ceil($totalRecords / $recordsPerPage);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Flowers</title>
    <link rel="stylesheet" href="./css/admin-style.css">
</head>

<body>

    <!-- Header -->
    <header>
        <div class="logosec">
            <div class="logo">
                <img src="./img/finalhome.png" alt="Image Failed to Load" width="240" height="50" style="margin-top: 22px;">
            </div>
        </div>

        
        <form method="GET" class="searchbtn">
        <div class="searchbar">
          
                <input type="text" name="search" placeholder="Search">
               
                <div class="searchbtn">
                <img src="./images/search.png" class="nav-img" alt="search">
                </div>
               
           
        </div>
        </form>

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

    <!-- Main Container -->
    <div class="main-container">
        <!-- Navigation -->
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
        <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;

                    }

                    th,
                    td {
                        text-align: left;
                        padding: 10px;
                        /* Add padding for spacing */

                    }

                    .t-op {
                        font-size: 15px;
                    }

                    /* Match the width of thead cells with tbody cells */
                    thead th {

                        width: 9%;
                        /* 100% / 6 columns  16.67%*/

                    }
                </style>


        <!-- Main Content -->
        <div class="main">
            <div class="searchbar2">
                <input type="text" name="" id="" placeholder="Search">
                <div class="searchbtn">
                    <img src="https://media.geeksforgeeks.org/wp-content/uploads/20221210180758/Untitled-design-(28).png" class="icn srchicn" alt="search-button">
                </div>
            </div>

            <!-- Report Container -->
            <div class="report-container">
                <div class="report-header">
                    <h1 class="recent-Articles">Flowers</h1>
                    <button class="view" onclick="window.location.href='add_flowers.php'">Add</button>
                </div>

                <!-- Table -->
                <table>
                    <thead>
                        <tr>
                            <th><h3 class="t-op">Image</h3></th>
                            <th><h3 class="t-op">Flower ID</h3></th>
                            <th><h3 class="t-op">Flower Name</h3></th>
                            <th><h3 class="t-op">Available Size</h3></th>
                            <th><h3 class="t-op">Prices</h3></th>
                            <th><h3 class="t-op">Actions</h3></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (empty($flowersOnPage)) {
                        echo '<tr><td colspan="6">No flowers found.</td></tr>';
                    } else {
                        foreach ($flowersOnPage as $flower) :
                    ?>
                            <tr>
                                <td style="font-size: 15px">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($flower['image_filename']); ?>" alt="Image" style="width: 40%; height: 40%;">
                                </td>
                                <td style="font-size: 15px"><?php echo $flower['Flower_Id']; ?></td>
                                <td style="font-size: 15px"><?php echo $flower['Flower_Name']; ?></td>
                                <td style="font-size: 15px"><?php echo $flower['available_sizes']; ?></td>
                                <td style="font-size: 15px"><?php echo $flower['available_prices']; ?></td>
                                <td>
                                    <button class="header-top-btn view">
                                        <a href='edit_flowers.php?flower_id=<?php echo $flower["Flower_Id"]; ?>' style="color:white;   text-decoration: none;">Edit</a>
                                    </button>
                                </td>
                            </tr>
                    <?php endforeach;
                    } ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class='pagination' style="text-align: right; margin-right: 40px; margin-top: 10px; font-size: 15px;">
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href='?page=<?php echo $i; ?>&search=<?php echo $searchQuery; ?>'><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>


               

            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="./js/admin.js"></script>
  
</body>

</html>
