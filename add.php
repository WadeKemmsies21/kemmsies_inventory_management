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

    $checkStmt = $conn->prepare("SELECT item_description, quantity FROM items WHERE item_number = ?");
    $checkStmt->bind_param("s", $item_number);  

    $updateStmt = $conn->prepare("UPDATE items SET quantity = quantity + ? WHERE item_number = ?");
    $updateStmt->bind_param("is", $quantity, $item_number); 

    $insertStmt = $conn->prepare("INSERT INTO items (item_number, item_description, quantity) VALUES (?, ?, ?)");
    $insertStmt->bind_param("ssi", $item_number, $item_description, $quantity);  

    for ($i = 0; $i < count($item_numbers); $i++) {
        $item_number = $item_numbers[$i];
        $item_description = $item_descriptions[$i];
        $quantity = $quantities[$i];

        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            $checkStmt->bind_result($existing_description, $existing_quantity);
            $checkStmt->fetch();

            if ($existing_description !== $item_description) {
                echo "Item $item_number already exists. The description will not be updated.<br>";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Form</title>

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

                <button type="button" class="remove-btn" onclick="removeItemForm(this)">Remove this Item</button>
            `;
            container.appendChild(newItemForm);
        }

        function removeItemForm(button) {
            button.parentElement.remove();
        }
    </script>
</head>
<body>
    <header>
        <h2>Kemmsies Inventory Management System</h2>
    </header>

    <a href="inventory.php"><button>Total Inventory</button></a>
    <a href="add.php"><button>Add Inventory</button></a>
    <a href="scan.php"><button>Scan/Remove</button></a>
    <a href="print.php"><button>Print Inventory</button></a>

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
                </div>
            </div>

            <button type="button" onclick="addNewItemForm()">Add Another Item</button><br><br>
            <input type="submit" value="Submit">
        </form>
    </div>

</body>
</html>