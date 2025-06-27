<?php
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";


if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$con = connection();
/*Doctor List count*/
$sql = "SELECT COUNT(*) AS doctor_count FROM doctortb";
$result = $con->query($sql);
$doctor_count = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $doctor_count = $row["doctor_count"];
}
?>

<!-- Loader -->
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="container-fluid pt-4 px-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <h3 class="mb-0">Welcome to Admin Dashboard</h3>
                <p class="text-muted">Here's what's happening today</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4 position-relative overflow-hidden">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Total Doctors</h6>
                    <h2 class="mb-0"><?php echo $doctor_count; ?></h2>
                    <span class="text-success mt-2">
                        <i class="fas fa-arrow-up me-1"></i>5% increase
                    </span>
                </div>
                <i class="fa fa-user-doctor fa-3x text-primary ms-auto"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4 position-relative overflow-hidden">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Total Patients</h6>
                    <h2 class="mb-0">248</h2>
                    <span class="text-success mt-2">
                        <i class="fas fa-arrow-up me-1"></i>8% increase
                    </span>
                </div>
                <i class="fa fa-hospital-user fa-3x text-primary ms-auto"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4 position-relative overflow-hidden">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Appointments</h6>
                    <h2 class="mb-0">45</h2>
                    <span class="text-danger mt-2">
                        <i class="fas fa-arrow-down me-1"></i>2% decrease
                    </span>
                </div>
                <i class="fa fa-calendar-check fa-3x text-primary ms-auto"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4 position-relative overflow-hidden">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Today's Revenue</h6>
                    <h2 class="mb-0">$1,245</h2>
                    <span class="text-success mt-2">
                        <i class="fas fa-arrow-up me-1"></i>12% increase
                    </span>
                </div>
                <i class="fa fa-dollar-sign fa-3x text-primary ms-auto"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="bg-light rounded p-4">
                <h6 class="mb-4">Appointment Statistics</h6>
                <canvas id="appointmentChart"></canvas>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="bg-light rounded p-4">
                <h6 class="mb-4">Department Distribution</h6>
                <canvas id="departmentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <div class="d-flex justify-content-between mb-4">
                    <h6>Recent Activities</h6>
                    <a href="">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Activity</th>
                                <th scope="col">Time</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td>New appointment scheduled</td>
                                <td>10 mins ago</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td>Patient registration</td>
                                <td>30 mins ago</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/script.php";?>

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Appointment Statistics Chart
const appointmentChart = new Chart(document.getElementById('appointmentChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Appointments',
            data: [65, 59, 80, 81, 56, 55],
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    }
});

// Department Distribution Chart
const departmentChart = new Chart(document.getElementById('departmentChart'), {
    type: 'doughnut',
    data: {
        labels: ['Cardiology', 'Neurology', 'Dental', 'Others'],
        datasets: [{
            data: [30, 25, 20, 25],
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)'
            ]
        }]
    }
});
</script>

