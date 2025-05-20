<?php
include("../includes/header.php");
include("../includes/sidebar.php");
require_once("../connection/globalConnection.php");
$con = connection();

$showToast = false;
$toastMessage = '';
$isSuccess = true;

if (isset($_POST['save'])) {

    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename']; 
    $lastname = $_POST['lastname']; 
    $gender = $_POST['gender']; 
    $birthdate = $_POST['birthdate']; 
    $age = $_POST['age'];
    $pnum = $_POST['pnum'];
    $address = $_POST['address'];
    $eaddress = $_POST['emailaddress'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $specialization = $_POST['specialization'];
    $mnl = $_POST['mnl'];
    $affiliation = $_POST['affiliation'];
    $biography = $_POST['biography'];



    //data for insertion to database
    if (
        empty($firstname) || empty($lastname) || empty($age) || empty($pnum) || empty($address) || empty($eaddress) ||
        empty($username) || empty($password || empty($middlename) || empty($gender) || empty($birthdate) || empty($specialization) ||
        empty($mnl) || empty($affiliation))
    ) {
        $showToast = true;
        $toastMessage = "Please fill the required field";
        $isSuccess = false;
        $con->connect_error;
    } else {
        try {
            $fname = $con->real_escape_string($firstname);
            $lname = $con->real_escape_string($lastname);
            $page = $con->real_escape_string($age);
            $contact = $con->real_escape_string($pnum);
            $addr = $con->real_escape_string($address);
            $email = $con->real_escape_string($eaddress);
            $user = $con->real_escape_string($username);
            $pword = $con->real_escape_string($password);
            $mname = $con->real_escape_string($middlename);
            $sex = $con->real_escape_string($gender);
            $bday = $con->real_escape_string($birthdate);
            $specialty = $con->real_escape_string($specialization);
            $medlicense = $con->real_escape_string($mnl);
            $aff = $con->real_escape_string($affiliation);
            $bio = $con->real_escape_string($biography);
            $userType = "Doctor";


            $sql_login = "INSERT INTO userlogintb (User_Name, Password, User_Type) 
                VALUES ('$user', '$pword', '$userType')";

            if ($con->query($sql_login) === TRUE) {
                $login_doctor_id = $con->insert_id;

                $sql_doctor = "INSERT INTO doctortb (doctor_acc_id, First_Name, Middle_Name, Last_Name, Gender, Date_Birth, Age, Phone_Number, Address, Email_address,
                Specialization, Med_lic_num, Affiliation, Biography) 
                VALUES ('$login_doctor_id', '$fname', '$mname', '$lname','$sex', '$bday', '$age', '$pnum', '$addr', '$email', '$specialty', '$medlicense', '$aff', '$bio')";

                if ($con->query($sql_doctor) === TRUE) {
                    $con->commit();
                    $showToast = true;
                    $toastMessage = "Successfully Added!";
                    $isSuccess = true;
                } else {
                    throw new Exception("Error adding doctor: " . $con->error);
                }
            } else {
                throw new Exception("Error adding login: " . $con->error);
            }
        } catch (Throwable $e) {
            $con->rollback();
            $showToast = true;
            $toastMessage = $e->getMessage();
            $isSuccess = false;
        }
    }
} else {
    $con->connect_error;
}

?>
<div class="container-fluid pt-4 px-4">
    <div class=" d-flex justify-content-between">
        <h5 class=" fw-light"><a href="../admin/doctor-list.php"><span class="text-muted">Doctor</span></a>
            <span class="text-dark"> / Add New</span>
        </h5>
    </div>

    <div class="row py-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="" method="post">
                        <h6 class="mb-2">Personal Information</h6>
                        <div class="alert alert-success p-2" role="alert">
                            <span style="font-size: 14px;" class="fst-italic"> Please fill the required field</span>
                        </div>
                        <div class="row">
                            <div class="col-lg-4"><input name="firstname" type="text" class=" form-control mb-4" placeholder="First name *"></div>
                            <div class="col-lg-4"><input name="middlename" type="text" class=" form-control mb-4" placeholder="Middle name *"></div>
                            <div class="col-lg-4"><input name="lastname" type="text" class=" form-control mb-4" placeholder="Last name *"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3"><input name="age" type="number" class=" form-control mb-4" placeholder="Age *"></div>
                            <div class="col-lg-3">
                                <select name="gender" class="form-select">
                                    <option value="" disabled selected hidden>Select gender *</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-lg-3"><input type="text" id="birthdate" name="birthdate" class="form-control mb-4" placeholder="Date of Birth *" onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
                                </div>
                            <div class="col-lg-3"><input name="pnum" type="tel" class=" form-control mb-4" placeholder="Phone number *"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12"><input name="address" type="text" class=" form-control mb-4" placeholder="Home Address *"></div>
                            <div class="col-lg-12"><input name="emailaddress" type="email" class=" form-control" placeholder="Email address *"></div>
                        </div>
                        <hr>
                        <h6 class="mb-2">Professional Information</h6>
                        <div class="alert alert-success p-2" role="alert">
                            <span style="font-size: 14px;" class="fst-italic"> Please fill the required field</span>
                        </div>
                            <div class="row">
                            <div class="col-lg-12"><input name="specialization" type="text" class=" form-control mb-4" placeholder="Specialization *"></div>
                            <div class="col-lg-12"><input name="mnl" type="text" class=" form-control mb-4" placeholder="Medical License Number *"></div>
                            <div class="col-lg-12"><input name="affiliation" type="text" class=" form-control mb-4" placeholder="Affiliation *"></div>
                            <div class="col-lg-12"><textarea style="height: 120px" name="biography" class=" form-control" placeholder="Your biography here.."></textarea></div>
                        </div>
                        <hr>
                        <h6 class="mb-2 mt-2">Account Information</h6>
                        <div class="alert alert-success p-2" role="alert">
                            <span style="font-size: 14px;" class="fst-italic"> Please fill the required field</span>
                        </div>
                        <div class="row">
                            <div class="col-lg-12"><input name="username" type="text" class=" form-control mb-4" placeholder="User name *"></div>
                            <div class="col-lg-6"><input name="password" type="password" class=" form-control mb-4" placeholder="Password *"></div>
                            <div class="col-lg-6"><input type="password" class=" form-control mb-4" placeholder="Confirm password *"></div>
                        </div>
                        <input name="save" type="submit" value="Save" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
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
    <?php include("../includes/script.php"); ?>
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