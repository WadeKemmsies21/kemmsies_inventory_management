<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kemmsies_inventory";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful";
}

$sql = "SELECT item_number, item_description, quantity FROM items";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inventory.css">
    <title>Inventory</title>
</head>
<style>
        
    </style>
<body>
    <header>
        <h2>Kemmsies Inventory Management System</h2>
    </header>

    <a href="inventory.php"><button>Total Inventory</button></a>
    <a href="add.php"><button>Add Inventory</button></a>
    <a href="scan.php"><button>Scan/Remove</button></a>
    <a href="print.php"><button>Print Inventory</button></a>

    <h2>Inventory</h2>

    <table>
        <tr>
            <th>Item #</th>
            <th>Item Desc.</th>
            <th>Quantity</th>
        </tr>
    <?php
    if ($result->num_rows > 0) {
        echo "<table><tr><th>Item #</th><th>Item Desc.</th><th>Quantity</th></tr>";
        while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["item_number"]."</td><td>".$row["item_description"]."</td><td>".$row["quantity"]."</td></tr>";
        }
        echo "</table>";
    } 
      $conn->close();
    ?>
</body>
</html>

