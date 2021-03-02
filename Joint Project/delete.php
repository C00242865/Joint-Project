<?php
include "db.php";
session_start();
if(!isset($_SESSION["loggedin"]))
{
    header("location: adminLogin.php");
    exit;
}
$cipher = 'AES-128-CBC';
$sql = "SELECT patient.pps_no, encrypttable.randkey, encrypttable.iv FROM patient INNER JOIN encrypttable WHERE patient.pps_no LIKE encrypttable.pps_no";
$result = $dbconnect-> query($sql);
if (($result->num_rows > 0)) 
{
    while($row = $result->fetch_assoc())
    {
        $decipher_pps = openssl_decrypt(hex2bin($row['pps_no']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        $cipher_pps = $row['pps_no'];
        if($decipher_pps == $_POST["update_pps"])
        {
            
            $sql = "DELETE FROM patient WHERE pps_no='$cipher_pps'";
            if(mysqli_query($dbconnect, $sql))
            {
                $sql = "DELETE FROM encrypttable WHERE pps_no='$cipher_pps'";
                if(mysqli_query($dbconnect, $sql))
                {
                    $sql = "DELETE FROM appointment WHERE pps_no='$cipher_pps'";
                    if(mysqli_query($dbconnect, $sql))
                    {
                        header("location: deletePatient.php");
                    }

                }
            }
            
        }
    }   
}
header("location: deletePatient.php");
?>