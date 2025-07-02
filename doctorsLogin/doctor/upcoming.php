<?php
include "../includes/header.php";
include "../includes/sidebar-doctor.php";
require_once "../connection/globalConnection.php";


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$con = connection();

// Initialize variables for modal and toast
$show_modal = false;
$showToast = false;
$toastMessage = '';
$isSuccess = false;
$appointment_details = null;

// Get doctor's ID from session
$doctor_id = $_SESSION['user_id'];

// Fetch today's appointments count
$today_sql = "SELECT COUNT(*) as today_count FROM appointmenttb 
              WHERE doctor_app_acc_id = ? 
              AND appt_date = CURDATE() 
              AND status = 'Approved'";
$stmt = $con->prepare($today_sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$today_count = $stmt->get_result()->fetch_assoc()['today_count'];

// Fetch this week's appointments count
$week_sql = "SELECT COUNT(*) as week_count FROM appointmenttb 
             WHERE doctor_app_acc_id = ? 
             AND WEEK(appt_date) = WEEK(CURDATE()) 
             AND status = 'Approved'";
$stmt = $con->prepare($week_sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$week_count = $stmt->get_result()->fetch_assoc()['week_count'];

// Handle appointment status update
if (isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    $sql = "UPDATE appointmenttb SET status = ? WHERE appt_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $status, $appointment_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
        exit;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error updating appointment'
        ]);
        exit;
    }
}

// Handle view details request
if (isset($_GET['edit'])) {
    $appointment_id = $_GET['edit'];
    $show_modal = true;

    // Fetch appointment details
    $sql = "SELECT a.*, 
        CONCAT(COALESCE(p.First_Name, ''), ' ', COALESCE(p.Last_Name, '')) as patient_name,
        p.Profile_img as patient_img,
        p.Phone_Number as patient_phone,
        p.Email_address as patient_email,
        p.Address as patient_address,
        CONCAT(COALESCE(d.First_Name, ''), ' ', COALESCE(d.Last_Name, '')) as doctor_name,
        d.Profile_img as doctor_profile_img,
        d.Phone_Number as doctor_phone,
        d.Email_address as doctor_email,
        COALESCE(d.Specialization, 'General') as Specialization
        FROM appointmenttb a 
        LEFT JOIN patienttb p ON a.patient_app_acc_id = p.patient_acc_id 
        LEFT JOIN doctortb d ON a.doctor_app_acc_id = d.doctor_acc_id 
        WHERE a.appt_id = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $appointment_details = $result->fetch_assoc();
        // Clean up data
        $appointment_details['patient_name'] = !empty(trim($appointment_details["patient_name"])) ? trim($appointment_details["patient_name"]) : 'Unknown Patient';
        $appointment_details['doctor_name'] = !empty(trim($appointment_details["doctor_name"])) ? trim($appointment_details["doctor_name"]) : 'Unknown Doctor';
        $appointment_details['Specialization'] = !empty($appointment_details["Specialization"]) ? $appointment_details["Specialization"] : 'General';
        $appointment_details['doctor_hospital'] = !empty($appointment_details["doctor_hospital"]) ? $appointment_details["doctor_hospital"] : 'N/A';
    }

    $health_report = null;
if (!empty($appointment_details['patient_app_acc_id'])) {
    $stmt = $con->prepare("SELECT * FROM health_recordstb WHERE patient_id = ? ORDER BY record_date DESC LIMIT 1");
    $stmt->bind_param("i", $appointment_details['patient_app_acc_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $health_report = $result->fetch_assoc();
    }
}
}
// Handle mark as approved request
if (isset($_POST['markApprove']) && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    if ($appointment_id) {
        $status = 'Approved';
        $sql = "UPDATE appointmenttb SET status = ? WHERE appt_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $status, $appointment_id);

        if ($stmt->execute()) {
            $showToast = true;
            $toastMessage = 'Appointment approved successfully!';
            $isSuccess = true;
        } else {
            $toastMessage = 'Error approving appointment.';
            $isSuccess = false;
            $showToast = true;
        }
        $show_modal = false;
    }
}

// Handle mark as completed request


