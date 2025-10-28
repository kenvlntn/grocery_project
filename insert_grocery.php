<?php
require 'dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $unit = $_POST['unit'] ?? '';
    $quantity = $_POST['quantity'] ?? '';

    if (empty($item_name) || empty($price) || empty($category_id) || empty($unit) || empty($quantity)) {
        $message = "⚠️ All fields are required.";
    } else {
        try {
            $pdo->beginTransaction();

            // Insert into grocery_items
            $stmt = $pdo->prepare("INSERT INTO grocery_items (item_name, price, category_id, unit, date_added) 
                                   VALUES (:iname, :price, :cat, :unit, NOW())");
            $stmt->execute([
                ':iname' => $item_name,
                ':price' => $price,
                ':cat' => $category_id,
                ':unit' => $unit
            ]);

            // Get the newly inserted item ID
            $item_id = $pdo->lastInsertId();

            // Insert initial quantity into inventory
            $inv = $pdo->prepare("INSERT INTO inventory (item_id, quantity) VALUES (:iid, :qty)");
            $inv->execute([':iid' => $item_id, ':qty' => $quantity]);

            $pdo->commit();
            $message = "✅ Grocery item inserted successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "❌ Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Insert Grocery Item</title>
<style>
body { font-family: Arial; background: #f9f9f9; padding: 20px; }
form { background: white; padding: 20px; border-radius: 10px; max-width: 400px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
input, select { width: 100%; padding: 8px; margin: 8px 0; }
button { padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background: #218838; }
.message { text-align: center; margin-bottom: 15px; font-weight: bold; }
</style>
</head>
<body>

<h2 style="text-align:center;">🟩 Insert Grocery Item</h2>
<div class="message"><?= $message ?? '' ?></div>

<form method="POST">
    <label>Item Name:</label>
    <input type="text" name="item_name" required>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" required>

    <label>Category ID:</label>
    <input type="number" name="category_id" required>

    <label>Unit:</label>
    <input type="text" name="unit" placeholder="e.g. pcs, kg, pack" required>

    <label>Initial Quantity:</label>
    <input type="number" name="quantity" required>

    <button type="submit">Insert Item</button>
</form>

</body>
</html>
