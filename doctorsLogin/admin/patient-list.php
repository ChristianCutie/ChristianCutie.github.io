<?php 
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";
$con = connection();
?>

<div class="container-fluid pt-4 px-4">
    <!-- Header Section with Statistics -->
    <div class="row mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-users fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total<br> Patients</p>
                    <h6 class="mb-0">
                        <?php
                        $total_result = $con->query("SELECT COUNT(*) as total FROM patienttb");
                        echo $total_result->fetch_assoc()['total'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-calendar-check fa-3x text-success"></i>
                <div class="ms-3">
                    <p class="mb-2">Active Appointments</p>
                    <h6 class="mb-0">
                        <?php
                        $appt_result = $con->query("SELECT COUNT(*) as active FROM appointmenttb WHERE status = 'Approved'");
                        echo $appt_result->fetch_assoc()['active'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient List Section -->
    <div class="bg-light rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">Patient List</h5>
                <p class="text-muted small mb-0">Manage registered patients</p>
            </div>
            <a href="../admin/add-patient.php" class="btn btn-primary btn-sm rounded-0">
                <i class="fa fa-plus me-2"></i>Add Patient
            </a>
        </div>

        <div class="table-responsive">
            <table id="myTable" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Patient Info</th>
                        <th>Contact Details</th>
                        <th>Medical Info</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql = "SELECT * FROM patienttb";
                    $result = $con->query($sql);
                    if($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <td>
                                <span class="fw-bold">#<?= htmlspecialchars($row["patient_acc_id"]) ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="position-relative">
                                        <?php
                                        $profile_image = !empty($row['Profile_img']) && file_exists('../images/' . $row['Profile_img']) 
                                            ? '../images/' . $row['Profile_img'] 
                                            : '../images/team_placeholder.jpg';
                                        ?>
                                        <img src="<?= htmlspecialchars($profile_image) ?>" 
                                             class="rounded-circle" 
                                             width="40" height="40"
                                             style="object-fit: cover;">
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0">
                                            <?= htmlspecialchars($row["First_Name"] . " " . $row["Last_Name"]) ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($row["Email_address"]) ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div><i class="fa fa-phone text-primary me-2"></i><?= htmlspecialchars($row["Phone_Number"]) ?></div>
                                    <div><i class="fa fa-map-marker text-primary me-2"></i><?= htmlspecialchars($row["Address"]) ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary-subtle text-primary">
                                        <?= htmlspecialchars($row["Blood_Type"]) ?>
                                    </span>
                                    <span class="badge bg-info-subtle text-info">
                                        Age: <?= htmlspecialchars($row["Age"]) ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success px-3 rounded-pill">
                                    Active
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-light" 
                                            onclick="editPatient(<?= $row['patient_id'] ?>)"
                                            data-bs-toggle="tooltip"
                                            title="Edit">
                                        <i class="fa fa-edit text-primary"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light" 
                                            onclick="deletePatient(<?= $row['patient_id'] ?>)"
                                            data-bs-toggle="tooltip"
                                            title="Delete">
                                        <i class="fa fa-trash text-danger"></i>
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
                                <h6 class="text-muted">No patients found</h6>
                                <a href="../admin/add-patient.php" class="btn btn-primary btn-sm mt-3">
                                    <i class="fa fa-plus me-2"></i>Add New Patient
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
            "searchPlaceholder": "Search patients...",
            "lengthMenu": "_MENU_ per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ patients",
            "infoEmpty": "No patients found",
            "infoFiltered": "(filtered from _MAX_ total patients)"
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => 
        new bootstrap.Tooltip(tooltipTriggerEl)
    );
});

function editPatient(patientId) {
    window.location.href = `edit-patient.php?id=${patientId}`;
}

function deletePatient(patientId) {
    if (confirm('Are you sure you want to delete this patient?')) {
        window.location.href = `patient-list.php?delete=${patientId}`;
    }
}
</script>

<style>
.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.badge {
    font-weight: 500;
}

.table > :not(caption) > * > * {
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
<?php include "../includes/script.php";?>

