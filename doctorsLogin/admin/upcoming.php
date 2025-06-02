<?php
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";

if (session_status()  == PHP_SESSION_NONE) {
    session_start();
}

$showToast = false;
$toastMessage = '';
$isSuccess = true;
$con = connection();

if (isset($_POST['update_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    $sql = "UPDATE appointmenttb SET 
            appt_date = ?, 
            appt_time = ?, 
            status = ?, 
            notes = ? 
            WHERE appt_id = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssi", $appointment_date, $appointment_time, $status, $notes, $appointment_id);

    if ($stmt->execute()) {
        $showToast = true;
        $toastMessage = "Appointment updated successfully";
        $isSuccess = true;
    } else {
        $showToast = true;
        $toastMessage = "Error updating appointment";
        $isSuccess = false;
    }
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $sql = "SELECT a.*, 
            CONCAT(p.First_Name, ' ', p.Last_Name) as patient_name,
            CONCAT(d.First_Name, ' ', d.Last_Name) as doctor_name,
            d.Profile_img as profile_photo
            FROM appointmenttb a
            LEFT JOIN patienttb p ON a.patient_app_acc_id = p.patient_acc_id
            LEFT JOIN doctortb d ON a.doctor_app_acc_id = d.doctor_acc_id
            WHERE a.appt_id = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data);
}



?>
<style>
    .modal .rounded-circle.border {
        border: 2px solid #fff !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .position-relative {
        display: inline-block;
    }

    #edit_profile_photo {
        background-color: #f8f9fa;
    }

    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }

    .badge {
        font-weight: 500;
    }

    .table> :not(caption)>*>* {
        padding: 1rem 0.75rem;
    }

    .btn-light:hover {
        background-color: #e9ecef;
    }

    @media print {

        .sidebar,
        .navbar,
        .btn,
        .dataTables_filter,
        .dataTables_length,
        .dataTables_paginate {
            display: none !important;
        }
    }
