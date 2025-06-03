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
                    WHERE a.status='Completed' AND a.appt_date >= CURDATE() 
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
                                    <span class="badge bg-success-subtle text-warning px-3 rounded-pill">
                                        <?= htmlspecialchars($row["status"]) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-sm btn-light"
                                                onclick="openEditModal(<?= $row['appt_id'] ?>)"
                                                data-bs-toggle="tooltip"
                                                title="View Details">
                                            <i class="fa fa-eye text-primary"></i>
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
                                <i class="fa-solid fa-file-excel fa-2x text-secondary mb-3"></i>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-check text-primary me-2"></i>Completed Appointment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Status Banner -->
                <div class="alert bg-success-subtle text-success border-0 d-flex align-items-center mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>This appointment has been completed</div>
                </div>

                <div class="row g-4">
                    <!-- Patient Information -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Patient Information</h6>
                                <div class="d-flex align-items-center mb-3">
                                    <img id="patient_photo" src="../images/team_placeholder.jpg" 
                                         class="rounded-circle border" width="48" height="48" 
                                         style="object-fit: cover;">
                                    <div class="ms-3">
                                        <h6 class="mb-1" id="patient_name_display"></h6>
                                        <div class="small text-muted" id="patient_contact"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Information -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Doctor Information</h6>
                                <div class="d-flex align-items-center mb-3">
                                    <img id="doctor_photo" src="../images/team_placeholder.jpg" 
                                         class="rounded-circle border" width="48" height="48" 
                                         style="object-fit: cover;">
                                    <div class="ms-3">
                                        <h6 class="mb-1" id="doctor_name_display"></h6>
                                        <span class="badge bg-primary-subtle text-primary" 
                                              id="doctor_specialization"></span>
                                        <div class="small text-muted mt-1" id="doctor_contact"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Details -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Appointment Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="small text-muted mb-1">Date</label>
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-calendar text-primary me-2"></i>
                                            <span id="appointment_date"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small text-muted mb-1">Time</label>
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-clock text-primary me-2"></i>
                                            <span id="appointment_time"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-muted mb-1">Notes</label>
                                        <div class="bg-white p-3 rounded small" id="appointment_notes">
                                            Loading notes...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

    function openEditModal(apptId) {
        // Show loading states first
        const modal = new bootstrap.Modal(document.getElementById('editAppointmentModal'));
        modal.show();
        
        // Show loading indicators
        document.getElementById('patient_name_display').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        document.getElementById('patient_contact').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        document.getElementById('doctor_name_display').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        document.getElementById('doctor_specialization').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        document.getElementById('doctor_contact').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        document.getElementById('appointment_date').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        document.getElementById('appointment_time').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        document.getElementById('appointment_notes').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading notes...';
        
        // Fetch appointment details
        $.ajax({
            url: window.location.href, // Use current page URL
            type: 'POST',
            data: { id: apptId },
            dataType: 'json',
            success: function(data) {
                console.log('Received data:', data); // Debug log
                
                if (data && !data.error) {
                    try {
                        // Patient Information
                        document.getElementById('patient_name_display').textContent = data.patient_name || 'N/A';
                        document.getElementById('patient_contact').innerHTML = `
                            <div><i class="fas fa-envelope me-1"></i>${data.patient_email || 'N/A'}</div>
                            <div><i class="fas fa-phone me-1"></i>${data.patient_phone || 'N/A'}</div>
                        `;
                        
                        // Set patient photo
                        const patientPhoto = document.getElementById('patient_photo');
                        if (data.patient_img) {
                            patientPhoto.src = '../images/' + data.patient_img;
                            patientPhoto.onerror = function() {
                                this.src = '../images/team_placeholder.jpg';
                            };
                        }

                        // Doctor Information
                        document.getElementById('doctor_name_display').textContent = 'Dr. ' + (data.doctor_name || 'N/A');
                        document.getElementById('doctor_specialization').textContent = data.Specialization || 'N/A';
                        document.getElementById('doctor_contact').innerHTML = `
                            <div><i class="fas fa-envelope me-1"></i>${data.doctor_email || 'N/A'}</div>
                            <div><i class="fas fa-phone me-1"></i>${data.doctor_phone || 'N/A'}</div>
                        `;
                        
                        // Set doctor photo
                        const doctorPhoto = document.getElementById('doctor_photo');
                        if (data.doctor_profile_img) {
                            doctorPhoto.src = '../images/' + data.doctor_profile_img;
                            doctorPhoto.onerror = function() {
                                this.src = '../images/team_placeholder.jpg';
                            };
                        }

                        // Appointment Details - using native JavaScript date formatting
                        document.getElementById('appointment_date').textContent = formatDate(data.appt_date);
                        document.getElementById('appointment_time').textContent = formatTime(data.appt_time);
                        
                        // Notes
                        const notesElement = document.getElementById('appointment_notes');
                        if (data.notes && data.notes.trim() !== '') {
                            notesElement.innerHTML = data.notes;
                        } else {
                            notesElement.innerHTML = '<em class="text-muted">No notes available</em>';
                        }

                    } catch (error) {
                        console.error('Error processing data:', error);
                        showErrorMessage('Error processing appointment details');
                    }
                } else {
                    showErrorMessage(data.error || 'No appointment data found');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.log('Status:', status);
                console.log('Response:', xhr.responseText);
                
                let errorMessage = 'Error loading appointment details';
                if (xhr.responseText) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        errorMessage = errorData.error || errorMessage;
                    } catch (e) {
                        // If response is not JSON, use the text
                        errorMessage = xhr.responseText.length > 100 ? 
                            'Server error occurred' : xhr.responseText;
                    }
                }
                showErrorMessage(errorMessage);
            }
        });
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
</script>