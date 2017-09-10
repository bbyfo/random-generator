<?php
header('Content-Type: application/json');

$output = new stdClass();
$output->formHelper = array();

      // Connect to database server
    $dbhost = (getenv('MYSQL_HOST') ? getenv('MYSQL_HOST') : "localhost");
    $dbuser = (getenv('MYSQL_USER') ? getenv('MYSQL_USER') : "root");
    $dbpwd = (getenv('MYSQL_PASSWORD') ? getenv('MYSQL_PASSWORD') : "root");
    $dbname = (getenv('MYSQL_DATABASE') ? getenv('MYSQL_DATABASE') : "rpgaid");
    $mysqli = new mysqli($dbhost, $dbuser, $dbpwd, $dbname);

// DUMMY DATA 
//$campaign_params = array(0, 'SELECT * FROM campaigns;');

$campaign_params = (isset($_GET['campaign']) ? $_GET['campaign'] : FALSE);

if(!$campaign_params){
  $campaign_params = array(2);
}

/*
echo "campaign_params<pre>";
var_dump($campaign_params);
echo "</pre>";
//*/
$campaign_params_sanatized = array();
foreach($campaign_params as $p) {
  if(is_numeric($p)){
    $campaign_params_sanatized[] = $p;
  }
}
/*
echo "campaign_params_sanatized<pre>";
var_dump($campaign_params_sanatized);
echo "</pre>";
//*/


$cids_for_in_clause = implode(',', $campaign_params_sanatized);


/*
echo "cids_for_in_clause<pre>";
var_dump($cids_for_in_clause);
echo "</pre>";
//*/

// Get the metadata.  This is used to assemble the final gen_data variable in the JS, which is used by the generator.js file.
$datakeys_sql = "SELECT DISTINCT
  tid,datakey,title
   FROM templates
  WHERE cid IN (".$cids_for_in_clause.")
  ORDER BY title";

///*
echo "datakeys_sql<pre>";
var_export($datakeys_sql);
echo "</pre>";
//*/
  
// Execute the query
$datakeys_results = $mysqli->query($datakeys_sql);
// Use the query results to build the data


///*
echo "datakeys_results<pre>";
var_export($datakeys_results);
echo "</pre>";
//*/


while ($row = $datakeys_results->fetch_array(MYSQLI_ASSOC)) {
  $stringholder = new stdClass();
  /*
  echo "row<pre>";
  var_export($row);
  echo "</pre>";
  //*/
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
      var_export($bobo);
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
  var_export($output);
  print "</pre>";
  // */

$output->params = $campaign_params_sanatized;

print json_encode($output);
?>
