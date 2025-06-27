<?php
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";
$con = connection();

// Modal variables
$edit_id = "";
$edit_acc_id = "";
$edit_first_name = "";
$edit_last_name = "";
$edit_email = "";
$edit_phone = "";
$edit_address = "";
$edit_blood = "";
$edit_age = "";
$edit_profile_img = "";
$edit_username = "";
$edit_password = "";
$show_modal = false;

// Handle edit button
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $sql = "SELECT p.*, u.User_Name, u.Password FROM patienttb p
            LEFT JOIN userlogintb u ON p.patient_acc_id = u.id
            WHERE p.patient_id = '$edit_id'";
    $result = $con->query($sql);
    if ($result && $result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        $edit_id = $patient['patient_id'];
        $edit_acc_id = $patient['patient_acc_id'];
        $edit_first_name = $patient['First_Name'];
        $edit_last_name = $patient['Last_Name'];
        $edit_email = $patient['Email_address'];
        $edit_phone = $patient['Phone_Number'];
        $edit_address = $patient['Address'];
        $edit_blood = $patient['Blood_Type'];
        $edit_age = $patient['Age'];
        $edit_username = $patient['User_Name'] ?? '';
        $edit_password = $patient['Password'] ?? '';
        $edit_profile_img = $patient['Profile_img'];
        $show_modal = true;
    }
}

// Handle update
if (isset($_POST['btnupdate'])) {
    $update_id = $_POST['patient_id'];
    $update_firstname = $_POST['first_name'];
    $update_lastname = $_POST['last_name'];
    $update_email = $_POST['emailaddress'];
    $update_phone = $_POST['pnum'];
    $update_address = $_POST['address'];
    $update_blood = $_POST['blood_type'];
    $update_age = $_POST['age'];

    // Handle profile image upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $fileName = time() . '_' . $_FILES['profile_photo']['name'];
        $targetDir = "../images/";
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile);
            $sql_update = "UPDATE patienttb SET First_Name='$update_firstname', Last_Name='$update_lastname', Email_address='$update_email', Phone_Number='$update_phone', Address='$update_address', Blood_Type='$update_blood', Age='$update_age', Profile_img='$fileName' WHERE patient_id='$update_id'";
        } else {
            $sql_update = "UPDATE patienttb SET First_Name='$update_firstname', Last_Name='$update_lastname', Email_address='$update_email', Phone_Number='$update_phone', Address='$update_address', Blood_Type='$update_blood', Age='$update_age' WHERE patient_id='$update_id'";
        }
    } else {
        $sql_update = "UPDATE patienttb SET First_Name='$update_firstname', Last_Name='$update_lastname', Email_address='$update_email', Phone_Number='$update_phone', Address='$update_address', Blood_Type='$update_blood', Age='$update_age' WHERE patient_id='$update_id'";
    }

    if ($con->query($sql_update) === TRUE) {
        $showToast = true;
        $toastMessage = "Patient updated successfully!";
        $isSuccess = true;
    } else {
        $showToast = true;
        $toastMessage = "Error updating patient: " . $con->error;
        $isSuccess = false;
    }
}

// Modal variables for deactivation
$deact_id = "";
$deact_acc_id = "";
$deact_first_name = "";
$deact_last_name = "";
$deact_status = "";
$show_modal_deact = false;

// Handle deactivate modal open
if (isset($_GET['deact_id'])) {
    $deact_id = $_GET['deact_id'];

    $sql = "SELECT p.*, u.User_Name, u.Password FROM patienttb p
            LEFT JOIN userlogintb u ON p.patient_acc_id = u.id
            WHERE p.patient_id = '$deact_id'";
    $result = $con->query($sql);
    if ($result && $result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        $deact_id = $patient['patient_id'];
        $deact_acc_id = $patient['patient_acc_id'];
        $deact_first_name = $patient['First_Name'];
        $deact_last_name = $patient['Last_Name'];
        $deact_status = $patient['Status'] ?? '';
        $deact_profile_img = $patient['Profile_img'];
        $show_modal_deact = true;
    }
}

