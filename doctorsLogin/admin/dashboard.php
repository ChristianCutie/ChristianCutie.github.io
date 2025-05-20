<?php
include "../includes/header.php";
include "../includes/sidebar.php";
require_once "../connection/globalConnection.php";

//$doc_id = $_SESSION['user_id'];

$con = connection();
/*Doctor List count*/
$sql = "SELECT COUNT(*) AS doctor_count FROM doctortb";
$result = $con->query($sql);
$doctor_count = 0;  
if($result-> num_rows > 0)
{
    $row = $result->fetch_assoc();
    $doctor_count = $row["doctor_count"];
}
/*/Doctor List count*/
?>
<!-- Sale & Revenue Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-user-doctor fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Doctor List</p>
                    <h6 class="mb-0"><?php echo $doctor_count;?></h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-chart-line fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Today Sale</p>
                    <h6 class="mb-0">$1234</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-chart-line fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Today Sale</p>
                    <h6 class="mb-0">$1234</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-chart-line fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Today Sale</p>
                    <h6 class="mb-0">$1234</h6>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 

include "../includes/script.php";

?>