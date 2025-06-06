<?php
include "../includes/header.php";
include "../includes/sidebar-admin.php";
require_once "../connection/globalConnection.php";

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
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
    <div class=" d-flex justify-content-between mb-3 ">
        <h5 class=" fw-light"><span class="text-muted">List</span><span class="text-dark">/Staff List</span></h5>
        <a href="../admin/add-staff.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Staff</a>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="table-responsive">
                    <table id="myTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <td>Staff Id</td>
                                <td>Full Name</td>
                                <td>Date Birth</td>
                                <td>Contact Number</td>
                                <td>Email Address</td>
                                <td> Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM  stafftb";
                            $result = $con->query($sql);
                            if ($result->num_rows > 0) {
                                while ($rows = $result->fetch_assoc()) {

                                    $fullname = $rows["First_Name"] . " " . $rows["Last_Name"];
                                    echo "<tr>
                                <td>" . $rows["id"] . "</td>
                                <td>" . $fullname . "</td>
                                <td>" . $rows["Date_Birth"] . "</td>
                                <td>" . $rows["Email_address"] . "</td> 
                                <td>" . $rows["Phone_Number"] . "</td>
                                <td><div class='float-end'>
                                <a class='btn btn-sm btn-primary' href='staff-list.php?edit=" . $rows['id'] . "'><i class='fa-solid fa-pencil'></i></a>
                                <button class='btn btn-sm btn-danger' href='staff-list.php?delete=" . $rows['id'] . "'><i class='fa-solid fa-trash'></i></button>
                                </div></td>
                                </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade <?php echo $show_modal ? 'show' : ''; ?>" id="updateModal" tabindex="-1"
        aria-labelledby="updateModalLabel" aria-hidden="true"
        style="<?php echo $show_modal ? 'display: block; background-color: rgba(0,0,0,0.5);' : ''; ?>">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update staff</h5>
                    <a href="staff-list.php" class="btn-close" aria-label="Close"></a>
                </div>
                <form action="staff-list.php" method="POST">
                    <div class="modal-body" style="overflow-y: scroll; height:75vh">
                        <input type="hidden" name="staff_id" value="<?php echo $edit_id; ?>">
                        <input type="hidden" name="staff_acc_id" value="<?php echo $edit_acc_id; ?>">

                        <h6 class="mb-2">Personal Information</h6>
                        <div class="alert alert-success p-2" role="alert">
                            <span style="font-size: 14px;" class="fst-italic"> Please fill the required field</span>
                        </div>
                        <div class="row">
                            <div class="col-lg-4"> <input type="text" class="form-control mb-4" id="first_name" name="first_name"
                                    value="<?php echo $edit_first_name; ?>" required></div>
                            <div class="col-lg-4"> <input type="text" class="form-control mb-4" id="middle_name" name="middle_name"
                                    value="<?php echo $edit_middle_name; ?>"></div>
                            <div class="col-lg-4"> <input type="text" class="form-control mb-4" id="last_name" name="last_name"
                                    value="<?php echo $edit_last_name; ?>" required></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3"><input name="age" type="number" value="<?php echo $edit_age; ?>" class=" form-control mb-4" placeholder="Age *"></div>
                            <div class="col-lg-3">
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo ($edit_gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($edit_gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            <div class="col-lg-3"><input type="text" id="birthdate" name="birthdate" value="<?php echo $edit_birthdate; ?>" class="form-control mb-4" placeholder="Date of Birth *" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                            </div>
                            <div class="col-lg-3"><input name="pnum" type="tel" value="<?php echo $edit_phone; ?>" class=" form-control mb-4" placeholder="Phone number *"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6"><input name="address" type="text" value="<?php echo $edit_home_address; ?>" class=" form-control mb-4" placeholder="Home Address *"></div>
                            <div class="col-lg-6"><input name="emailaddress" type="email" value="<?php echo $edit_email; ?>" class=" form-control" placeholder="Email address *"></div>
                        </div>
                        <hr>
                        <h6 class="mb-2">Professional Information</h6>
                        <div class="alert alert-success p-2" role="alert">
                            <span style="font-size: 14px;" class="fst-italic"> Please fill the required field</span>
                        </div>
                        <div class="row">
                            <div class="col-lg-12"><input name="specialization" type="text" value="<?php echo $edit_specialization; ?>" class=" form-control mb-4" placeholder="Specialization *"></div>
                            <div class="col-lg-12"><input name="mnl" type="text" value="<?php echo $edit_mnl; ?>" class=" form-control mb-4" placeholder="Medical License Number *"></div>
                            <div class="col-lg-12"><input name="affiliation" type="text" value="<?php echo $edit_affiliation; ?>" class=" form-control mb-4" placeholder="Affiliation *"></div>
                            <div class="col-lg-12"><textarea style="height: 120px" name="biography" class=" form-control" placeholder="Your biography here.."><?php echo $edit_biography; ?></textarea></div>
                        </div>
                        <hr>
                        <h6 class="mb-2 mt-2">Account Information</h6>
                        <div class="alert alert-success p-2" role="alert">
                            <span style="font-size: 14px;" class="fst-italic"> Please fill the required field</span>
                        </div>
                        <div class="row">
                            <div class="col-lg-6"><input name="username" type="text" value="<?php echo $edit_username; ?>" class=" form-control mb-4" placeholder="User name *"></div>
                            <div class="col-lg-6"><input name="password" type="password" value="<?php echo $edit_password; ?>" class=" form-control mb-4" placeholder="Password *"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="staff-list.php" class="btn btn-secondary">Close</a>
                        <input type="submit" class="btn btn-primary" name="btnupdate" value="Save changes">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999999">
        <div id="loginToast" class="toast <?php echo $showToast ? 'show' : ''; ?>" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header <?php echo $isSuccess ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                <strong class="me-auto" id="toastTitle"><?php echo $isSuccess ? 'Success' : 'Error'; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
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
                    window.location.href = 'staff-list.php';
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
            $('#myTable').DataTable();
        });
    </script>
<?php include "../includes/script.php";?>