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
$edit_job_title = "";
$edit_department = "";
$edit_specialization = "";
$edit_med_lic_num = "";
$edit_profile_img = "";
$edit_username = "";
$edit_password = "";
$show_modal = false;

// Deactivate modal variables
$deact_id = "";
$deact_first_name = "";
$deact_last_name = "";
$deact_status = "";
$deact_specialization = "";
$deact_department = "";
$show_modal_deact = false;

// Reactivate modal variables
$react_id = "";
$react_acc_id = "";
$react_first_name = "";
$react_last_name = "";
$react_status = "";
$react_department = "";
$react_profile_img = "";
$show_modal_react = false;

// Deactivate staff
if (isset($_GET['deact_id'])) {

    $deact_id = $_GET['deact_id'];

    $sql_deact_info = "SELECT s.*, u.User_Name, u.Password 
                       FROM stafftb s 
                       INNER JOIN userlogintb u ON s.staff_acc_id = u.id 
                       WHERE s.staff_acc_id = '$deact_id'";

    $result = $con->query($sql_deact_info);

    if ($result && $result->num_rows > 0) {
        $deact_staff = $result->fetch_assoc();

        $deact_acc_id = $deact_staff['staff_acc_id'] ?? '';
        $deact_first_name = $deact_staff['First_Name'] ?? '';
        $deact_last_name = $deact_staff['Last_Name'] ?? '';
        $deact_specialization = $deact_staff['Specialization'] ?? '';
        $deact_status = $deact_staff['Status'] ?? '';
        $deact_id = $deact_staff['id'] ?? '';
        $deact_profile_img = $deact_staff['Profile_img'] ?? '';
        $deact_department = $deact_staff['Department'] ?? '';
        $show_modal_deact = true;
    }
}

//Update status to deactivated - staff
if (isset($_POST['deactivateStaffButton'])) {
    $deact_id = $_POST['staff_acc_id'];
    $deact_reason = $_POST['deact_reason'];

    if (strtoupper($deact_reason) === 'CONFIRM') {
        $sql_update_status = "UPDATE stafftb SET Status = 'Deactivated' WHERE staff_acc_id = '$deact_id'";
        $sql_update_login = "UPDATE userlogintb SET Status = 'Deactivated' WHERE id = '$deact_id'";

        if ($con->query($sql_update_status) === TRUE && $con->query($sql_update_login) === TRUE) {
            $showToast = true;
            $toastMessage = "Doctor account deactivated successfully!";
            $isSuccess = true;
        } else {
            $showToast = true;
            $toastMessage = "Error deactivating staff: " . $con->error;
            $isSuccess = false;
        }
    } else {
        $showToast = true;
        $toastMessage = "Please type 'CONFIRM' to proceed.";
        $isSuccess = false;
    }
}

// Reactivate staff
if (isset($_GET['react_id'])) {
    $react_id = $_GET['react_id'];

    $sql_react_info = "SELECT s.*, u.User_Name, u.Password 
                       FROM stafftb s 
                       INNER JOIN userlogintb u ON s.staff_acc_id = u.id 
                       WHERE s.staff_acc_id = '$react_id'";

    $result = $con->query($sql_react_info);

    if ($result && $result->num_rows > 0) {
        $react_staff = $result->fetch_assoc();

        $react_acc_id = $react_staff['staff_acc_id'] ?? '';
        $react_first_name = $react_staff['First_Name'] ?? '';
        $react_last_name = $react_staff['Last_Name'] ?? '';
        $react_status = $react_staff['Status'] ?? '';
        $react_id = $react_staff['id'] ?? '';
        $react_profile_img = $react_staff['Profile_img'] ?? '';
        $react_department = $react_staff['Department'] ?? '';
        $show_modal_react = true;
    }
}

// Update status to reactivated - staff
if (isset($_POST['reactivateStaffButton'])) {
    $react_id = $_POST['staff_acc_id'];
    $react_reason = $_POST['react_reason'];

    if (strtoupper($react_reason) === 'CONFIRM') {

        $sql_update_status = "UPDATE stafftb SET Status = 'Active' WHERE staff_acc_id = '$react_id'";
        $sql_update_login = "UPDATE userlogintb SET Status = 'Active' WHERE id = '$react_id'";

        if ($con->query($sql_update_status) === TRUE && $con->query($sql_update_login) === TRUE) {
            $showToast = true;
            $toastMessage = "Staff account reactivated successfully!";
            $isSuccess = true;
        } else {
            $showToast = true;
            $toastMessage = "Error reactivating staff: " . $con->error;
            $isSuccess = false;
        }
    } else {
        $showToast = true;
        $toastMessage = "Please type 'CONFIRM' to proceed.";
        $isSuccess = false;
    }
}

