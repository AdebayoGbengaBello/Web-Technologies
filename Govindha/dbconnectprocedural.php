<?php
// Database connection details
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create a connection
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Write an SQL query
$sql = "SELECT id, name FROM users";

// Execute the query
$result = mysqli_query($connection, $sql);

// Check if any rows were returned
if (mysqli_num_rows($result) > 0) {
    // Fetch and display each row as an associative array
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row["id"] . " - Name: " . $row["name"] . "<br>";
    }
} else {
    echo "No results found.";
}

// Close the connection
mysqli_close($connection);
?>
