<?php 
include "db.php";
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $user = trim(strip_tags($_POST['user']));
    $password_check = true;


    
    if(($_POST['password'] != $_POST['cpassword']))
    {
        $errorMsg .= "\nPasswords do not match\n";
        $password_check = false;
    }
    

    if ($password_check == true) 
    {
      
        $password = $_POST['password'];
        $pass = password_hash($password, PASSWORD_DEFAULT);
        $cipher = 'AES-128-CBC';
        $key = random_bytes(16);
        $iv = random_bytes(16);
        $hex_key = bin2hex($key);
        $hex_iv = bin2hex($iv);


        $cipher_user = bin2hex(openssl_encrypt($user, $cipher, $key, OPENSSL_RAW_DATA, $iv));

        $sql = "INSERT INTO doctor(user, pass, randkey, iv) VALUES (?, ?, ?, ?)";
        if($stmt = mysqli_prepare($dbconnect, $sql))
        {
            mysqli_stmt_bind_param($stmt,"ssss", $cipher_user, $pass, $hex_key, $hex_iv);
            if(mysqli_stmt_execute($stmt))
            {
                mysqli_stmt_close($stmt);
                mysqli_close($dbconnect);
                header("location: adminLogin.php");
            }
        }
        else
        {
            echo "Something went wrong!";
        }
    }
    else
    {
        echo $errorMsg;
    }
}


?>

<link href="css/loginform.css" rel="stylesheet">

<html>
<head>
<title>Administrator Portal</title>
</head>
<body>
<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
<p>
<label for="user">Username:</label>
<input type="text" placeholder = "Enter Username" name="user" id="user" required>
</p>
<p>
<label for="password">Password:</label>
<input type="password" placeholder="Enter Password" name="password" id="password" required>
</p>
<label for="cpassword">Confirm Password:</label>
<input type="password" placeholder="Enter Password" name="cpassword" id="cpassword" required>
</p>
<a href="adminLogin.php">Return to Admin Portal</a>
<input type="submit" value="Submit">
</form>
</div>
</body>
</html>