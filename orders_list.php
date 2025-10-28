<?php
// orders_list.php
require 'dbconfig.php';

$sql = "SELECT o.order_id, o.order_date, o.customer_name, o.total_amount, 
               e.first_name AS emp_first, e.last_name AS emp_last, 
               p.promo_name
        FROM orders o
        LEFT JOIN employees e ON o.employee_id = e.employee_id
        LEFT JOIN promotions p ON o.promo_id = p.promo_id
        ORDER BY o.order_date DESC
        LIMIT 100";
$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll();

// we will fetch order_details for each order (efficient approach: get all details then group)
$orderIds = array_column($orders, 'order_id');
$details = [];
if (!empty($orderIds)) {
    $in  = str_repeat('?,', count($orderIds) - 1) . '?';
    $detailSql = "SELECT od.order_id, od.quantity, od.price, gi.item_name 
                  FROM order_details od
                  JOIN grocery_items gi ON od.item_id = gi.item_id
                  WHERE od.order_id IN ($in)";
    $detailStmt = $pdo->prepare($detailSql);
    $detailStmt->execute($orderIds);
    $rows = $detailStmt->fetchAll();
    foreach ($rows as $r) {
        $details[$r['order_id']][] = $r;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders List</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background:#f8f9fb; }
        .order { background: #fff; border-radius:8px; margin-bottom:16px; padding:16px; box-shadow:0 1px 4px rgba(0,0,0,.08);}
        table { width:100%; border-collapse:collapse; }
        th, td { padding:8px; border-bottom:1px solid #eee; text-align:left; }
        .small { font-size:0.9em; color:#666; }
    </style>
</head>
<body>
<h2>Recent Orders</h2>
<?php foreach ($orders as $o): ?>
<div class="order">
    <div><strong>Order #<?= htmlspecialchars($o['order_id']) ?></strong> — <?= htmlspecialchars($o['customer_name']) ?> <span class="small">(<?= $o['order_date'] ?>)</span></div>
    <div class="small">Processed by <?= htmlspecialchars($o['emp_first'] . ' ' . $o['emp_last']) ?><?= $o['promo_name'] ? " — Promo: " . htmlspecialchars($o['promo_name']) : "" ?></div>
    <table>
        <thead>
            <tr><th>Item</th><th>Qty</th><th>Unit Price</th><th>Line Total</th></tr>
        </thead>
        <tbody>
            <?php if (!empty($details[$o['order_id']])): ?>
                <?php foreach ($details[$o['order_id']] as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['item_name']) ?></td>
                    <td><?= (int)$d['quantity'] ?></td>
                    <td><?= number_format($d['price'],2) ?></td>
                    <td><?= number_format($d['price'] * $d['quantity'],2) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No details found for this order.</td></tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3" style="text-align:right"><strong>Total:</strong></td><td><strong><?= number_format($o['total_amount'],2) ?></strong></td></tr>
        </tfoot>
    </table>
</div>
<?php endforeach; ?>
</body>
</html>
