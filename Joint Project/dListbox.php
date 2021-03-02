<?php
include "db.php";
$cipher = 'AES-128-CBC';
$sql = "SELECT patient.id, patient.pps_no, encrypttable.randkey, encrypttable.iv, patient.vac_state, patient.test_state, patient.first_name, patient.last_name, patient.email_address, patient.home_address, patient.phone_no FROM patient INNER JOIN encrypttable ON patient.pps_no LIKE encrypttable.pps_no";
echo "<br><select name = 'listbox' id = 'listbox' onclick = 'populate()'>";
$result = $dbconnect-> query($sql);
//Second Year Notes
if ($result->num_rows > 0) 
{
    while($row = $result->fetch_assoc())
    {
        $decipher_pps = openssl_decrypt(hex2bin($row['pps_no']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        $decipher_fname = openssl_decrypt(hex2bin($row['first_name']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        $decipher_lname = openssl_decrypt(hex2bin($row['last_name']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        $decipher_email = openssl_decrypt(hex2bin($row['email_address']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        $decipher_phone = openssl_decrypt(hex2bin($row['phone_no']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        $decipher_home = openssl_decrypt(hex2bin($row['home_address']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        $concat_result = "$decipher_pps,$decipher_fname,$decipher_lname,$decipher_email,$decipher_phone,$decipher_home";
        echo "<option value = $concat_result>$decipher_pps</option>";
    }
    echo "</select>";
    mysqli_close($dbconnect);   
}
?>