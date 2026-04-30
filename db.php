<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'kwu16_smoothin';

// Create connection
$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($dbname);
} else {
    die("Error creating database: " . $conn->error);
}

// Create users table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'customer',
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($table_sql)) {
    die("Error creating table: " . $conn->error);
}

// Check if admin exists, if not create default
$check_admin = "SELECT * FROM users WHERE username = 'admin'";
$res = $conn->query($check_admin);
if ($res->num_rows == 0) {
    $conn->query("INSERT INTO users (username, password, role) VALUES ('admin', 'admin', 'admin')");
}

return $conn;
