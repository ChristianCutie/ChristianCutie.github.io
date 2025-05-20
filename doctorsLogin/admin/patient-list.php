<?php 
include "../includes/header.php";
include "../includes/sidebar.php";
require_once "../connection/globalConnection.php";
$con = connection();
?>
<div class="container-fluid pt-4 px-4">
    <div class=" d-flex justify-content-between mb-3 ">
        <h5 class=" fw-light"><span class="text-muted">List</span><span class="text-dark">/Patient List</span></h5>
        <a href="../admin/add-patient.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add Patient</a>
    </div>
    <div class="row">
        <div class="col-lg-12">
        <div class="card">
            <div class=" card-body">
                <div class="table-responsive">
                    <table id="myTable" class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <td>Patient Id</td>
                            <td>First Name</td>
                            <td>Last Name</td>
                            <td>Email Address</td>
                            <td>Action</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $sql = "SELECT * FROM  patienttb";
                        $result = $con-> query($sql);
                        if($result-> num_rows > 0){
                            while ($rows = $result -> fetch_assoc()){
                                echo "<tr>
                                <td>" . $rows["patient_acc_id"]. "</td>
                                <td>" . $rows["First_Name"] . "</td>
                                <td>" . $rows["Last_Name"] . "</td>
                                <td>" . $rows["Email_address"] . "</td> 
                                <td><div class='float-end'>
                                <button class='btn btn-sm btn-primary' onclick='viewDoctor(" . $rows["patient_id"] . ")'>View</button>
                                <button class='btn btn-sm btn-danger' onclick='viewDoctor(" . $rows["patient_id"] . ")'>Delete</button>
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
    </div>
     <?php include('../includes/script.php');?>
    <script>
    $(document).ready( function () {
        $('#myTable').DataTable();
    });
    </script>
   
