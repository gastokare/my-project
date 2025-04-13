<?php
$conn = new mysqli("localhost", "root", "", "hospital_db");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST['action'] === 'add_bill') {
        $patient_id = $_POST['patient_id'];
        $bill_type = $_POST['bill_type'];
        $amount = $_POST['amount'];

        $stmt = $conn->prepare("INSERT INTO billing (patient_id, bill_type, amount, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("isd", $patient_id, $bill_type, $amount);

        if ($stmt->execute()) {
            echo "Bill added successfully!";
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    }

    if ($_POST['action'] === 'pay_bill') {
        $bill_id = $_POST['bill_id'];
        $amount = $_POST['amount'];
        $payment_method = $_POST['payment_method'];

        // Insert payment into payments table
        $stmt = $conn->prepare("INSERT INTO payments (bill_id, amount_paid, payment_method) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $bill_id, $amount, $payment_method);

        if ($stmt->execute()) {
            // Update the billing table to mark the bill as paid
            $updateStmt = $conn->prepare("UPDATE billing SET status = 'Paid' WHERE bill_id = ?");
            $updateStmt->bind_param("i", $bill_id);
            $updateStmt->execute();
            $updateStmt->close();

            echo "Payment successful!";
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    }
}
$conn->close();
?>