if (isset($_POST['markCompleted']) && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];

    if ($appointment_id) {
        $status = 'Completed';
        $sql = "UPDATE appointmenttb SET status = ? WHERE appt_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $status, $appointment_id);

        if ($stmt->execute()) {
            $showToast = true;
            $toastMessage = 'Appointment completed successfully!';
            $isSuccess = true;
        } else {
            $toastMessage = 'Error completing appointment.';
            $isSuccess = false;
            $showToast = true;
        }
        $show_modal = false;
    }
}

// Fetch upcoming appointments for the main table
$sql = "SELECT * FROM appointmenttb WHERE status='Pending' AND appt_date >= CURDATE() ORDER BY appt_date ASC, appt_time ASC";
$result = mysqli_query($con, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

// Add this before your HTML output to provide events as JSON if requested
if (isset($_GET['calendar_events'])) {
    $events = [];
    $sql = "SELECT a.appt_id, a.appt_date, a.appt_time, a.status, 
                   CONCAT(COALESCE(p.First_Name, ''), ' ', COALESCE(p.Last_Name, '')) AS patient_name
            FROM appointmenttb a
            JOIN patienttb p ON a.patient_app_acc_id = p.patient_acc_id
            WHERE a.doctor_app_acc_id = ?
              AND a.appt_date >= CURDATE()
              AND a.status IN ('Pending', 'Approved') -- include Completed if you want
            ORDER BY a.appt_date, a.appt_time";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $patient_name = trim($row['patient_name']) ?: 'Patient';
        $status = ucfirst(strtolower($row['status']));
        $events[] = [
            'id' => $row['appt_id'],
            'title' => $patient_name . " (" . $status . ")", // Patient name in event title
            'start' => $row['appt_date'] . 'T' . $row['appt_time'],
            'color' => $status === 'Approved' ? '#ffc107' : ($status === 'Completed' ? '#198754' : '#0d6efd')
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($events);
    exit;
}
?>
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-2">Upcoming Appointments</h3>
                        <p class="text-muted mb-0">Manage your upcoming patient consultations</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm rounded-0" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print Schedule
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Today's Appointments -->
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <div class="d-flex flex-column">
                    <span class="text-muted small mb-1">Today's Appointments</span>
                    <h2 class="mb-2"><?php echo $today_count; ?></h2>
                    <span class="small text-<?php echo $today_count > 0 ? 'success' : 'warning'; ?>">
                        <i class="fas fa-<?php echo $today_count > 0 ? 'calendar-check' : 'calendar-xmark'; ?> me-2"></i>
                        <?php echo $today_count > 0 ? 'Appointments scheduled' : 'No appointments'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- This Week's Appointments -->
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <div class="d-flex flex-column">
                    <span class="text-muted small mb-1">This Week</span>
                    <h2 class="mb-2"><?php echo $week_count; ?></h2>
                    <span class="small text-primary">
                        <i class="fas fa-calendar-week me-2"></i>
                        Upcoming this week
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Calendar View -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-light rounded p-4">
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#today">Today</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#upcoming">Upcoming</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Today's Appointments Tab -->
                    <div class="tab-pane fade show active" id="today">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="myTable">
                                <thead>
                                    <tr>
                                        <th>Appointment ID</th>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch today's appointments
                                    $today_appointments_sql = "SELECT a.*, p.*, CONCAT(COALESCE(p.First_Name, ''), ' ', COALESCE(p.Last_Name, '')) AS patient_name 
                                        FROM appointmenttb a 
                                        JOIN patienttb p ON a.patient_app_acc_id = p.patient_acc_id 
                                        WHERE a.doctor_app_acc_id = ?
                                        AND a.status = 'Approved' 
                                        AND a.appt_date = CURDATE() 
                                        ORDER BY a.appt_time ASC";
                                    $stmt = $con->prepare($today_appointments_sql);
                                    $stmt->bind_param("i", $doctor_id);
                                    $stmt->execute();
                                    $today_appointments = $stmt->get_result();
                                    $hasData = false;

                                    if ($today_appointments->num_rows > 0) {
                                        $hasData = true;
                                        while ($appointment = $today_appointments->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td># <?php echo $appointment['appt_id'] ?></td>
                                                <td><?php echo date('h:i A', strtotime($appointment['appt_time'])); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo !empty($appointment['Profile_img']) ? '../images/' . $appointment['Profile_img'] : '../images/placeholder.jpg'; ?>"
                                                            class="rounded-circle me-2"
                                                            width="40" height="40">
                                                        <div>
                                                            <h6 class="mb-0"><?php echo $appointment['patient_name']; ?></h6>
                                                            <small class="text-muted"><?php echo $appointment['Email_address']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $appointment['Phone_Number']; ?></td>
                                                <td>
                                                    <span class="badge bg-warning"><?php echo $appointment['status']; ?></span>
                                                </td>
                                                <td>
                                                    <div class="float-end">
                                                        <button type="button" class="btn btn-sm btn-light me-2" onclick="viewDetails(<?php echo $appointment['appt_id']; ?>)">
                                                            <i class="fas fa-eye text-primary"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-light" onclick="markAsComplete(<?php echo $appointment['appt_id']; ?>)">
                                                            <i class="fas fa-check text-success"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                    <?php }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!-- No Data Message (Hidden by default, shown when table is empty) -->
                            <div id="noDataMessage" class="text-center py-5" style="display: none;">
                                <i class="fas fa-calendar fa-2x text-secondary mb-3"></i>
                                <h6 class="text-muted">No upcoming appointments found</h6>
                                <p class="text-muted small mb-0">New appointments will appear here when scheduled.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Appointments Tab -->
                    <div class="tab-pane fade" id="upcoming">
                        <!-- Calendar view here -->
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for View Details -->
    <div class="modal fade <?php echo $show_modal ? 'show' : ''; ?>" id="viewDetailsModal" tabindex="-1"
        aria-labelledby="viewDetailsModalLabel" aria-hidden="true"
        style="<?php echo $show_modal ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content border-0 rounded-0 d-flex flex-column" style="height: 100vh;">
                <div class="modal-header bg-light border-0 flex-shrink-0">
                    <h5 class="modal-title" id="viewDetailsModalLabel">
                        <i class="fas fa-calendar-check me-2"></i>Appointment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" class="d-flex flex-column h-100">
                    <div class="modal-body p-4 flex-grow-1 overflow-auto">
                        <?php if ($appointment_details): ?>
                        <div class="row g-4">
                            <!-- Left Column: Appointment & Patient/Doctor Info -->
                            <div class="col-md-6">
                                <!-- Patient & Doctor Info Cards -->
                                <div class="row g-4 mb-4">
                                    <!-- Patient Card -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 bg-light">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <img src="<?= !empty($appointment_details['patient_img']) ? '../images/' . htmlspecialchars($appointment_details['patient_img']) : '../images/team_placeholder.jpg' ?>"
                                                        class="rounded-circle border"
                                                        width="60" height="60"
                                                        style="object-fit: cover;"
                                                        alt="Patient Image">
                                                    <div class="ms-3">
                                                        <h6 class="mb-1">Patient Information</h6>
                                                        <span class="text-muted small">Patient Details</span>
                                                    </div>
                                                </div>
                                                <div class="list-group list-group-flush">
                                                    <div class="list-group-item bg-transparent px-0">
                                                        <small class="text-muted d-block">Full Name</small>
                                                        <span><?= htmlspecialchars($appointment_details['patient_name']) ?></span>
                                                    </div>
                                                    <div class="list-group-item bg-transparent px-0">
                                                        <small class="text-muted d-block">Phone</small>
                                                        <span><?= htmlspecialchars($appointment_details['patient_phone'] ?? 'N/A') ?></span>
                                                    </div>
                                                    <div class="list-group-item bg-transparent px-0">
                                                        <small class="text-muted d-block">Email</small>
                                                        <span><?= htmlspecialchars($appointment_details['patient_email'] ?? 'N/A') ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Doctor Card -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 bg-light">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <img src="<?= !empty($appointment_details['doctor_profile_img']) ? '../images/' . htmlspecialchars($appointment_details['doctor_profile_img']) : '../images/team_placeholder.jpg' ?>"
                                                        class="rounded-circle border"
                                                        width="60" height="60"
                                                        style="object-fit: cover;"
                                                        alt="Doctor Image">
                                                    <div class="ms-3">
                                                        <h6 class="mb-1">Doctor Information</h6>
                                                        <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($appointment_details['Specialization']) ?></span>
                                                    </div>
                                                </div>
                                                <div class="list-group list-group-flush">
                                                    <div class="list-group-item bg-transparent px-0">
                                                        <small class="text-muted d-block">Doctor Name</small>
                                                        <span>Dr. <?= htmlspecialchars($appointment_details['doctor_name']) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Appointment Details Card -->
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-4">
                                            <i class="fas fa-info-circle me-2"></i>Appointment Details
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Appointment ID</small>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-hashtag text-primary me-2"></i>
                                                        <span><?= htmlspecialchars($appointment_details['appt_id']) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Status</small>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-info-circle text-warning me-2"></i>
                                                        <span class="badge bg-warning text-white"><?= htmlspecialchars($appointment_details['status']) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Date</small>
                                                    <div class="d-flex align-items-center">
                                                        <i class="far fa-calendar text-primary me-2"></i>
                                                        <span><?= date('F d, Y', strtotime($appointment_details['appt_date'])) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Time</small>
                                                    <div class="d-flex align-items-center">
                                                        <i class="far fa-clock text-primary me-2"></i>
                                                        <span><?= date('h:i A', strtotime($appointment_details['appt_time'])) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Reason for Visit</small>
                                                    <p class="mb-0"><?= htmlspecialchars($appointment_details['notes'] ?? 'No reason provided') ?></p>
                                                </div>
                                            </div>
                                            <?php if (!empty($appointment_details['symptoms'])): ?>
                                                <div class="col-12">
                                                    <div class="p-3 bg-white rounded">
                                                        <small class="text-muted d-block">Symptoms</small>
                                                        <p class="mb-0"><?= htmlspecialchars($appointment_details['symptoms']) ?></p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Health Report -->
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title mb-4">
                                            <i class="fas fa-notes-medical me-2"></i>Health Report
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Blood Pressure</small>
                                                    <input type="text" class="form-control" name="blood_pressure">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Heart Rate</small>
                                                    <input type="text" class="form-control" name="heart_rate">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Temperature</small>
                                                    <input type="text" class="form-control" name="temperature">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Respiratory Rate</small>
                                                    <input type="text" class="form-control" name="respiratory_rate">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Weight (kg)</small>
                                                    <input type="text" class="form-control" name="weight">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Height (cm)</small>
                                                    <input type="text" class="form-control" name="height">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">BMI</small>
                                                    <input type="text" class="form-control" name="bmi">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Notes</small>
                                                    <textarea class="form-control" name="notes" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="p-3 bg-white rounded">
                                                    <small class="text-muted d-block">Record Date</small>
                                                    <input type="date" class="form-control" name="record_date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                                <h6>Appointment not found</h6>
                                <p class="text-muted">The requested appointment details could not be loaded.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer border-0 bg-white flex-shrink-0 border-top" style="position: sticky; bottom: 0; z-index: 1050;">
                        <div class="d-flex align-items-center w-100">
                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_details['appt_id']) ?>">
                            <button type="submit" name="markApprove" class="btn btn-primary rounded-0 me-3 btn-sm">
                                <i class="fas fa-check me-2"></i> Mark as Approve
                            </button>
                            <button type="submit" name="markCompleted" class="btn btn-success rounded-0 btn-sm">
                                <i class="fas fa-check me-2"></i>Mark as Complete
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 9999;">
        <div id="loginToast" class="toast <?php echo $showToast ? 'show' : ''; ?>" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header <?php echo $isSuccess ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                <strong class="me-auto" id="toastTitle"><?php echo $isSuccess ? 'Success' : 'Error'; ?></strong>
            </div>
            <div class="toast-body" id="toastMessage">
                <?php echo $showToast ? $toastMessage : ''; ?>
            </div>
        </div>
    </div>
</div>
<?php include "../includes/script.php"; ?>
<!-- Add FullCalendar CSS/JS (add these before your custom scripts) -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<!-- Pass PHP data to JavaScript -->
<script>
    const hasData = <?= json_encode($hasData) ?>;
    const showModal = <?= json_encode($show_modal) ?>;
</script>

<!-- Add CSS styles -->
<style>
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        padding: 0.5rem 1rem;
        margin-right: 1rem;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 2px solid #0d6efd;
        background: none;
    }

    .table img.rounded-circle {
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }

    #calendar {
        min-height: 600px;
        background: #f8fafc;
        padding: 1rem;
        border-radius: 8px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
        });

        <?php if ($showToast): ?>
            toastList[0].show();
        <?php endif; ?>
    });
