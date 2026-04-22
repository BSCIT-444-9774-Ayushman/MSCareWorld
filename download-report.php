<?php
session_start();
include("../config/connection.php");
require("../pdf/fpdf.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// patient info
$patient_query = mysqli_query($conn, "
    SELECT p.patient_id, u.full_name, u.email, u.phone
    FROM patients p
    JOIN users u ON p.user_id = u.id
    WHERE p.user_id = '$user_id'
    LIMIT 1
");

$patient = mysqli_fetch_assoc($patient_query);

if (!$patient) {
    die("Patient record not found");
}

$patient_id = $patient['patient_id'];

// latest appointment + doctor
$appointment_query = mysqli_query($conn, "
    SELECT a.*, u.full_name AS doctor_name
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    JOIN users u ON d.user_id = u.id
    WHERE a.patient_id = '$patient_id'
    ORDER BY a.appointment_id DESC
    LIMIT 1
");

$appointment = mysqli_fetch_assoc($appointment_query);

// latest prescription
$prescription_query = mysqli_query($conn, "
    SELECT *
    FROM prescriptions
    WHERE patient_id = '$patient_id'
    ORDER BY prescription_id DESC
    LIMIT 1
");

$prescription = mysqli_fetch_assoc($prescription_query);

// latest payment
$payment_query = mysqli_query($conn, "
    SELECT *
    FROM payments
    WHERE patient_id = '$patient_id'
    ORDER BY payment_id DESC
    LIMIT 1
");

$payment = mysqli_fetch_assoc($payment_query);

// PDF start
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

// Title
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 12, 'MSCareWorld - Patient Medical Report', 0, 1, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, 'Generated on: ' . date("d-m-Y h:i A"), 0, 1, 'C');
$pdf->Ln(5);

// Patient Details
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Patient Details', 0, 1);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 8, 'Patient Name:', 0, 0);
$pdf->Cell(0, 8, $patient['full_name'], 0, 1);

$pdf->Cell(50, 8, 'Email:', 0, 0);
$pdf->Cell(0, 8, $patient['email'], 0, 1);

$pdf->Cell(50, 8, 'Phone:', 0, 0);
$pdf->Cell(0, 8, $patient['phone'], 0, 1);

$pdf->Ln(4);

// Appointment Details
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Latest Appointment Details', 0, 1);

$pdf->SetFont('Arial', '', 12);

if ($appointment) {
    $pdf->Cell(50, 8, 'Doctor Name:', 0, 0);
    $pdf->Cell(0, 8, $appointment['doctor_name'], 0, 1);

    $pdf->Cell(50, 8, 'Appointment Date:', 0, 0);
    $pdf->Cell(0, 8, $appointment['appointment_date'], 0, 1);

    $pdf->Cell(50, 8, 'Appointment Time:', 0, 0);
    $pdf->Cell(0, 8, $appointment['appointment_time'], 0, 1);

    $pdf->Cell(50, 8, 'Status:', 0, 0);
    $pdf->Cell(0, 8, $appointment['status'], 0, 1);

    $pdf->Cell(50, 8, 'Problem:', 0, 1);
    $pdf->MultiCell(0, 8, $appointment['problem_description']);
} else {
    $pdf->Cell(0, 8, 'No appointment found.', 0, 1);
}

$pdf->Ln(4);

// Prescription Details
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Latest Prescription Details', 0, 1);

$pdf->SetFont('Arial', '', 12);

if ($prescription) {
    $pdf->Cell(50, 8, 'Symptoms:', 0, 1);
    $pdf->MultiCell(0, 8, $prescription['symptoms']);

    $pdf->Cell(50, 8, 'Diagnosis:', 0, 1);
    $pdf->MultiCell(0, 8, $prescription['diagnosis']);

    $pdf->Cell(50, 8, 'Medicines:', 0, 1);
    $pdf->MultiCell(0, 8, $prescription['medicines']);

    $pdf->Cell(50, 8, 'Notes:', 0, 1);
    $pdf->MultiCell(0, 8, $prescription['notes']);
} else {
    $pdf->Cell(0, 8, 'No prescription found.', 0, 1);
}

$pdf->Ln(4);

// Payment Details
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Latest Payment Details', 0, 1);

$pdf->SetFont('Arial', '', 12);

if ($payment) {
    $pdf->Cell(50, 8, 'Amount:', 0, 0);
    $pdf->Cell(0, 8, $payment['amount'], 0, 1);

    $pdf->Cell(50, 8, 'Payment Method:', 0, 0);
    $pdf->Cell(0, 8, $payment['payment_method'], 0, 1);

    $pdf->Cell(50, 8, 'Payment Status:', 0, 0);
    $pdf->Cell(0, 8, $payment['payment_status'], 0, 1);

    $pdf->Cell(50, 8, 'Transaction ID:', 0, 0);
    $pdf->Cell(0, 8, $payment['transaction_id'], 0, 1);

    $pdf->Cell(50, 8, 'Paid At:', 0, 0);
    $pdf->Cell(0, 8, $payment['paid_at'], 0, 1);
} else {
    $pdf->Cell(0, 8, 'No payment found.', 0, 1);
}

// Output
$pdf->Output('D', 'Patient_Medical_Report.pdf');
exit;
?>