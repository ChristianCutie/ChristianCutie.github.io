<?php
include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";


if (session_status()  == PHP_SESSION_NONE) {
    session_start();
}
$con = connection();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
} else {
    $con->connect_error;
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM patienttb WHERE patient_acc_id = '$user_id'";
$result = $con->query($sql);
$user_name = $result->fetch_assoc();

$fullname = $user_name['First_Name'] . " " . $user_name['Last_Name'];
if ($result === false) {
    die("Error fetching user data: " . htmlspecialchars($con->error));
}

if ($result->num_rows === 0) {
    header('Location: /login.php');
    exit();
}
if ($user_name === null) {
    die("User not found.");
}
if ($user_name === false) {
    die("Error fetching user data: " . htmlspecialchars($con->error));
}
$result = $con->query($sql);
if ($result === false) {
    die("Error fetching user data: " . htmlspecialchars($con->error));
}



?>
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between mb-4">
        <h5 class="fw-light">
            <span class="text-muted">Profile</span>
            <span class="text-dark">/Patient Profile</span>
        </h5>
        <a href="../patient/edit-profile.php" class="btn btn-primary btn-sm rounded-0">
            <i class="fa fa-edit me-1"></i> Edit Profile
        </a>
    </div>

    <!-- Main Profile Section -->
    <div class="row">
        <!-- Left Column - Profile Info -->
        <div class="col-xl-4 col-lg-5">
            <div class="bg-light rounded p-4 mb-4">
                <div class="text-center mb-4">
                    <img src="../images/team_placeholder.jpg" alt="Patient Image" 
                         class="img-fluid rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 0 15px rgba(0,0,0,0.1);">
                    <h5 class="mb-0"><?= htmlspecialchars($fullname) ?></h5>
                    <p class="text-muted small">Patient ID: <?= htmlspecialchars($user_name['patient_acc_id']) ?></p>
                </div>
                
                <div class="border-top pt-3">
                    <h6 class="mb-3">Contact Information</h6>
                    <div class="mb-2">
                        <i class="fa fa-envelope text-primary me-2"></i>
                        <?php echo !empty($user_name['Email_address']) ? 
                            htmlspecialchars($user_name['Email_address']) : 
                            '<span class="text-muted">No email provided</span>'; ?>
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-phone text-primary me-2"></i>
                        <?php echo !empty($user_name['Phone_Number']) ? 
                            htmlspecialchars($user_name['Phone_Number']) : 
                            '<span class="text-muted">No phone provided</span>'; ?>
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-map-marker-alt text-primary me-2"></i>
                        <?php echo !empty($user_name['Address']) ? 
                            htmlspecialchars($user_name['Address']) : 
                            '<span class="text-muted">No address provided</span>'; ?>
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-calendar text-primary me-2"></i>
                        <?php echo !empty($user_name['Date_of_Birth']) ? 
                            htmlspecialchars($user_name['Date_of_Birth']) : 
                            '<span class="text-muted">No birth date provided</span>'; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Medical Information -->
        <div class="col-xl-8 col-lg-7">
            <!-- Medical History -->
            <div class="bg-light rounded p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Medical History</h6>
                    <button class="btn btn-sm btn-outline-primary rounded-0">View All</button>
                </div>
                <div class="border-top pt-3">
                    <p class="text-muted">No medical history available.</p>
                </div>
            </div>

            <!-- Health Reports -->
            <div class="bg-light rounded p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Health Reports</h6>
                    <button class="btn btn-sm btn-outline-primary rounded-0">View All</button>
                </div>
                <div class="border-top pt-3">
                    <p class="text-muted">No health reports available.</p>
                </div>
            </div>

            <!-- Doctor Notes -->
            <div class="bg-light rounded p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Doctor Notes</h6>
                    <button class="btn btn-sm btn-outline-primary rounded-0">View All</button>
                </div>
                <div class="border-top pt-3">
                    <p class="text-muted">No doctor notes available.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/script.php"; ?>