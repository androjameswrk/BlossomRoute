
<?php
require_once 'db_config.php'; 

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sqlSelectFlowers = "
    SELECT
        flowers.image_filename,
        flowers.Flower_Id,
        flowers.Flower_Name,
        GROUP_CONCAT(flower_sizes.Size ORDER BY flower_sizes.Size DESC SEPARATOR ', ') AS available_sizes,
        GROUP_CONCAT(flower_sizes.Price ORDER BY flower_sizes.Size DESC SEPARATOR ', ') AS available_prices
    FROM
        flowers
    INNER JOIN
        flower_sizes ON flowers.Flower_Id = flower_sizes.Flower_Id
    GROUP BY
        flowers.Flower_Id, flowers.Flower_Name;
";

$resultFlowers = $conn->query($sqlSelectFlowers);

if ($resultFlowers->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Flower Name</th><th>Available Sizes</th><th>Available Prices</th><th>Action</th></tr>";

    while ($rowFlower = $resultFlowers->fetch_assoc()) {
        $flowerId = $rowFlower['Flower_Id'];
        $flowerName = $rowFlower['Flower_Name'];
        $availableSizes = $rowFlower['available_sizes'];
        $availablePrices = $rowFlower['available_prices'];

        echo "<tr><td>$flowerId</td><td>$flowerName</td><td>$availableSizes</td><td>$availablePrices</td>";
        echo "<td><a href='edit_flower.php?flower_id=$flowerId'>Edit</a></td></tr>";
    }

    echo "</table>";
} else {
    echo "No flowers found";
}

$conn->close();
?>

