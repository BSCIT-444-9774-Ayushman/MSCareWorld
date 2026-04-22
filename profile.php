<?php
session_start();
include("../config/connection.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT u.full_name, u.email, u.phone, p.*
    FROM users u
    JOIN patients p ON u.id = p.user_id
    WHERE u.id = '$user_id'
    LIMIT 1
");

$patient = mysqli_fetch_assoc($query);

if (!$patient) {
    die("Patient profile not found");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="../assets/css/patient/profile.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h2>My Profile</h2>

        <?php if (!empty($patient['profile_image'])) { ?>
            <img src="../uploads/patients/<?php echo htmlspecialchars($patient['profile_image']); ?>"
                 alt="Patient Profile Image"
                 width="120"
                 height="120"
                 style="object-fit: cover; border-radius: 50%; margin-bottom: 15px;">
        <?php } else { ?>
            <p>No profile image uploaded</p>
        <?php } ?>

        <table>
            <tr><th>Field</th><th>Value</th></tr>
            <tr><td>Full Name</td><td><?php echo htmlspecialchars($patient['full_name']); ?></td></tr>
            <tr><td>Email</td><td><?php echo htmlspecialchars($patient['email']); ?></td></tr>
            <tr><td>Phone</td><td><?php echo htmlspecialchars($patient['phone']); ?></td></tr>
            <tr><td>Gender</td><td><?php echo htmlspecialchars($patient['gender']); ?></td></tr>
            <tr><td>Date of Birth</td><td><?php echo htmlspecialchars($patient['dob']); ?></td></tr>
            <tr><td>Age</td><td><?php echo htmlspecialchars($patient['age']); ?></td></tr>
            <tr><td>Blood Group</td><td><?php echo htmlspecialchars($patient['blood_group']); ?></td></tr>
            <tr><td>Address</td><td><?php echo htmlspecialchars($patient['address']); ?></td></tr>
            <tr><td>City</td><td><?php echo htmlspecialchars($patient['city']); ?></td></tr>
            <tr><td>State</td><td><?php echo htmlspecialchars($patient['state']); ?></td></tr>
            <tr><td>Pincode</td><td><?php echo htmlspecialchars($patient['pincode']); ?></td></tr>
            <tr><td>Emergency Contact</td><td><?php echo htmlspecialchars($patient['emergency_contact']); ?></td></tr>
        </table>

        <br>
        <a class="btn btn-primary" href="profile-edit.php">Edit Profile</a>
        <a class="btn btn-primary" href="dashboard.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>