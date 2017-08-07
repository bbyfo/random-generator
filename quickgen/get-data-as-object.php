<?php


$output = new stdClass();
$output->formHelper = array();

// Connect to database server
$dbhost = (getenv('OPENSHIFT_MYSQL_DB_HOST') ? getenv('OPENSHIFT_MYSQL_DB_HOST') : "localhost");
$dbuser = (getenv('OPENSHIFT_MYSQL_DB_USERNAME') ? getenv('OPENSHIFT_MYSQL_DB_USERNAME') : "root");
$dbpwd = (getenv('OPENSHIFT_MYSQL_DB_PASSWORD') ? getenv('OPENSHIFT_MYSQL_DB_PASSWORD') : "root");

$mysqli = new mysqli($dbhost, $dbuser, $dbpwd, "rpgaid");

// DUMMY DATA 
//$campaign_params = array(0, 'SELECT * FROM campaigns;');

$campaign_params = (isset($_GET['campaign']) ? $_GET['campaign'] : 0);

/*
echo "<pre>";
var_dump($campaign_params);
echo "</pre>";
*/
$campaign_params_sanatized = array();
foreach($campaign_params as $p) {
  if(is_numeric($p)){
    $campaign_params_sanatized[] = $p;
  }
}

//var_dump($campaign_params_sanatized);

$cids_for_in_clause = implode(',', $campaign_params_sanatized);

//var_dump($cids_for_in_clause);

// Get the metadata.  This is used to assemble the final gen_data variable in the JS, which is used by the generator.js file.
$datakeys_sql = "SELECT DISTINCT
  tid,datakey,title
   FROM templates
  WHERE cid IN (".$cids_for_in_clause.")
  ORDER BY title";
// Execute the query
$datakeys_results = $mysqli->query($datakeys_sql);
// Use the query results to build the data
while ($row = $datakeys_results->fetch_array(MYSQLI_ASSOC)) {
  $stringholder = new stdClass();
  /*
    print "<pre>row: ";
    print_r($row);
    print "</pre>";
    // */

  // for each metadata datakey, grab all the actual data and prepare/assemble it for output
  $mystring = $row['datakey'];
  $strings_sql = "SELECT gdata.string, gdata.`range`, tpls.title
          FROM gen_data gdata
          INNER JOIN templates tpls ON gdata.tid = tpls.tid
          where datakey = '$mystring' ORDER BY title";
  $strings_result = $mysqli->query($strings_sql);

  $i = 1;
  while ($stringrow = $strings_result->fetch_array(MYSQLI_ASSOC)) {
    if ($stringrow['range'] != "") {
      $stringholder->$stringrow['range'] = $stringrow['string'];
    } else {
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
  // This formHelper is used to populate select lists...and maybe more in the future.
  $output->formHelper[$row['datakey']] = $row['title'];
};


// Encode and spit out the output
/*
  print "<pre>output: ";
  print_r($output);
  print "</pre>";
  // */

$output->params = $_GET;

echo json_encode($output);
?>
