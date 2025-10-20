<?php
define('hostname', 'localhost');
define('username', 'root'); 
define('password', '');
define('database', 'team_project');

function getDBConnection() {
    $conn = new mysqli(hostname,username,password,database);
    
    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    
    return $conn;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
