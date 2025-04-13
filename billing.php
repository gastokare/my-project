<?php
$conn = new mysqli("localhost", "root", "", "hospital_db");

// Fetch Bills
$result = $conn->query("SELECT * FROM billing");
$bills = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Billing System</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        button { background: green; color: white; padding: 5px; }
    </style>
</head>
<body>

    <h2>Hospital Billing System</h2>

    <form id="billingForm">
        <label>Patient ID:</label>
        <input type="number" id="patient_id" required><br><br>

        <label>Bill Type:</label>
        <select id="bill_type">
            <option value="Consultation">Consultation</option>
            <option value="Lab Test">Lab Test</option>
            <option value="IPD">IPD</option>
            <option value="Pharmacy">Pharmacy</option>
            <option value="Surgery">Surgery</option>
        </select><br><br>

        <label>Amount:</label>
        <input type="number" id="amount" required><br><br>

        <button type="button" onclick="addBill()">Generate Bill</button>
    </form>

    <h3>Patient's Bills Status</h3>
    <table>
        <thead>
            <tr>
                <th>Bill ID</th>
                <th>Patient ID</th>
                <th>Bill Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="billList">
            <?php foreach ($bills as $bill): ?>
                <tr>
                <td><?= isset($bill['bill_id']) ? $bill['bill_id'] : 'N/A' ?></td>
                    <td><?= $bill['patient_id'] ?></td>
                    <td><?= $bill['bill_type'] ?></td>
                    <td><?= $bill['amount'] ?></td>
                    <td><?= $bill['status'] === 'Paid' ? '✅ Paid' : '⏳ Pending' ?></td>
                    <td>
                        <?php if ($bill['status'] == 'Pending'): ?>
                            <button onclick="payBill(<?= $bill['bill_id'] ?>, <?= $bill['amount'] ?>)">Pay</button>
                        <?php else: ?>
                            Paid
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function addBill() {
            let patient_id = document.getElementById("patient_id").value;
            let bill_type = document.getElementById("bill_type").value;
            let amount = document.getElementById("amount").value;

            fetch("process.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=add_bill&patient_id=${patient_id}&bill_type=${bill_type}&amount=${amount}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
        }

        function payBill(bill_id, amount) {
            let paymentMethod = prompt("Enter payment method (Cash, Insurance, Mobile Money, Bank Transfer):");
            if (!paymentMethod) {
                alert("Payment method is required!");
             return;
        }

            fetch("process.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=pay_bill&bill_id=${bill_id}&amount=${amount}&payment_method=${paymentMethod}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
        }
    </script>

</body>
</html>
