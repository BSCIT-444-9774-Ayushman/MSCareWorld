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

$msg = "";

if (isset($_POST['update_profile'])) {
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $emergency_contact = mysqli_real_escape_string($conn, $_POST['emergency_contact']);

    $age = NULL;

    if (!empty($dob)) {
        $birthDate = new DateTime($dob);
        $today = new DateTime();

        if ($birthDate > $today) {
            $msg = "Date of birth cannot be in the future";
        } else {
            $age = $today->diff($birthDate)->y;
        }
    }

    $profile_image = $patient['profile_image'];

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
        $file_name = $_FILES['profile_image']['name'];
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_size = $_FILES['profile_image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            if ($file_size <= 2 * 1024 * 1024) {
                $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
                $upload_path = "../uploads/patients/" . $new_file_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    if (!empty($patient['profile_image']) && file_exists("../uploads/patients/" . $patient['profile_image'])) {
                        unlink("../uploads/patients/" . $patient['profile_image']);
                    }
                    $profile_image = $new_file_name;
                } else {
                    $msg = "Image upload failed";
                }
            } else {
                $msg = "Image size must be less than 2MB";
            }
        } else {
            $msg = "Only JPG, JPEG, PNG, and WEBP files are allowed";
        }
    }

    if (empty($msg)) {
        $update = mysqli_query($conn, "
            UPDATE patients SET
                gender='$gender',
                dob='$dob',
                age=" . ($age === NULL ? "NULL" : "'$age'") . ",
                blood_group='$blood_group',
                address='$address',
                city='$city',
                state='$state',
                pincode='$pincode',
                emergency_contact='$emergency_contact',
                profile_image='$profile_image'
            WHERE user_id='$user_id'
        ");

        if ($update) {
            $msg = "Profile updated successfully";

            $query = mysqli_query($conn, "
                SELECT u.full_name, u.email, u.phone, p.*
                FROM users u
                JOIN patients p ON u.id = p.user_id
                WHERE u.id = '$user_id'
                LIMIT 1
            ");
            $patient = mysqli_fetch_assoc($query);
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../assets/css/patient/edit-profile.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h2>Edit Patient Profile</h2>

        <?php if (!empty($msg)) echo "<p>$msg</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Full Name</label>
            <input type="text" value="<?php echo htmlspecialchars($patient['full_name']); ?>" readonly>

            <label>Email</label>
            <input type="email" value="<?php echo htmlspecialchars($patient['email']); ?>" readonly>

            <label>Phone</label>
            <input type="text" value="<?php echo htmlspecialchars($patient['phone']); ?>" readonly>

            <label>Profile Image</label>
            <input type="file" name="profile_image" accept="image/*">

            <?php if (!empty($patient['profile_image'])) { ?>
                <img src="../uploads/patients/<?php echo htmlspecialchars($patient['profile_image']); ?>"
                     alt="Patient Profile Image"
                     width="100"
                     height="100"
                     style="object-fit: cover; border-radius: 50%; display: block; margin: 10px 0;">
            <?php } ?>

            <label>Gender</label>
            <select name="gender">
                <option value="">-- Select Gender --</option>
                <option value="male" <?php if ($patient['gender'] == 'male') echo 'selected'; ?>>Male</option>
                <option value="female" <?php if ($patient['gender'] == 'female') echo 'selected'; ?>>Female</option>
                <option value="other" <?php if ($patient['gender'] == 'other') echo 'selected'; ?>>Other</option>
            </select>

            <label>Date of Birth</label>
            <input type="date" name="dob" value="<?php echo htmlspecialchars($patient['dob']); ?>">

            <label>Age</label>
            <input type="number" value="<?php echo htmlspecialchars($patient['age']); ?>" readonly>

            <label>Blood Group</label>
            <input type="text" name="blood_group" value="<?php echo htmlspecialchars($patient['blood_group']); ?>">

            <label>Address</label>
            <textarea name="address"><?php echo htmlspecialchars($patient['address']); ?></textarea>

            <label>City</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($patient['city']); ?>">

            <label>State</label>
            <input type="text" name="state" value="<?php echo htmlspecialchars($patient['state']); ?>">

            <label>Pincode</label>
            <input type="text" name="pincode" value="<?php echo htmlspecialchars($patient['pincode']); ?>">

            <label>Emergency Contact</label>
            <input type="text" name="emergency_contact" value="<?php echo htmlspecialchars($patient['emergency_contact']); ?>">

            <button class="btn btn-success" type="submit" name="update_profile">Update Profile</button>
            <a class="btn btn-primary" href="profile.php">View Profile</a>
        </form>
    </div>
</div>

</body>
</html>