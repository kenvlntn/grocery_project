<?php
// complex_report.php
require 'dbconfig.php';

$sql = "SELECT c.category_name,
               s.supplier_name,
               SUM(od.quantity) AS total_sold,
               SUM(od.quantity * od.price) AS total_revenue,
               p.promo_name
        FROM order_details od
        JOIN grocery_items gi ON od.item_id = gi.item_id
        JOIN categories c ON gi.category_id = c.category_id
        JOIN suppliers s ON gi.supplier_id = s.supplier_id
        LEFT JOIN orders o ON od.order_id = o.order_id
        LEFT JOIN promotions p ON o.promo_id = p.promo_id
        GROUP BY c.category_name, s.supplier_name, p.promo_name
        ORDER BY total_revenue DESC
        LIMIT 200";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales by Category & Supplier</title>
    <style>
        table { width:90%; border-collapse:collapse; margin:20px auto; font-family: Arial, sans-serif; }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; }
        th { background:#f0f2f5; }
    </style>
</head>
<body>
<h2 style="text-align:center">Sales Report: Category × Supplier × Promotion</h2>
<table>
    <tr>
        <th>Category</th>
        <th>Supplier</th>
        <th>Promo</th>
        <th>Total Units Sold</th>
        <th>Total Revenue (₱)</th>
    </tr>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['category_name']) ?></td>
        <td><?= htmlspecialchars($r['supplier_name']) ?></td>
        <td><?= $r['promo_name'] ? htmlspecialchars($r['promo_name']) : '-' ?></td>
        <td><?= (int)$r['total_sold'] ?></td>
        <td><?= number_format($r['total_revenue'], 2) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
