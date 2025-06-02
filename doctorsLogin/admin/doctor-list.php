<?php   
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";

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

    $sql_update_doctor = "UPDATE doctortb SET First_Name = '$update_firstname', Middle_Name = '$update_middlename', Last_Name = '$update_lastname', 
     Gender = '$update_gender', Phone_Number = '$update_phone', Email_Address = '$update_email', Age = '$update_age', Date_Birth = '$update_birthdate', Address = '$update_home_address',
      Specialization = '$update_specialization', Med_lic_num = '$update_mnl', Affiliation = '$update_affiliation', Biography = '$update_biography' WHERE doctor_id = '$update_id'";

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

//delete data doctor
// if(isset($_GET['delete'])){
//     $delete_id = $_GET['delete'];

//     $sql_remove_data_doctor = "DELETE FROM USER_INFO WHERE ID = '$id'";
// }
?>
<div class="container-fluid pt-4 px-4">
    <!-- Header Section with Statistics -->
    <div class="row mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center p-4">
                <i class="fa fa-user-md fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Doctors</p>
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
                <i class="fa fa-stethoscope fa-3x text-success"></i>
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
                    $sql = "SELECT * FROM doctortb";
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
                                    <span class="badge bg-success-subtle text-success px-3 rounded-pill">
                                        Active
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
                                                onclick="deleteDoctor(<?= $row['doctor_id'] ?>)"
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
    </div>

    <!-- Modal -->
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

        // Add delete confirmation
        function deleteDoctor(doctorId) {
            if (confirm('Are you sure you want to delete this doctor?')) {
                window.location.href = `doctor-list.php?delete=${doctorId}`;
            }
        }

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