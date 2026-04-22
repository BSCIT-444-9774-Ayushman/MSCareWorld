<?php
session_start();
include("../config/connection.php");

// 🔐 patient check
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctors List</title>
    <link rel="stylesheet" href="../assets/css/patient/doctors.css">
</head>
<body>

<div class="page-wrapper">
    <div class="doctors-card">
        <h2 class="page-title">Available Doctors</h2>

        <div class="table-responsive">
            <table class="doctors-table">
                <tr>
                    <th>ID</th>
                    <th>Doctor Name</th>
                    <th>Department</th>
                    <th>Specialization</th>
                    <th>Action</th>
                </tr>

                <?php
                $query = "SELECT d.doctor_id, u.full_name, dep.department_name, spec.specialization_name
                          FROM doctors d
                          JOIN users u ON d.user_id = u.id
                          JOIN departments dep ON d.department_id = dep.department_id
                          JOIN specializations spec ON d.specialization_id = spec.specialization_id
                          ORDER BY d.doctor_id DESC";

                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $row['doctor_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['specialization_name']); ?></td>
                    <td>
                        <a class="btn-book"
                           href="book-appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>">
                           Book Appointment
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

    </div>
</div>

</body>
</html>