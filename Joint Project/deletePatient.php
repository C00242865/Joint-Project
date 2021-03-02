<?php
session_start();
if(!isset($_SESSION["loggedin"]))
{
    header("location: adminLogin.php");
    exit;
}
?>
<html>
<head><link href="css/non.css" rel="stylesheet"></head>
<body>
<div class = "header"><h1>Covid - 19 Booking Application</h1></div>
<div class = "display">
<div class="menu">
<a href="adminMenu.php">Home</a>
  <a href="updateResults.php">Update Results</a>
  <a href="deletePatient.php"class="active">Delete Patient</a>
  <a href="adminLogout.php">Logout</a>
</div>
<p><div class = "p">
<h3> Delete Patient Details</h3>
<?php include 'dListbox.php'; ?>
<script>
  //Second Year Notes
function populate()
{
    var sel = document.getElementById("listbox");
    var result;
    result = sel.options[sel.selectedIndex].value;
    var patientDetails = result.split(','); 
    document.getElementById("update_pps").value = patientDetails[0];
    document.getElementById("update_fname").value = patientDetails[1];
    document.getElementById("update_lname").value = patientDetails[2];
    document.getElementById("update_email").value = patientDetails[3];
    document.getElementById("update_phone").value = patientDetails[4];
    document.getElementById("update_home").value = patientDetails[5];

}
</script>
<form name = "myForm" style = "non" action="delete.php" method = "post">
    <label for "update_pps">PPS </label>
    <input type = "text" name = "update_pps" id = "update_pps"readonly>
    <br>
    <label for "update_fname">First Name </label>
    <input type = "text" name = "update_fname" id = "update_fname"readonly>
    <br>
    <label for "update_lname">Last Name </label>
    <input type = "text" name = "update_lname" id = "update_lname"readonly>
    <br>
    <label for "update_email">Email </label>
    <input type = "text" name = "update_email" id = "update_email"readonly>
    <br>
    <label for "update_phone">Phone Number </label>
    <input type = "text" name = "update_phone" id = "update_phone"readonly>
    <br>
    <label for "update_home">Home Address </label>
    <input type = "text" name = "update_home" id = "update_home"readonly>
    <br>
    <input type='checkbox' id='agree' name='agree' value='agree' required>
    <label for='agree'>Confirm</label><br>
    <input type = "submit" value = "Delete" >
</form>
</p></div> </div>
</body>
<footer class = "footer">
  <p>Author: Jonathon Bourke</p>
  <p><a href="mailto:C00242865@itcarlow.ie">Contact us</a></p>
</footer>
</html>