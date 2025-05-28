<?php
include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";


if (session_status()  == PHP_SESSION_NONE) {
    session_start();
}

$showToast = isset($_SESSION['toast']['show']) ? $_SESSION['toast']['show'] : false;
$toastMessage = isset($_SESSION['toast']['message']) ? $_SESSION['toast']['message'] : '';
$isSuccess = isset($_SESSION['toast']['success']) ? $_SESSION['toast']['success'] : false;

// Clear the toast session data after retrieving
unset($_SESSION['toast']);
$con = connection();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

$sql = "SELECT * FROM appointmenttb WHERE patient_app_acc_id = '{$_SESSION['user_id']}'";
$result = $con->query($sql);
if ($result === false) {
    die("Error fetching appointments: " . htmlspecialchars($con->error));
} else {
    $user_name = $result->fetch_assoc();
}
if ($result->num_rows === 0) {
    echo "<div class='alert alert-info'>No appointments found.</div>";
    exit();
}

// Fetching the count of pending appointments
$sql_pending = "SELECT COUNT(*) as pending_count FROM appointmenttb WHERE patient_app_acc_id = '{$_SESSION['user_id']}' AND status = 'Pending'";
$pending_result = $con->query($sql_pending);

if ($pending_result === false) {
    die("Error fetching pending appointments: " . htmlspecialchars($con->error));
} else {
    $pending_count = $pending_result->fetch_assoc()['pending_count'];
}

// Fetching the count of completed appointments
$sql_completed = "SELECT COUNT(*) as completed_count FROM appointmenttb WHERE patient_app_acc_id = '{$_SESSION['user_id']}' AND status = 'Approved'";
$completed_result = $con->query($sql_completed);
if ($completed_result === false) {
    die("Error fetching completed appointments: " . htmlspecialchars($con->error));
} else {
    $completed_count = $completed_result->fetch_assoc()['completed_count'];
}

// Fetching the count of cancelled appointments
$sql_cancelled = "SELECT COUNT(*) as cancelled_count FROM appointmenttb WHERE patient_app_acc_id = '{$_SESSION['user_id']}' AND status = 'Cancelled'";
$cancelled_result = $con->query($sql_cancelled);
if ($cancelled_result === false) {
    die("Error fetching cancelled appointments: " . htmlspecialchars($con->error));
} else {
    $cancelled_count = $cancelled_result->fetch_assoc()['cancelled_count'];
}

