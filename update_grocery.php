<?php
require 'dbconfig.php';
$message = '';
// Handle form submission for updating a grocery item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'] ?? '';
    $item_name = $_POST['item_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';
    $unit = $_POST['unit'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    // Basic validation
    if (empty($item_id)) {
        $message = "⚠️ Item ID is required.";
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE grocery_items SET item_name = :iname, price = :price, category = :cat, unit = :unit WHERE item_id = :id");
            $stmt->execute([
                ':iname' => $item_name,
                ':price' => $price,
                ':cat' => $category,
                ':unit' => $unit,
                ':id' => $item_id
            ]);

            $inv = $pdo->prepare("UPDATE inventory SET quantity = :qty WHERE item_id = :iid");
            $inv->execute([':qty' => $quantity, ':iid' => $item_id]);

            $pdo->commit();
            $message = "✅ Grocery item updated successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "❌ Error: " . $e->getMessage();
        }
    }
}
// Fetch all grocery items for the dropdown
$items = $pdo->query("SELECT gi.item_id, gi.item_name FROM grocery_items gi ORDER BY gi.item_name")->fetchAll(PDO::FETCH_ASSOC);
?>
// --------------------------------------------------
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Grocery Item</title>
<style>
body { font-family: Arial; background: #f9f9f9; padding: 20px; }
form { background: white; padding: 20px; border-radius: 10px; max-width: 400px; margin: auto; }
input, select { width: 100%; padding: 8px; margin: 8px 0; }
button { padding: 10px 15px; background: #ffc107; color: white; border: none; border-radius: 5px; }
.message { text-align: center; margin-bottom: 15px; }
</style>
</head>
<body>

<h2 style="text-align:center;">🟨 Update Grocery Item</h2>
<div class="message"><?= $message ?></div>

<form method="POST">
    <label>Select Item to Update:</label>
    <select name="item_id" required>
        <option value="">-- Choose an Item --</option>
        <?php foreach ($items as $it): ?>
            <option value="<?= $it['item_id'] ?>"><?= htmlspecialchars($it['item_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>New Item Name:</label>
    <input type="text" name="item_name">

    <label>New Price:</label>
    <input type="number" step="0.01" name="price">

    <label>New Category:</label>
    <input type="text" name="category">

    <label>New Unit:</label>
    <input type="text" name="unit">

    <label>New Quantity:</label>
    <input type="number" name="quantity">

    <button type="submit">Update Item</button>
</form>

</body>
</html>