</script>
<script>
    // Tab persistence
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function(tabLink) {
        tabLink.addEventListener('shown.bs.tab', function(event) {
            const target = event.target.getAttribute('href');
            localStorage.setItem('activeTab', target);
        });
    });
    window.addEventListener('DOMContentLoaded', function() {
        const activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            const tabToActivate = document.querySelector(`a[href="${activeTab}"]`);
            if (tabToActivate) {
                new bootstrap.Tab(tabToActivate).show();
            }
        }
    });
</script>


<!-- Add JavaScript for DataTable and functionality -->
<script>
    $(document).ready(function() {
        // Check if table has data
        if (hasData) {
            // Initialize DataTable only if there's data
            $('#myTable').DataTable({
                "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
                "pageLength": 10,
                "ordering": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                    "targets": [5], // Actions column
                    "orderable": false,
                    "searchable": false
                }],
                "language": {
                    "search": "<i class='fa fa-search'></i>",
                    "searchPlaceholder": "Search appointments...",
                    "lengthMenu": "_MENU_ per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ appointments",
                    "infoEmpty": "No appointments found",
                    "infoFiltered": "(filtered from _MAX_ total appointments)",
                    "emptyTable": "No upcoming appointments found",
                    "zeroRecords": "No matching appointments found"
                },
                "initComplete": function() {
                    console.log('DataTable initialized successfully');
                },
                "drawCallback": function() {
                    // Reinitialize tooltips after each draw
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // Initialize tooltips for the table data
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl =>
                new bootstrap.Tooltip(tooltipTriggerEl)
            );
        } else {
            // Hide the table and show the no data message
            $('#myTable').hide();
            $('#noDataMessage').show();
            console.log('No data available - DataTable not initialized');
        }

        // Handle view details button click
        $(document).on('click', '.view-details-btn', function() {
            const appointmentId = $(this).data('appointment-id');
            viewDetails(appointmentId);
        });

        // Show modal if PHP indicates it should be shown
        if (showModal) {
            $('#viewDetailsModal').modal('show');
        }

        // Handle modal close and clean URL
        $('#viewDetailsModal').on('hidden.bs.modal', function() {
            // Clean the URL by removing the edit parameter
            const url = new URL(window.location);
            url.searchParams.delete('edit');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        });

        // Handle add appointment form submission
        $('#addAppointmentForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'upcoming.php',
                type: 'POST',
                data: $(this).serialize() + '&action=add_appointment',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Appointment added!');
                        if (calendar) {
                            calendar.refetchEvents();
                        }
                        $('#addAppointmentForm')[0].reset();
                    } else {
                        alert(response.message || 'Error adding appointment');
                    }
                }
            });
        });
    });

    function viewDetails(appointmentId) {
        // Redirect to the same page with edit parameter to show modal
        window.location.href = 'upcoming.php?edit=' + appointmentId;
    }

    function updateStatus(appointmentId, status) {
        if (confirm('Are you sure you want to mark this appointment as completed?')) {
            $.ajax({
                url: 'upcoming.php',
                type: 'POST',
                data: {
                    appointment_id: appointmentId,
                    status: status
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message and reload
                        alert('Appointment status updated successfully!');
                        location.reload();
                    } else {
                        alert(response.message || 'Error updating appointment status');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Error updating appointment status. Please try again.');
                }
            });
        }
    }

</script>
<script>
    let calendar; // Make calendar variable accessible

   function initCalendar() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl && !calendar) {
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: new Date(),
            height: 600,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: 'upcoming.php?calendar_events=1', // Loads your PHP JSON
            eventClick: function(info) {
                window.location.href = 'upcoming.php?edit=' + info.event.id;
            },
            eventDidMount: function(info) {
                var tooltip = new bootstrap.Tooltip(info.el, {
                    title: info.event.title,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
        calendar.render();
    }
}

    $(document).ready(function() {
        // ...existing DataTable and modal code...

        // Tab persistence and calendar initialization
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const target = $(e.target).attr('href');
            localStorage.setItem('activeTab', target);
            if (target === '#upcoming') {
                setTimeout(initCalendar, 100); // Delay to ensure tab is visible
            }
        });

        // On page load, if Upcoming tab is active, initialize calendar
        const activeTab = localStorage.getItem('activeTab');
        if (activeTab === '#upcoming') {
            setTimeout(initCalendar, 100);
        }
    });
</script>