<?php
include "../includes/header.php";
include "../includes/sidebar-admin.php";
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

// Handle reschedule request
if (isset($_POST['rescheduleAppointment'])) {
    $appointment_id = $_POST['appointment_id'] ?? null;
    $new_date = $_POST['new_date'];
    $new_time = $_POST['new_time'];

    $sql = "UPDATE appointmenttb SET appt_date = ?, appt_time = ?, status = 'Rescheduled' WHERE appt_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssi", $new_date, $new_time, $appointment_id);

    if ($stmt->execute()) {
        $showToast = true;
        $toastMessage = 'Appointment rescheduled successfully!';
        $isSuccess = true;
    } else {
        $showToast = true;
        $toastMessage = 'Error rescheduling appointment.';
        $isSuccess = false;
    }
    $show_modal = false;
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
        CONCAT(COALESCE(d.First_Name, ''), ' ', COALESCE(d.Last_Name, '')) as doctor_name,
        d.Profile_img as doctor_profile_img,
        d.Specialization as Specialization
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
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-calendar-alt fa-3x text-warning"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Rescheduled<br>Appointments</p>
                    <h6 class="mb-0">
                        <?php
                        $total_result = $con->query("SELECT COUNT(*) as total FROM appointmenttb WHERE status='Rescheduled'");
                        echo $total_result->fetch_assoc()['total'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments List Section -->
    <div class="bg-light rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">Rescheduled Appointments</h5>
                <p class="text-muted small mb-0">Manage rescheduled appointments</p>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table id="myTable" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Original Schedule</th>
                        <th>New Schedule</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT a.*, 
                        CONCAT(COALESCE(p.First_Name, ''), ' ', COALESCE(p.Last_Name, '')) as patient_name,
                        p.Profile_img as patient_img,
                        CONCAT(COALESCE(d.First_Name, ''), ' ', COALESCE(d.Last_Name, '')) as doctor_name,
                        d.Profile_img as doctor_profile_img,
                        d.Specialization
                        FROM appointmenttb a 
                        LEFT JOIN patienttb p ON a.patient_app_acc_id = p.patient_acc_id 
                        LEFT JOIN doctortb d ON a.doctor_app_acc_id = d.doctor_acc_id 
                        WHERE a.status='Rescheduled'
                        ORDER BY a.appt_date ASC, a.appt_time ASC";

                    $result = $con->query($sql);
                    $hasData = false;

                    if ($result && $result->num_rows > 0) {
                        $hasData = true;
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><span class="fw-bold">#<?= htmlspecialchars($row["appt_id"]) ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= !empty($row['patient_img']) ? '../images/' . htmlspecialchars($row['patient_img']) : '../images/team_placeholder.jpg' ?>"
                                            class="rounded-circle border"
                                            width="40" height="40"
                                            style="object-fit: cover;"
                                            alt="Patient Image">
                                        <div class="ms-3">
                                            <h6 class="mb-0"><?= htmlspecialchars($row["patient_name"]) ?></h6>
                                            <small class="text-muted">Patient</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= !empty($row['doctor_profile_img']) ? '../images/' . htmlspecialchars($row['doctor_profile_img']) : '../images/team_placeholder.jpg' ?>"
                                            class="rounded-circle border"
                                            width="40" height="40"
                                            style="object-fit: cover;"
                                            alt="Doctor Image">
                                        <div class="ms-3">
                                            <h6 class="mb-0">Dr. <?= htmlspecialchars($row["doctor_name"]) ?></h6>
                                            <span class="badge bg-primary-subtle text-primary">
                                                <?= htmlspecialchars($row["Specialization"]) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        <i class="far fa-calendar text-warning me-2"></i><?= date('F d, Y', strtotime($row["appt_date"])) ?><br>
                                        <i class="far fa-clock text-warning me-2"></i><?= date('h:i A', strtotime($row["appt_time"])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="far fa-calendar-check text-success me-2"></i><?= date('F d, Y', strtotime($row["new_date"] ?? $row["appt_date"])) ?><br>
                                        <i class="far fa-clock text-success me-2"></i><?= date('h:i A', strtotime($row["new_time"] ?? $row["appt_time"])) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark px-3 rounded-pill">
                                        <?= htmlspecialchars($row["status"]) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light view-details-btn"
                                        data-appointment-id="<?= $row['appt_id'] ?>"
                                        data-bs-toggle="tooltip"
                                        title="View Details">
                                        <i class="fa fa-eye text-primary"></i>
                                    </button>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- No Data Message -->
        <div id="noDataMessage" class="text-center py-5" style="display: none;">
            <i class="fas fa-calendar-times fa-2x text-secondary mb-3"></i>
            <h6 class="text-muted">No rescheduled appointments found</h6>
            <p class="text-muted small mb-0">Rescheduled appointments will appear here.</p>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div class="modal fade <?php echo $show_modal ? 'show' : ''; ?>" id="viewDetailsModal" tabindex="-1"
        style="<?php echo $show_modal ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt me-2"></i>Reschedule Appointment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body p-4">
                        <?php if ($appointment_details): ?>
                            <!-- Appointment Details -->
                            <div class="row g-4">
                                <!-- Current Schedule -->
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Current Schedule</h6>
                                            <div class="list-group list-group-flush">
                                                <div class="list-group-item bg-transparent px-0">
                                                    <small class="text-muted d-block">Date</small>
                                                    <span><?= date('F d, Y', strtotime($appointment_details['appt_date'])) ?></span>
                                                </div>
                                                <div class="list-group-item bg-transparent px-0">
                                                    <small class="text-muted d-block">Time</small>
                                                    <span><?= date('h:i A', strtotime($appointment_details['appt_time'])) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- New Schedule -->
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">New Schedule</h6>
                                            <div class="mb-3">
                                                <label class="form-label">New Date</label>
                                                <input type="date" class="form-control" name="new_date" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">New Time</label>
                                                <input type="time" class="form-control" name="new_time" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_details['appt_id']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer border-0">
                         <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_details['appt_id'])?>" >
                        <button type="submit" name="rescheduleAppointment" class="btn btn-primary btn-sm rounded-0">
                            <i class="fas fa-calendar-check me-2"></i>Confirm Reschedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 9999;">
        <div id="loginToast" class="toast <?php echo $showToast ? 'show' : ''; ?>" role="alert" aria-atomic="true">
            <div class="toast-header <?php echo $isSuccess ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                <strong class="me-auto"><?php echo $isSuccess ? 'Success' : 'Error'; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <?php echo $showToast ? $toastMessage : ''; ?>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/script.php"; ?>

<!-- Pass PHP data to JavaScript -->
<script>
    const hasData = <?= json_encode($hasData) ?>;
    const showModal = <?= json_encode($show_modal) ?>;
</script>
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
    });

    function viewDetails(appointmentId) {
        // Redirect to the same page with edit parameter to show modal
        window.location.href = 'reschedule.php?edit=' + appointmentId;
    }

    function updateStatus(appointmentId, status) {
        if (confirm('Are you sure you want to mark this appointment as completed?')) {
            $.ajax({
                url: 'reschedule.php',
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
<style>
    .badge.bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .table img.rounded-circle.border {
        border: 2px solid #fff !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .modal-content {
        border-radius: 0;
    }

    .form-control {
        border-radius: 0;
    }
</style>