// BIND MODAL DATA
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];

    $sql_edit_staff = "SELECT * FROM stafftb s1 INNER JOIN userlogintb u1 ON s1.staff_acc_id = u1.id WHERE u1.id = '$edit_id'";
    $result = $con->query($sql_edit_staff);

    if ($result && $result->num_rows > 0) {
        $staff = $result->fetch_assoc();

        $edit_id = $staff['id'];
        $edit_acc_id = $staff['staff_acc_id'];
        $edit_first_name = $staff['First_Name'];
        $edit_middle_name = $staff['Middle_Name'];
        $edit_last_name = $staff['Last_Name'];
        $edit_gender = $staff['Gender'];
        $edit_phone = $staff['Phone_Number'];
        $edit_email = $staff['Email_address'];
        $edit_age = $staff['Age'];
        $edit_birthdate = $staff['Date_Birth'];
        $edit_home_address = $staff['Address'];
        $edit_job_title = $staff['Job_Title'];
        $edit_department = $staff['Department'];
        $edit_specialization = $staff['Specialization'];
        $edit_med_lic_num = $staff['Med_lic_num'];
        $edit_profile_img = $staff['Profile_img'];
        $edit_username = $staff['User_Name'];
        $edit_password = $staff['Password'];
        $show_modal = true;
    }
}

// UPDATE STAFF DATA
if (isset($_POST['btnupdate'])) {
    $update_id = $_POST['staff_id'];
    $update_acc_id = $_POST['staff_acc_id'];
    $update_firstname = $_POST['first_name'];
    $update_middlename = $_POST['middle_name'];
    $update_lastname = $_POST['last_name'];
    $update_gender = $_POST['gender'];
    $update_age = $_POST['age'];
    $update_birthdate = $_POST['birthdate'];
    $update_phone = $_POST['pnum'];
    $update_email = $_POST['emailaddress'];
    $update_home_address = $_POST['address'];
    $update_job_title = $_POST['job_title'];
    $update_department = $_POST['department'];
    $update_specialization = $_POST['specialization'];
    $update_med_lic_num = $_POST['med_lic_num'];
    $update_username = $_POST['username'];
    $update_password = $_POST['password'];


    if (isset($_FILES['staff_profile_photo']) && $_FILES['staff_profile_photo']['error'] === UPLOAD_ERR_OK) {

        $fileName = time() . '_' . $_FILES['staff_profile_photo']['name'];
        $targetDir = "../images/";
        $targetFile = $targetDir . $fileName;

        // Validate image
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Upload image
        if (!move_uploaded_file($_FILES['staff_profile_photo']['tmp_name'], $targetFile)) {
            throw new Exception("Failed to upload image.");
        }

        $sql_update_staff = "UPDATE stafftb SET 
                    First_Name = '$update_firstname', 
                    Middle_Name = '$update_middlename', 
                    Last_Name = '$update_lastname', 
                    Gender = '$update_gender', 
                    Age = '$update_age',
                    Date_Birth = '$update_birthdate',
                    Phone_Number = '$update_phone', 
                    Email_address = '$update_email', 
                    Address = '$update_home_address', 
                    Job_Title = '$update_job_title', 
                    Department = '$update_department',
                    Specialization = '$update_specialization',
                    Med_lic_num = '$update_med_lic_num',
                    Profile_img = '$fileName'
                    WHERE staff_acc_id = '$update_id'";

        $sql_update_login = "UPDATE userlogintb SET 
        User_Name = '$update_username', 
        Password = '$update_password' 
        WHERE id = '$update_acc_id'";

        if (($con->query($sql_update_staff) === TRUE) && ($con->query($sql_update_login) === TRUE)) {
            $showToast = true;
            $toastMessage = "Profile updated successfully!";
            $isSuccess = true;
        } else {
            $showToast = true;
            $toastMessage = "Error updating doctor: " . $con->error;
            $isSuccess = false;
        }
    } else {

        $sql_update_staff = "UPDATE stafftb SET 
            First_Name = '$update_firstname', 
            Middle_Name = '$update_middlename', 
            Last_Name = '$update_lastname', 
            Gender = '$update_gender', 
            Age = '$update_age',
            Date_Birth = '$update_birthdate',
            Phone_Number = '$update_phone', 
            Email_address = '$update_email', 
            Address = '$update_home_address', 
            Job_Title = '$update_job_title', 
            Department = '$update_department',
            Specialization = '$update_specialization',
            Med_lic_num = '$update_med_lic_num'
            WHERE staff_acc_id = '$update_id'";

        $sql_update_login = "UPDATE userlogintb SET 
        User_Name = '$update_username', 
        Password = '$update_password' 
        WHERE id = '$update_acc_id'";

        if (($con->query($sql_update_staff) === TRUE) && ($con->query($sql_update_login) === TRUE)) {
            $showToast = true;
            $toastMessage = "Profile updated successfully!";
            $isSuccess = true;
        } else {
            $showToast = true;
            $toastMessage = "Error updating doctor: " . $con->error;
            $isSuccess = false;
        }
    }
}

