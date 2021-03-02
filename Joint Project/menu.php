<?php
session_start();
if(!isset($_SESSION["loggedin"]))
{
    header("location: login.php");
    exit;
}
?>
<html>
<head><link href="css/menu.css" rel="stylesheet"></head>

<body>
<div class = "header"><h1>Covid - 19 Booking Application</h1></div>
<div class = "display">
<div class="menu">
  <a href="menu.php" class="active">Home</a>
  <a href="appointment.php">Book an Appointment</a>
  <a href="test.php">Check Your Test Results</a>
  <a href="logout.php">Logout</a>
</div>
<p><div class = "p"><h3>Hello <?php echo $_SESSION["fname"];?></h2><br><br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vel sagittis risus. Sed odio leo, venenatis non nunc in, bibendum semper quam. Sed aliquam feugiat dignissim. Duis dignissim, ligula eget rhoncus scelerisque, leo arcu tempor nulla, quis fringilla turpis erat quis elit. Proin vehicula purus id congue lacinia. Quisque ac tincidunt risus. Curabitur at fringilla tortor. Curabitur vel lectus turpis. Phasellus id venenatis nunc. In hac habitasse platea dictumst. Suspendisse euismod lobortis tincidunt.

In faucibus turpis ut nibh iaculis porta. Praesent vitae tincidunt nulla, sed lobortis eros. Nam lectus ante, gravida id ligula vitae, imperdiet consequat neque. Vestibulum tempor diam ut dolor facilisis, eu interdum risus ultrices. Donec accumsan magna ac leo mattis bibendum. Etiam venenatis sed ante quis dictum. Morbi tempor tempus nulla eu lacinia. Morbi imperdiet mauris vitae purus accumsan, in aliquet erat scelerisque. Maecenas enim dui, accumsan sed interdum dapibus, interdum in lorem. Sed erat urna, commodo ut egestas in, cursus non risus. Fusce justo purus, posuere vitae rutrum volutpat, ultricies ut libero. Curabitur ipsum erat, rhoncus at ornare in, commodo ac justo.

</p></div> </div>
</body>
<footer class = "footer">
  <p>Author: Jonathon Bourke</p>
  <p><a href="mailto:C00242865@itcarlow.ie">Contact us</a></p>
</footer>
</html>