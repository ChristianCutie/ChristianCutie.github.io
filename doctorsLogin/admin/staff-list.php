<?php
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";
$con = connection();

//session start
if (session_status()  == PHP_SESSION_NONE) {
    session_start();
}
$showToast = false;
$toastMessage = '';
$isSuccess = true;

//open connection
$con = connection();

$edit_id = "";
$edit_acc_id = "";
$edit_first_name = "";
$edit_middle_name = "";
$edit_last_name = "";
$edit_gender = "";
$edit_phone = "";
$edit_email = "";
$edit_age = "";
$edit_birthdate = "";
$edit_home_address = "";
$edit_specialization = "";
$edit_affiliation = "";
$edit_mnl = "";
$edit_biography = "";
$edit_username = "";
$edit_password = "";
$show_modal = false;

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];

    $sql_edit_doctor = "SELECT * FROM doctortb d1 inner join userlogintb u1 on d1.doctor_acc_id = u1.id WHERE d1.doctor_id = '$edit_id'";
    $result = $con->query($sql_edit_doctor);

    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();

        $edit_id = $doctor['doctor_id'];
        $edit_acc_id = $doctor['doctor_acc_id'];
        $edit_first_name = $doctor['First_Name'];
        $edit_middle_name = $doctor['Middle_Name'];
        $edit_last_name = $doctor['Last_Name'];
        $edit_gender = $doctor['Gender'];
        $edit_phone = $doctor['Phone_Number'];
        $edit_email = $doctor['Email_address'];
        $edit_age = $doctor['Age'];
        $edit_birthdate = $doctor['Date_Birth'];
        $edit_home_address = $doctor['Address'];
        $edit_specialization = $doctor['Specialization'];
        $edit_mnl = $doctor['mnl'];
        $edit_affiliation = $doctor['Affiliation'];
        $edit_biography = $doctor['Biography'];
        $edit_username = $doctor['User_Name'];
        $edit_password = $doctor['Password'];
        $show_modal = true;
    }
}


//update data doctor
if (isset($_POST['btnupdate'])) {
    $update_id = $_POST['doctor_id'];
    $update_acc_id = $_POST['doctor_acc_id'];
    $update_firstname = $_POST['first_name'];
    $update_middlename = $_POST['middle_name'];
    $update_lastname = $_POST['last_name'];
    $update_gender = $_POST['gender'];
    $update_phone = $_POST['pnum'];
    $update_email = $_POST['emailaddress'];
    $update_age = $_POST['age'];
    $update_birthdate = $_POST['birthdate'];
    $update_home_address = $_POST['address'];
    $update_specialization = $_POST['specialization'];
    $update_mnl = $_POST['mnl'];
    $update_affiliation = $_POST['affiliation'];
    $update_username = $_POST['username'];
    $update_password = $_POST['password'];
    $update_biography = $_POST['biography'];

    $sql_update_doctor = "UPDATE doctortb SET First_Name = '$update_firstname', Middle_Name = '$update_middlename', Last_Name = '$update_lastname', 
     Gender = '$update_gender', Phone_Number = '$update_phone', Email_Address = '$update_email', Age = '$update_age', Date_Birth = '$update_birthdate', Address = '$update_home_address',
      Specialization = '$update_specialization', Med_lic_num = '$update_mnl', Affiliation = '$update_affiliation', Biography = '$update_biography' WHERE doctor_id = '$update_id'";

    $sql_update_login = "UPDATE userlogintb SET User_Name = '$update_username', Password = '$update_password' WHERE id = '$update_acc_id'";

    if (($con->query($sql_update_doctor) === TRUE) && ($con->query($sql_update_login) === TRUE)) {
        $showToast = true;
        $toastMessage = "Successfully Updated!";
        $isSuccess = true;
    } else {
        $showToast = true;
        $toastMessage = "Error updating doctor: " . $con->error;
        $isSuccess = false;
    }
}

//delete data doctor
// if(isset($_GET['delete'])){
//     $delete_id = $_GET['delete'];

//     $sql_remove_data_doctor = "DELETE FROM USER_INFO WHERE ID = '$id'";
// }


