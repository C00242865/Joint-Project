<?php
?>
<html>
<head><link href="css/menu.css" rel="stylesheet"></head>

<body>
<div class = "header"><h1>Covid - 19 Booking Application</h1></div>
<div class = "display">
<div class="menu">
  <a href="menu.php">Home</a>
  <a href="appointment.php" class="active">Book an Appointment</a>
  <a href="test.php">Check Your Test Results</a>
  <a href="logout.php">Logout</a>
</div>

 <div class="tab">
  <button class="appointment" onclick="booking(event, 'CovidTest')">Covid Test</button>
  <button class="appointment" onclick="booking(event, 'Vaccination')">Vaccination</button>
  <button class="appointment" onclick="booking(event, 'Cancel')">Cancellation</button>
</div>

<div id="CovidTest" class="tabcontent">
  <h3>Book your Test</h3>
<form action="testbook.php" class = "form" method="post">
<p>
<label for="dateinput">Date:</label>
<input type="date" class = "input" name="date" id="dateinput" required pattern="\d{4}-\d{2}-\d{2}" required>
</p>
<p>
<label for="settime">Time:</label>
<input type="time" placeholder = "hh:mm"class = "input" name="settime" min = "9:00" max = "17:00" step = 1800 id="settime" required>
</p>
<p>
<label for="TestCenter">Test Center location:</label>
<select name = "TestCenter" required>
    <option value="Dublin">Dublin</option>
    <option value="Cork">Cork</option>
    <option value="Limerick">Limerick</option>
    <option value="Galway">Galway</option>
</select></p>
<input type="submit" value="Submit">
</form>
</div>

<div id="Vaccination" class="tabcontent">
  <h3>Book your Vaccination</h3>
  <form action="apbook.php" class = "form" method="post">
<label for="dateinput">Date:</label>
<input type="date" name="date" id="dateinput" required pattern="\d{4}-\d{2}-\d{2}" required>
</p>
<p>
<label for="settime">Time:</label>
<input type="time" placeholder = "First Name" name="settime" min = "9:00" max = "17:00" step = 1800 id="settime" required>
</p>
<p>
<label for="TestCenter">Test Center location:</label>
<select name = "TestCenter" required>
    <option value="Dublin">Dublin</option>
    <option value="Cork">Cork</option>
    <option value="Limerick">Limerick</option>
    <option value="Galway">Galway</option>
  </select></p>
<input type="submit" value="Submit"></form>
</div>
<div id="Cancel" class="tabcontent">
  <h3>Cancel an Appointment</h3>
  <p>Current Appointment: 
  <?php 
  include "db.php";
  session_start();
  $myId = $_SESSION['id'];
  $cipher = "AES-128-CBC";
  $sql = "SELECT patient.id,appointment.apId, appointment.apType, appointment.apTime, appointment.apDate, encrypttable.randkey, encrypttable.iv FROM patient INNER JOIN encrypttable ON encrypttable.pps_no LIKE patient.pps_no INNER JOIN appointment ON patient.pps_no LIKE appointment.pps_no WHERE '$myId' LIKE patient.id";
  $result = $dbconnect-> query($sql);
  if ($result->num_rows == 1) 
  {
      $row = $result->fetch_assoc();
      $decipher_type = openssl_decrypt(hex2bin($row['apType']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']));
      $decipher_apDate = date('y-m-d', strtotime(openssl_decrypt(hex2bin($row['apDate']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']))));
      $decipher_apTime = date('h:i:s', strtotime(openssl_decrypt(hex2bin($row['apTime']), $cipher, hex2bin($row['randkey']), OPENSSL_RAW_DATA, hex2bin($row['iv']))));
      $apId = $row['apId'];
      echo $decipher_type;
      echo " booking at ";
      echo $decipher_apDate;
      echo " ";
      echo $decipher_apTime;
      echo "<form action = 'apCancel.php' class = 'form' method = 'post'>";
      echo "<input type='hidden' id='apId' name='apId' value='$apId'>";
      echo "<input type='checkbox' id='agree' name='agree' value='agree' required>";
      echo "<label for='agree'> I want to cancel my appointment</label><br>";
      echo "<input type='submit' value='Submit'></form>";
  }
  else
  {
      echo "No appointment scheduled";
  }
  ?>
  </p>
</div>

</div>
<script>
function booking(evt, bookingType) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(bookingType).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>
</body>
<footer class = "footer">
  <p>Author: Jonathon Bourke</p>
  <p><a href="C00242865@itcarlow.ie">Contact us</a></p>
</footer>
</html>