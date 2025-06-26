<?php
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";

$con = connection();


// Edit modal variables

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



// Deactivate modal variables
$deact_id = "";
$deact_first_name = "";
$deact_last_name = "";
$deact_status = "";
$deact_specialization = "";
$show_modal_deact = false;

// Reactivate doctor
if (isset($_GET['react_id'])) {
    $react_id = $_GET['react_id'];

    // Fixed SQL query to get both doctor and account info in one query
    $sql_react_info = "SELECT d.*, u.User_Name, u.Password 
                       FROM doctortb d 
                       INNER JOIN userlogintb u ON d.doctor_acc_id = u.id 
                       WHERE d.doctor_id = '$react_id'";

    $result = $con->query($sql_react_info);

    if ($result && $result->num_rows > 0) {
        $react_doctor = $result->fetch_assoc();

        // Properly assign values from the fetched data
        $react_acc_id = $react_doctor['doctor_acc_id'] ?? '';
        $react_first_name = $react_doctor['First_Name'] ?? '';
        $react_last_name = $react_doctor['Last_Name'] ?? '';
        $react_specialization = $react_doctor['Specialization'] ?? '';
        $react_status = $react_doctor['Status'] ?? '';
        $react_id = $react_doctor['doctor_id'] ?? '';
        $react_profile_img = $react_doctor['Profile_img'] ?? '';
        $show_modal_react = true;
    }
}

//update status to reactivated
if (isset($_POST['reactivateButton'])) {
    $react_id = $_POST['doctor_id'];
    $react_acc_id = $_POST['doctor_acc_id'];

    // Update the doctor's status to 'Active'
    $sql_update_status = "UPDATE doctortb SET Status = 'Active' WHERE doctor_id = '$react_id'";
    $sql_update_login = "UPDATE userlogintb SET Status = 'Active' WHERE id = '$react_acc_id'";

    if ($con->query($sql_update_status) === TRUE && $con->query($sql_update_login) === TRUE) {
        $showToast = true;
        $toastMessage = "Doctor account reactivated successfully!";
        $isSuccess = true;
    } else {
        $showToast = true;
        $toastMessage = "Error reactivating doctor: " . $con->error;
        $isSuccess = false;
    }
}

// Deactivate doctor
if (isset($_GET['deact_id'])) {

    $deact_id = $_GET['deact_id'];

    // Fixed SQL query to get both doctor and account info in one query
    $sql_deact_info = "SELECT d.*, u.User_Name, u.Password 
                       FROM doctortb d 
                       INNER JOIN userlogintb u ON d.doctor_acc_id = u.id 
                       WHERE d.doctor_id = '$deact_id'";

    $result = $con->query($sql_deact_info);

    if ($result && $result->num_rows > 0) {
        $deact_doctor = $result->fetch_assoc();

        // Properly assign values from the fetched data
        $deact_acc_id = $deact_doctor['doctor_acc_id'] ?? '';
        $deact_first_name = $deact_doctor['First_Name'] ?? '';
        $deact_last_name = $deact_doctor['Last_Name'] ?? '';
        $deact_specialization = $deact_doctor['Specialization'] ?? '';
        $deact_status = $deact_doctor['Status'] ?? '';
        $deact_id = $deact_doctor['doctor_id'] ?? '';
        $deact_profile_img = $deact_doctor['Profile_img'] ?? '';
        $show_modal_deact = true;
    }
}

//Update status to deactivated
if (isset($_POST['deactivateButton'])) {
    $deact_id = $_POST['doctor_id'];
    $deact_acc_id = $_POST['doctor_acc_id'];
    $deact_reason = $_POST['deact_reason'];

    // Check if the input matches 'CONFIRM'
    if (strtoupper($deact_reason) === 'CONFIRM') {
        // Update the doctor's status to 'Deactivated'
        $sql_update_status = "UPDATE doctortb SET Status = 'Deactivated' WHERE doctor_id = '$deact_id'";
        $sql_update_login = "UPDATE userlogintb SET Status = 'Deactivated' WHERE id = '$deact_acc_id'";

        if ($con->query($sql_update_status) === TRUE && $con->query($sql_update_login) === TRUE) {
            $showToast = true;
            $toastMessage = "Doctor account deactivated successfully!";
            $isSuccess = true;
        } else {
            $showToast = true;
            $toastMessage = "Error deactivating doctor: " . $con->error;
            $isSuccess = false;
        }
    } else {
        $showToast = true;
        $toastMessage = "Please type 'CONFIRM' to proceed.";
        $isSuccess = false;
    }
}

