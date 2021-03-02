<?php
include "db.php";
session_start();
if(!isset($_SESSION["loggedin"]))
{
    header("location: login.php");
    exit;
}
$errormsg = "";
$datenow = strtotime("now");
$timenow  = strtotime("now");
$datenow = date('y-m-d', $datenow);
$timenow = date('h:i:s', $timenow);
$bookdate = date('y-m-d', strtotime($_POST['date']));
$booktime = date('h:i:s', strtotime($_POST['settime']));
echo $datenow;
echo $timenow;
echo $bookdate;
echo $booktime;
echo $_POST['TestCenter'];

if(($booktime < $timenow && $bookdate == $datenow)|| $bookdate < $datenow)
{
    $errormsg .= "\nAppointment time has expired\n";
}
else
{
    $isfound = false;
    $cipher = 'AES-128-CBC';
    $sql = "SELECT patient.pps_no,patient.vac_state,patient.test_state, encrypttable.randkey, encrypttable.iv FROM patient INNER JOIN encrypttable WHERE patient.pps_no LIKE encrypttable.pps_no";
    $result = $dbconnect-> query($sql);
    if ($result->num_rows > 0) 
    {
        while(($row = $result->fetch_assoc()) && $isfound == false)
        {
            $iv = hex2bin($row['iv']);
            $key = hex2bin($row['randkey']);
            $decipher_pps = openssl_decrypt(hex2bin($row['pps_no']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
            $encrypted_pps = bin2hex($row['pps_no']);
            if($decipher_pps == $_SESSION["pps"])
            {
                $test = openssl_decrypt(hex2bin($row['test_state']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
                echo $test;
                if($test == "Not tested")
                {
                    $sql = "SELECT appointment.pps_no, appointment.apTime, appointment.apDate, appointment.apType, appointment.testCenter, encrypttable.randkey, encrypttable.iv FROM appointment INNER JOIN encrypttable WHERE appointment.pps_no LIKE encrypttable.pps_no";
                    $result = $dbconnect-> query($sql);
                    $conflict = false;
                    if ($result->num_rows > 0) 
                    {
                        while(($row = $result->fetch_assoc()) && $conflict == false)
                        {
                            $decipher_pps2 = openssl_decrypt(hex2bin($row['pps_no']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
                            $decipher_apType = openssl_decrypt(hex2bin($row['apType']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
                            $decipher_apDate = date('y-m-d', strtotime(openssl_decrypt(hex2bin($row['apDate']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']))));
                            $decipher_apTime = date('h:i:s', strtotime(openssl_decrypt(hex2bin($row['apTime']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']))));
                            $decipher_center = openssl_decrypt(hex2bin($row['testCenter']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
                            if($decipher_pps == $decipher_pps2)
                            {
                                $conflict = true;
                                $errormsg .= "You have already booked an appointment";
                            }
                            elseif($decipher_apDate == $bookdate && $decipher_apTime == $booktime && $decipher_center == $_POST['TestCenter'])
                            {
                                $conflict = true;
                                $errormsg .= "This timeslot is already taken";
                            }
                        
                        }
                    }
                    if($conflict == false)
                    {
                            $cipher_booktime = bin2hex(openssl_encrypt($booktime, $cipher, $key, OPENSSL_RAW_DATA, $iv));
                            $cipher_bookdate = bin2hex(openssl_encrypt($bookdate, $cipher, $key, OPENSSL_RAW_DATA, $iv));
                            $cipher_testcenter = bin2hex(openssl_encrypt($_POST['TestCenter'], $cipher, $key, OPENSSL_RAW_DATA, $iv));
                            $cipher_apType = bin2hex(openssl_encrypt("Test", $cipher, $key, OPENSSL_RAW_DATA, $iv));
                            $cipher_pps = bin2hex(openssl_encrypt($_SESSION['pps'], $cipher, $key, OPENSSL_RAW_DATA, $iv));

                            $sql = "INSERT INTO appointment(apTime, apDate, testCenter, pps_no, apType) VALUES (?, ?, ?, ?, ?)";
                            if($stmt = mysqli_prepare($dbconnect, $sql))
                            {
                                mysqli_stmt_bind_param($stmt,"sssss", $cipher_booktime, $cipher_bookdate, $cipher_testcenter, $cipher_pps, $cipher_apType);
                                if(mysqli_stmt_execute($stmt))
                                {
                                    mysqli_stmt_close($stmt);
                                    $sql = "UPDATE patient SET test_state = ? WHERE pps_no = ?";
                                    if($stmt = mysqli_prepare($dbconnect, $sql))
                                    {
                                        $param_teststate = bin2hex(openssl_encrypt("Test Pending", $cipher, $key, OPENSSL_RAW_DATA, $iv));;
                                        $param_pps_no = $cipher_pps;
                                        mysqli_stmt_bind_param($stmt, "ss", $param_teststate, $param_pps_no);
                                        
                                        if(mysqli_stmt_execute($stmt))
                                        {
                                            header("location: appointment.php");
                                            mysqli_close($dbconnect);
                                            exit();
                                        }
                                    }
                                }
        
                            }
                    }
                    
                } 
                else
                {
                    $isfound = true;
                    $errormsg .= "You are awaiting results of a previous appointment";
                }   
            }
        } 
    }  
}
echo $errormsg;
header("location: appointment.php");
exit();
?>