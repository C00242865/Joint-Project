<?php 
include "db.php";
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $first_name = trim(strip_tags($_POST['fname']));
    $last_name = trim(strip_tags($_POST['sname']));
    $email_address = trim(strip_tags($_POST['email']));
    $home_address = strip_tags($_POST['haddress']);
    $pps_no = trim(strip_tags(strtoupper($_POST['pps'])));
    $password_check = 0;
    $valid_pps = 0;
    $valid_email = true;
    $namecheck = true;
    $home_check = true;
    $phone_check = true;
    $phone = trim(strip_tags($_POST['phone']));
    $errorMsg = "";


    if(!preg_match('/[0-9]{9,}/', $phone) || (strlen($phone) > 11 || strlen($phone) < 9) || preg_match('/[a-zA-Z@.,!£$%^*_={}()#~@;:]/', $phone))
    {
        $phone_check = false;
        $errorMsg .= "\nInvalid Phone Number\n";
    }
    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) 
    {
        $valid_email = false;
        $errorMsg .= "\nInvalid Email Address\n";
    }
    if(preg_match('/[@.,!£$%^*_+={}()#~@;:]/', $home_address))
    {
        $home_check = false;
        $errorMsg .= "\nInvalid Home Address\n";
    }
    if(!preg_match('/^[A-Z]+[a-z]+-*[A-Z]*[a-z]/', $first_name) || !preg_match('/^[A-Z]+[a-z]+-*[a-zA-Z]*/', $last_name) || preg_match('/[0-9@.,!£$%^*()_+={}#~@;:]/', $first_name) || preg_match('/[0-9@.,!£$%^*()_+={}#~@;:]/', $last_name))
    {
        $namecheck = false;
        $errorMsg .= "\nInvalid Name\n";
    }
    if(strlen($_POST['password'])  > 7 && preg_match('/[a-z]{1,}/', $_POST['password']) && preg_match('/[A-Z]{1,}/', $_POST['password']))
    {
        if(preg_match('/[0-9]{1,}/', $_POST['password']))
        {
            $password_check++;
        }
    }
    else 
    {
        $errorMsg .= "\nPassword does not meet minimum requirements\n";
    }
    if(($_POST['password'] != $_POST['cpassword']))
    {
        $errorMsg .= "\nPasswords do not match\n";
        $password_check--;
    }
    if(strlen($pps_no) > 7 && strlen($pps_no) < 10)
    {
        if(preg_match('/[0-9]{7,}/', substr($pps_no,0,7)))
        {
            echo preg_match('/[A-Z]{1,}/', substr($pps_no,-1));
            echo preg_match('/[A-Z]{2,}/', substr($pps_no,-2));

            if(strlen($pps_no) == 8)
            {
                if(preg_match('/[A-Z]{1,}/', substr($pps_no,-1)))
                {
                    $valid_pps++;
                }
            }
            elseif (strlen($pps_no) == 9) 
            {
                if(preg_match('/[A-Z]{2,}/', substr($pps_no,-2)))
                {
                    $valid_pps++;
                }
            }
        }
        else
        {
            $errorMsg .= "\nInvalid PPS String\n";
        }
        if($valid_pps == 1)
        {
            $isfound = false;
            $cipher = 'AES-128-CBC';
            $sql = "SELECT patient.pps_no, encrypttable.randkey, encrypttable.iv FROM patient INNER JOIN encrypttable WHERE patient.pps_no LIKE encrypttable.pps_no";
            $result = $dbconnect-> query($sql);
            if ($result->num_rows > 0) 
            {
                while(($row = $result->fetch_assoc()) && $isfound == false)
                {
                    $decipher_pps = openssl_decrypt(hex2bin($row['pps_no']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
                    if($decipher_pps == $pps_no)
                    {
                        $errorMsg .= "\nPPS already in use\n";
                        $isfound = true;
                        $valid_pps--;
                    }
                }
                    
            }
        }
    }

    if ($valid_pps == 1 && $password_check == 1 && $valid_email && $namecheck && $phone_check) 
    {
      
        $password = $_POST['password'];
        $pass = password_hash($password, PASSWORD_DEFAULT);
        $cipher = 'AES-128-CBC';
        $key = random_bytes(16);
        $iv = random_bytes(16);
        $test = "Not tested";
        $vacc = "Not Vaccinated";


        $cipher_fname = bin2hex(openssl_encrypt($first_name, $cipher, $key, OPENSSL_RAW_DATA, $iv));
        $cipher_lname = bin2hex(openssl_encrypt($last_name, $cipher, $key, OPENSSL_RAW_DATA, $iv));
        $cipher_email = bin2hex(openssl_encrypt($email_address, $cipher, $key, OPENSSL_RAW_DATA, $iv));
        $cipher_address = bin2hex(openssl_encrypt($home_address, $cipher, $key, OPENSSL_RAW_DATA, $iv));
        $cipher_pps = bin2hex(openssl_encrypt($pps_no, $cipher, $key, OPENSSL_RAW_DATA, $iv));
        $cipher_phone = bin2hex(openssl_encrypt($phone, $cipher, $key, OPENSSL_RAW_DATA, $iv));
        $cipher_tested = bin2hex(openssl_encrypt($test, $cipher, $key, OPENSSL_RAW_DATA, $iv));
        $cipher_vaccinated = bin2hex(openssl_encrypt($vacc, $cipher, $key, OPENSSL_RAW_DATA, $iv));

        $sql = "INSERT INTO patient(first_name, last_name, pass, email_address, home_address, pps_no, phone_no, vac_state, test_state) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($dbconnect, $sql))
        {
            mysqli_stmt_bind_param($stmt,"sssssssss", $cipher_fname, $cipher_lname, $pass, $cipher_email, $cipher_address, $cipher_pps, $cipher_phone, $cipher_vaccinated, $cipher_tested);
            if(mysqli_stmt_execute($stmt))
            {
                mysqli_stmt_close($stmt);
                    
                $sql = "INSERT INTO encrypttable(randkey, iv, pps_no) VALUES (?,?,?)";
                if($stmt = mysqli_prepare($dbconnect, $sql))
                {
                    $hex_key = bin2hex($key);
                    $hex_iv = bin2hex($iv);
                    mysqli_stmt_bind_param($stmt,"sss", $hex_key, $hex_iv, $cipher_pps);
                    if(mysqli_stmt_execute($stmt))
                    {
                            mysqli_stmt_close($stmt);
                    }
                    mysqli_close($dbconnect);
                    header("location: login.php");
                }
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

<link href="css/register.css" rel="stylesheet">

<html>
<head>
<title>Register</title>
</head>
<body>
<div class = "b">
<p class="agreement">Dear Sir/Madam,<br><br>
By signing up to use the Covid Test Book you are agreeing to:<br>
<br><br>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc varius, tellus id volutpat ultricies, justo massa cursus quam, nec accumsan augue ex ut arcu. Etiam pretium tincidunt metus, fringilla sagittis nisi varius id. Mauris cursus tortor vitae velit lacinia, ut fermentum tellus gravida. In eget porttitor mauris, non laoreet risus. Nulla facilisi. Maecenas ac finibus urna, ut congue sem. Etiam placerat scelerisque sapien, at posuere erat cursus a. Maecenas non ultricies felis, vel varius sapien. Quisque lectus diam, vulputate et tristique eu, eleifend vitae nisi. Vivamus sollicitudin pellentesque erat eget semper.

Ut lacinia sapien ut enim luctus, vitae imperdiet massa semper. Nam lacinia ultrices felis a porta. Aenean placerat varius elit, a tristique lectus maximus id. Nullam ante ligula, pretium vitae tristique quis, pellentesque ut lacus. Nam erat mi, viverra eget iaculis ut, venenatis iaculis odio. Maecenas egestas ultrices volutpat. Suspendisse lacinia massa sed lorem imperdiet, sed consequat metus facilisis. In vitae lectus metus. Vivamus consectetur mauris erat, sit amet rutrum sapien vulputate eget. Sed at sodales turpis. Cras nulla elit, malesuada in volutpat eu, pretium quis mauris. Suspendisse potenti.

Sed convallis vehicula porttitor. Pellentesque sagittis consequat dui tincidunt ornare. Aliquam blandit mattis ante, vitae bibendum tortor lacinia ac. Vestibulum vestibulum, ipsum et pellentesque pulvinar, augue neque suscipit libero, vel congue felis eros quis neque. Fusce ligula tellus, gravida at porta non, pharetra volutpat mauris. Suspendisse metus augue, bibendum sed dignissim eget, mollis eu nisl. Donec congue facilisis magna ut scelerisque. Quisque a elementum diam. Donec arcu turpis, bibendum in aliquam non, iaculis rutrum est. Praesent nunc odio, faucibus vitae malesuada vitae, mollis vel odio. Integer varius metus eget velit cursus rutrum. Aliquam vitae ultrices nisi. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;

In ligula felis, gravida sed dolor at, pretium tristique tortor. Sed sed lorem sed diam tristique facilisis. Pellentesque sed nisl in nulla posuere scelerisque sit amet at velit. Aliquam commodo gravida arcu, non feugiat odio mattis sit amet. Donec lobortis fringilla leo a vulputate. Donec pharetra euismod risus, a tristique lectus feugiat nec. Fusce rutrum, odio et semper iaculis, dui metus blandit ligula, in finibus urna dolor eu risus.

Praesent viverra sem commodo ipsum facilisis tristique. Cras pretium lorem at aliquam pretium. Donec facilisis porta aliquet. Suspendisse pellentesque tellus id facilisis tristique. Proin ligula erat, auctor in dui lobortis, hendrerit luctus lacus. Maecenas posuere massa ac sapien porta, in vehicula metus feugiat. Fusce cursus ex ac ultrices rutrum.
<br><br><br><br><br><br><br><br><br><br><br><br>
<mark>John Doe</mark>
</p>
<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
<p>
<label for="fname">First Name:</label>
<input type="text" placeholder = "First Name" name="fname" id="fname" required>
</p>
<p>
<label for="sname">Last Name:</label>
<input type="text" placeholder = "Last Name" name="sname" id="sname" required>
</p>
<p>
<label for="pps">PPS Number:</label>
<input type="text" placeholder = "Enter PPS" name="pps" id="pps" required>
</p>
<p>    
<label for="haddress">Home Address:</label>
<input type="text" placeholder = "Enter Address" name="haddress" id="haddress" required>
</p>
<p>
<label for="email">Email Address:</label>
<input type="text" placeholder = "Enter Email" name="email" id="email" required>
</p>
<p>
<label for="phone">Phone Number:</label>
<input type="text" placeholder = "Enter Phone Number" name="phone" id="phone" required>
</p>
<p>
<label for="password">Password:</label>
<input type="password" placeholder="Enter Password" name="password" id="password" required>
</p>
<label for="cpassword">Confirm Password:</label>
<input type="password" placeholder="Enter Password" name="cpassword" id="cpassword" required>
</p>
<input type="checkbox" id="agree" name="agree" value="agree" required>
<label for="agree"> I have read and agree with the terms and conditions</label><br>
<a href="login.php">Return to login screen</a>
<input type="submit" value="Submit">
</form>
</div>
</body>
</html>
