<?php
header('Content-Type: application/json');
/*
-- Get everything
 SELECT n.*,a.*
  FROM npcs AS n
 INNER JOIN activities AS a
    ON n.npcID = a.npcID
    
-- Get Activities by NPC
SELECT *
  FROM activities
 WHERE npcID IN (2)
 */

// Connect to database server
$dbhost = (getenv('OPENSHIFT_MYSQL_DB_HOST') ? getenv('OPENSHIFT_MYSQL_DB_HOST') : "localhost");
$dbuser = (getenv('OPENSHIFT_MYSQL_DB_USERNAME') ? getenv('OPENSHIFT_MYSQL_DB_USERNAME') : "npctracker");
$dbpwd = (getenv('OPENSHIFT_MYSQL_DB_PASSWORD') ? getenv('OPENSHIFT_MYSQL_DB_PASSWORD') : "mylocaldev");

$mysqli = new mysqli($dbhost, $dbuser, $dbpwd, "npctracker");

    // Check connection
    if(mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

$qp = $_GET["q"];
switch($qp){
	case "incrementDay":
		$sql = "UPDATE activities SET activityProgress = activityProgress + 1 WHERE activityProgress < activityDuration AND activityActive = 1";
		break;
	case "decrementDay":
		$sql = "UPDATE activities SET activityProgress = activityProgress - 1 WHERE activityProgress > 0 AND activityActive = 1";
		break;
	case "npcsAll":
		$sql  = 'SELECT * FROM npcs ORDER BY name';
		break;
	case "activitiesAll":
		$sql = 'SELECT * FROM activities ORDER BY activityOrder';
		break;
	case "activitiesByNPC":
		$sql = 'SELECT * FROM activities WHERE npcID = ' . $_GET["npcID"] . ' ORDER BY activityOrder';
		break;
	case "deactivateActivity":
		// $_GET['actid']
		$sql = 'UPDATE activities SET activityActive = "0" WHERE activityID = ' . $_GET["actid"];
		break;
	case "activateActivity":
		// $_GET['actid']
		$sql = 'UPDATE activities SET activityActive = "1" WHERE activityID = ' . $_GET["actid"];
		break;
	case "activitySortUp":
		$sql = 'UPDATE activities SET activityOrder = (activityOrder - 1) WHERE activityID = ' . $_GET["actid"];
		break;
	case "activitySortDown":
		$sql = 'UPDATE activities SET activityOrder = (activityOrder + 1) WHERE activityID = ' . $_GET["actid"];
		break;
	case "activityAddNew":
		
		$npc = urldecode($_GET["npc"]);
		$activityType = urldecode($_GET{"activityType"});
		$activityDesc = urldecode($_GET["activityDesc"]);
		
		$npcidsql ="SELECT npcID FROM npcs WHERE name = '" . $npc . "'";
		var_export($npcidsql);

		// THE QUERY SEEMS TO FAIL WHEN A NAME CONTAINS A '.
		
		$getnpcid = mysqli_fetch_assoc(mysqli_query($mysqli, $npcidsql));
		$npcid = $getnpcid['npcID'];
		
		var_export($getnpcid);
		
		
		$sql = "INSERT INTO `activities` (`npcID`, `activityType`, `activityDuration`, `activityProgress`, `activityDescription`, `activityOrder`, `activityActive`) VALUES ($npcid, '$activityType', '5', '0', '$activityDesc', '5', '1')";
		
		break;
	default:
		$sql = ' SELECT n.*,a.*
          		   FROM npcs AS n
                  INNER JOIN activities AS a
                     ON n.npcID = a.npcID';

}

//var_export($sql);

 $result = mysqli_query($mysqli, $sql);

    if(!$result) {
        die('Query failed: ' . mysqli_error());
    }
$rows = array();
while($row = mysqli_fetch_object($result)){
	$rows[] = $row;
}

$ret = json_encode($rows);

echo $ret;

?>
