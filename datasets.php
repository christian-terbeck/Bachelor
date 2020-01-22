<?php

include_once("mysql.php");

$query = "SELECT * FROM rooms ORDER BY level ASC, no ASC";
$result = mysqli_query($mysql, $query);

	while ($row = mysqli_fetch_assoc($result))
	{
	$tmpId = $row["id"];
	$tmpPeople = Array();
	
	$query2 = "SELECT name FROM people WHERE room = '$tmpId'";
	$result2 = mysqli_query($mysql, $query2);
	
		while ($row2 = mysqli_fetch_assoc($result2))
		{
		$tmpPeople[] = $row2["name"];
		}
	
	$people = implode("\n", $tmpPeople);
	$peopleReadable = str_replace("\n", ", ", $people);
	
	echo "<div class='data__row clickable' data-id='{$row["id"]}' data-level='{$row["level"]}' data-no='{$row["no"]}' data-description='{$row["description"]}' data-institute='{$row["institute"]}' data-category='{$row["category"]}' data-x1='{$row["x1"]}' data-y1='{$row["y1"]}' data-x2='{$row["x2"]}' data-y2='{$row["y2"]}' data-doorX='{$row["doorX"]}' data-doorY='{$row["doorY"]}' data-people='$people' data-hoursStart='{$row["hoursStart"]}' data-hoursEnd='{$row["hoursEnd"]}' onclick='editDataset(this)'>Room {$row["no"]} &ndash; $peopleReadable</div>";
	}
	
?>