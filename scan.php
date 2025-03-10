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
    $quantities = $_POST['quantity'];

    $checkStmt = $conn->prepare("SELECT quantity FROM items WHERE item_number = ?");
    $checkStmt->bind_param("s", $item_number);  

    $updateStmt = $conn->prepare("UPDATE items SET quantity = quantity - ? WHERE item_number = ?");
    $updateStmt->bind_param("is", $quantity, $item_number); 

    for ($i = 0; $i < count($item_numbers); $i++) {
        $item_number = $item_numbers[$i];
        $quantity = $quantities[$i];

        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $checkStmt->bind_result($existing_quantity);
            $checkStmt->fetch();

            if ($existing_quantity < $quantity) {
                echo "Insufficient quantity for item $item_number. Only $existing_quantity available.<br>";
            } else {
                $updateStmt->execute();
                echo "Quantity for item $item_number reduced by $quantity.<br>";
            }
        } else {
            echo "Item $item_number does not exist in the inventory.<br>";
        }
    }

    $checkStmt->close();
    $updateStmt->close();
}

$conn->close();
?>

<?php include('index.php'); ?> <!-- Include the header -->

<h2>Remove Item Details</h2>

<div class="form-container">
    <form action="" method="POST">
        <div id="remove-item-form-container">
            <div class="form-row">
                <label for="item_number">Item Number:</label>
                <input type="text" name="item_number[]" required autofocus>

                <label for="quantity">Quantity to Remove:</label>
                <input type="number" name="quantity[]" required>
            </div>
        </div>

        <button type="button" onclick="addRemoveItemForm()">Add Another Item</button><br><br>
        <input type="submit" value="Remove Items">
    </form>
</div>

<script>
    function addRemoveItemForm() {
        const container = document.getElementById("remove-item-form-container");
        const newItemForm = document.createElement("div");
        newItemForm.classList.add("form-row");
        newItemForm.innerHTML = `
            <label for="item_number">Item Number:</label>
            <input type="text" name="item_number[]" required>

            <label for="quantity">Quantity to Remove:</label>
            <input type="number" name="quantity[]" required>

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
