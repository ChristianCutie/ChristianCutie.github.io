<?php
include "../includes/header.php";
include_once(__DIR__ . "/../connection/globalConnection.php");

$con = connection();

if (session_status()  == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
} else {
    $con->connect_error;
}
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM patienttb WHERE patient_acc_id = '$user_id'";
$result = $con->query(query: $query);
$row = $result->fetch_assoc();



$current_page = basename($_SERVER['PHP_SELF']);

// Fetch user profile image
if (!empty($row['Profile_img']) && file_exists('../images/' . $row['Profile_img'])) {
    $profile_photo_default = '../images/' . $row['Profile_img'];
} else {
    $profile_photo_default = '../images/team_placeholder.jpg';
}
?>

<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

<!-- Sidebar Start -->
<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="../admin/dashboard.php" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary text-uppercase">MedRec</h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="<?= htmlspecialchars($profile_photo_default) ?>" alt="" style="width: 40px; height: 40px;">
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0"><?= $row['First_Name'] . " " . $row['Last_Name'] ?></h6>
                <span>Patient</span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="../patient/dashboard.php" class="nav-item nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle <?= in_array($current_page, ['appointment.php', 'app-history.php','add.php']) ? 'active show' : '' ?>" data-bs-toggle="dropdown"><i class="fa-regular fa-calendar-check me-2"></i>Appointments</a>
                 <div class="dropdown-menu bg-transparent border-0 <?= in_array($current_page, ['appointment.php', 'app-history.php','add.php']) ? 'show' : '' ?>"> 
                     <!--New Apointment-->
                    <a href="../patient/add.php" class="dropdown-item <?= in_array($current_page, ['add.php']) ? 'active' : '' ?>">
                        <i class="fa-solid fa-plus me-2"></i>New
                    </a>
                    <!--Apointment List-->
                    <a href="../patient/appointment.php" class="dropdown-item <?= in_array($current_page, ['appointment.php']) ? 'active' : '' ?>">
                        <i class="fa-solid fa-list me-2"></i>List
                    </a>
                     <!--Apointment History-->
                     <a href="../patient/app-history.php" class="dropdown-item <?= in_array($current_page, ['app-history.php']) ? 'active' : '' ?>">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>History
                    </a>
                   </div>  
            <a href="../patient/health-report.php" class="nav-item nav-link  <?= ($current_page == 'health-report.php') ? 'active' : '' ?>"><i class="fa-solid fa-file me-2"></i>Health Reports</a>
            <a href="../patient/medical-history.php" class="nav-item nav-link  <?= ($current_page == 'medical-history.php') ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left me-2"></i>Medical History</a>
            <a href="../patient/notes.php" class="nav-item nav-link  <?= ($current_page == 'notes.php') ? 'active' : '' ?>"><i class="fa-solid fa-file-circle-plus me-2"></i>Doctor Notes</a>
           
        </div>
    </nav>
</div>
<!-- Sidebar End -->


<!-- Content Start -->
<div class="content">
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
        <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
            <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
        </a>
        <a href="#" class="sidebar-toggler flex-shrink-0">
            <i class="fa fa-bars"></i>
        </a>
        <form class="d-none d-md-flex ms-4">
            <input class="form-control border-0" type="search" placeholder="Search">
        </form>
        <div class="navbar-nav align-items-center ms-auto">
            <div class="nav-item dropdown d-none">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-envelope me-lg-2"></i>
                    <span class="d-none d-lg-inline-flex">Message</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    <a href="#" class="dropdown-item">
                        <div class="d-flex align-items-center">
                            <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <div class="ms-2">
                                <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                <small>15 minutes ago</small>
                            </div>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item">
                        <div class="d-flex align-items-center">
                            <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <div class="ms-2">
                                <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                <small>15 minutes ago</small>
                            </div>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item">
                        <div class="d-flex align-items-center">
                            <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <div class="ms-2">
                                <h6 class="fw-normal mb-0">Jhon send you a message</h6>
                                <small>15 minutes ago</small>
                            </div>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item text-center">See all message</a>
                </div>
            </div>
            <div class="nav-item dropdown d-none">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-bell me-lg-2"></i>
                    <span class="d-none d-lg-inline-flex">Notificatin</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    <a href="#" class="dropdown-item">
                        <h6 class="fw-normal mb-0">Profile updated</h6>
                        <small>15 minutes ago</small>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item">
                        <h6 class="fw-normal mb-0">New user added</h6>
                        <small>15 minutes ago</small>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item">
                        <h6 class="fw-normal mb-0">Password changed</h6>
                        <small>15 minutes ago</small>
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item text-center">See all notifications</a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img class="rounded-circle me-lg-2" src="<?= htmlspecialchars($profile_photo_default) ?>" alt="" style="width: 40px; height: 40px;">
                    <span class="d-none d-lg-inline-flex"><?= $row['First_Name'] . " " . $row['Last_Name'] ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    <a href="../patient/profile.php" class="dropdown-item <?= in_array($current_page, ['profile.php']) ? 'active' : '' ?></a>">
                        <i class="fa-solid fa-user me-2"></i>My Profile
                    </a>
                    <a href="../patient/change-pass.php" class="dropdown-item">
                        <i class="fa-solid fa-key me-2"></i>Change Password
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="../login.php" class="dropdown-item text-danger">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>Log Out
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
    <!-- Sidebar End -->