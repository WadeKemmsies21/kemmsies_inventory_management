<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kemmsies_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_numbers = $_POST['item_number'];
    $item_descriptions = $_POST['item_description'];
    $quantities = $_POST['quantity'];
    $prices_per_unit = $_POST['price_per_unit']; // Added field for price per unit

    $checkStmt = $conn->prepare("SELECT item_description, quantity, price_per_unit FROM items WHERE item_number = ?");
    $checkStmt->bind_param("s", $item_number);

    $updateStmt = $conn->prepare("UPDATE items SET quantity = quantity + ?, price_per_unit = ? WHERE item_number = ?");
    $updateStmt->bind_param("dss", $quantity, $price_per_unit, $item_number);

    $insertStmt = $conn->prepare("INSERT INTO items (item_number, item_description, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("ssds", $item_number, $item_description, $quantity, $price_per_unit); // Adjusted to bind decimal

    for ($i = 0; $i < count($item_numbers); $i++) {
        $item_number = $item_numbers[$i];
        $item_description = $item_descriptions[$i];
        $quantity = $quantities[$i];
        $price_per_unit = $prices_per_unit[$i]; // Get the price per unit from the form

        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $checkStmt->bind_result($existing_description, $existing_quantity, $existing_price_per_unit);
            $checkStmt->fetch();

            if ($existing_description !== $item_description) {
                echo "Item $item_number already exists. The description will not be updated.<br>";
            }

            // Update the quantity and price per unit if necessary
            if ($existing_price_per_unit != $price_per_unit) {
                echo "Item $item_number price per unit updated successfully.<br>";
            }

            $updateStmt->execute();
            echo "Quantity for item $item_number updated successfully.<br>";
        } else {
            $insertStmt->execute();
            header("Location: inventory.php");
            // echo "New item $item_number added successfully.<br>";
        }
    }

    $checkStmt->close();
    $updateStmt->close();
    $insertStmt->close();
}

$conn->close();
?>

<?php include('index.php'); ?>  <!-- Include the header -->

<h2>Enter Item Details</h2>

<div class="form-container">
    <form action="" method="POST">
        <div id="item-form-container">
            <div class="form-row">
                <label for="item_number">Item Number:</label>
                <input type="text" name="item_number[]" required>

                <label for="item_description">Item Description: (if new item)</label>
                <input type="text" name="item_description[]" required>

                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity[]" required>

                <label for="price_per_unit">Price per Unit:</label>
                <input type="number" step="0.01" name="price_per_unit[]" required> <!-- Added price per unit field -->
            </div>
        </div>

        <button type="button" onclick="addNewItemForm()">Add Another Item</button><br><br>
        <input type="submit" value="Submit">
    </form>
</div>

<script>
    function addNewItemForm() {
        const container = document.getElementById("item-form-container");
        const newItemForm = document.createElement("div");
        newItemForm.classList.add("form-row");
        newItemForm.innerHTML = `
            <label for="item_number">Item Number:</label>
            <input type="text" name="item_number[]" required>

            <label for="item_description">Item Description:</label>
            <input type="text" name="item_description[]" required>

            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity[]" required>

            <label for="price_per_unit">Price per Unit:</label>
            <input type="number" step="0.01" name="price_per_unit[]" required> <!-- Added price per unit field -->

            <button type="button" class="remove-btn" onclick="removeItemForm(this)">Remove this Item</button>
        `;
        container.appendChild(newItemForm);
    }

    function removeItemForm(button) {
        button.parentElement.remove();
    }
</script>

</body>
</html>
