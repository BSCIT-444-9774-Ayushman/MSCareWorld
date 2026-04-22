<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

$patientName = $_SESSION['name'] ?? 'Patient';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: Arial, sans-serif;
        }

        body{
            background:#f4f7fb;
        }

        .wrapper{
            display:flex;
            min-height:100vh;
        }

        /* SIDEBAR */
        .sidebar{
            width:260px;
            background:linear-gradient(180deg, #0f172a, #1e293b);
            color:#fff;
            padding:25px 18px;
        }

        .sidebar h2{
            text-align:center;
            margin-bottom:30px;
        }

        .profile-box{
            text-align:center;
            background:rgba(255,255,255,0.08);
            padding:15px;
            border-radius:15px;
            margin-bottom:25px;
        }

        .avatar{
            width:70px;
            height:70px;
            border-radius:50%;
            background:#3b82f6;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:28px;
            font-weight:bold;
            margin:0 auto 10px;
        }

        .profile-box h3{
            font-size:18px;
        }

        .menu a{
            display:block;
            color:#fff;
            text-decoration:none;
            padding:12px 15px;
            margin-bottom:10px;
            border-radius:10px;
            background:rgba(255,255,255,0.05);
            transition:0.3s;
        }

        .menu a:hover{
            background:#2563eb;
            transform:translateX(4px);
        }

        .logout{
            background:#dc2626 !important;
        }

        /* CONTENT */
        .content{
            flex:1;
            padding:30px;
        }

        .topbar{
            background:linear-gradient(135deg, #2563eb, #1d4ed8);
            color:#fff;
            padding:25px;
            border-radius:18px;
            margin-bottom:25px;
        }

        .topbar h1{
            font-size:28px;
        }

        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
            gap:20px;
        }

        .card{
            background:#fff;
            padding:22px;
            border-radius:16px;
            box-shadow:0 6px 15px rgba(0,0,0,0.08);
            transition:0.3s;
        }

        .card:hover{
            transform:translateY(-5px);
        }

        .card h3{
            margin-bottom:10px;
        }

        .card a{
            display:inline-block;
            margin-top:10px;
            text-decoration:none;
            background:#2563eb;
            color:#fff;
            padding:8px 14px;
            border-radius:8px;
            font-size:14px;
        }

        .card a:hover{
            background:#1d4ed8;
        }

        @media(max-width:900px){
            .wrapper{
                flex-direction:column;
            }

            .sidebar{
                width:100%;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Patient Panel</h2>

        <div class="profile-box">
            <div class="avatar">
                <?php echo strtoupper(substr($patientName, 0, 1)); ?>
            </div>
            <h3><?php echo htmlspecialchars($patientName); ?></h3>
        </div>

        <div class="menu">
            <a href="profile.php">My Profile</a>
            <a href="profile-edit.php">Edit Profile</a>
            <a href="doctors.php">View Doctors</a>
            <a href="my-appointments.php">My Appointments</a>
            <a href="prescriptions.php">My Prescriptions</a>
            <a href="payments.php">Payments</a>
            <a href="download-report.php">Download Report</a>
            <a href="../auth/logout.php" class="logout">Logout</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content">

        <div class="topbar">
            <h1>Welcome, <?php echo htmlspecialchars($patientName); ?> 👋</h1>
            <p>Manage your appointments, profile and health records easily.</p>
        </div>

        <div class="cards">

            <div class="card">
                <h3>My Profile</h3>
                <p>View your personal information.</p>
                <a href="profile.php">Open</a>
            </div>

            <div class="card">
                <h3>Edit Profile</h3>
                <p>Update your details anytime.</p>
                <a href="profile-edit.php">Edit</a>
            </div>

            <div class="card">
                <h3>Doctors</h3>
                <p>Browse available doctors.</p>
                <a href="doctors.php">View</a>
            </div>

            <div class="card">
                <h3>Appointments</h3>
                <p>Check your booked appointments.</p>
                <a href="my-appointments.php">Open</a>
            </div>

            <div class="card">
                <h3>Prescriptions</h3>
                <p>View your prescriptions.</p>
                <a href="prescriptions.php">View</a>
            </div>

            <div class="card">
                <h3>Payments</h3>
                <p>Manage your payments.</p>
                <a href="payments.php">Open</a>
            </div>

            <div class="card">
                <h3>Medical Report</h3>
                <p>Download your report in PDF.</p>
                <a href="download-report.php">Download</a>
            </div>

        </div>

    </div>

</div>

</body>
</html>