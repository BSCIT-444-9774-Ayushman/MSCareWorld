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

$query = "SELECT pr.*, u.full_name AS doctor_name
          FROM prescriptions pr
          JOIN doctors d ON pr.doctor_id = d.doctor_id
          JOIN users u ON d.user_id = u.id
          WHERE pr.patient_id = '$patient_id'
          ORDER BY pr.prescription_id DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Prescription query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Prescriptions</title>
    <link rel="stylesheet" href="../assets/css/patient/prescription.css">
</head>
<body>

<div class="page-wrapper">
    <div class="prescription-card">
        <h2 class="page-title">My Prescriptions</h2>

        <?php if (mysqli_num_rows($result) == 0) { ?>
            <p class="empty-message">No prescriptions found.</p>
        <?php } else { ?>

        <div class="table-responsive">
            <table class="prescription-table">
                <tr>
                    <th>ID</th>
                    <th>Doctor</th>
                    <th>Symptoms</th>
                    <th>Diagnosis</th>
                    <th>Medicines</th>
                    <th>Notes</th>
                    <th>Date</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['prescription_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['symptoms'])); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['diagnosis'])); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['medicines'])); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['notes'])); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
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