// Handle deactivate action
if (isset($_POST['deactivateButton'])) {
    $deact_id = $_POST['patient_id'];
    $deact_acc_id = $_POST['patient_acc_id'];
    $deact_reason = $_POST['deact_reason'];

    if (strtoupper($deact_reason) === 'CONFIRM') {
        $sql_update_status = "UPDATE patienttb SET status = 'Deactivated' WHERE patient_id = '$deact_id'";
        $sql_update_login = "UPDATE userlogintb SET Status = 'Deactivated' WHERE id = '$deact_acc_id'";
        if ($con->query($sql_update_status) === TRUE && $con->query($sql_update_login) === TRUE) {
            $showToast = true;
            $toastMessage = "Patient account deactivated successfully!";
            $isSuccess = true;
        } else {
            $showToast = true;
            $toastMessage = "Error deactivating patient: " . $con->error;
            $isSuccess = false;
        }
    } else {
        $showToast = true;
        $toastMessage = "Please type 'CONFIRM' to proceed.";
        $isSuccess = false;
    }
}

// Modal variables for reactivation
$react_id = "";
$react_acc_id = "";
$react_first_name = "";
$react_last_name = "";
$react_status = "";
$react_profile_img = "";
$show_modal_react = false;

// Handle reactivate modal open
if (isset($_GET['react_id'])) {
    $react_id = $_GET['react_id'];
    $sql = "SELECT p.*, u.User_Name, u.Password FROM patienttb p
            LEFT JOIN userlogintb u ON p.patient_acc_id = u.id
            WHERE p.patient_id = '$react_id'";
    $result = $con->query($sql);
    if ($result && $result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        $react_id = $patient['patient_id'];
        $react_acc_id = $patient['patient_acc_id'];
        $react_first_name = $patient['First_Name'];
        $react_last_name = $patient['Last_Name'];
        $react_status = $patient['Status'] ?? '';
        $react_profile_img = $patient['Profile_img'];
        $show_modal_react = true;
    }
}