?>
<!-- <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div> -->
<div class="container-fluid pt-4 px-4">
    <!-- Header Section with Statistics -->
    <div class="row mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-users fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total</p>
                    <h6 class="mb-0">
                        <?php
                        $total_result = $con->query("SELECT COUNT(*) as total FROM stafftb");
                        echo $total_result->fetch_assoc()['total'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa-solid fa-user-check fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Active</p>
                    <h6 class="mb-0">
                        <?php
                        $active_result = $con->query("SELECT COUNT(*) as active FROM stafftb WHERE Status = 'Active'");
                        echo $active_result->fetch_assoc()['active'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa-solid fa-user-xmark fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Deactivated</p>
                    <h6 class="mb-0">
                        <?php
                        $deact_result = $con->query("SELECT COUNT(*) as deactivated FROM stafftb WHERE Status = 'Deactivated'");
                        echo $deact_result->fetch_assoc()['deactivated'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff List Section -->
    <div class="bg-light rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">Staff List</h5>
                <p class="text-muted small mb-0">Manage registered staff</p>
            </div>
            <a href="../admin/add-staff.php" class="btn btn-primary btn-sm rounded-0">
                <i class="fa fa-plus me-2"></i>Add Staff
            </a>
        </div>

        <div class="table-responsive">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-active-tab" data-bs-toggle="tab" data-bs-target="#nav-active" type="button" role="tab" aria-controls="nav-active" aria-selected="true">Active</button>
                    <button class="nav-link" id="nav-deact-tab" data-bs-toggle="tab" data-bs-target="#nav-deact" type="button" role="tab" aria-controls="nav-deact" aria-selected="false">Deactivated</button>
                </div>
            </nav>
            <div class="tab-content pt-3" id="nav-tabContent">
                <!-- Active Staff Table -->
                <div class="tab-pane fade show active" id="nav-active" role="tabpanel" aria-labelledby="nav-active-tab">
                    <div class="table-responsive">
                        <table id="activeStaffTable" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Staff Info</th>
                                    <th>Job Title</th>
                                    <th>Department</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM stafftb WHERE Status = 'Active' ORDER BY id DESC";
                                $result = $con->query($sql);
                                $activeHasData = false;
                                if ($result->num_rows > 0) {
                                    $activeHasData = true;
                                    while ($row = $result->fetch_assoc()) {
                                        $profile_image = !empty($row['Profile_img']) && file_exists('../images/' . $row['Profile_img'])
                                            ? '../images/' . $row['Profile_img']
                                            : '../images/team_placeholder.jpg';
                                ?>
                                        <tr>
                                            <td><span class="fw-bold">#<?= htmlspecialchars($row["id"]) ?></span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= htmlspecialchars($profile_image) ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                                    <div class="ms-3">
                                                        <h6 class="mb-0"><?= htmlspecialchars($row["First_Name"] . " " . $row["Last_Name"]) ?></h6>
                                                        <small class="text-muted"><?= htmlspecialchars($row["Email_address"]) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($row["Job_Title"]) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info"><?= htmlspecialchars($row["Department"]) ?></span>
                                            </td>
                                            <td>
                                                <i class="fa fa-phone text-primary me-2"></i><?= htmlspecialchars($row["Phone_Number"]) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success text-white px-3 rounded-pill"><?= htmlspecialchars($row["Status"]) ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn btn-sm btn-light" onclick="window.location.href='staff-list.php?edit=<?= $row['id'] ?>'" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-edit text-primary"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-light" onclick="window.location.href='staff-list.php?deact_id=<?= $row['id'] ?>'" data-bs-toggle="tooltip" title="Deactivate">
                                                        <i class="fa-solid fa-user-xmark text-danger"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="noDataMessageFromActiveTable" class="text-center py-5" style="display: none;">
                        <i class="fas fa-user-xmark fa-2x text-secondary mb-3"></i>
                        <h6 class="text-muted">No active accounts found</h6>
                        <p class="text-muted small mb-0">All active accounts will appear here.</p>
                    </div>

                </div>
                <!-- Deactivated Staff Table -->
                <div class="tab-pane fade" id="nav-deact" role="tabpanel" aria-labelledby="nav-deact-tab">
                    <div class=" table-responsive">
                        <table id="deactStaffTable" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Staff Info</th>
                                    <th>Job Title</th>
                                    <th>Department</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM stafftb WHERE Status = 'Deactivated' ORDER BY id DESC";
                                $result = $con->query($sql);
                                $deactHasData = false;
                                if ($result->num_rows > 0) {
                                    $deactHasData = true;
                                    while ($row = $result->fetch_assoc()) {
                                        $profile_image = !empty($row['Profile_img']) && file_exists('../images/' . $row['Profile_img'])
                                            ? '../images/' . $row['Profile_img']
                                            : '../images/team_placeholder.jpg';
                                ?>
                                        <tr>
                                            <td><span class="fw-bold">#<?= htmlspecialchars($row["id"]) ?></span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= htmlspecialchars($profile_image) ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                                    <div class="ms-3">
                                                        <h6 class="mb-0"><?= htmlspecialchars($row["First_Name"] . " " . $row["Last_Name"]) ?></h6>
                                                        <small class="text-muted"><?= htmlspecialchars($row["Email_address"]) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($row["Job_Title"]) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info"><?= htmlspecialchars($row["Department"]) ?></span>
                                            </td>
                                            <td>
                                                <i class="fa fa-phone text-primary me-2"></i><?= htmlspecialchars($row["Phone_Number"]) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success text-white px-3 rounded-pill"><?= htmlspecialchars($row["Status"]) ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn btn-sm btn-light" onclick="window.location.href='staff-list.php?edit=<?= $row['id'] ?>'" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-edit text-primary"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-light" onclick="window.location.href='staff-list.php?deact_id=<?= $row['id'] ?>'" data-bs-toggle="tooltip" title="Deactivate">
                                                        <i class="fa-solid fa-user-xmark text-danger"></i>
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
                    <div id="noDataMessageFromDeactTable" class="text-center py-5" style="display: none;">
                       <i class="fas fa-user-xmark fa-2x text-secondary mb-3"></i>
                        <h6 class="text-muted">No deactivated accounts found</h6>
                        <p class="text-muted small mb-0">All deactivated accounts will appear here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modals for Edit, Deactivate, Reactivate (copy structure from doctor-list.php and adjust fields) -->
    <!-- Toast notification (copy from doctor-list.php) -->
</div>
<?php include "../includes/script.php"; ?>
<script>
    const activeHasData = <?= json_encode($activeHasData) ?>;
    const deactHasData = <?= json_encode($deactHasData) ?>;
    const showModal = <?= json_encode($show_modal) ?>;
</script>
<script>
    $(document).ready(function() {
        if (activeHasData) {
            // Initialize DataTable only if there's data
            $('#activeStaffTable').DataTable({
                "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
                "pageLength": 10,
                "ordering": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                    "targets": [6], // Actions column
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

        } else {
            // Hide the table and show the no data message
            $('#activeStaffTable').hide();
            $('#noDataMessageFromActiveTable').show();
            console.log('No data available - DataTable not initialized');
        }

        if (deactHasData) {
            // Initialize DataTable only if there's data
            $('#deactStaffTable').DataTable({
                "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
                "pageLength": 10,
                "ordering": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                    "targets": [6], // Actions column
                    "orderable": false,
                    "searchable": false
                }],
                "language": {
                    "search": "<i class='fa fa-search'></i>",
                    "searchPlaceholder": "Search staff...",
                    "lengthMenu": "_MENU_ per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ staff",
                    "infoEmpty": "No appointments found",
                    "infoFiltered": "(filtered from _MAX_ total staff)",
                    "emptyTable": "No deactivated accounts found",
                    "zeroRecords": "No deactivated accounts found"
                },
                "initComplete": function() {
                    console.log('DataTable initialized successfully');
                },
                "drawCallback": function() {
                    // Reinitialize tooltips after each draw
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });


        } else {
            // Hide the table and show the no data message
            $('#deactStaffTable').hide();
            $('#noDataMessageFromDeactTable').show();
            console.log('No data available - DataTable not initialized');
        }
        // Initialize tooltips for the table data
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl =>
            new bootstrap.Tooltip(tooltipTriggerEl)
        );
    });

    // Tab persistence
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(tabButton) {
        tabButton.addEventListener('shown.bs.tab', function(event) {
            const target = event.target.getAttribute('data-bs-target');
            localStorage.setItem('activeTab', target);
        });
    });
    window.addEventListener('DOMContentLoaded', function() {
        const activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            const tabToActivate = document.querySelector(`button[data-bs-target="${activeTab}"]`);
            if (tabToActivate) {
                new bootstrap.Tab(tabToActivate).show();
            }
        }
    });
</script>