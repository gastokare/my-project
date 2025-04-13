<?php
session_start();
include 'db.php'; // Database connection file

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        $user_id = $_POST['user_id'];
        $conn->query("DELETE FROM users WHERE user_id = $user_id");
        header("Location: admin_register.php");
    } elseif (isset($_POST['update'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $conn->query("UPDATE users SET username='$username', role='$role' WHERE user_id=$user_id");
        header("Location: admin_register.php");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Admin Dashboard</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <form method="post">
                    <td><?php echo $row['user_id']; ?></td>
                    <td><input type="text" name="username" value="<?php echo $row['username']; ?>" required></td>
                    <td>
                        <select name="role">
                            <option value="receptionist" <?php if ($row['role'] == 'receptionist') echo 'selected'; ?>>Receptionist</option>
                            <option value="bursar" <?php if ($row['role'] == 'bursar') echo 'selected'; ?>>Bursar</option>
                            <option value="doctor" <?php if ($row['role'] == 'doctor') echo 'selected'; ?>>Doctor</option>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                        <button type="submit" name="update">Update</button>
                        <button type="submit" name="delete" onclick="return confirm('Are you sure?')">Delete</button>
                    </td>
                </form>
            </tr>
        <?php } ?>
    </table>
    <a href="logout.php">Logout</a>
</body>
</html>