// Handle reactivate action
if (isset($_POST['reactivateButton'])) {
    $react_id = $_POST['patient_id'];
    $react_acc_id = $_POST['patient_acc_id'];
    $react_reason = $_POST['react_reason'];

    if (strtoupper($react_reason) === 'CONFIRM') {
        $sql_update_status = "UPDATE patienttb SET status = 'Active' WHERE patient_id = '$react_id'";
        $sql_update_login = "UPDATE userlogintb SET Status = 'Active' WHERE id = '$react_acc_id'";
        if ($con->query($sql_update_status) === TRUE && $con->query($sql_update_login) === TRUE) {
            $showToast = true;
            $toastMessage = "Patient account reactivated successfully!";
            $isSuccess = true;
        } else {
            $showToast = true;
            $toastMessage = "Error reactivating patient: " . $con->error;
            $isSuccess = false;
        }
    } else {
        $showToast = true;
        $toastMessage = "Please type 'CONFIRM' to proceed.";
        $isSuccess = false;
    }
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
                <i class="fa fa-users fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total</p>
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
                <i class="fa-solid fa-user-check fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Active</p>
                    <h6 class="mb-0">
                        <?php
                        $appt_result = $con->query("SELECT COUNT(*) as active FROM patienttb WHERE status = 'Active'");
                        echo $appt_result->fetch_assoc()['active'];
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
                        $appt_result = $con->query("SELECT COUNT(*) as deactivate FROM patienttb WHERE status = 'Deactivated'");
                        echo $appt_result->fetch_assoc()['deactivate'];
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
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Active</button>
                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Deactivated</button>
                </div>
            </nav>
            <div class="tab-content pt-3" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="table-responsive">
                        <table id="activeTable" class="table table-hover align-middle">
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
                                $sql = "SELECT * FROM patienttb WHERE status = 'Active' ORDER BY patient_acc_id DESC";
                                $result = $con->query($sql);
                                $activeUserData = false;
                                if ($result->num_rows > 0) {
                                    $activeUserData = true;
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
                                                        onclick="window.location.href='patient-list.php?edit=<?= $row['patient_id'] ?>'"
                                                        data-bs-toggle="tooltip"
                                                        title="Edit">
                                                        <i class="fa fa-edit text-primary"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-light"
                                                        onclick="window.location.href='patient-list.php?deact_id=<?= $row['patient_id'] ?>'"
                                                        data-bs-toggle="tooltip"
                                                        title="Deactivate">
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
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="table-responsive">
                        <table id="deactTable" class="table table-hover align-middle">
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
                                $sql = "SELECT * FROM patienttb WHERE status != 'Active' ORDER BY patient_acc_id DESC";
                                $result = $con->query($sql);
                                $deactUserData = false;
                                if ($result->num_rows > 0) {
                                    $deactUserData = true;
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
                                                <span class="badge bg-danger text-white px-3 rounded-pill">
                                                    <?= htmlspecialchars($row["Status"]) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn btn-sm btn-light"
                                                        onclick="window.location.href='patient-list.php?react_id=<?= $row['patient_id'] ?>'"
                                                        data-bs-toggle="tooltip"
                                                        title="Reactivate">
                                                        <i class="fa-solid fa-user-check text-success"></i>
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
</div>

<!-- EDIT PATIENT MODAL -->
<div class="modal fade <?php echo $show_modal ? 'show' : ''; ?>" id="editPatientModal" tabindex="-1"
    aria-labelledby="editPatientModalLabel" aria-hidden="true"
    style="<?php echo $show_modal ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="editPatientModalLabel">
                    <i class="fa fa-user me-2 text-primary"></i>Edit Patient Information
                </h5>
                <a href="patient-list.php" class="btn-close" aria-label="Close"></a>
            </div>
            <form action="patient-list.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                    <input type="hidden" name="patient_id" value="<?php echo $edit_id; ?>">

                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <?php
                            $profile_image = !empty($edit_profile_img) && file_exists('../images/' . $edit_profile_img)
                                ? '../images/' . $edit_profile_img
                                : '../images/team_placeholder.jpg';
                            ?>
                            <img src="<?= htmlspecialchars($profile_image) ?>"
                                id="profilePreview"
                                class="rounded-circle border"
                                style="width: 120px; height: 120px; object-fit: cover;">
                            <label for="profile_photo"
                                class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle">
                                <i class="fa fa-camera"></i>
                            </label>
                            <input type="file"
                                id="profile_photo"
                                name="profile_photo"
                                class="d-none"
                                accept="image/*"
                                onchange="previewImage(this);">
                        </div>
                        <p class="text-muted small mt-2">Click the camera icon to change profile photo</p>
                    </div>

                    <!-- Personal Information -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="card-title mb-0">
                                    <i class="fa fa-user me-2"></i>Personal Information
                                </h6>
                                <span class="badge bg-primary-subtle text-primary">Required Fields *</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">First Name *</label>
                                    <input type="text" class="form-control rounded-0" name="first_name"
                                        value="<?php echo htmlspecialchars($edit_first_name); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Last Name *</label>
                                    <input type="text" class="form-control rounded-0" name="last_name"
                                        value="<?php echo htmlspecialchars($edit_last_name); ?>" required>
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
                                    <label class="form-label small fw-bold">Address *</label>
                                    <input type="text" class="form-control rounded-0" name="address"
                                        value="<?php echo htmlspecialchars($edit_address); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    <div class="card border-0 bg-light">
                        <div class="card-body p-4">
                            <h6 class="card-title mb-4">
                                <i class="fa fa-heartbeat me-2"></i>Medical Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Blood Type *</label>
                                    <input type="text" class="form-control rounded-0" name="blood_type"
                                        value="<?php echo htmlspecialchars($edit_blood); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Age *</label>
                                    <input type="number" class="form-control rounded-0" name="age"
                                        value="<?php echo htmlspecialchars($edit_age); ?>" required>
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
                    <a href="patient-list.php" class="btn btn-light rounded-0">Cancel</a>
                    <button type="submit" name="btnupdate" class="btn btn-primary rounded-0 btn-sm">
                        <i class="fa fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /EDIT PATIENT MODAL -->

<!-- DEACTIVATE PATIENT MODAL -->
<div class="modal fade <?php echo $show_modal_deact ? 'show' : ''; ?>" id="deactPatientModal" tabindex="-1"
    aria-labelledby="deactPatientModalLabel" aria-hidden="true"
    style="<?php echo $show_modal_deact ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="deactPatientModalLabel">
                    <i class="fa-solid fa-user-xmark me-2 text-danger"></i>Deactivate Patient
                </h5>
                <a href="patient-list.php" class="btn-close" aria-label="Close"></a>
            </div>
            <form action="patient-list.php" method="POST" class="needs-validation" novalidate>
                <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                    <input type="hidden" name="patient_id" value="<?php echo $deact_id; ?>">
                    <input type="hidden" name="patient_acc_id" value="<?php echo $deact_acc_id; ?>">

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
                        <h6 class="mt-2"><?= htmlspecialchars($deact_first_name . ' ' . $deact_last_name) ?></h6>
                        <p class="text-muted fw-light small">Patient ID: <?= htmlspecialchars($deact_acc_id) ?></p>
                    </div>
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="p-3 bg-white rounded d-flex justify-content-between mb-3">
                                        <small class="text-muted d-block">Status</small>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-circle-check text-success me-2"></i>
                                            <span class="badge bg-success text-white"><?= htmlspecialchars($deact_status) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-3 bg-white rounded d-flex justify-content-between ">
                                        <small class="text-muted d-block">Account</small>
                                        <div class="d-flex align-items-center">
                                            <span class="text-primary small"><?= htmlspecialchars($deact_first_name . ' ' . $deact_last_name) ?></span>
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
                                        <input id="confirmInputPatient" type="text" class="form-control rounded-0" placeholder="Type 'CONFIRM'" name="deact_reason" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <a href="patient-list.php" class="btn btn-light rounded-0">Cancel</a>
                    <button class="btn btn-sm btn-danger rounded-0" id="confirmButtonPatient" disabled name="deactivateButton">
                        <i class="fa-solid fa-xmark me-2"></i>Deactivate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /DEACTIVATE PATIENT MODAL -->

<!-- REACTIVATE PATIENT MODAL -->
<div class="modal fade <?php echo $show_modal_react ? 'show' : ''; ?>" id="reactPatientModal" tabindex="-1"
    aria-labelledby="reactPatientModalLabel" aria-hidden="true"
    style="<?php echo $show_modal_react ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="reactPatientModalLabel">
                    <i class="fa-solid fa-user-check me-2 text-success"></i>Reactivate Patient
                </h5>
                <a href="patient-list.php" class="btn-close" aria-label="Close"></a>
            </div>
            <form action="patient-list.php" method="POST" class="needs-validation" novalidate>
                <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                    <input type="hidden" name="patient_id" value="<?php echo $react_id; ?>">
                    <input type="hidden" name="patient_acc_id" value="<?php echo $react_acc_id; ?>">

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
                        <h6 class="mt-2"><?= htmlspecialchars($react_first_name . ' ' . $react_last_name) ?></h6>
                        <p class="text-muted fw-light small">Patient ID: <?= htmlspecialchars($react_acc_id) ?></p>
                    </div>
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="p-3 bg-white rounded d-flex justify-content-between mb-3">
                                        <small class="text-muted d-block">Status</small>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-circle-xmark text-danger me-2"></i>
                                            <span class="badge bg-danger text-white"><?= htmlspecialchars($react_status) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-3 bg-white rounded d-flex justify-content-between ">
                                        <small class="text-muted d-block">Account</small>
                                        <div class="d-flex align-items-center">
                                            <span class="text-primary small"><?= htmlspecialchars($react_first_name . ' ' . $react_last_name) ?></span>
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
                                    <div class="alert alert-info small" role="alert">
                                        <i class="fa fa-info-circle me-2"></i>
                                        Type <strong>'CONFIRM'</strong> in the box below to confirm reactivation.
                                    </div>
                                    <div class="p-3 bg-white rounded">
                                        <input id="confirmInputPatientReact" type="text" class="form-control rounded-0" placeholder="Type 'CONFIRM'" name="react_reason" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <a href="patient-list.php" class="btn btn-light rounded-0">Cancel</a>
                    <button class="btn btn-sm btn-success rounded-0" id="confirmButtonPatientReact" disabled name="reactivateButton">
                        <i class="fa-solid fa-check me-2"></i>Reactivate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /REACTIVATE PATIENT MODAL -->

<?php include "../includes/script.php"; ?>
<script>
    const activeUserData = <?= json_encode($activeUserData) ?>;
    const deactUserData = <?= json_encode($deactUserData) ?>;
    const showModal = <?= json_encode($show_modal) ?>;
</script>
<script>
    $(document).ready(function() {
        if (activeUserData) {
            // Initialize DataTable only if there's data
            $('#activeTable').DataTable({
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
                    "searchPlaceholder": "Search patient...",
                    "lengthMenu": "_MENU_ per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ patients",
                    "infoEmpty": "No appointments found",
                    "infoFiltered": "(filtered from _MAX_ total patients)",
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
            $('#activeTable').hide();
            $('#noDataMessageFromActiveTable').show();
            console.log('No data available - DataTable not initialized');
        }

        if (deactUserData) {
            // Initialize DataTable only if there's data
            $('#deactTable').DataTable({
                "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
                "pageLength": 10,
                "ordering": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [{
                    "targets": [5],
                    "orderable": false,
                    "searchable": false
                }],
                "language": {
                    "search": "<i class='fa fa-search'></i>",
                    "searchPlaceholder": "Search patient...",
                    "lengthMenu": "_MENU_ per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ patients",
                    "infoEmpty": "No appointments found",
                    "infoFiltered": "(filtered from _MAX_ total patients)",
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
            $('#deactTable').hide();
            $('#noDataMessageFromDeactTable').show();
            console.log('No data available - DataTable not initialized');
        }


        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl =>
            new bootstrap.Tooltip(tooltipTriggerEl)
        );
    });
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

        setupConfirmInput('confirmInputPatient', 'confirmButtonPatient');
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show toast if needed
        <?php if ($showToast): ?>
            var toastEl = document.getElementById('loginToast');
            var toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
            toast.show();
        <?php endif; ?>

        // Show modals if needed
        <?php if ($show_modal): ?>
            var modal = new bootstrap.Modal(document.getElementById('editPatientModal'));
            modal.show();
        <?php endif; ?>
        <?php if ($show_modal_deact): ?>
            var modalDeact = new bootstrap.Modal(document.getElementById('deactPatientModal'));
            modalDeact.show();
        <?php endif; ?>
        <?php if ($show_modal_react): ?>
            var modalReact = new bootstrap.Modal(document.getElementById('reactPatientModal'));
            modalReact.show();
        <?php endif; ?>

       
    });
</script>
<script>
    function previewImage(input) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        }
        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>
<script>
    // Save the active tab when changed
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(function(tabButton) {
        tabButton.addEventListener('shown.bs.tab', function(event) {
            const target = event.target.getAttribute('data-bs-target');
            localStorage.setItem('activeTab', target);
        });
    });

    // Restore the active tab on page load
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

<style>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputReact = document.getElementById('confirmInputPatientReact');
        const buttonReact = document.getElementById('confirmButtonPatientReact');
        if (inputReact && buttonReact) {
            inputReact.addEventListener('input', function() {
                buttonReact.disabled = (this.value !== 'CONFIRM');
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('confirmInputReact');
        const button = document.getElementById('confirmButtonReact');
        if (input && button) {
            input.addEventListener('input', function() {
                button.disabled = (this.value !== 'CONFIRM');
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show toast if needed
        <?php if ($showToast): ?>
            var toastEl = document.getElementById('loginToast');
            var toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 5000
            });
            toast.show();
        <?php endif; ?>

        // Show modals if needed
        <?php if ($show_modal): ?>
            var modal = new bootstrap.Modal(document.getElementById('editPatientModal'));
            modal.show();
        <?php endif; ?>
        <?php if ($show_modal_deact): ?>
            var modalDeact = new bootstrap.Modal(document.getElementById('deactPatientModal'));
            modalDeact.show();
        <?php endif; ?>
        <?php if ($show_modal_react): ?>
            var modalReact = new bootstrap.Modal(document.getElementById('reactPatientModal'));
            modalReact.show();
        <?php endif; ?>

        // Enable/disable deactivate button
        const input = document.getElementById('confirmInputPatient');
        const button = document.getElementById('confirmButtonPatient');
        if (input && button) {
            input.addEventListener('input', function() {
                button.disabled = (this.value !== 'CONFIRM');
            });
        }
    });
</script>