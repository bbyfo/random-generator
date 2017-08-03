<?php

$output = "";
// Connect to database server
$db = mysql_connect("localhost", "phptojs_dev", "");
if (!$db) {
  die("Database connection failed miserably: " . mysql_error());
}
// Select the database
$db_select = mysql_select_db("phptojs_dev", $db);
if (!$db_select) {
  die("Database selection also failed miserably: " . mysql_error());
}

//Step3
$amt = (isset($_GET['amt']) ? $_GET['amt'] : 1);
//print_r($amt);



$result = mysql_query("SELECT * FROM sampledata ORDER BY RAND() limit $amt", $db);
if (!$result) {
  die("Database query failed: " . mysql_error());
}
//Step4

$output .= "<table border='1'>";
$output .="<thead>";
$output .= "<th>Sample ID</th>";
$output .= "<th>Sample Title</th>";
$output .= "</thead>";
while ($row = mysql_fetch_array($result)) {
  $output .= "<tr>";
  $output .= "<td>";
  $output .= $row['sid'];
  $output .= "</td>";
  $output .= "<td>";
  $output .= $row['title'];
  $output .= "</td>";
  $output .= "</tr>";
}

echo $output;
?>
