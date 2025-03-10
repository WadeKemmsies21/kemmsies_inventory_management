<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kemmsies_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include('index.php'); // Include the header

?>

<h2>Inventory</h2>

<table>
    <tr>
        <th>Item #</th>
        <th>Item Desc.</th>
        <th>Quantity</th>
        <th>Price per unit</th>
        <th>Total Amount</th>
    </tr>
    <?php
    $sql = "SELECT item_number, item_description, quantity, price_per_unit FROM items";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Calculate the total price
            $total_price = $row['price_per_unit'] * $row['quantity'];

            echo "<tr>
                    <td>{$row['item_number']}</td>
                    <td>{$row['item_description']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['price_per_unit']}</td>
                    <td>{$total_price}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No inventory found</td></tr>";
    }
    $conn->close();
    ?>
</table>
</body>
</html>
