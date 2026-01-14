<?php
include "../includes/header.php";
include "../includes/sidebar-doctor.php";
require_once "../connection/globalConnection.php";

$con = connection();

$doc_id = $_SESSION['user_id'];

// Fetch doctor details
$sql = "SELECT * FROM doctortb WHERE doctor_acc_id = $doc_id";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    $doctor_Fullname = $doctor['First_Name'] . ' ' . $doctor['Last_Name'];
} 

/*Doctor List count*/
$sql = "SELECT COUNT(*) AS doctor_count FROM doctortb";
$result = $con->query($sql);
$doctor_count = 0;  
if($result-> num_rows > 0)
{
    $row = $result->fetch_assoc();
    $doctor_count = $row["doctor_count"];
}



/*/Doctor List count*/
?>
<!-- Dashboard Design Start -->
<div class="container-fluid pt-4 px-4">
     <div class="row mb-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <h3 class="mb-0">Welcome, Dr. <?=htmlspecialchars($doctor_Fullname)?></h3>
                <p class="text-muted">Here’s a quick overview of your dashboard.</p>
            </div>
        </div>
    </div>
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4 position-relative overflow-hidden">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Total Patients</h6>
                    <h2 class="mb-0">25</h2>
                </div>
                <i class="fa fa-users fa-3x text-primary ms-auto"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4 position-relative overflow-hidden">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Appointments</h6>
                    <h2 class="mb-0">248</h2>
                </div>
                <i class="fa fa-calendar-check fa-3x text-primary ms-auto"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4 position-relative overflow-hidden">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Reschedule</h6>
                    <h2 class="mb-0">45</h2>
                </div>
                <i class="fa fa-calendar-week fa-3x text-primary ms-auto"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4 position-relative overflow-hidden">
                <div class="d-flex flex-column">
                    <h6 style="text-wrap: nowrap;" class="text-muted mb-1">Today Appointments</h6>
                    <h2 class="mb-0">$1,245</h2>
                </div>
                <i class="fa fa-dollar-sign fa-3x text-primary ms-auto"></i>
            </div>
        </div>
    </div>
    <!-- Add more dashboard sections below as needed -->
</div>
<!-- Dashboard Design End -->
<?php 
include "../includes/script.php";
?>