//delete data doctor
// if(isset($_GET['delete'])){
//     $delete_id = $_GET['delete'];

//     $sql_remove_data_doctor = "DELETE FROM USER_INFO WHERE ID = '$id'";
// }


?>
<style>
    .nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    padding: 0.5rem 1rem;
    margin-right: 1rem;
    font-weight: 500;
    background: none;
    border-radius: 0;
    transition: color 0.2s;
}
.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
    background: none;
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
            <ul class="nav nav-tabs mb-4" id="staffTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="nav-active-tab" data-bs-toggle="tab" href="#nav-active" role="tab" aria-controls="nav-active" aria-selected="true">
                        Active
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="nav-deact-tab" data-bs-toggle="tab" href="#nav-deact" role="tab" aria-controls="nav-deact" aria-selected="false">
                      Deactivated
                    </a>
                </li>
            </ul>
            <div class="tab-content" id="nav-tabContent">
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
                                            <td><span class="fw-bold">#<?= htmlspecialchars($row["staff_acc_id"]) ?></span></td>
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
                                                    <button class="btn btn-sm btn-light" onclick="window.location.href='staff-list.php?edit=<?= $row['staff_acc_id'] ?>'" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-edit text-primary"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-light" onclick="window.location.href='staff-list.php?deact_id=<?= $row['staff_acc_id'] ?>'" data-bs-toggle="tooltip" title="Deactivate">
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
                                            <td><span class="fw-bold">#<?= htmlspecialchars($row["staff_acc_id"]) ?></span></td>
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
                                                <span class="badge bg-danger text-white px-3 rounded-pill"><?= htmlspecialchars($row["Status"]) ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn btn-sm btn-light" onclick="window.location.href='staff-list.php?react_id=<?= $row['staff_acc_id'] ?>'" data-bs-toggle="tooltip" title="Reactivate">
                                                        <i class="fa-solid fa-user-check text-success"></i>
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


    <!-- EDIT STAFF MODAL -->
    <div class="modal fade <?php echo $show_modal ? 'show' : ''; ?>" id="editStaffModal" tabindex="-1"
        aria-labelledby="editStaffModalLabel" aria-hidden="true"
        style="<?php echo $show_modal ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-xl">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="editStaffModalLabel">
                        <i class="fa fa-user me-2 text-primary"></i>Edit Staff Information
                    </h5>
                    <a href="staff-list.php" class="btn-close" aria-label="Close"></a>
                </div>
                <form action="staff-list.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                        <input type="hidden" name="staff_id" value="<?php echo $edit_id; ?>">
                        <input type="hidden" name="staff_acc_id" value="<?php echo $edit_acc_id; ?>">

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <?php
                                $profile_image = !empty($edit_profile_img) && file_exists('../images/' . $edit_profile_img)
                                    ? '../images/' . $edit_profile_img
                                    : '../images/team_placeholder.jpg';
                                ?>
                                <img src="<?= htmlspecialchars($profile_image) ?>"
                                    id="staffProfilePreview"
                                    class="rounded-circle border"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                                <label for="staff_profile_photo"
                                    class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle">
                                    <i class="fa fa-camera"></i>
                                </label>
                                <input type="file"
                                    id="staff_profile_photo"
                                    name="staff_profile_photo"
                                    class="d-none"
                                    accept="image/*"
                                    onchange="previewStaffImage(this);">
                            </div>
                            <p class="text-muted small mt-2">Click the camera icon to change profile photo</p>
                        </div>

                        <!-- Personal Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">First Name *</label>
                                        <input type="text" class="form-control rounded-0" name="first_name"
                                            value="<?php echo htmlspecialchars($edit_first_name); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Middle Name</label>
                                        <input type="text" class="form-control rounded-0" name="middle_name"
                                            value="<?php echo htmlspecialchars($edit_middle_name); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Last Name *</label>
                                        <input type="text" class="form-control rounded-0" name="last_name"
                                            value="<?php echo htmlspecialchars($edit_last_name); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Gender *</label>
                                        <select class="form-select rounded-0" name="gender" required>
                                            <option value="">Select</option>
                                            <option value="Male" <?php if ($edit_gender == "Male") echo "selected"; ?>>Male</option>
                                            <option value="Female" <?php if ($edit_gender == "Female") echo "selected"; ?>>Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Age</label>
                                        <input type="number" class="form-control rounded-0" name="age"
                                            value="<?php echo htmlspecialchars($edit_age); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Birthdate</label>
                                        <input type="date" class="form-control rounded-0" name="birthdate"
                                            value="<?php echo htmlspecialchars($edit_birthdate); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Email Address *</label>
                                        <input type="email" class="form-control rounded-0" name="emailaddress"
                                            value="<?php echo htmlspecialchars($edit_email); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Phone Number *</label>
                                        <input type="text" class="form-control rounded-0" name="pnum"
                                            value="<?php echo htmlspecialchars($edit_phone); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Home Address *</label>
                                        <input type="text" class="form-control rounded-0" name="address"
                                            value="<?php echo htmlspecialchars($edit_home_address); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Job Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Job Title *</label>
                                        <input type="text" class="form-control rounded-0" name="job_title"
                                            value="<?php echo htmlspecialchars($edit_specialization); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Department *</label>
                                        <input type="text" class="form-control rounded-0" name="department"
                                            value="<?php echo htmlspecialchars($edit_department); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Specialization</label>
                                        <input type="text" class="form-control rounded-0" name="specialization"
                                            value="<?php echo htmlspecialchars($edit_specialization); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Medical License Number</label>
                                        <input type="text" class="form-control rounded-0" name="med_lic_num"
                                            value="<?php echo htmlspecialchars($edit_med_lic_num ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="card border-0 bg-light mt-4">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-4">
                                    <i class="fa fa-lock me-2"></i>Account Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Username *</label>
                                        <input type="text" class="form-control rounded-0" name="username"
                                            value="<?php echo htmlspecialchars($edit_username); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Password *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control rounded-0" name="password"
                                                value="<?php echo htmlspecialchars($edit_password); ?>" required>
                                            <button class="btn btn-outline-secondary rounded-0 toggle-password" type="button">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <a href="staff-list.php" class="btn btn-light rounded-0">Cancel</a>
                        <button type="submit" name="btnupdate" class="btn btn-primary rounded-0 btn-sm">
                            <i class="fa fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /EDIT STAFF MODAL -->

    <!-- DEACTIVATE STAFF MODAL -->
    <div class="modal fade <?php echo isset($show_modal_deact) && $show_modal_deact ? 'show' : ''; ?>" id="deactStaffModal" tabindex="-1"
        aria-labelledby="deactStaffModalLabel" aria-hidden="true"
        style="<?php echo isset($show_modal_deact) && $show_modal_deact ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="deactStaffModalLabel">
                        <i class="fa-solid fa-user-xmark me-2 text-danger"></i>Deactivate Staff
                    </h5>
                    <a href="staff-list.php" class="btn-close" aria-label="Close"></a>
                </div>
                <form action="staff-list.php" method="POST" class="needs-validation" novalidate>
                    <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                        <input type="hidden" name="staff_id" value="<?php echo $deact_id ?? ''; ?>">
                        <input type="hidden" name="staff_acc_id" value="<?php echo $deact_acc_id ?? ''; ?>">

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <?php
                                $profile_image = !empty($deact_profile_img) && file_exists('../images/' . $deact_profile_img)
                                    ? '../images/' . $deact_profile_img
                                    : '../images/team_placeholder.jpg';
                                ?>
                                <img src="<?= htmlspecialchars($profile_image) ?>"
                                    class="rounded-circle border"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <h6 class="mt-2"><?= htmlspecialchars(($deact_first_name ?? '') . ' ' . ($deact_last_name ?? '')) ?></h6>
                            <p class="text-muted fw-light small">Staff ID: <?= htmlspecialchars($deact_id ?? '') ?></p>
                        </div>
                        <div class="card bg-light border-0 mb-sm-3">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="p-3 bg-white rounded d-flex justify-content-between mb-3">
                                            <small class="text-muted d-block">Status</small>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-circle-check text-success me-2"></i>
                                                <span class="badge bg-success text-white"><?= htmlspecialchars($deact_status ?? '') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="p-3 bg-white rounded d-flex justify-content-between ">
                                            <small class="text-muted d-block">Department</small>
                                            <div class="d-flex align-items-center">
                                                <span class="text-primary small"><?= htmlspecialchars($deact_department ?? '') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="alert alert-warning small" role="alert">
                                            <i class="fa fa-exclamation-triangle me-2"></i>
                                            Type <strong>'CONFIRM'</strong> in the box below to confirm deactivation.
                                        </div>
                                        <div class="p-3 bg-white rounded">
                                            <input id="confirmStaffInput" type="text" class="form-control rounded-0" placeholder="Type 'CONFIRM'" name="deact_reason" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <a href="staff-list.php" class="btn btn-light rounded-0">Cancel</a>
                        <button class="btn btn-sm btn-danger rounded-0" id="confirmStaffButton" disabled name="deactivateStaffButton">
                            <i class="fa-solid fa-xmark me-2"></i>Deactivate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /DEACTIVATE STAFF MODAL -->

    <!-- REACTIVATE STAFF MODAL -->
    <div class="modal fade <?php echo isset($show_modal_react) && $show_modal_react ? 'show' : ''; ?>" id="reactStaffModal" tabindex="-1"
        aria-labelledby="reactStaffModalLabel" aria-hidden="true"
        style="<?php echo isset($show_modal_react) && $show_modal_react ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="reactStaffModalLabel">
                        <i class="fa-solid fa-rotate me-2 text-primary"></i>Reactivate Staff
                    </h5>
                    <a href="staff-list.php" class="btn-close" aria-label="Close"></a>
                </div>
                <form action="staff-list.php" method="POST" class="needs-validation" novalidate>
                    <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                        <input type="hidden" name="staff_id" value="<?php echo $react_id ?? ''; ?>">
                        <input type="hidden" name="staff_acc_id" value="<?php echo $react_acc_id ?? ''; ?>">

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <?php
                                $profile_image = !empty($react_profile_img) && file_exists('../images/' . $react_profile_img)
                                    ? '../images/' . $react_profile_img
                                    : '../images/team_placeholder.jpg';
                                ?>
                                <img src="<?= htmlspecialchars($profile_image) ?>"
                                    class="rounded-circle border"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <h6 class="mt-2"><?= htmlspecialchars(($react_first_name ?? '') . ' ' . ($react_last_name ?? '')) ?></h6>
                            <p class="text-muted fw-light small">Staff ID: <?= htmlspecialchars($react_id ?? '') ?></p>
                        </div>
                        <div class="card bg-light border-0 mb-sm-3">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="p-3 bg-white rounded d-flex justify-content-between mb-3">
                                            <small class="text-muted d-block">Status</small>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-circle-exclamation text-danger me-2"></i>
                                                <span class="badge bg-danger text-white"><?= htmlspecialchars($react_status ?? '') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="p-3 bg-white rounded d-flex justify-content-between ">
                                            <small class="text-muted d-block">Department</small>
                                            <div class="d-flex align-items-center">
                                                <span class="text-primary small"><?= htmlspecialchars($react_department ?? '') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="alert alert-warning small" role="alert">
                                            <i class="fa fa-exclamation-triangle me-2"></i>
                                            Type <strong>'CONFIRM'</strong> in the box below to confirm reactivation.
                                        </div>
                                        <div class="p-3 bg-white rounded">
                                            <input id="reactivateStaffInput" type="text" class="form-control rounded-0" placeholder="Type 'CONFIRM'" name="react_reason" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <a href="staff-list.php" class="btn btn-light rounded-0">Cancel</a>
                        <button class="btn btn-sm btn-primary rounded-0" disabled name="reactivateStaffButton" id="reactivateStaffButton">
                            <i class="fa-solid fa-rotate me-2"></i>Reactivate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /REACTIVATE STAFF MODAL -->
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
                    "searchPlaceholder": "Search staff...",
                    "lengthMenu": "_MENU_ per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ staffs",
                    "infoEmpty": "No appointments found",
                    "infoFiltered": "(filtered from _MAX_ total staffs)",
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
                    "info": "Showing _START_ to _END_ of _TOTAL_ staffs",
                    "infoEmpty": "No appointments found",
                    "infoFiltered": "(filtered from _MAX_ total staffs)",
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
    function previewStaffImage(input) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('staffProfilePreview').src = e.target.result;
        }
        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>
<script>
    function setupConfirmInput(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        if (input && button) {
            input.addEventListener('input', function() {
                button.disabled = (this.value !== 'CONFIRM');
            });
        }
    }

    setupConfirmInput('confirmStaffInput', 'confirmStaffButton');
    setupConfirmInput('reactivateStaffInput', 'reactivateStaffButton');
</script>