<?php
session_start();

if(session_destroy()){
    header("Location: login.php");
    exit();
} else {
    echo "Error logging out. Please try again.";
}
?>