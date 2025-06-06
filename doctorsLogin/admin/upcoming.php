<?php
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";


if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

$con = connection();

// Initialize variables for modal and toast
$show_modal = false;
$showToast = false;
$toastMessage = '';
$isSuccess = false;
$appointment_details = null;

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
}
// Handle mark as approved request
if(isset($_POST['markApprove']) && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
    
    if($appointment_id) {
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


if(isset($_POST['markCompleted']) && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
    
    if($appointment_id) {
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
?>

<div class="container-fluid pt-4 px-4">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-calendar-check fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Upcoming<br>Appointments</p>
                    <h6 class="mb-0">
                        <?php
                        $total_result = $con->query("SELECT COUNT(*) as total FROM appointmenttb WHERE status='Pending' AND appt_date >= CURDATE()");
                        echo $total_result->fetch_assoc()['total'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-user-md fa-3x text-success"></i>
                <div class="ms-3">
                    <p class="mb-2">Available<br>Doctors</p>
                    <h6 class="mb-0">
                        <?php
                        $doc_result = $con->query("SELECT COUNT(*) as docs FROM doctortb WHERE Status='Active'");
                        echo $doc_result->fetch_assoc()['docs'];
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
                <h5 class="mb-0">Upcoming Appointments</h5>
                <p class="text-muted small mb-0">View and manage upcoming appointments</p>
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
                        <th>Schedule</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
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
                        COALESCE(d.Specialization, 'General') as Specialization,
                        d.Address as doctor_address
                        FROM appointmenttb a 
                        LEFT JOIN patienttb p ON a.patient_app_acc_id = p.patient_acc_id 
                        LEFT JOIN doctortb d ON a.doctor_app_acc_id = d.doctor_acc_id 
                        WHERE a.status='Pending' AND a.appt_date >= CURDATE()
                        ORDER BY a.appt_date ASC, a.appt_time ASC";

                    $result = $con->query($sql);
                    $hasData = false;

                    if ($result && $result->num_rows > 0) {
                        $hasData = true;
                        while ($row = $result->fetch_assoc()) {
                            // Ensure all required fields have values
                            $patient_name = !empty(trim($row["patient_name"])) ? trim($row["patient_name"]) : 'Unknown Patient';
                            $doctor_name = !empty(trim($row["doctor_name"])) ? trim($row["doctor_name"]) : 'Unknown Doctor';
                            $specialization = !empty($row["Specialization"]) ? $row["Specialization"] : 'General';
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
                                            <h6 class="mb-0"><?= htmlspecialchars($patient_name) ?></h6>
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
                                            <h6 class="mb-0">Dr. <?= htmlspecialchars($doctor_name) ?></h6>
                                            <span class="badge bg-primary-subtle text-primary">
                                                <?= htmlspecialchars($specialization) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div><i class="far fa-calendar text-primary me-2"></i><?= date('F d, Y', strtotime($row["appt_date"])) ?></div>
                                        <div><i class="far fa-clock text-primary me-2"></i><?= date('h:i A', strtotime($row["appt_time"])) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-white px-3 rounded-pill">
                                        <?= htmlspecialchars($row["status"]) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-sm btn-light view-details-btn"
                                            data-appointment-id="<?= $row['appt_id'] ?>"
                                            data-bs-toggle="tooltip"
                                            title="View Details">
                                            <i class="fa fa-eye text-primary"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                    <?php }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- No Data Message (Hidden by default, shown when table is empty) -->
        <div id="noDataMessage" class="text-center py-5" style="display: none;">
            <i class="fas fa-calendar fa-2x text-secondary mb-3"></i>
            <h6 class="text-muted">No upcoming appointments found</h6>
            <p class="text-muted small mb-0">New appointments will appear here when scheduled.</p>
        </div>
    </div>

    <!-- Modal for View Details -->
    <div class="modal fade <?php echo $show_modal ? 'show' : ''; ?>" id="viewDetailsModal" tabindex="-1"
        aria-labelledby="viewDetailsModalLabel" aria-hidden="true"
        style="<?php echo $show_modal ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 rounded-0">
                <!-- Modal Header -->
                <div class="modal-header bg-light border-0">
                   
                    <h5 class="modal-title" id="viewDetailsModalLabel">
                        <i class="fas fa-calendar-check me-2"></i>Appointment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <!-- Modal Body -->
                    <div class="modal-body p-4">
                        <?php if ($appointment_details): ?>
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
                                                <p class="mb-0"><?= htmlspecialchars($appointment_details['reason'] ?? 'No reason provided') ?></p>
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
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                                <h6>Appointment not found</h6>
                                <p class="text-muted">The requested appointment details could not be loaded.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                      <div class="modal-footer border-0">
                    <div class="d-flex align-items-center">
                         <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_details['appt_id'])?>" >
                        <button type="submit" name="markApprove" class="btn btn-primary rounded-0 me-3  btn-sm">
                            <i class="fas fa-check me-2"></i> Mark as Approve
                        </button>
                        <button type="submit" name="markCompleted" class="btn btn-success rounded-0  btn-sm">
                            <i class="fas fa-check me-2"></i>Mark as Complete
                        </button>
                    </div>
                </div>
                </form>
                <!-- Modal Footer -->
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
<?php include "../includes/script.php";?>

<!-- Pass PHP data to JavaScript -->
<script>    
    const hasData = <?= json_encode($hasData) ?>;
    const showModal = <?= json_encode($show_modal) ?>;
</script>

<!-- Add CSS styles -->
<style>
    .badge.bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .table img.rounded-circle.border {
        border: 2px solid #fff !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-light:hover {
        background-color: #e9ecef;
    }

    .modal.show {
        display: block !important;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
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