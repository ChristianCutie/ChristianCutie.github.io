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
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $id = $_POST['id'];

    try {
        $sql = "SELECT a.*, 
                CONCAT(p.First_Name, ' ', p.Last_Name) as patient_name,
                p.Profile_img as patient_img,
                p.Email as patient_email,
                p.Phone_No as patient_phone,
                CONCAT(d.First_Name, ' ', d.Last_Name) as doctor_name,
                d.Profile_img as doctor_profile_img,
                d.Specialization,
                d.Email as doctor_email,
                d.Phone_No as doctor_phone
                FROM appointmenttb a
                LEFT JOIN patienttb p ON a.patient_app_acc_id = p.patient_acc_id
                LEFT JOIN doctortb d ON a.doctor_app_acc_id = d.doctor_acc_id
                WHERE a.appt_id = ?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Appointment not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

?>
<style>
    .info-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: #212529;
        font-size: 1rem;
    }

    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

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

    /* Add to your existing styles */
.modal .bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.modal .bg-light {
    background-color: #f8f9fa !important;
}

.modal img.rounded-circle.border {
    border: 2px solid #fff !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.badge {
    font-weight: 500;
}

.table img.rounded-circle.border {
    border: 2px solid #fff !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

@media print {
    .modal-footer,
    .btn-close {
        display: none !important;
    }
}
</style>

<!-- Add Moment.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

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
                        $total_result = $con->query("SELECT COUNT(*) as total FROM appointmenttb WHERE status='Completed' AND appt_date >= CURDATE()");
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
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">Completed Appointments</h5>
                <p class="text-muted small mb-0">View completed appointments history</p>
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
                            CONCAT(p.First_Name, ' ', p.Last_Name) as patient_name,
                            p.Profile_img as patient_img,
                            CONCAT(d.First_Name, ' ', d.Last_Name) as doctor_name,
                            d.Profile_img as doctor_profile_img,
                            d.Specialization
                            FROM appointmenttb a 
                            LEFT JOIN patienttb p ON a.patient_app_acc_id = p.patient_acc_id 
                            LEFT JOIN doctortb d ON a.doctor_app_acc_id = d.doctor_acc_id 
                            WHERE a.status='Completed'
                            ORDER BY a.appt_date DESC, a.appt_time DESC";
                    
                    $result = $con->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><span class="fw-bold">#<?= htmlspecialchars($row["appt_id"]) ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= !empty($row['patient_img']) ? '../images/' . htmlspecialchars($row['patient_img']) : '../images/team_placeholder.jpg' ?>"
                                             class="rounded-circle border"
                                             width="40" height="40"
                                             style="object-fit: cover;">
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
                                             style="object-fit: cover;">
                                        <div class="ms-3">
                                            <h6 class="mb-0">Dr. <?= htmlspecialchars($row["doctor_name"]) ?></h6>
                                            <span class="badge bg-primary-subtle text-primary">
                                                <?= htmlspecialchars($row["Specialization"]) ?>
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
                                    <span class="badge bg-success text-white px-3 rounded-pill">
                                        <?= htmlspecialchars($row["status"]) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light" 
                                            onclick="viewDetails(<?= $row['appt_id'] ?>)"
                                            data-bs-toggle="tooltip" 
                                            title="View Details">
                                        <i class="fa fa-eye text-primary"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-clipboard-check fa-2x text-secondary mb-3"></i>
                                <h6 class="text-muted">No completed appointments found</h6>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Section -->

<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title">
                    <i class="fas fa-circle-info text-primary me-2"></i>Appointment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Status Banner -->
                <div class="alert bg-success-subtle text-success d-flex align-items-center mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>This appointment has been completed</div>
                </div>

                <!-- Patient Info -->
                <div class="mb-4">
                    <label class="form-label small text-muted mb-2">Patient Information</label>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <img id="view_patient_photo" src="../images/team_placeholder.jpg" 
                             class="rounded-circle border" width="48" height="48" 
                             style="object-fit: cover;">
                        <div class="ms-3">
                            <h6 class="mb-1" id="view_patient_name">Loading...</h6>
                            <div class="small text-muted" id="view_patient_contact"></div>
                        </div>
                    </div>
                </div>

                <!-- Doctor Info -->
                <div class="mb-4">
                    <label class="form-label small text-muted mb-2">Doctor Information</label>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <img id="view_doctor_photo" src="../images/team_placeholder.jpg" 
                             class="rounded-circle border" width="48" height="48" 
                             style="object-fit: cover;">
                        <div class="ms-3">
                            <h6 class="mb-1" id="view_doctor_name">Loading...</h6>
                            <span class="badge bg-primary-subtle text-primary mb-1" id="view_specialization"></span>
                            <div class="small text-muted" id="view_doctor_contact"></div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Details -->
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label small text-muted mb-2">Date</label>
                        <div class="p-3 bg-light rounded">
                            <i class="far fa-calendar text-primary me-2"></i>
                            <span id="view_date">Loading...</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small text-muted mb-2">Time</label>
                        <div class="p-3 bg-light rounded">
                            <i class="far fa-clock text-primary me-2"></i>
                            <span id="view_time">Loading...</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted mb-2">Notes</label>
                        <div class="p-3 bg-light rounded small" id="view_notes">Loading...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Details
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Helper function to format date without moment.js
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Helper function to format time without moment.js
    function formatTime(timeString) {
        if (!timeString) return 'N/A';
        // Handle both HH:mm:ss and HH:mm formats
        const timeParts = timeString.split(':');
        if (timeParts.length >= 2) {
            const hours = parseInt(timeParts[0]);
            const minutes = timeParts[1];
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            return `${displayHours}:${minutes} ${ampm}`;
        }
        return timeString;
    }

    

    function showErrorMessage(message) {
        document.getElementById('patient_name_display').innerHTML = '<span class="text-danger">Error</span>';
        document.getElementById('patient_contact').innerHTML = '';
        document.getElementById('doctor_name_display').innerHTML = '<span class="text-danger">Error</span>';
        document.getElementById('doctor_specialization').innerHTML = '';
        document.getElementById('doctor_contact').innerHTML = '';
        document.getElementById('appointment_date').innerHTML = '';
        document.getElementById('appointment_time').innerHTML = '';
        document.getElementById('appointment_notes').innerHTML = `
            <div class="alert alert-danger mb-0">
                <i class="fas fa-exclamation-circle me-2"></i>${message}
            </div>
        `;
    }

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
    // Show loading state first
    const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
    modal.show();

    // Show initial loading state in modal
    $('#view_patient_name').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
    $('#view_patient_contact').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_doctor_name').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
    $('#view_specialization').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_doctor_contact').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_date').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_time').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_notes').html('<i class="fas fa-spinner fa-spin"></i> Loading...');

    // Fetch appointment details
    $.ajax({
        url: window.location.pathname, // Use current page path
        type: 'POST',
        data: { id: appointmentId },
        dataType: 'json',
        success: function(response) {
            if (!response) {
                showErrorInModal('No data received');
                return;
            }

            try {
                // Update patient info
                $('#view_patient_name').text(response.patient_name || 'N/A');
                $('#view_patient_contact').html(`
                    <div><i class="fas fa-envelope me-1"></i>${response.patient_email || 'N/A'}</div>
                    <div><i class="fas fa-phone me-1"></i>${response.patient_phone || 'N/A'}</div>
                `);
                $('#view_patient_photo').attr('src', response.patient_img ? 
                    '../images/' + response.patient_img : '../images/team_placeholder.jpg');

                // Update doctor info
                $('#view_doctor_name').text('Dr. ' + (response.doctor_name || 'N/A'));
                $('#view_specialization').text(response.Specialization || 'N/A');
                $('#view_doctor_contact').html(`
                    <div><i class="fas fa-envelope me-1"></i>${response.doctor_email || 'N/A'}</div>
                    <div><i class="fas fa-phone me-1"></i>${response.doctor_phone || 'N/A'}</div>
                `);
                $('#view_doctor_photo').attr('src', response.doctor_profile_img ? 
                    '../images/' + response.doctor_profile_img : '../images/team_placeholder.jpg');

                // Update appointment details
                $('#view_date').text(formatDate(response.appt_date));
                $('#view_time').text(formatTime(response.appt_time));
                $('#view_notes').html(response.notes || '<em class="text-muted">No notes available</em>');

                // Handle image errors
                $('img.rounded-circle').on('error', function() {
                    $(this).attr('src', '../images/team_placeholder.jpg');
                });

            } catch (error) {
                console.error('Error processing response:', error);
                showErrorInModal('Error processing appointment details');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.log('Response:', xhr.responseText);
            showErrorInModal('Error loading appointment details');
        }
    });
}

    // Add this new function to handle errors in modal
    function showErrorInModal(message) {
        $('#view_patient_name').html('<span class="text-danger">Error</span>');
        $('#view_patient_contact').html('');
        $('#view_doctor_name').html('<span class="text-danger">Error</span>');
        $('#view_specialization').html('');
        $('#view_doctor_contact').html('');
        $('#view_date').html('');
        $('#view_time').html('');
        $('#view_notes').html(`
            <div class="alert alert-danger mb-0">
                <i class="fas fa-exclamation-circle me-2"></i>${message}
            </div>
        `);
    }
