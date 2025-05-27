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

// Fetch patient's health data
// $sql_health = "SELECT * FROM health_recordstb WHERE patient_id = '$user_id' ORDER BY record_date DESC LIMIT 1";
// $health_result = $con->query($sql_health);
// $health_data = $health_result->fetch_assoc();

// Fetch recent appointments
$sql_appointments = "SELECT * FROM appointmenttb WHERE patient_app_acc_id = '$user_id' ORDER BY appt_date DESC LIMIT 3";
$appointments_result = $con->query($sql_appointments);
?>

<div class="container-fluid pt-4 px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Health Report</h5>
            <p class="text-muted small mb-0">View and manage your health records</p>
        </div>
        <button class="btn btn-primary btn-sm rounded-0" onclick="window.print()">
            <i class="fa-solid fa-print me-2"></i>Print Report
        </button>
    </div>

    <div class="row g-4">
        <!-- Patient Information Card -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="../images/team_placeholder.jpg" class="rounded-circle" width="80" height="80">
                        </div>
                        <div class="col">
                            <h5 class="mb-1"><?= htmlspecialchars($fullname) ?></h5>
                            <p class="text-muted mb-0">Patient ID: <?= htmlspecialchars($user_id) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vital Statistics -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="mb-0">Blood Pressure</h6>
                        <span class="bg-primary-subtle text-primary rounded-pill px-2 py-1 small">
                            <?= date('M d, Y', strtotime($health_data['record_date'] ?? 'now')) ?>
                        </span>
                    </div>
                    <h3 class="mb-0 text-primary"><?= $health_data['blood_pressure'] ?? 'N/A' ?></h3>
                    <small class="text-muted">Last recorded BP</small>
                </div>
            </div>
        </div>

        <!-- Heart Rate -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="mb-0">Heart Rate</h6>
                        <i class="fa-solid fa-heartbeat text-danger"></i>
                    </div>
                    <h3 class="mb-0 text-danger"><?= $health_data['heart_rate'] ?? 'N/A' ?> BPM</h3>
                    <small class="text-muted">Normal range: 60-100 BPM</small>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-4">Recent Medical Appointments</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Doctor</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($appointment = $appointments_result->fetch_assoc()):
                                    $statusClass = match ($appointment['status']) {
                                        'Pending' => 'warning',
                                        'Approved' => 'success',
                                        'Cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($appointment['appt_date'])) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../images/team_placeholder.jpg" class="rounded-circle me-2" width="32" height="32">
                                                <div>
                                                    <h6 class="mb-0"><?= htmlspecialchars($appointment['doctor_name']) ?></h6>
                                                    <small class="text-muted"><?= htmlspecialchars($appointment['appt_type']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($appointment['appt_type']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $statusClass ?>-subtle text-<?= $statusClass ?>">
                                                <?= htmlspecialchars($appointment['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($appointment['notes']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include "../includes/script.php";
?>

<style>
    @media print {

        .sidebar,
        .navbar,
        .btn-primary {
            display: none !important;
        }

        .card {
            break-inside: avoid;
        }
    }
</style>