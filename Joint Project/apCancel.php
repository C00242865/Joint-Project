<?php 
    session_start();
    include "db.php";
    $apId = $_POST['apId'];
    $sql = "DELETE FROM appointment WHERE apId = $apId";
    if(mysqli_query($dbconnect, $sql))
    {
        echo "Appointment deleted successfully.";
    }
    else
    {
        echo "Something went wrong";
    }
?>