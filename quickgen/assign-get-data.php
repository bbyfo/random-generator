<?php

$output = array();
// Connect to database server
$db = mysql_connect("localhost", "root", "quickstart");
if (!$db) {
  die("Database connection failed miserably: " . mysql_error());
}
// Select the database
$db_select = mysql_select_db("rpgaid", $db);
if (!$db_select) {
  die("Database selection also failed miserably: " . mysql_error());
}

//Step3
$amt = (isset($_GET['amt']) ? $_GET['amt'] : 100);
$dataset = (isset($_GET['dataset']) ? $_GET['dataset'] : 'all');

switch ($dataset) {

  case 'all':
    $result = mysql_query("SELECT datakey, string, `range` FROM gen_data", $db);
    if (!$result) {
      die("Database query failed: " . mysql_error());
    }
    while ($row = mysql_fetch_array($result)) {
/*
      $output[$row['datakey']][] = array(
          'id' => $row['id'],
          'datakey' => $row['datakey'],
          'title' => $row['title'],
          'string' => $row['string']
      );
 *
 */
      // Setting the value as an array works, but not for custom ranges.
      $output[$row['datakey']][] = $row['string'];


     

    }
    break;
};


//Step4
//print "<pre>";
//print_r($output);
//print "</pre>";
echo json_encode($output);
?>
