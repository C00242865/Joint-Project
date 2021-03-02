<?php
session_start();
if(!isset($_SESSION["loggedin"]))
{
    header("location: adminLogin.php");
    exit;
}
?>
<html>
<head><link href="css/menu.css" rel="stylesheet"></head>

<body>
<div class = "header"><h1>Covid - 19 Booking Application</h1></div>
<div class = "display">
<div class="menu">
  <a href="adminMenu.php" class="active">Home</a>
  <a href="updateResults.php">Update Results</a>
  <a href="deletePatient.php">Delete Patient</a>
  <a href="adminLogout.php">Logout</a>
</div>
<p><div class = "p"><h3>Access is prohibited unless authorized</h3>

</p></div> </div>
</body>
<footer class = "footer">
  <p>Author: Jonathon Bourke</p>
  <p><a href="mailto:C00242865@itcarlow.ie">Contact us</a></p>
</footer>
</html>