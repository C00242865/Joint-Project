<?php
include "db.php";
$cipher = 'AES-128-CBC';
$sql = "SELECT patient.pps_no, encrypttable.randkey, encrypttable.iv, patient.vac_state, patient.test_state FROM patient INNER JOIN encrypttable ON patient.pps_no LIKE encrypttable.pps_no";
echo "<br><select name = 'listbox' id = 'listbox' onclick = 'populate()'>";
$result = $dbconnect-> query($sql);
//Second Year Notes
if ($result->num_rows > 0) 
{
    while($row = $result->fetch_assoc())
    {
        $decipher_pps = openssl_decrypt(hex2bin($row['pps_no']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        $decipher_vac = openssl_decrypt(hex2bin($row['vac_state']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        $decipher_test = openssl_decrypt(hex2bin($row['test_state']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
        echo "<option value = $decipher_pps>$decipher_pps</option>";
    }
    echo "</select>";
    mysqli_close($dbconnect);   
}
?>