</style>
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
                <p class="text-muted small mb-0">Manage scheduled appointments</p>
            </div>
        </div>

        <div class="table-responsive">
            <table id="myTable" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Patient Info</th>
                        <th>Doctor</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT a.*, 
                    p.First_Name as patient_fname, 
                    p.Last_Name as patient_lname, 
                    p.Profile_img as patient_img, 
                    d.First_Name as doctor_fname, 
                    d.Last_Name as doctor_lname, 
                    d.Specialization,
                    d.Profile_img as doctor_profile_img
                    FROM appointmenttb a 
                    LEFT JOIN patienttb p ON a.patient_app_acc_id = p.patient_acc_id 
                    LEFT JOIN doctortb d ON a.doctor_app_acc_id = d.doctor_acc_id 
                    WHERE a.status='Pending' AND a.appt_date >= CURDATE() 
                    ORDER BY a.appt_date ASC, a.appt_time ASC";
                    $result = $con->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <tr>
                                <td>
                                    <span class="fw-bold">#<?= htmlspecialchars($row["appt_id"]) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative">
                                            <?php
                                            $profile_image = !empty($row['patient_img']) && file_exists('../images/' . $row['patient_img'])
                                                ? '../images/' . $row['patient_img']
                                                : '../images/team_placeholder.jpg';
                                            ?>
                                            <img src="<?= htmlspecialchars($profile_image) ?>"
                                                class="rounded-circle"
                                                width="40" height="40"
                                                style="object-fit: cover;">
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0">
                                                <?= htmlspecialchars($row["patient_fname"] . " " . $row["patient_lname"]) ?>
                                            </h6>
                                            <small class="text-muted">Patient</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">Dr. <?= htmlspecialchars($row["doctor_fname"] . " " . $row["doctor_lname"]) ?></h6>
                                        <span class="badge bg-primary-subtle text-primary">
                                            <?= htmlspecialchars($row["Specialization"]) ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div><i class="fa fa-calendar text-primary me-2"></i><?= date('F d, Y', strtotime($row["appt_date"])) ?></div>
                                        <div><i class="fa fa-clock text-primary me-2"></i><?= date('h:i A', strtotime($row["appt_time"])) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success px-3 rounded-pill">
                                        <?= htmlspecialchars($row["status"]) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-sm btn-light"
                                            onclick="openEditModal(
                                                '<?= $row['appt_id'] ?>', 
                                                'Dr. <?= htmlspecialchars($row['doctor_fname'] . " " . $row['doctor_lname']) ?>', 
                                                '<?= htmlspecialchars($row["Specialization"]) ?>', 
                                                '<?= $row["appt_date"] ?>', 
                                                '<?= $row["appt_time"] ?>', 
                                                '<?= htmlspecialchars($row["status"]) ?>', 
                                                '<?= !empty($row['doctor_profile_img']) ? "../images/" . htmlspecialchars($row['doctor_profile_img']) : "../images/team_placeholder.jpg" ?>'
                                            )"
                                            data-bs-toggle="tooltip"
                                            title="Edit Appointment">
                                            <i class="fa fa-edit text-primary"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light"
                                            onclick="cancelAppointment(<?= $row['appt_id'] ?>)"
                                            data-bs-toggle="tooltip"
                                            title="Cancel Appointment">
                                            <i class="fa fa-times text-danger"></i>
                                        </button>
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
                                <h6 class="text-muted">No upcoming appointments found</h6>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Section -->

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
                        <div class="position-relative">
                            <img src="../images/team_placeholder.jpg"
                                id="edit_profile_photo"
                                class="rounded-circle border"
                                width="50"
                                height="50"
                                style="object-fit: cover;">
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1" id="doctor_name_display"></h6>
                            <span class="badge bg-primary-subtle text-primary" id="appointment_type_display"></span>
                        </div>
                    </div>

                    <!-- Appointment Details -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" class="form-control bg-light border-0"
                                name="appointment_date" id="edit_appointment_date"
                                min="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Time</label>
                            <input type="time" class="form-control bg-light border-0"
                                name="appointment_time" id="edit_appointment_time" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Status</label>
                            <select class="form-select bg-light border-0"
                                name="status" id="edit_status" required>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Notes</label>
                            <textarea class="form-control bg-light border-0"
                                name="notes" id="edit_notes"
                                rows="3" placeholder="Add notes..."></textarea>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_appointment" class="btn btn-primary rounded-0">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function viewDetails(appointmentId) {
        $.ajax({
            url: 'upcoming.php',
            type: 'POST',
            data: {
                id: appointmentId
            },
            success: function(response) {
                const data = JSON.parse(response);

                $('#edit_appointment_id').val(data.appointment_id);
                $('#patient_name').val(data.patient_name);
                $('#doctor_name').val('Dr. ' + data.doctor_name);
                $('#edit_date').val(data.appointment_date);
                $('#edit_time').val(data.appointment_time);
                $('#edit_status').val(data.status);
                $('#edit_notes').val(data.notes);
                //$('#profile_photo').val(data.profile_photo);

                $('#editModal').modal('show');
            },
            error: function() {
                alert('Error fetching appointment details');
            }
        });
    }

    // Update the table action buttons
    function editButton(appointmentId) {
        return `
        <button class="btn btn-sm btn-light" 
                onclick="viewDetails(${appointmentId})"
                data-bs-toggle="tooltip"
                title="Edit Appointment">
            <i class="fa fa-edit text-primary"></i>
        </button>
    `;
    }
</script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#myTable').DataTable({
            "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
            "pageLength": 10,
            "ordering": true,
            "autoWidth": false,
            "responsive": true,
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
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl =>
            new bootstrap.Tooltip(tooltipTriggerEl)
        );
    });

    function viewDetails(appointmentId) {
        // Implement view details functionality
        window.location.href = `appointment-details.php?id=${appointmentId}`;
    }

    function cancelAppointment(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            window.location.href = `upcoming.php?cancel=${appointmentId}`;
        }
    }

    function openEditModal(apptId, doctorName, specialization, apptDate, apptTime, status, profilePhoto) {
        // Set the values in the modal fields
        document.getElementById('edit_appointment_id').value = apptId;
        document.getElementById('doctor_name_display').textContent = doctorName;
        document.getElementById('appointment_type_display').textContent = specialization;
        document.getElementById('edit_appointment_date').value = apptDate;
        document.getElementById('edit_appointment_time').value = apptTime;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_notes').value = '';

        const profileImage = document.getElementById('edit_profile_photo');
        if (profilePhoto && profilePhoto.trim() !== '') {
            profileImage.src = profilePhoto;
        } else {
            profileImage.src = '../images/team_placeholder.jpg';
        }

        // Add error handler for image loading
        profileImage.onerror = function() {
            this.src = '../images/team_placeholder.jpg';
            this.onerror = null; // Prevent infinite loop
        };

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('editAppointmentModal'));
        modal.show();
    }

    // Add form validation
    document.getElementById('editAppointmentForm').addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
</script>