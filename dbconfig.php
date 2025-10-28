<?php
// Database configuration settings
$host = '127.0.0.1'; 
$db   = 'grocery_db';  // The database name you used
$user = 'root';         // Your MySQL username
$pass = '';         // Your MySQL password
$charset = 'utf8mb4';   // Use utf8mb4 for full character support

// Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO Connection Options
$options = [
    // Throw an exception when a database error occurs
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Set the default fetch mode to associative array
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // Disable emulated prepared statements for security
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// This is the global PDO connection object.
$pdo = null;

try {
    // Create the PDO database connection
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Handle connection error
    // Use htmlspecialchars to prevent XSS if you echo the error
    $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    throw new \PDOException($errorMessage, (int)$e->getCode());
}

?>
