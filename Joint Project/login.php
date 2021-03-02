<?php 
include "db.php";
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: menu.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    $email_address = trim(strip_tags($_POST["email"]));
    $pass = ($_POST["password"]);
    $cipher = 'AES-128-CBC';
    $sql = "SELECT patient.pps_no, encrypttable.randkey, encrypttable.iv, patient.first_name,patient.id,patient.pass, patient.email_address FROM patient INNER JOIN encrypttable WHERE patient.pps_no LIKE encrypttable.pps_no";

  
    $isfound = false;

    $result = $dbconnect-> query($sql);
     if ($result->num_rows > 0) {
        while(($row = $result->fetch_assoc()) && $isfound == false)
        {
            $decipher_email = openssl_decrypt(hex2bin($row['email_address']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
            if($decipher_email == $email_address)
            {
                echo "working?";
                if(password_verify($pass, $row['pass']))
                {
                    session_start();
                    $fname = openssl_decrypt(hex2bin($row['first_name']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
                    $pps = openssl_decrypt(hex2bin($row['pps_no']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $row['id'];
                    $_SESSION["fname"] = $fname;
                    $_SESSION["pps"] = $pps;
                    $isfound = true; 
                    mysqli_close($dbconnect);                        
                    header("location: menu.php");
                }
            }

            echo "Executing?";
        }
        mysqli_close($dbconnect);
    }
}
?>
<link href="css/loginform.css" rel="stylesheet">
<html>
<head>
<title>Login Screen</title>
</head>
<body>
<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
<p>
<div class = "loginform">
<label for="email">Email Address:</label>
<input type="text" placeholder = "Enter Email" name="email" id="email" required>
</p>
<p>
<label for="password">Password:</label>
<input type="password" placeholder="Enter Password" name="password" id="password" required>
</p>
</div>
<a href="register.php">Register an account</a>
<input type="submit" value="Submit">
</form>
</body>
</html>

