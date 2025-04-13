<?php
// Connect to MySQL database
$conn = new mysqli("localhost", "root", "", "hospital_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the last allocated patient_id
$sql = "SELECT patient_id, password FROM patients ORDER BY patient_id DESC LIMIT 1";
$result = $conn->query($sql);

$lastPatientID = 0; // Default if no records exist
$lastPassword = 99; // Default password before first user

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastPatientID = (int) $row["patient_id"];
    $lastPassword = (int) $row["password"];
}

// Generate the next available patient ID and password
$nextPatientID = $lastPatientID + 1;
$nextPassword = $lastPassword + 1;

// Ensure patient ID stays within range (1 to 999999999)
if ($nextPatientID > 999999999) {
    die("Error: Patient ID limit reached.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") 
if (empty($_POST["full_name"]) || empty($_POST["age"]) || empty($_POST["contact"]) || empty($_POST["hospital"]) || empty($_POST["status"]) || empty($_POST["address"]) || empty($_POST["gender"]) || empty($_POST["marital_status"])) {
    $error = "Error: All fields must be filled out.";
} else {
    $hospital = strtoupper(trim($_POST["hospital"]));
    $patientName = strtoupper(trim($_POST["full_name"]));
    $age = (float) $_POST["age"];
    $contact = trim($_POST["contact"]);
    $maritalStatus = $_POST["marital_status"];
    $gender = $_POST["gender"];
    $date = $_POST["date"];
    $status = $_POST["status"];
    $address = $_POST["address"];

    // Hospital name validation (must be MTERI)
    if ($hospital !== "MTERI") {
        $error = "Error: Hospital name must be 'MTERI'.";
    }
    
    // Validate full name format (three words, <15 chars each)
    $nameParts = explode(" ", $patientName);
    if (count($nameParts) !== 3 || strlen($nameParts[0]) >= 15 || strlen($nameParts[1]) >= 15 || strlen($nameParts[2]) >= 15) {
        $error = "Error: Full name must be three words, each less than 15 characters.";
    }

    // Validate age range (0.1 - 150)
    if ($age <= 0 || $age > 150) {
        $error = "Error: Age must be between 0.1 and 150.";
    }

    // Validate contact number format (+255XXXXXXXXX)
    if (!preg_match("/^\+255\d{9}$/", $contact)) {
        $error = "Error: Contact must start with +255 followed by 9 digits.";
    }

    if (!isset($error)) {
        // Insert new patient record
        $stmt = $conn->prepare("INSERT INTO patients (patient_id, hospital, patient_name, age, contact, marital_status, gender, registration_date, status, address, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdssssssi", $nextPatientID, $hospital, $patientName, $age, $contact, $maritalStatus, $gender, $date, $status, $address, $nextPassword);

        if ($stmt->execute()) {
            $success = "Patient registered successfully!";
        } else {
            $error = "Error registering patient.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 10px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

    <h2>Patient Registration Form</h2>
    <h3>Add Patient</h3>

    <!-- Display last allocated patient ID and password -->
    <p><strong>Last Allocated ID:</strong> <?php echo $lastPatientID; ?></p>
    <p><strong>Next patient_id:</strong> <?php echo $nextPatientID; ?></p>
    <p><strong>Next Password:</strong> <?php echo $nextPassword; ?></p>

    <!-- Registration Form -->
    <form id="myForm" method="POST">
        <div class="form-group">
            <label for="hospital">Hospital: </label>
            <input type="text" id="hospital" name="hospital" required>
            <span class="error" id="hospitalError"></span>
        </div>

        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" required>
            <span class="error" id="nameError"></span>
        </div>
        <div class="form-group">
        <label for="date">registration date:</label>
        <input type="date" id="date" name="date" required><br><br>
        </div>

        <div class="form-group">
            <label for="patient_id">Patient ID:</label>
            <input type="text" id="patient_id" name="patient_id" value="<?php echo $nextPatientID; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" step="any" required>
            <span class="error" id="ageError"></span>
        </div>

        <div class="form-group">
            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" required>
            <span class="error" id="contactError"></span>
        </div>
        <div class="form-group">
        <label for="address">Patient Address: </label>
        <input type="text" id="address" name="address"><br><br>
        </div>
        <label for="marital_status">Marital status: </label>
            <select id="marital_status" name="marital_status" required>
                <option value="married">Married</option>
                <option value="single">Single/Not married</option>
            </select><br><br>
            <label for="gender">Gender: </label>
            <select id="gender" id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select><br><br>
            <label for="status">Patient Status: </label>
            <select id="status" id="status" name="status" required>
                <option value="normal">Normal</option>
                <option value="critical">Critical</option>
                <option value="other">Needs quick appointment</option>
            </select><br><br>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="text" id="password" name="password" value="<?php echo $nextPassword; ?>" readonly>
        </div>

        <button type="submit">Register</button>
        <button type="reset"> Reset </button>
    </form>

    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <script>
        document.getElementById("hospital").addEventListener("input", function () {
            if (this.value.toUpperCase() !== "MTERI") {
                document.getElementById("hospitalError").textContent = "Hospital name must be 'MTERI'.";
            } else {
                document.getElementById("hospitalError").textContent = "";
            }
        });

        document.getElementById("full_name").addEventListener("input", function () {
            let nameInput = this.value.trim().toUpperCase();
            let words = nameInput.split(" ");
            if (words.length !== 3 || words.some(word => word.length >= 15)) {
                document.getElementById("nameError").textContent = "Full name must have exactly three words, each <15 chars.";
            } else {
                document.getElementById("nameError").textContent = "";
            }
        });

        document.getElementById("contact").addEventListener("input", function () {
            if (!/^\+255\d{9}$/.test(this.value)) {
                document.getElementById("contactError").textContent = "Contact must start with +255 followed by 9 digits.";
            } else {
                document.getElementById("contactError").textContent = "";
            }
        });
    </script>

</body>
<p>Already registered? <a href="update_patient.php">View Patient Details</a></p>
</html>
