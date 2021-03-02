<?php
include "db.php";
session_start();
if(!isset($_SESSION["loggedin"]))
{
    header("location: login.php");
    exit;
}
$isfound = false;
$cipher = 'AES-128-CBC';
$sql = "SELECT patient.pps_no,patient.vac_state,patient.test_state, encrypttable.randkey, encrypttable.iv FROM patient INNER JOIN encrypttable WHERE patient.pps_no LIKE encrypttable.pps_no";
$result = $dbconnect-> query($sql);
if ($result->num_rows > 0) 
{
    while(($row = $result->fetch_assoc()) && $isfound == false)
    {
        $decipher_pps = openssl_decrypt(hex2bin($row['pps_no']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        if($decipher_pps == $_SESSION["pps"])
        {
            $vac = openssl_decrypt(hex2bin($row['vac_state']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
            $test = openssl_decrypt(hex2bin($row['test_state']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
            $isfound = true;
        }
    }   
}
?>
<html>
<head><link href="css/menu.css" rel="stylesheet"></head>

<body>
<div class = "header"><h1>Covid - 19 Booking Application</h1></div>
<div class = "display">
<div class="menu">
  <a href="menu.php">Home</a>
  <a href="appointment.php">Book an Appointment</a>
  <a href="test.php" class="active">Check Your Test Results</a>
  <a href="logout.php">Logout</a>
</div>
<p><div class = "p">
</p>
<h3>Testing State</h3>
<p>
<?php echo $test; ?>
</p>
<h3>Vaccination State</h3>
<p>
<?php echo $vac; ?>
</p>
</div> </div>

</body>
<footer class = "footer">
  <p>Author: Jonathon Bourke</p>
  <p><a href="mailto:C00242865@itcarlow.ie">Contact us</a></p>
</footer>
</html>