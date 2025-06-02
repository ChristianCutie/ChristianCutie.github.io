<?php
include "../includes/header.php";
include_once(__DIR__ . "/../connection/globalConnection.php");

$con = connection();
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
} else {
    $con->connect_error;
}
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM s_admintb WHERE id = '$user_id'";
$result = $con->query(query: $query);
$row = $result->fetch_assoc();


// Fetch user profile image
if (!empty($row['Profile_img']) && file_exists('../images/' . $row['Profile_img'])) {
    $profile_photo_default = '../images/' . $row['Profile_img'];
} else {
    $profile_photo_default = '../images/team_placeholder.jpg';
}


$current_page = basename($_SERVER['PHP_SELF']);
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
                <span>Super Admin</span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="../admin/dashboard.php" class="nav-item nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle <?= in_array($current_page, ['upcoming.php']) ? 'active show' : '' ?>" data-bs-toggle="dropdown"><i class="fa-regular fa-calendar-check me-2"></i>Appointment</a>
                <div class="dropdown-menu bg-transparent border-0 <?= in_array($current_page, ['upcoming.php']) ? 'show' : '' ?>">
                    <a href="../admin/upcoming.php" class="dropdown-item <?= in_array($current_page, ['upcoming.php']) ? 'active' : '' ?>">
                        <i class="fa-solid fa-calendar-day me-2"></i>Upcoming
                    </a>
                    <a href="signup.html" class="dropdown-item">
                        <i class="fa-solid fa-calendar-check me-2"></i>Completed
                    </a>
                    <a href="404.html" class="dropdown-item">
                        <i class="fa-solid fa-calendar-xmark me-2"></i>Reschedule
                    </a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle <?= in_array($current_page, ['patient-list.php', 'doctor-list.php', 'add-doctor.php', 'add-patient.php', 'staff-list.php']) ? 'active show' : '' ?>"
                    data-bs-toggle="dropdown">
                    <i class="fa fa-user me-2"></i>Account
                </a>
                <div class="dropdown-menu bg-transparent border-0 <?= in_array($current_page, ['patient-list.php', 'doctor-list.php', 'add-doctor.php', 'add-patient.php', 'staff-list.php']) ? 'show' : '' ?>">
                    <a href="../admin/doctor-list.php" class="dropdown-item <?= in_array($current_page, ['doctor-list.php', 'add-doctor.php']) ? 'active' : '' ?>">
                        <i class="fa-solid fa-user-doctor me-2"></i>Doctor
                    </a>
                    <a href="../admin/patient-list.php" class="dropdown-item <?= in_array($current_page, ['patient-list.php', 'add-patient.php']) ? 'active' : '' ?>">
                        <i class="fa-solid fa-hospital-user me-2"></i>Patient
                    </a>
                    <a href="../admin/staff-list.php" class="dropdown-item <?= $current_page == 'staff-list.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-user-nurse me-2"></i>Staff
                    </a>
                </div>
            </div>
            <a href="form.html" class="nav-item nav-link"><i class="fa-solid fa-right-from-bracket me-2"></i>Leave</a>
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
                    <img class="rounded-circle me-lg-2" src="../images/team_placeholder.jpg" alt="" style="width: 40px; height: 40px;">
                    <span class="d-none d-lg-inline-flex"><?= $row['First_Name'] . " " . $row['Last_Name'] ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    <a href="../admin/profile.php" class="dropdown-item">
                        <i class="fa-solid fa-user me-2"></i>My Profile
                    </a>
                    <a href="../admin/change-pass.php" class="dropdown-item">
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
    <?php include "../includes/script.php";?>