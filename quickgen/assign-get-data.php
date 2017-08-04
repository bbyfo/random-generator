<?php

$output = array();
// Connect to database server
$dbhost = (getenv('OPENSHIFT_MYSQL_DB_HOST') ? getenv('OPENSHIFT_MYSQL_DB_HOST') : "localhost");
$dbuser = (getenv('OPENSHIFT_MYSQL_DB_USERNAME') ? getenv('OPENSHIFT_MYSQL_DB_USERNAME') : "root");
$dbpwd = (getenv('OPENSHIFT_MYSQL_DB_PASSWORD') ? getenv('OPENSHIFT_MYSQL_DB_PASSWORD') : "root");

$mysqli = new mysqli($dbhost, $dbuser, $dbpwd, "rpgaid");

//Step3
$amt = (isset($_GET['amt']) ? $_GET['amt'] : 100);
$campaign = (isset($_GET['campaign']) ? $_GET['campaign'] : '0');

switch ($campaign) {

  case '0':
    $result = mysql_query("SELECT datakey, string, `range` FROM gen_data", $dbhost);
    if (!$result) {
      die("Database query failed: " . mysql_error());
    }
    while ($row = mysql_fetch_array($result)) {
      // Setting the value as an array works, but not for custom ranges.
      $output[$row['datakey']][] = $row['string'];
    }
    break;
};

//print "<pre>";
//print_r($output);
//print "</pre>";
echo json_encode($output);
?>
