<?php
include('functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Secure the password
    $role = $_POST["role"];

    $conn = getDBConnection();

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error registering user.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
</head>
<body>

    <h2>User Registration (Admin)</h2>

    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <label for="role">Role:</label>
        <select name="role" required>
            <option value="admin">Admin</option>
            <option value="receptionist">Receptionist</option>
            <option value="bursar">Bursar</option>
            <option value="doctor">Doctor</option>
        </select><br><br>

        <button type="submit">Register User</button>
    </form>

</body>
</html>
