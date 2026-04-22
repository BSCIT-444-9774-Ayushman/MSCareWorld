<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/connection.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['doctor_id'])) {
    die("Invalid Access: doctor_id missing");
}

$doctor_id = $_GET['doctor_id'];

$user_id = $_SESSION['user_id'];

$patient_query = mysqli_query($conn, "SELECT patient_id FROM patients WHERE user_id='$user_id' LIMIT 1");

if (!$patient_query) {
    die("Patient query failed: " . mysqli_error($conn));
}

$patient = mysqli_fetch_assoc($patient_query);

if (!$patient) {
    die("Patient record not found for this user.");
}

$patient_id = $patient['patient_id'];

if (isset($_POST['book'])) {
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $problem = mysqli_real_escape_string($conn, $_POST['problem']);

    $insert = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, problem_description)
               VALUES ('$patient_id', '$doctor_id', '$date', '$time', '$problem')";

    if (mysqli_query($conn, $insert)) {
        $msg = "Appointment booked successfully";
    } else {
        $msg = "Insert error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <link rel="stylesheet" href="../assets/css/patient/book-appoint.css">
</head>
<body>

<div class="appointment-form-wrapper">
    <div class="appointment-form-card">
        <h2>Book Appointment</h2>

        <?php if (isset($msg)) echo "<p class='form-message'>$msg</p>"; ?>

        <form method="POST" class="appointment-form">
            <label>Date</label>
            <input type="date" name="appointment_date" required>

            <label>Time</label>
            <input type="time" name="appointment_time" required>

            <label>Problem</label>
            <textarea name="problem" required></textarea>

            <button type="submit" name="book">Book Appointment</button>
        </form>
    </div>
</div>
</body>
</html>