<?php
// Start session
session_start();

// Database connection
function getDBConnection() {
    $conn = new mysqli("localhost", "root", "", "hospital_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Check if the user is logged in
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

// Check if the logged-in user has the specified role
function checkRole($role) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === $role) {
        return true;
    }
    return false;
}
?>
