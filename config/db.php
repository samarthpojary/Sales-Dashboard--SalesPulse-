<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'Samarth@9019';
$DB_NAME = 'sales_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_errno) {
    die('DB connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>