<?php

      // Connect to database server
    $dbhost = (getenv('MYSQL_HOST') ? getenv('MYSQL_HOST') : "localhost");
    $dbuser = (getenv('MYSQL_USER') ? getenv('MYSQL_USER') : "root");
    $dbpwd = (getenv('MYSQL_PASSWORD') ? getenv('MYSQL_PASSWORD') : "root");
    $dbname = (getenv('MYSQL_DATABASE') ? getenv('MYSQL_DATABASE') : "rpgaid");
    $mysqli = new mysqli($dbhost, $dbuser, $dbpwd, $dbname);



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
