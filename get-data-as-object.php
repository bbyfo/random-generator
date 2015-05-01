<?php

$output = new stdClass();
$output->formHelper = array();
// Connect to database server
$db = mysql_connect($OPENSHIFT_MYSQL_DB_HOST, $OPENSHIFT_MYSQL_DB_USERNAME, $OPENSHIFT_MYSQL_DB_PASSWORD);
if (!$db) {
  die("Database connection failed miserably: " . mysql_error());
}
// Select the database
$db_select = mysql_select_db("rpgaid", $db);
if (!$db_select) {
  die("Database selection also failed miserably: " . mysql_error());
}

//Step3

$datakeys_result = mysql_query("SELECT DISTINCT datakey,title FROM gen_data ORDER BY title", $db);
if (!$datakeys_result) {
  die("Database query failed: " . mysql_error());
}

while ($row = mysql_fetch_array($datakeys_result)) {
$stringholder = new stdClass();
 /*
  print "<pre>row: ";
  print_r($row);
  print "</pre>";
  // */
  $mystring = $row['datakey'];
  $strings_result = mysql_query("SELECT string,`range`, title FROM gen_data where datakey = '$mystring' ORDER BY title", $db);

  $i = 1;
  while ($stringrow = mysql_fetch_array($strings_result)) {
if($stringrow['range'] != ""){
$stringholder->$stringrow['range'] = $stringrow['string'];
}else{
  $stringholder->$i = $stringrow['string'];
  $i++;
}
/*
    print "<pre>bobo: ";
    print_r($bobo);
    print "</pre>";
    // */
  }
$output->$row['datakey'] = $stringholder;
$output->formHelper[$row['datakey']] = $row['title'];
};


//Step4
 /*
  print "<pre>output: ";
  print_r($output);
  print "</pre>";
  // */
echo json_encode($output);
?>
