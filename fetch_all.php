<?php
// fetch_all_demo.php
require 'dbconfig.php';

try {
    // Example 1: fetchAll() on grocery_items (joined with categories & suppliers)
    $sql = "
        SELECT gi.item_id, gi.item_name, c.category_name, s.supplier_name, gi.price, gi.unit
        FROM grocery_items gi
        LEFT JOIN categories c ON gi.category_id = c.category_id
        LEFT JOIN suppliers s ON gi.supplier_id = s.supplier_id
        ORDER BY gi.item_name
    ";
    $stmt = $pdo->query($sql);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>";
    print_r($items);
    echo "</pre>";

    // Example 2: fetchAll() for recent orders with order_details
    $sql2 = "
        SELECT o.order_id, o.order_date, o.customer_name, od.item_id, gi.item_name, od.quantity, od.price
        FROM orders o
        JOIN order_details od ON o.order_id = od.order_id
        JOIN grocery_items gi ON od.item_id = gi.item_id
        ORDER BY o.order_date DESC
        LIMIT 50
    ";
    $stmt2 = $pdo->query($sql2);
    $orders = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>";
    print_r($orders);
    echo "</pre>";

} catch (PDOException $e) {
    echo "<pre>PDO Error: " . $e->getMessage() . "</pre>";
}