</script>

<!-- Add this script section to handle the AJAX update -->

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

    // Add form submission handler
    $('#editAppointmentForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'completed.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        // Show success toast
                        showToast('Success', result.message, 'success');
                        // Refresh table
                        $('#myTable').DataTable().ajax.reload();
                        // Close modal
                        $('#editAppointmentModal').modal('hide');
                    } else {
                        showToast('Error', result.message, 'error');
                    }
                } catch (error) {
                    showToast('Error', 'An error occurred', 'error');
                }
            },
            error: function() {
                showToast('Error', 'An error occurred', 'error');
            }
        });
    });
});

function openEditModal(apptId) {
    // Show loading state first
    const modal = new bootstrap.Modal(document.getElementById('editAppointmentModal'));
    modal.show();

    // Show loading indicators
    $('#view_patient_name').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
    $('#view_patient_contact').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_doctor_name').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
    $('#view_specialization').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_doctor_contact').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_date').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_time').html('<i class="fas fa-spinner fa-spin"></i>');
    $('#view_notes').html('<i class="fas fa-spinner fa-spin"></i> Loading...');

    // Fetch appointment details
    $.ajax({
        url: 'get_appointment_details.php',
        type: 'POST',
        data: { id: apptId },
        dataType: 'json',
        success: function(response) {
            if (!response) {
                showErrorInModal('No data received');
                return;
            }

            updateModalContent(response);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showErrorInModal('Error loading appointment details');
        }
    });
}

