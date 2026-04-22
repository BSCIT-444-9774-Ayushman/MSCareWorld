<?php
session_start();
include("../config/connection.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// patient_id nikalna
$p_query = mysqli_query($conn, "SELECT patient_id FROM patients WHERE user_id='$user_id' LIMIT 1");

$p = mysqli_fetch_assoc($p_query);

if (!$p) {
    die("Patient record not found");
}

$patient_id = $p['patient_id'];

$query = "SELECT a.*, u.full_name AS doctor_name
          FROM appointments a
          JOIN doctors d ON a.doctor_id = d.doctor_id
          JOIN users u ON d.user_id = u.id
          WHERE a.patient_id = '$patient_id'
          ORDER BY a.appointment_id DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <link rel="stylesheet" href="../assets/css/patient/my-appoint.css">
</head>
<body>

<div class="page-wrapper">
    <div class="appointments-card">
        <h2 class="page-title">My Appointments</h2>

        <?php if (mysqli_num_rows($result) == 0) { ?>
            <p class="empty-message">No appointments found.</p>
        <?php } else { ?>

        <div class="table-responsive">
            <table class="appointments-table">
                <tr>
                    <th>ID</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Problem</th>
                    <th>Status</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['appointment_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['problem_description']); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
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