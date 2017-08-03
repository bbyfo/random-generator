<?php

$output = new stdClass();

// Connect to database server
$dbhost = (getenv('OPENSHIFT_MYSQL_DB_HOST') ? getenv('OPENSHIFT_MYSQL_DB_HOST') : "localhost");
$dbuser = (getenv('OPENSHIFT_MYSQL_DB_USERNAME') ? getenv('OPENSHIFT_MYSQL_DB_USERNAME') : "phptojs_dev");
$dbpwd = (getenv('OPENSHIFT_MYSQL_DB_PASSWORD') ? getenv('OPENSHIFT_MYSQL_DB_PASSWORD') : "mylocaldev");

$mysqli = new mysqli($dbhost, $dbuser, $dbpwd, "rpgaid");

//print_r($_GET);
// Add a new value to a template
if (isset($_GET['newValue'])) {
  $sql = "SELECT
  tid
  FROM templates
  WHERE datakey = '" . $_GET['addToTemplate'] . "'";

  $results = $mysqli->query($sql);

  while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
    $output = $row['tid'];
    $values = $row['tid'] . ",'" . $_GET['newValue'] . "'";

    $insert_sql = "INSERT INTO gen_data (tid, `string`) VALUES (" . $values . ")";
    $insert_results = $mysqli->query($insert_sql);
  }

  echo json_encode($_GET['newValue']);
// end adding new value
// Add a new template to the system
} else if (isset($_GET['newTemplate'])) {
  // Create a safe datakey
  $datakey = $_GET['newTemplate'];
  $datakey = strtolower($datakey);
  $datakey = str_replace(" ", "_", $datakey);

  // Set the title to the value the user entered into the text field
  $templateValues = "'" . $_GET['newTemplate'] . "'";
  // Add the safekey
  $templateValues .= "," . "'" . $datakey . "'";

  $insert_sql = "INSERT INTO templates (title,datakey) VALUES (" . $templateValues . ")";
  $insert_results = $mysqli->query($insert_sql);
  echo json_encode($_GET['newTemplate']);
  //echo json_encode($templateValues);
  //echo json_encode($datakey);
  //echo json_encode($insert_sql);

}
?>
