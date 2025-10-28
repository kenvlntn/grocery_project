<?php
// index.php — Grocery Project Dashboard
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Grocery Project Dashboard</title>
<style>
* {
  box-sizing: border-box;
  font-family: "Poppins", Arial, sans-serif;
}
body {
  margin: 0;
  background: #f0f2f5;
  color: #333;
}
header {
  background: #2d6a4f;
  color: white;
  text-align: center;
  padding: 20px 0;
  font-size: 28px;
  letter-spacing: 1px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.container {
  display: flex;
  min-height: calc(100vh - 70px);
}
.sidebar {
  width: 250px;
  background: #40916c;
  padding: 20px;
  color: white;
  display: flex;
  flex-direction: column;
}
.sidebar h2 {
  margin-bottom: 20px;
  font-size: 20px;
  text-align: center;
  border-bottom: 2px solid rgba(255,255,255,0.3);
  padding-bottom: 10px;
}
.sidebar a {
  color: white;
  text-decoration: none;
  background: rgba(255,255,255,0.1);
  padding: 12px 15px;
  margin: 8px 0;
  border-radius: 8px;
  transition: 0.3s;
  font-size: 15px;
}
.sidebar a:hover {
  background: #1b4332;
  transform: translateX(5px);
}
.main-content {
  flex: 1;
  padding: 40px;
  background: #fff;
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
  margin: 20px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.main-content h1 {
  color: #1b4332;
  margin-bottom: 20px;
  font-size: 26px;
}
.main-content p {
  font-size: 16px;
  color: #444;
}
.dashboard-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin-top: 40px;
}
.card {
  background: #d8f3dc;
  border-radius: 15px;
  padding: 20px;
  text-align: center;
  transition: 0.3s;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
.card:hover {
  background: #b7e4c7;
  transform: scale(1.05);
}
.card h3 {
  margin-bottom: 10px;
  color: #081c15;
}
.card a {
  display: inline-block;
  margin-top: 10px;
  text-decoration: none;
  color: #1b4332;
  background: #95d5b2;
  padding: 8px 15px;
  border-radius: 8px;
  transition: 0.3s;
}
.card a:hover {
  background: #40916c;
  color: white;
}
footer {
  text-align: center;
  padding: 15px;
  font-size: 14px;
  background: #2d6a4f;
  color: white;
}
</style>
</head>
<body>

<header>🛒 Grocery Project Dashboard</header>

<div class="container">
  <div class="sidebar">
    <h2>Navigation</h2>
    <a href="insert_grocery.php" target="_blank">➕ Insert Grocery Item</a>
    <a href="update_grocery.php" target="_blank">✏️ Update Grocery Item</a>
    <a href="delete_grocery.php" target="_blank">🗑️ Delete Grocery Item</a>
    <a href="create_order.php" target="_blank">🧾 Create New Order</a>
    <a href="orders_list.php" target="_blank">📋 View Orders</a>
    <a href="display_table.php" target="_blank">📦 Display Grocery Table</a>
    <a href="fetch_demo.php" target="_blank">🔍 Fetch() Demo</a>
    <a href="fetch_all.php" target="_blank">📊 FetchAll() Demo</a>
    <a href="complex_report.php" target="_blank">📈 Complex Report</a>
  </div>

  <div class="main-content">
    <h1>Welcome to Your Grocery Management System</h1>
    <p>This dashboard serves as your central hub for managing grocery items, handling orders, and viewing data using PDO in PHP. Use the sidebar to navigate to different functions.</p>

    <div class="dashboard-cards">
      <div class="card">
        <h3>Insert Item</h3>
        <p>Add new grocery items to your inventory.</p>
        <a href="insert_grocery.php" target="_blank">Go</a>
      </div>
      <div class="card">
        <h3>Update Item</h3>
        <p>Modify prices, units, or stock of existing items.</p>
        <a href="update_grocery.php" target="_blank">Go</a>
      </div>
      <div class="card">
        <h3>Delete Item</h3>
        <p>Remove grocery items from your database.</p>
        <a href="delete_grocery.php" target="_blank">Go</a>
      </div>
      <div class="card">
        <h3>Create Order</h3>
        <p>Record a customer order and update inventory.</p>
        <a href="create_order.php" target="_blank">Go</a>
      </div>
      <div class="card">
        <h3>View Orders</h3>
        <p>See all orders and details placed so far.</p>
        <a href="orders_list.php" target="_blank">Go</a>
      </div>
      <div class="card">
        <h3>Data Display</h3>
        <p>View your grocery items in an HTML table.</p>
        <a href="display_table.php" target="_blank">Go</a>
      </div>
      <div class="card">
        <h3>Fetch Demos</h3>
        <p>See examples of PDO fetch() and fetchAll() methods.</p>
        <a href="fetch_demo.php" target="_blank">Go</a>
      </div>
      <div class="card">
        <h3>Complex Report</h3>
        <p>Generate summarized analytics and reports.</p>
        <a href="complex_report.php" target="_blank">Go</a>
      </div>
    </div>
  </div>
</div>

<footer>
  &copy; <?= date("Y") ?> Grocery Project | Developed with ❤️ using PHP PDO
</footer>

</body>
</html>
