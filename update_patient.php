<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to MySQL database (Update credentials if necessary)
$host = "localhost";
$username = "root";  // Default XAMPP username
$password = "";      // Default XAMPP password (empty)
$database = "hospital_db"; // Change to your actual database name

$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize patient data
$patientData = [];

// Handle search request (When the user searches for a patient)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $search_id = $_POST["patient_id"];
    $search_name = $_POST["patient_name"];

    $sql = "SELECT * FROM patients WHERE patient_id = ? OR patient_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $search_id, $search_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch data if found
    if ($result->num_rows > 0) {
        $patientData = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "<p class='error'>Patient not found.</p>";
    }

    $stmt->close();
}

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $patient_id = $_POST["patient_id"];
    $patient_name = $_POST["patient_name"];
    $age = $_POST["age"];
    $contact = $_POST["contact"];
    $hospital = $_POST["hospital"];
    $status = $_POST["status"];
    $address = $_POST["address"];
    $gender = $_POST["gender"];
    $marital_status = $_POST["marital_status"];

    // Update query
    $sql = "UPDATE patients SET 
                patient_name = ?, 
                age = ?, 
                contact = ?, 
                hospital = ?, 
                status = ?, 
                address = ?, 
                gender = ?, 
                marital_status = ? 
            WHERE patient_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssssi", 
        $patient_name, $age, $contact, $hospital, $status, $address, $gender, $marital_status, $patient_id);
    
    if ($stmt->execute()) {
        echo "<p class='success'>Patient details updated successfully.</p>";
    } else {
        echo "<p class='error'>Error updating record: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Patient</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        input[type="text"], input[type="number"] { width: 100%; padding: 5px; }
        button { padding: 8px 15px; margin-top: 10px; cursor: pointer; }
    </style>
</head>
<body>

    <h2>Search for Patient</h2>
    <form method="POST">
    <label>Enter Patient ID or Name:</label>
    <input type="number" name="patient_id" placeholder="Enter Patient ID">
    <input type="text" name="patient_name" placeholder="Enter Patient Name">
    <button type="submit" name="search">Search</button>
    <a href="patient_register.php" class="btn">Back to Registration</a>
    </form>

    <?php if (!empty($patientData)): ?>
        <h3>Patient Details</h3>
        <form method="POST">
            <table>
                <tr>
                    <th>Patient ID</th>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>Contact</th>
                    <th>Hospital</th>
                    <th>Status</th>
                    <th>Address</th>
                    <th>Gender</th>
                    <th>Marital Status</th>
                    <th>Registration Date</th>
                </tr>
                <?php foreach ($patientData as $patient): ?>
                    <tr>
                        <td><input type="text" name="patient_id" value="<?php echo htmlspecialchars($patient['patient_id']); ?>" readonly></td>
                        <td><input type="text" name="patient_name" value="<?php echo htmlspecialchars($patient['patient_name']); ?>"></td>
                        <td><input type="number" name="age" value="<?php echo htmlspecialchars($patient['age']); ?>"></td>
                        <td><input type="text" name="contact" value="<?php echo htmlspecialchars($patient['contact']); ?>"></td>
                        <td><input type="text" name="hospital" value="<?php echo htmlspecialchars($patient['hospital']); ?>"></td>
                        <td><input type="text" name="status" value="<?php echo htmlspecialchars($patient['status']); ?>"></td>
                        <td><input type="text" name="address" value="<?php echo htmlspecialchars($patient['address']); ?>"></td>
                        <td><input type="text" name="gender" value="<?php echo htmlspecialchars($patient['gender']); ?>"></td>
                        <td><input type="text" name="marital_status" value="<?php echo htmlspecialchars($patient['marital_status']); ?>"></td>
                        <td><?php echo htmlspecialchars($patient['registration_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <button type="submit" name="update">Update</button>
        </form>
    <?php endif; ?>

</body>
</html>

