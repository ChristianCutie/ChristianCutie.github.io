<?php
include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$con = connection();

// Get appointment count for the patient
$patient_id = $_SESSION['user_id']; // Assuming you store patient ID in session
$sql = "SELECT COUNT(*) AS appointment_count FROM appointmenttb WHERE patient_app_acc_id = $patient_id";
$result = $con->query($sql);
$appointment_count = ($result->num_rows > 0) ? $result->fetch_assoc()["appointment_count"] : 0;

// Get upcoming appointment
$sql_upcoming = "SELECT * FROM appointmenttb WHERE patient_app_acc_id = $patient_id AND appt_date >= CURDATE() ORDER BY appt_date ASC LIMIT 1";
$result_upcoming = $con->query($sql_upcoming);
$upcoming_appointment = ($result_upcoming->num_rows > 0) ? $result_upcoming->fetch_assoc() : null;
?>

<!-- Loader -->
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid pt-4 px-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <h3 class="mb-2">Welcome, <?php echo $_SESSION['name'] ?? 'Patient'; ?>!</h3>
                <p class="text-muted">Your health dashboard overview</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">My Appointments</h6>
                    <h2 class="mb-0"><?php echo $appointment_count; ?></h2>
                    <a href="../patient/appointment.php" class="text-primary mt-2">View All</a>
                </div>
                <i class="fa fa-calendar-check fa-3x text-primary ms-auto"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Prescriptions</h6>
                    <h2 class="mb-0">4</h2>
                    <a href="#" class="text-primary mt-2">View All</a>
                </div>
                <i class="fa fa-prescription fa-3x text-primary ms-auto"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Medical Records</h6>
                    <h2 class="mb-0">6</h2>
                    <a href="#" class="text-primary mt-2">View All</a>
                </div>
                <i class="fa fa-file-medical fa-3x text-primary ms-auto"></i>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <div class="d-flex flex-column">
                    <h6 class="text-muted mb-1">Bills & Payments</h6>
                    <h2 class="mb-0">$240</h2>
                    <a href="#" class="text-primary mt-2">View Details</a>
                </div>
                <i class="fa fa-receipt fa-3x text-primary ms-auto"></i>
            </div>
        </div>
    </div>

    <!-- Upcoming Appointment & Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="bg-light rounded p-4">
                <h5 class="mb-4">Upcoming Appointment</h5>
                <?php if ($upcoming_appointment): ?>
                <div class="d-flex align-items-center border-bottom pb-3">
                    <i class="fa fa-calendar fa-3x text-primary me-3"></i>
                    <div>
                        <h6 class="mb-1">Dr. <?php echo $upcoming_appointment['doctor_name']; ?></h6>
                        <small class="text-muted">Date: <?php echo date('d M Y', strtotime($upcoming_appointment['appt_date'])); ?></small><br>
                        <small class="text-muted">Time: <?php echo $upcoming_appointment['appt_time']; ?></small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary ms-auto">Reschedule</button>
                </div>
                <?php else: ?>
                <p class="text-muted">No upcoming appointments</p>
                <a href="book-appointment.php" class="btn btn-primary">Book New Appointment</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="bg-light rounded p-4">
                <h5 class="mb-4">Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="../patient/add.php" class="btn btn-primary"><i class="fa fa-plus me-2"></i>New Appointment</a>
                    <a href="../patient/notes.php" class="btn btn-outline-primary"><i class="fa fa-notes-medical me-2"></i>Doctor's Notes</a>
                    <a href="#" class="btn btn-outline-primary"><i class="fa fa-file-upload me-2"></i>Upload Documents</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <h5 class="mb-4">Recent Activity</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Activity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>June 5, 2025</td>
                                <td>Appointment with Dr. Smith</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>June 3, 2025</td>
                                <td>Lab Test Results</td>
                                <td><span class="badge bg-info">Available</span></td>
                            </tr>
                            <tr>
                                <td>June 1, 2025</td>
                                <td>Prescription Renewal</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/script.php"; ?>