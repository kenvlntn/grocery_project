<?php
require 'dbconfig.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'] ?? '';

    if (empty($item_id)) {
        $message = "⚠️ Please select an item to delete.";
    } else {
        try {
            $pdo->beginTransaction();

            $pdo->prepare("DELETE FROM inventory WHERE item_id = :iid")->execute([':iid' => $item_id]);
            $pdo->prepare("DELETE FROM grocery_items WHERE item_id = :iid")->execute([':iid' => $item_id]);

            $pdo->commit();
            $message = "✅ Grocery item deleted successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "❌ Error: " . $e->getMessage();
        }
    }
}

$items = $pdo->query("SELECT item_id, item_name FROM grocery_items ORDER BY item_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Delete Grocery Item</title>
<style>
body { font-family: Arial; background: #f9f9f9; padding: 20px; }
form { background: white; padding: 20px; border-radius: 10px; max-width: 400px; margin: auto; }
select { width: 100%; padding: 8px; margin: 8px 0; }
button { padding: 10px 15px; background: #dc3545; color: white; border: none; border-radius: 5px; }
.message { text-align: center; margin-bottom: 15px; }
</style>
</head>
<body>

<h2 style="text-align:center;">🟥 Delete Grocery Item</h2>
<div class="message"><?= $message ?></div>

<form method="POST">
    <label>Select Item to Delete:</label>
    <select name="item_id" required>
        <option value="">-- Choose an Item --</option>
        <?php foreach ($items as $it): ?>
            <option value="<?= $it['item_id'] ?>"><?= htmlspecialchars($it['item_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Delete Item</button>
</form>

</body>
</html>
