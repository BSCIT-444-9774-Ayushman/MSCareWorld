<?php
session_start();
include("../config/connection.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$p_query = mysqli_query($conn, "SELECT patient_id FROM patients WHERE user_id='$user_id' LIMIT 1");
$p = mysqli_fetch_assoc($p_query);

if (!$p) {
    die("Patient record not found");
}

$patient_id = $p['patient_id'];

// payment submit
if (isset($_POST['pay_now'])) {
    $appointment_id = $_POST['appointment_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);

    $check_payment = mysqli_query($conn, "SELECT * FROM payments WHERE appointment_id='$appointment_id' AND patient_id='$patient_id'");

    if (mysqli_num_rows($check_payment) > 0) {
        $msg = "Payment for this appointment already exists";
    } else {
        $insert = "INSERT INTO payments 
                   (appointment_id, patient_id, amount, payment_method, payment_status, transaction_id, paid_at)
                   VALUES
                   ('$appointment_id', '$patient_id', '$amount', '$payment_method', 'paid', '$transaction_id', NOW())";

        if (mysqli_query($conn, $insert)) {
            $msg = "Payment successful";
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}

// patient appointments for dropdown
$appointments_query = mysqli_query($conn, "
    SELECT a.appointment_id, a.appointment_date, a.appointment_time, u.full_name AS doctor_name
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    JOIN users u ON d.user_id = u.id
    WHERE a.patient_id = '$patient_id'
    ORDER BY a.appointment_id DESC
");

// payment history
$history_query = mysqli_query($conn, "
    SELECT py.*, u.full_name AS doctor_name
    FROM payments py
    JOIN appointments a ON py.appointment_id = a.appointment_id
    JOIN doctors d ON a.doctor_id = d.doctor_id
    JOIN users u ON d.user_id = u.id
    WHERE py.patient_id = '$patient_id'
    ORDER BY py.payment_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payments</title>
    <link rel="stylesheet" href="../assets/css/patient/payment.css">
</head>
<body>

<div class="page-wrapper">

    <div class="payment-card">
        <h2 class="page-title">Make Payment</h2>

        <?php if (isset($msg)) { ?>
            <p class="message"><?php echo htmlspecialchars($msg); ?></p>
        <?php } ?>

        <form method="POST" class="payment-form">

            <div class="form-group">
                <label>Select Appointment</label>
                <select name="appointment_id" required>
                    <option value="">-- Select Appointment --</option>
                    <?php while ($row = mysqli_fetch_assoc($appointments_query)) { ?>
                        <option value="<?php echo $row['appointment_id']; ?>">
                            <?php echo htmlspecialchars($row['doctor_name'] . " - " . $row['appointment_date'] . " " . $row['appointment_time']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Amount</label>
                <input type="number" name="amount" step="0.01" placeholder="Enter amount" required>
            </div>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="">-- Select Method --</option>
                    <option value="cash">Cash</option>
                    <option value="upi">UPI</option>
                    <option value="card">Card</option>
                    <option value="netbanking">Net Banking</option>
                </select>
            </div>

            <div class="form-group">
                <label>Transaction ID</label>
                <input type="text" name="transaction_id" placeholder="Enter transaction ID">
            </div>

            <button type="submit" name="pay_now" class="btn-pay">Pay Now</button>
        </form>
    </div>

    <div class="history-card">
        <h2 class="page-title small-title">Payment History</h2>

        <?php if (mysqli_num_rows($history_query) == 0) { ?>
            <p class="empty-message">No payment records found.</p>
        <?php } else { ?>
            <div class="table-responsive">
                <table class="payment-table">
                    <tr>
                        <th>ID</th>
                        <th>Doctor</th>
                        <th>Appointment ID</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Transaction ID</th>
                        <th>Paid At</th>
                    </tr>

                    <?php while ($pay = mysqli_fetch_assoc($history_query)) { ?>
                    <tr>
                        <td><?php echo $pay['payment_id']; ?></td>
                        <td><?php echo htmlspecialchars($pay['doctor_name']); ?></td>
                        <td><?php echo $pay['appointment_id']; ?></td>
                        <td>₹<?php echo htmlspecialchars($pay['amount']); ?></td>
                        <td><?php echo htmlspecialchars($pay['payment_method']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($pay['payment_status']); ?>">
                                <?php echo htmlspecialchars($pay['payment_status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($pay['transaction_id']); ?></td>
                        <td><?php echo htmlspecialchars($pay['paid_at']); ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>

        <div class="back-wrap">
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>
    </div>

</div>

</body>
</html>