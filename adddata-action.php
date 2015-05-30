<?php

$output = new stdClass();

// Connect to database server
$dbhost = (getenv('OPENSHIFT_MYSQL_DB_HOST') ? getenv('OPENSHIFT_MYSQL_DB_HOST') : "localhost");
$dbuser = (getenv('OPENSHIFT_MYSQL_DB_USERNAME') ? getenv('OPENSHIFT_MYSQL_DB_USERNAME') : "phptojs_dev");
$dbpwd = (getenv('OPENSHIFT_MYSQL_DB_PASSWORD') ? getenv('OPENSHIFT_MYSQL_DB_PASSWORD') : "mylocaldev");

$mysqli = new mysqli($dbhost, $dbuser, $dbpwd, "rpgaid");

$sql = "SELECT 
  mdid
  FROM gen_metadata
  WHERE datakey = '" . $_GET['addTo'] . "'";

$results = $mysqli->query($sql);

while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
  $output = $row['mdid'];
  $values = $row['mdid'] . ",'" . $_GET['newValue'] . "'";

  $insert_sql = "INSERT INTO gen_data (mdid, `string`) VALUES (" . $values . ")";
  $insert_results = $mysqli->query($insert_sql);
}

echo json_encode($_GET['newValue']);
?>
