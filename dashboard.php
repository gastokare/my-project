<?php
include('functions.php');
checkLogin();  // Ensure user is logged in

$role = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>

    <h1>Yes !!!!!!!, Welcome to your page</h1>

    <?php if ($role == 'receptionist'): ?>
        <h3>Receptionist Actions</h3>
        <ul>
            <li><a href="patient_register.php">Register a new patient</a></li>
            <li><a href="create_appointment.php">Make an appointment</a></li>
        </ul>
    <?php elseif ($role == 'bursar'): ?>
        <h3>Bursar Actions</h3>
        <ul>
            <li><a href="billing.php">Verify payment</a></li>
        </ul>
    <?php elseif ($role == 'admin'): ?>
        <h3>Admin Actions</h3>
        <ul>
            <li><a href="all.php">All users that you have registered</a></li>
            <li><a href="billing.php">View payment records</a></li>
            <li><a href="appointments.php">view appointments made and make changes where necessary</a></li>
        </ul>
    <?php elseif ($role == 'doctor'): ?>
        <h3>Doctor Actions</h3>
        <ul>
            <li><a href="view_patients.php">View patient details</a></li>
            <li><a href="prescribe_treatment.php">Prescribe treatment</a></li>
        </ul>
    <?php else: ?>
        <h3>Unauthorized access</h3>
    <?php endif; ?>

    <br>
    <a href="logout.php">Log out</a>

</body>
</html>