function updateModalContent(data) {
    // Update patient info
    $('#view_patient_name').text(data.patient_name || 'N/A');
    $('#view_patient_contact').html(`
        <div><i class="fas fa-envelope me-1"></i>${data.patient_email || 'N/A'}</div>
        <div><i class="fas fa-phone me-1"></i>${data.patient_phone || 'N/A'}</div>
    `);
    $('#view_patient_photo').attr('src', data.patient_img ? 
        '../images/' + data.patient_img : '../images/team_placeholder.jpg');

    // Update doctor info
    $('#view_doctor_name').text('Dr. ' + (data.doctor_name || 'N/A'));
    $('#view_specialization').text(data.Specialization || 'N/A');
    $('#view_doctor_contact').html(`
        <div><i class="fas fa-envelope me-1"></i>${data.doctor_email || 'N/A'}</div>
        <div><i class="fas fa-phone me-1"></i>${data.doctor_phone || 'N/A'}</div>
    `);
    $('#view_doctor_photo').attr('src', data.doctor_profile_img ? 
        '../images/' + data.doctor_profile_img : '../images/team_placeholder.jpg');

    // Update appointment details
    $('#view_date').text(moment(data.appt_date).format('MMMM D, YYYY'));
    $('#view_time').text(moment(data.appt_time, 'HH:mm:ss').format('h:mm A'));
    $('#view_notes').html(data.notes || '<em class="text-muted">No notes available</em>');

    // Handle image errors
    $('img.rounded-circle').on('error', function() {
        $(this).attr('src', '../images/team_placeholder.jpg');
    });
}

function showToast(title, message, type) {
    const toast = $('#loginToast');
    const toastHeader = toast.find('.toast-header');
    const toastBody = toast.find('.toast-body');
    
    toastHeader.removeClass('bg-success bg-danger')
               .addClass(type === 'success' ? 'bg-success' : 'bg-danger')
               .find('strong').text(title);
               
    toastBody.text(message);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

function showErrorInModal(message) {
    $('#view_patient_name').html('<span class="text-danger">Error</span>');
    $('#view_patient_contact').html('');
    $('#view_doctor_name').html('<span class="text-danger">Error</span>');
    $('#view_specialization').html('');
    $('#view_doctor_contact').html('');
    $('#view_date').html('');
    $('#view_time').html('');
    $('#view_notes').html(`
        <div class="alert alert-danger mb-0">
            <i class="fas fa-exclamation-circle me-2"></i>${message}
        </div>
    `);
}
</script>