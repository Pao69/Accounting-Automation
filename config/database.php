<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'accounting_system';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . $database;
if(mysqli_query($conn, $sql)){
    mysqli_select_db($conn, $database);
} else {
    die("ERROR: Could not create database. " . mysqli_error($conn));
}
?> 