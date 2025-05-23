<?php 
include "../includes/header.php";
include "../includes/sidebar-patient.php";
require_once "../connection/globalConnection.php";


if (session_status()  == PHP_SESSION_NONE) {
    session_start();
}
$con = connection();


?>
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="container-fluid pt-4 px-4">
      <div class=" d-flex justify-content-between mb-3 ">
        <h5 class=" fw-light"><span class="text-muted">List</span><span class="text-dark">/Appointment List</span></h5>
        <a href="../patient/add.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Appointment</a>
    </div>
 <div class="col-lg-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="table-responsive">
                    <table id="myTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <td>Appt ID</td>
                                <td>Doctor Name</td>
                                <td>Appointment Type</td>
                                <td>Appointment Date</td>
                                <td>Status</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM  appointmenttb";
                            $result = $con->query($sql);
                            if ($result->num_rows > 0) {
                                while ($rows = $result->fetch_assoc()) {

                                    //$fullname = $rows["First_Name"] . " " . $rows["Middle_Name"] . " " . $rows["Last_Name"];
                                    echo "<tr>
                                <td>" . $rows["appt_id"] . "</td>
                                <td>" . $rows["doctor_name"] . "</td>
                                <td>" . $rows["appt_type"] . "</td>
                                <td>" . $rows["appt_date"] . "</td>
                                <td>" . $rows["status"] . "</td> 
                                <td><div class='float-end'>
                                <a class='btn btn-sm btn-primary' href='doctor-list.php?edit=" . $rows['appt_id'] . "'><i class='fa-solid fa-pencil'></i></a>
                                <button class='btn btn-sm btn-danger' href='doctor-list.php?delete=" . $rows['appt_id'] . "'><i class='fa-solid fa-user-xmark'></i></button>
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

<?php 

include "../includes/script.php";

?>
<script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>