?>
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <!-- Header Section with Statistics -->
    <div class="row mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-calendar-check fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Appointments</p>
                    <h6 class="mb-0"><?= $result->num_rows ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-xmark fa-3x text-danger"></i>
                <div class="ms-3">
                    <p class="mb-2">Cancelled Appointments</p>
                    <h6 class="mb-0"><?= htmlspecialchars($cancelled_count) ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-clock fa-3x text-warning"></i>
                <div class="ms-3">
                    <p class="mb-2">Pending Appointments</p>
                    <h6 class="mb-0"><?= htmlspecialchars($pending_count) ?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-check fa-3x text-success"></i>
                <div class="ms-3">
                    <p class="mb-2">Completed Appointments</p>
                    <h6 class="mb-0"><?= htmlspecialchars($completed_count) ?></h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment List Section -->
    <div class="bg-light rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">Appointment List</h5>
                <p class="text-muted small mb-0">Manage your medical appointments</p>
            </div>
            <a href="../patient/add.php" class="btn btn-primary btn-sm rounded-0">
                <i class="fa fa-plus me-2"></i>New Appointment
            </a>
        </div>

        <div class="table-responsive">
            <table id="myTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Appt ID</th>
                        <th>Doctor</th>
                        <th>Type</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM appointmenttb WHERE patient_app_acc_id = '{$_SESSION['user_id']}'";
                    $result = $con->query($sql);
                    if ($result->num_rows > 0) {
                        while ($rows = $result->fetch_assoc()) {
                            $statusClass = match ($rows["status"]) {
                                "Pending" => ["bg" => "warning", "icon" => "clock"],
                                "Approved" => ["bg" => "success", "icon" => "check-circle"],
                                "Cancelled" => ["bg" => "danger", "icon" => "times-circle"],
                                default => ["bg" => "secondary", "icon" => "circle"]
                            };
                    ?>
                            <tr>
                                <td class="align-middle">
                                    <span class="fw-bold">#<?= htmlspecialchars($rows["appt_id"]) ?></span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative">
                                            <img src="../images/team_placeholder.jpg" class="rounded-circle" width="40" height="40">
                                            <span class="position-absolute bottom-0 end-0 bg-success rounded-circle p-1"
                                                style="width:10px;height:10px;"></span>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0"><?= htmlspecialchars($rows["doctor_name"]) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($rows["appt_type"]) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle"><?= htmlspecialchars($rows["appt_type"]) ?></td>
                                <td class="align-middle">
                                    <i class="fa fa-calendar-day text-primary me-2"></i>
                                    <?= date('M d, Y', strtotime($rows["appt_date"])) ?>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-<?= $statusClass["icon"] ?> text-<?= $statusClass["bg"] ?> me-2"></i>
                                        <span class="badge bg-<?= $statusClass["bg"] ?>-subtle text-<?= $statusClass["bg"] ?> rounded-pill px-3">
                                            <?= htmlspecialchars($rows["status"]) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex justify-content-end gap-2">
                                        <?php if ($rows["status"] === "Pending"): ?>
                                            <button class="btn btn-link btn-sm text-primary p-0"
                                                data-bs-toggle="tooltip"
                                                title="Edit"
                                                onclick="openEditModal(<?= $rows['appt_id'] ?>, '<?= htmlspecialchars($rows['doctor_name']) ?>', '<?= htmlspecialchars($rows['appt_type']) ?>', '<?= date('Y-m-d', strtotime($rows['appt_date'])) ?>', '<?= htmlspecialchars($rows['notes']) ?>')">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-link btn-sm text-danger p-0"
                                                onclick="deleteAppointment(<?= $rows['appt_id'] ?>)"
                                                data-bs-toggle="tooltip"
                                                title="Cancel">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="../images/no-data.svg" alt="No Data" style="width: 120px;" class="mb-3">
                                <h6 class="text-muted">No appointments found</h6>
                                <a href="../patient/add.php" class="btn btn-primary btn-sm mt-3">
                                    <i class="fa fa-plus me-2"></i>Schedule New Appointment
                                </a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Appointment Modal -->
    <div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-labelledby="editAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="editAppointmentModalLabel">Edit Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editAppointmentForm" action="" method="post">
                        <input type="hidden" name="appointment_id" id="edit_appointment_id">

                        <!-- Doctor Info -->
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <img src="../images/team_placeholder.jpg" class="rounded-circle" width="50" height="50">
                            <div class="ms-3">
                                <h6 class="mb-1" id="doctor_name_display"></h6>
                                <span class="badge bg-primary-subtle text-primary" id="appointment_type_display"></span>
                            </div>
                        </div>

                        <!-- Appointment Date -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Appointment Date</label>
                            <input type="date" class="form-control bg-light border-0"
                                name="appointment_date" id="edit_appointment_date"
                                min="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Notes</label>
                            <textarea class="form-control bg-light border-0"
                                name="notes" id="edit_notes"
                                rows="4" required></textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-0">
                                <i class="fa-solid fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Toast Notification -->

    <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 9999;">
        <div id="appointmentToast" class="toast <?= $showToast ? 'show' : ''; ?>" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header <?= $isSuccess ? 'bg-success' : 'bg-danger'; ?> text-white">
                <strong class="me-auto"><?= $isSuccess ? 'Success' : 'Error'; ?></strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?= $showToast ? $toastMessage : ''; ?>
            </div>
        </div>
    </div>
</div>

<?php

include "../includes/script.php";

?>
<script>
    $(document).ready(function() {
        // Initialize DataTable with custom options
        $('#myTable').DataTable({
            "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
            "pageLength": 7,
            "ordering": true,
            "autoWidth": false,
            "responsive": false,
            "language": {
                "search": "<i class='fa fa-search'></i>",
                "searchPlaceholder": "Search appointments...",
                "lengthMenu": "_MENU_ per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ appointments",
                "infoEmpty": "No appointments found",
                "infoFiltered": "(filtered from _MAX_ total appointments)"
            }
        });

        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });

    function openEditModal(apptId, doctorName, apptType, apptDate, notes) {
        // Set the values in the modal fields
        document.getElementById('edit_appointment_id').value = apptId;
        document.getElementById('doctor_name_display').innerText = doctorName;
        document.getElementById('appointment_type_display').innerText = apptType;
        document.getElementById('edit_appointment_date').value = apptDate;
        document.getElementById('edit_notes').value = notes;

        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('editAppointmentModal'));
        myModal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize toast
        var toastElList = [].slice.call(document.querySelectorAll('.toast'));
        var toastList = toastElList.map(function(toastEl) {
            return new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
            });
        });

        // Show toast if needed
        <?php if ($showToast): ?>
            toastList[0].show();
        <?php endif; ?>
    });
</script>