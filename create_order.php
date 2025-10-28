<?php
require 'dbconfig.php';

/**
 * Create a new order with order details and update inventory.
 */
function createOrder(PDO $pdo, $employee_id, $customer_name, $promo_id, array $items) {
    if (empty($items)) {
        throw new InvalidArgumentException('Order must contain at least one item.');
    }

    $pdo->beginTransaction();
    try {
        // 1) Calculate total_amount from current item price
        $total = 0.0;
        $prices = [];
        $getPriceStmt = $pdo->prepare("SELECT price FROM grocery_items WHERE item_id = :id FOR UPDATE");
        foreach ($items as $it) {
            $getPriceStmt->execute([':id' => $it['item_id']]);
            $row = $getPriceStmt->fetch();
            if (!$row) {
                throw new Exception("Item ID {$it['item_id']} not found.");
            }
            $price = (float)$row['price'];
            $prices[$it['item_id']] = $price;
            $total += $price * (int)$it['quantity'];
        }

        // 2) Apply promo discount if available
        if ($promo_id) {
            $promoStmt = $pdo->prepare("SELECT discount_percentage FROM promotions WHERE promo_id = :pid");
            $promoStmt->execute([':pid' => $promo_id]);
            $promo = $promoStmt->fetch();
            if ($promo) {
                $discount = (float)$promo['discount_percentage'];
                $total = $total * (1 - $discount / 100.0);
            } else {
                $promo_id = null;
            }
        }

        // 3) Insert order
        $orderInsert = $pdo->prepare("
            INSERT INTO orders (employee_id, customer_name, total_amount, promo_id)
            VALUES (:eid, :cname, :total, :pid)
        ");
        $orderInsert->execute([
            ':eid' => $employee_id,
            ':cname' => $customer_name,
            ':total' => $total,
            ':pid' => $promo_id
        ]);
        $orderId = $pdo->lastInsertId();

        // 4) Insert order details + update inventory
        $detailInsert = $pdo->prepare("
            INSERT INTO order_details (order_id, item_id, quantity, price)
            VALUES (:oid, :iid, :qty, :price)
        ");
        $invSelect = $pdo->prepare("SELECT quantity FROM inventory WHERE item_id = :id FOR UPDATE");
        $invUpdate = $pdo->prepare("UPDATE inventory SET quantity = quantity - :q WHERE item_id = :id");

        foreach ($items as $it) {
            $iid = (int)$it['item_id'];
            $qty = (int)$it['quantity'];
            $price = $prices[$iid];

            $invSelect->execute([':id' => $iid]);
            $invRow = $invSelect->fetch();
            if (!$invRow) {
                throw new Exception("Inventory record not found for item_id {$iid}");
            }
            if ($invRow['quantity'] < $qty) {
                throw new Exception("Not enough stock for item_id {$iid}. Requested {$qty}, available {$invRow['quantity']}");
            }

            $detailInsert->execute([
                ':oid' => $orderId,
                ':iid' => $iid,
                ':qty' => $qty,
                ':price' => $price
            ]);

            $invUpdate->execute([':q' => $qty, ':id' => $iid]);
        }

        $pdo->commit();
        return $orderId;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// --------------------------------------------------
// FORM SUBMISSION HANDLING
// --------------------------------------------------
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $employee_id = (int)$_POST['employee_id'];
        $customer_name = trim($_POST['customer_name']);
        $promo_id = !empty($_POST['promo_id']) ? (int)$_POST['promo_id'] : null;

        // Build items array from the form
        $items = [];
        foreach ($_POST['item_id'] as $index => $id) {
            $quantity = (int)$_POST['quantity'][$index];
            if ($id && $quantity > 0) {
                $items[] = ['item_id' => (int)$id, 'quantity' => $quantity];
            }
        }

        $newOrderId = createOrder($pdo, $employee_id, $customer_name, $promo_id, $items);
        $message = "<div style='color:green;'><strong>Order Created Successfully! Order ID: {$newOrderId}</strong></div>";

    } catch (Exception $e) {
        $message = "<div style='color:red;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Fetch items, employees, and promos for dropdowns
$items = $pdo->query("SELECT item_id, item_name FROM grocery_items")->fetchAll();
$employees = $pdo->query("SELECT employee_id, last_name FROM employees")->fetchAll();
$promos = $pdo->query("SELECT promo_id, promo_name FROM promotions")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Order</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        form { background: #f8f8f8; padding: 20px; border-radius: 10px; width: 600px; }
        input, select { margin: 5px 0; padding: 5px; width: 100%; }
        button { padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; margin-top: 10px; }
        button:hover { background: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<h2>Create New Order</h2>
<?= $message ?>

<form method="POST">
    <label for="employee_id">Employee:</label>
    <select name="employee_id" required>
        <option value="">-- Select Employee --</option>
        <?php foreach ($employees as $emp): ?>
            <option value="<?= $emp['employee_id'] ?>"><?= htmlspecialchars($emp['last_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="customer_name">Customer Name:</label>
    <input type="text" name="customer_name" required>

    <label for="promo_id">Promotion (Optional):</label>
    <select name="promo_id">
        <option value="">-- None --</option>
        <?php foreach ($promos as $promo): ?>
            <option value="<?= $promo['promo_id'] ?>"><?= htmlspecialchars($promo['promo_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <h3>Order Items</h3>
    <table>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
        </tr>
        <?php for ($i = 0; $i < 3; $i++): ?>
        <tr>
            <td>
                <select name="item_id[]">
                    <option value="">-- Select Item --</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= $item['item_id'] ?>"><?= htmlspecialchars($item['item_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" name="quantity[]" min="1" placeholder="Enter quantity"></td>
        </tr>
        <?php endfor; ?>
    </table>

    <button type="submit">Create Order</button>
</form>

</body>
</html>