//
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];

    $sql_edit_doctor = "SELECT * FROM doctortb d1 inner join userlogintb u1 on d1.doctor_acc_id = u1.id WHERE d1.doctor_id = '$edit_id'";
    $result = $con->query($sql_edit_doctor);

    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();

        $edit_id = $doctor['doctor_id'] ?? '';
        $edit_acc_id = $doctor['doctor_acc_id'] ?? '';
        $edit_first_name = $doctor['First_Name'] ?? '';
        $edit_middle_name = $doctor['Middle_Name'] ?? '';
        $edit_last_name = $doctor['Last_Name'] ?? '';
        $edit_gender = $doctor['Gender'] ?? '';
        $edit_phone = $doctor['Phone_Number'] ?? '';
        $edit_email = $doctor['Email_address'] ?? '';
        $edit_age = $doctor['Age'] ?? '';
        $edit_birthdate = $doctor['Date_Birth'] ?? '';
        $edit_home_address = $doctor['Address'] ?? '';
        $edit_specialization = $doctor['Specialization'] ?? '';
        $edit_mnl = $doctor['Med_lic_num'] ?? '';
        $edit_affiliation = $doctor['Affiliation'] ?? '';
        $edit_biography = $doctor['Biography'] ?? '';
        $edit_username = $doctor['User_Name'] ?? '';
        $edit_password = $doctor['Password'] ?? '';
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

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {

        $fileName = time() . '_' . $_FILES['profile_photo']['name'];
        $targetDir = "../images/";
        $targetFile = $targetDir . $fileName;

        // Validate image
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Upload image
        if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
            throw new Exception("Failed to upload image.");
        }

        $sql_update_doctor = "UPDATE doctortb SET First_Name = '$update_firstname', Middle_Name = '$update_middlename', Last_Name = '$update_lastname', 
     Gender = '$update_gender', Phone_Number = '$update_phone', Email_Address = '$update_email', Age = '$update_age', Date_Birth = '$update_birthdate', Address = '$update_home_address',
      Specialization = '$update_specialization', Med_lic_num = '$update_mnl', Affiliation = '$update_affiliation', Biography = '$update_biography', Profile_img = '$fileName'  WHERE doctor_id = '$update_id'";

        $sql_update_login = "UPDATE userlogintb SET User_Name = '$update_username', Password = '$update_password' WHERE id = '$update_acc_id'";

        if (($con->query($sql_update_doctor) === TRUE) && ($con->query($sql_update_login) === TRUE)) {
            $showToast = true;
            $toastMessage = "Profile updated successfully!";
            $isSuccess = true;
        } else {
            $showToast = true;
            $toastMessage = "Error updating doctor: " . $con->error;
            $isSuccess = false;
        }
    } else {

        $sql_update_doctor = "UPDATE doctortb SET First_Name = '$update_firstname', Middle_Name = '$update_middlename', Last_Name = '$update_lastname', 
     Gender = '$update_gender', Phone_Number = '$update_phone', Email_Address = '$update_email', Age = '$update_age', Date_Birth = '$update_birthdate', Address = '$update_home_address',
      Specialization = '$update_specialization', Med_lic_num = '$update_mnl', Affiliation = '$update_affiliation', Biography = '$update_biography'  WHERE doctor_id = '$update_id'";

        $sql_update_login = "UPDATE userlogintb SET User_Name = '$update_username', Password = '$update_password' WHERE id = '$update_acc_id'";

        if (($con->query($sql_update_doctor) === TRUE) && ($con->query($sql_update_login) === TRUE)) {
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
                        $total_result = $con->query("SELECT COUNT(*) as total FROM doctortb");
                        echo $total_result->fetch_assoc()['total'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-stethoscope fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Specialties</p>
                    <h6 class="mb-0">
                        <?php
                        $spec_result = $con->query("SELECT COUNT(DISTINCT Specialization) as specs FROM doctortb");
                        echo $spec_result->fetch_assoc()['specs'];
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
                        $spec_result = $con->query("SELECT COUNT(Status) as actives FROM doctortb WHERE Status = 'Active'");
                        echo $spec_result->fetch_assoc()['actives'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa-solid fa-user-xmark fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2 ">Deactivated</p>
                    <h6 class="mb-0">
                        <?php
                        $spec_result = $con->query("SELECT COUNT(Status) as deactivate FROM doctortb WHERE Status = 'Deactivated'");
                        echo $spec_result->fetch_assoc()['deactivate'];
                        ?>
                    </h6>
                </div>
            </div>
        </div>
        <!-- Add more statistics cards as needed -->
    </div>

    <!-- Doctor List Section -->
    <div class="bg-light rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">Doctor List</h5>
                <p class="text-muted small mb-0">Manage registered doctors</p>
            </div>
            <a href="../admin/add-doctor.php" class="btn btn-primary btn-sm rounded-0">
                <i class="fa fa-plus me-2"></i>Add Doctor
            </a>
        </div>

        <div class="table-responsive">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Active</button>
                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Deactivated</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <table id="myTable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Doctor ID</th>
                                <th>Doctor Info</th>
                                <th>Specialty</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM doctortb WHERE Status != 'Deactivated' ORDER BY doctor_id DESC";
                            $result = $con->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold">#<?= htmlspecialchars($row["doctor_id"]) ?></span>
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
                                            <span class="badge bg-primary-subtle text-primary">
                                                <?= htmlspecialchars($row["Specialization"]) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fa fa-phone text-primary me-2"></i>
                                            <?= htmlspecialchars($row["Phone_Number"]) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-success text-white px-3 rounded-pill">
                                                <?= htmlspecialchars($row["Status"]) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-sm btn-light"
                                                    onclick="window.location.href='doctor-list.php?edit=<?= $row['doctor_id'] ?>'"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit">
                                                    <i class="fa fa-edit text-primary"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light"
                                                    onclick="window.location.href='doctor-list.php?deact_id=<?= $row['doctor_id'] ?>'"
                                                    data-bs-toggle="tooltip"
                                                    title="Deactivate">
                                                    <i class="fa-solid fa-user-xmark text-danger"></i>
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
                                        <h6 class="text-muted">No doctors found</h6>
                                        <a href="../admin/add-doctor.php" class="btn btn-primary btn-sm mt-3">
                                            <i class="fa fa-plus me-2"></i>Add New Doctor
                                        </a>`
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <table id="myTable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Doctor ID</th>
                                <th>Doctor Info</th>
                                <th>Specialty</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM doctortb WHERE Status = 'Deactivated' ORDER BY doctor_id DESC";
                            $result = $con->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold">#<?= htmlspecialchars($row["doctor_id"]) ?></span>
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
                                            <span class="badge bg-primary-subtle text-primary">
                                                <?= htmlspecialchars($row["Specialization"]) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fa fa-phone text-primary me-2"></i>
                                            <?= htmlspecialchars($row["Phone_Number"]) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger text-white px-3 rounded-pill">
                                                <?= htmlspecialchars($row["Status"]) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-sm btn-light"
                                                    onclick="window.location.href='doctor-list.php?react_id=<?= $row['doctor_id'] ?>'"
                                                    data-bs-toggle="tooltip"
                                                    title="Reactivate">
                                                    <i class="fa-solid fa-trash-arrow-up text-danger"></i>
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
                                        <i class="fa-solid fa-circle-xmark fa-2x"></i>
                                        <h6 class="text-muted">No deactivated doctors found</h6>
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
    </div>

    <!-- EDIT MODAL PROFILE -->
    <div class="modal fade <?php echo $show_modal ? 'show' : ''; ?>" id="updateModal" tabindex="-1"
        aria-labelledby="updateModalLabel" aria-hidden="true"
        style="<?php echo $show_modal ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-xl">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="updateModalLabel">
                        <i class="fa-solid fa-user-doctor me-2 text-primary"></i>Update Doctor Information
                    </h5>
                    <a href="doctor-list.php" class="btn-close" aria-label="Close"></a>
                </div>
                <form action="doctor-list.php" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                        <input type="hidden" name="doctor_id" value="<?php echo $edit_id; ?>">
                        <input type="hidden" name="doctor_acc_id" value="<?php echo $edit_acc_id; ?>">

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <?php
                                $profile_image = !empty($doctor['Profile_img']) && file_exists('../images/' . $doctor['Profile_img'])
                                    ? '../images/' . $doctor['Profile_img']
                                    : '../images/team_placeholder.jpg';
                                ?>
                                <img src="<?= htmlspecialchars($profile_image) ?>"
                                    id="profilePreview"
                                    class="rounded-circle border"
                                    style="width: 150px; height: 150px; object-fit: cover;">
                                <label for="profile_photo"
                                    class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle">
                                    <i class="fa-solid fa-camera"></i>
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
                                        <i class="fa-solid fa-user me-2"></i>Personal Information
                                    </h6>
                                    <span class="badge bg-primary-subtle text-primary">Required Fields *</span>
                                </div>

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

                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Birth Date *</label>
                                        <input type="date" class="form-control rounded-0" name="birthdate"
                                            value="<?php echo htmlspecialchars($edit_birthdate); ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Age</label>
                                        <input type="number" class="form-control rounded-0" name="age"
                                            value="<?php echo htmlspecialchars($edit_age); ?>" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Gender *</label>
                                        <select class="form-select rounded-0" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?php echo ($edit_gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="Female" <?php echo ($edit_gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Phone Number *</label>
                                        <input type="tel" class="form-control rounded-0" name="pnum"
                                            value="<?php echo htmlspecialchars($edit_phone); ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Email Address *</label>
                                        <input type="email" class="form-control rounded-0" name="emailaddress"
                                            value="<?php echo htmlspecialchars($edit_email); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Home Address *</label>
                                        <input type="text" class="form-control rounded-0" name="address"
                                            value="<?php echo htmlspecialchars($edit_home_address); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-4">
                                    <i class="fa-solid fa-stethoscope me-2"></i>Professional Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Specialization *</label>
                                        <input type="text" class="form-control rounded-0" name="specialization"
                                            value="<?php echo htmlspecialchars($edit_specialization); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Medical License Number *</label>
                                        <input type="text" class="form-control rounded-0" name="mnl"
                                            value="<?php echo htmlspecialchars($edit_mnl); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Hospital Affiliation *</label>
                                        <input type="text" class="form-control rounded-0" name="affiliation"
                                            value="<?php echo htmlspecialchars($edit_affiliation); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Biography</label>
                                        <textarea class="form-control rounded-0" name="biography" rows="4"
                                            placeholder="Enter doctor's biography..."><?php echo htmlspecialchars($edit_biography); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="card border-0 bg-light">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-4">
                                    <i class="fa-solid fa-lock me-2"></i>Account Information
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
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <a href="doctor-list.php" class="btn btn-light rounded-0">Cancel</a>
                        <button type="submit" name="btnupdate" class="btn btn-primary rounded-0">
                            <i class="fa-solid fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /EDIT MODAL PROFILE -->

    <!-- DEACTIVATED MODAL PROFILE -->
    <div class="modal fade <?php echo $show_modal_deact ? 'show' : ''; ?>" id="deactModal" tabindex="-1"
        aria-labelledby="updateModalLabel" aria-hidden="true"
        style="<?php echo $show_modal_deact ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="updateModalLabel">
                        <i class="fa-solid fa-user-xmark me-2 text-danger"></i>Deactivate Doctor
                    </h5>
                    <a href="doctor-list.php" class="btn-close" aria-label="Close"></a>
                </div>
                <form action="doctor-list.php" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                        <input type="hidden" name="doctor_id" value="<?php echo $deact_id; ?>">
                        <input type="hidden" name="doctor_acc_id" value="<?php echo $deact_acc_id; ?>">

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <?php
                                $profile_image = !empty($deact_doctor['Profile_img']) && file_exists('../images/' . $deact_doctor['Profile_img'])
                                    ? '../images/' . $deact_doctor['Profile_img']
                                    : '../images/team_placeholder.jpg';
                                ?>
                                <img src="<?= htmlspecialchars($profile_image) ?>"
                                    id="profilePreview"
                                    class="rounded-circle border"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <h6 class=" mt-2"><?= htmlspecialchars($deact_first_name . ' ' . $deact_last_name) ?></h6>
                            <p class="text-muted fw-light small">Doctor ID: <?= htmlspecialchars($deact_acc_id) ?></p>
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
                                            <small class="text-muted d-block">Specialization</small>
                                            <div class="d-flex align-items-center">
                                                <span class=" text-primary small"><?= htmlspecialchars($deact_specialization) ?></span>
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
                                            <input id="confirmInput" type="text" class="form-control rounded-0" placeholder="Type 'CONFIRM'" name="deact_reason" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <a href="doctor-list.php" class="btn btn-light rounded-0">Cancel</a>
                        <button class="btn btn-sm btn-danger rounded-0" id="confirmButton" disabled name="deactivateButton">
                            <i class="fa-solid fa-xmark me-2"></i>Deactivate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- /DEACTIVATED MODAL PROFILE -->

    <!-- REACTIVATE MODAL PROFILE -->
    <div class="modal fade <?php echo $show_modal_react ? 'show' : ''; ?>" id="reactivateModal" tabindex="-1"
        aria-labelledby="updateModalLabel" aria-hidden="true"
        style="<?php echo $show_modal_react ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="updateModalLabel">
                        <i class="fa-solid fa-rotate me-2 text-primary"></i>Reactivate Doctor
                    </h5>
                    <a href="doctor-list.php" class="btn-close" aria-label="Close"></a>
                </div>
                <form action="doctor-list.php" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                    <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                        <input type="hidden" name="doctor_id" value="<?php echo $react_id ?>">
                        <input type="hidden" name="doctor_acc_id" value="<?php echo $react_acc_id; ?>">

                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <?php
                                $profile_image = !empty($react_doctor['Profile_img']) && file_exists('../images/' . $react_doctor['Profile_img'])
                                    ? '../images/' . $react_doctor['Profile_img']
                                    : '../images/team_placeholder.jpg';
                                ?>
                                <img src="<?= htmlspecialchars($profile_image) ?>"
                                    id="profilePreview"
                                    class="rounded-circle border"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <h6 class=" mt-2"><?= htmlspecialchars($react_first_name . ' ' . $react_last_name) ?></h6>
                            <p class="text-muted fw-light small">Doctor ID: <?= htmlspecialchars($react_acc_id) ?></p>
                        </div>
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="p-3 bg-white rounded d-flex justify-content-between mb-3">
                                            <small class="text-muted d-block">Status</small>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-circle-exclamation text-danger me-2"></i>
                                                <span class="badge bg-danger text-white"><?= htmlspecialchars($react_status) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="p-3 bg-white rounded d-flex justify-content-between ">
                                            <small class="text-muted d-block">Specialization</small>
                                            <div class="d-flex align-items-center">
                                                <span class=" text-primary small"><?= htmlspecialchars($react_specialization) ?></span>
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
                                            <input id="confirmInput1" type="text" class="form-control rounded-0" placeholder="Type 'CONFIRM'" name="react_reason" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <a href="doctor-list.php" class="btn btn-light rounded-0">Cancel</a>
                        <button class="btn btn-sm btn-primary rounded-0" id="confirmButton1" disabled name="reactivateButton">
                            <i class="fa-solid fa-rotate me-2"></i>Reactivate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /REACTIVATE MODAL PROFILE -->




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
    <script>
        // Handle closing the modal with backdrop click
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    window.location.href = 'doctor-list.php';
                }
            });
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
        $(document).ready(function() {
            // Initialize DataTable with custom options
            $('#myTable').DataTable({
                "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
                "pageLength": 10,
                "ordering": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "search": "<i class='fa fa-search'></i>",
                    "searchPlaceholder": "Search doctors...",
                    "lengthMenu": "_MENU_ per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ doctors",
                    "infoEmpty": "No doctors found",
                    "infoFiltered": "(filtered from _MAX_ total doctors)"
                }
            });

            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl =>
                new bootstrap.Tooltip(tooltipTriggerEl)
            );
        });


        // Preview profile image before upload
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
        function setupConfirmInput(inputId, buttonId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(buttonId);
            if (input && button) {
                input.addEventListener('input', function() {
                    button.disabled = (this.value !== 'CONFIRM');
                });
            }
        }

        setupConfirmInput('confirmInput', 'confirmButton');
        setupConfirmInput('confirmInput1', 'confirmButton1');
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($show_modal): ?>
            var modal = new bootstrap.Modal(document.getElementById('updateModal'));
            modal.show();
        <?php endif; ?>

        <?php if ($show_modal_deact): ?>
            var modalDeact = new bootstrap.Modal(document.getElementById('deactModal'));
            modalDeact.show();
        <?php endif; ?>

        <?php if ($show_modal_react): ?>
            var modalReact = new bootstrap.Modal(document.getElementById('reactivateModal'));
            modalReact.show();
        <?php endif; ?>
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

        .not-allowed {
            cursor: not-allowed !important;
        }
    </style>

    <?php include "../includes/script.php"; ?>