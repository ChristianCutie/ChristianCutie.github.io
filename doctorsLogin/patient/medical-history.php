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

// Fetch medical history data
$sql_history = "SELECT * FROM medical_historytb WHERE patient_id = '$user_id' ORDER BY record_date DESC";
$history_result = $con->query($sql_history);
?>
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Medical History</h5>
            <p class="text-muted small mb-0">Review your complete medical records</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm rounded-0" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                <i class="fa-solid fa-plus me-2"></i>Add Record
            </button>
            <button class="btn btn-outline-primary btn-sm rounded-0" onclick="window.print()">
                <i class="fa-solid fa-print me-2"></i>Print
            </button>
        </div>
    </div>

    <!-- Timeline View -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="timeline position-relative">
                <?php if ($history_result && $history_result->num_rows > 0): 
                    while ($record = $history_result->fetch_assoc()): ?>
                    <div class="timeline-item pb-4 position-relative">
                        <div class="position-absolute top-0 start-0 p-2 bg-primary rounded-circle" 
                             style="width:12px;height:12px;transform:translateX(-50%);">
                        </div>
                        <div class="ms-4 ps-3 border-start">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0"><?= htmlspecialchars($record['condition_name']) ?></h6>
                                <span class="badge bg-primary-subtle text-primary rounded-pill">
                                    <?= date('M d, Y', strtotime($record['record_date'])) ?>
                                </span>
                            </div>
                            <p class="text-muted mb-2"><?= htmlspecialchars($record['diagnosis']) ?></p>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if (!empty($record['medications'])): ?>
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="fa-solid fa-pills me-1"></i>
                                        <?= htmlspecialchars($record['medications']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($record['treatment'])): ?>
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="fa-solid fa-stethoscope me-1"></i>
                                        <?= htmlspecialchars($record['treatment']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; 
                else: ?>
                    <div class="text-center py-5">
                           <i class="fa-solid fa-file-excel fa-2x text-secondary mb-3"></i>
                        <h6 class="text-muted">No medical records found</h6>
                        <button class="btn btn-primary btn-sm mt-3  rounded-0" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                            <i class="fa-solid fa-plus me-2"></i>Add First Record
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Record Modal -->
<div class="modal fade" id="addRecordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header border-0">
                <h5 class="modal-title">Add Medical Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="add_medical_record.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Condition/Disease</label>
                        <input type="text" class="form-control bg-light border-0" name="condition_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Diagnosis Details</label>
                        <textarea class="form-control bg-light border-0" name="diagnosis" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Medications</label>
                        <input type="text" class="form-control bg-light border-0" name="medications">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Treatment</label>
                        <input type="text" class="form-control bg-light border-0" name="treatment">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Date</label>
                        <input type="date" class="form-control bg-light border-0" name="record_date" required>
                    </div>
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-light  btn-sm rounded-0" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-0"> <i class="fa-solid fa-floppy-disk"></i> Save Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "../includes/script.php";?>

<style>
.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 2px;
    background: #dee2e6;
}

@media print {
    .sidebar, .navbar, .btn {
        display: none !important;
    }
    .timeline::before {
        display: none !important;
    }
}
</style>
