<?php 
include "db.php";
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: adminMenu.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    $user = trim(strip_tags($_POST["user"]));
    $pass = ($_POST["password"]);
    $cipher = 'AES-128-CBC';
    $sql = "SELECT doctor.user, doctor.randkey, doctor.iv, doctor.pass FROM doctor";

  
    $isfound = false;

    $result = $dbconnect-> query($sql);
     if ($result->num_rows > 0) {
        while(($row = $result->fetch_assoc()) && $isfound == false)
        {
            $decipher_user = openssl_decrypt(hex2bin($row['user']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
            if($decipher_user == $user)
            {
                if(password_verify($pass, $row['pass']))
                {
                    session_start();
                    $_SESSION["loggedin"] = true;
                    $_SESSION["adId"] = $row['adId'];
                    $isfound = true; 
                    mysqli_close($dbconnect);                        
                    header("location: adminMenu.php");
                }
            }
        }
        mysqli_close($dbconnect);
    }
}
?>
<link href="css/loginform.css" rel="stylesheet">
<html>
<head>
<title>Admin Portal</title>
</head>
<body>
<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
<p>
<div class = "loginform">
<label for="user">Username:</label>
<input type="text" placeholder = "Enter Username" name="user" id="user" required>
</p>
<p>
<label for="password">Password:</label>
<input type="password" placeholder="Enter Password" name="password" id="password" required>
</p>
</div>
<a href="adminCreate.php">Create an Admin</a>
<input type="submit" value="Submit">
</form>
</body>
</html>