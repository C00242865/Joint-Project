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
  <a href="updateResults.php" class="active">Update Results</a>
  <a href="deletePatient.php">Delete Patient</a>
  <a href="adminLogout.php">Logout</a>
</div>
<p><div class = "p">
<h3> Update Patient Vaccination and Test Details</h3>
<?php include 'listbox.php'; ?>
<script>
  //Second Year Notes
function populate()
{
    var sel = document.getElementById("listbox");
    var result;
    result = sel.options[sel.selectedIndex].value;
    document.getElementById("update_pps").value = result;
}
</script>
<form name = "myForm" action="update.php" method = "post">
    <label for "update_pps">PPS </label>
    <br>
    <input type = "text" name = "update_pps" id = "update_pps"readonly>
    <br>
    <label for "update_vac">Vaccination State </label>
    <br>
    <select name = "update_vac" id = "update_vac">
    <option value="Not Vaccinated">Not Vaccinated</option>
    <option value="Vaccination Pending">Vaccination Pending</option>
    <option value="Vaccinated">Vaccinated</option>
    </select>
    <br>
    <label for "update_test">Test State </label>
    <br>
    <select name = "update_test" id = "update_test">
    <option value="Not tested">Not tested</option>
    <option value="Test Pending">Test Pending</option>
    <option value="Test Positive">Test Positive</option>
    <option value="Test Negative">Test Negative</option>
    </select>
    <br>
    <br>
    <input type = "submit" value = "Save Changes" >
</form>
</p></div> </div>
</body>
<footer class = "footer">
  <p>Author: Jonathon Bourke</p>
  <p><a href="mailto:C00242865@itcarlow.ie">Contact us</a></p>
</footer>
</html>