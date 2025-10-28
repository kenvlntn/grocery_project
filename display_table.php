<?php
require 'dbconfig.php';

try {
    $sql = "
        SELECT gi.item_id, gi.item_name, c.category_name, s.supplier_name, gi.price, gi.unit, i.quantity
        FROM grocery_items gi
        LEFT JOIN categories c ON gi.category_id = c.category_id
        LEFT JOIN suppliers s ON gi.supplier_id = s.supplier_id
        LEFT JOIN inventory i ON gi.item_id = i.item_id
        ORDER BY gi.item_name ASC
    ";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("PDO Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grocery Items Table</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
<h2>🛒 Grocery Items with Categories, Suppliers & Inventory</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Item Name</th>
        <th>Category</th>
        <th>Supplier</th>
        <th>Price</th>
        <th>Unit</th>
        <th>Quantity</th>
    </tr>

    <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['item_id']); ?></td>
            <td><?= htmlspecialchars($row['item_name']); ?></td>
            <td><?= htmlspecialchars($row['category_name']); ?></td>
            <td><?= htmlspecialchars($row['supplier_name']); ?></td>
            <td>₱<?= htmlspecialchars(number_format($row['price'], 2)); ?></td>
            <td><?= htmlspecialchars($row['unit']); ?></td>
            <td><?= htmlspecialchars($row['quantity']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>