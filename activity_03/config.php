<?php
// Database configuration
define('hostname', 'localhost');
define('username', 'root');      // Change to your MySQL username
define('password', '');      // Change to your MySQL password
define('database', 'dash_db');

// Create database connection
function getDBConnection() {
    $conn = new mysqli(hostname,username,password,database);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }
    
    // Set character set to utf8
    $conn->set_charset("utf8");
    
    return $conn;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
