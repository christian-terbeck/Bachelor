<?php

include_once("mysql.php");

//-->Fetch updates

	if (isset($_POST["sendData"]))
	{
	$id = (int) mysqli_real_escape_string($mysql, $_POST["id"]);
	$level = (int) mysqli_real_escape_string($mysql, $_POST["level"]);
	$no = mysqli_real_escape_string($mysql, $_POST["no"]);
	$institute = (int) mysqli_real_escape_string($mysql, $_POST["institute"]);
	$description = mysqli_real_escape_string($mysql, $_POST["description"]);
	
		if (isset($_POST["category"]))
		{
		$category = (int) mysqli_real_escape_string($mysql, $_POST["category"]);
		}
		else
		{
		$category = 0;
		}
		
	$x1 = (float) mysqli_real_escape_string($mysql, $_POST["x1"]);
	$y1 = (float) mysqli_real_escape_string($mysql, $_POST["y1"]);
	$x2 = (float) mysqli_real_escape_string($mysql, $_POST["x2"]);
	$y2 = (float) mysqli_real_escape_string($mysql, $_POST["y2"]);
	$doorX = (float) mysqli_real_escape_string($mysql, $_POST["doorX"]);
	$doorY = (float) mysqli_real_escape_string($mysql, $_POST["doorY"]);
	$people = mysqli_real_escape_string($mysql, $_POST["people"]);
	$people = preg_replace('/\v+|\\\r\\\n/Ui', '<br/>', $people);
	$people = explode("<br/>", $people);
	$hoursStart = (int) mysqli_real_escape_string($mysql, $_POST["hoursStart"]);
	$hoursEnd = (int) mysqli_real_escape_string($mysql, $_POST["hoursEnd"]);
	
		if ($id > 0)
		{
		$query = "DELETE FROM ba_people WHERE roomId = '$id'";
		mysqli_query($mysql, $query);
		
		$query = "UPDATE ba_rooms SET level = '$level', no = '$no', institute = '$institute', description = '$description', category = '$category', x1 = '$x1', y1 = '$y1', x2 = '$x2', y2 = '$y2', doorX = '$doorX', doorY = '$doorY', hoursStart = '$hoursStart', hoursEnd = '$hoursEnd' WHERE id = '$id'";
		mysqli_query($mysql, $query);
		}
		else
		{
		$query = "INSERT INTO ba_rooms (`level`, `no`, `institute`, `description`, `category`, `x1`, `y1`, `x2`, `y2`, `doorX`, `doorY`, `hoursStart`, `hoursEnd`) VALUES ('$level', '$no', '$institute', '$description', '$category', '$x1', '$y1', '$x2', '$y2', '$doorX', '$doorY', '$hoursStart', '$hoursEnd')";
		mysqli_query($mysql, $query);
		
		$id = mysqli_insert_id($mysql);
		
		echo "$id";
		}
		
		if (count($people) > 0)
		{
			for ($i = 0; $i < count($people); $i++)
			{
			$tmpPerson = trim($people[$i]);
			
				if (strlen($tmpPerson) > 0)
				{
				$query = "INSERT INTO ba_people (`name`, `roomId`) VALUES ('$tmpPerson', '$id')";
				mysqli_query($mysql, $query);
				}
			}
		}
	}
	
	if (isset($_POST["delete"]) && $_POST["delete"] == "true")
	{
	$id = mysqli_real_escape_string($mysql, $_POST["id"]);
	
	$query = "DELETE FROM ba_rooms WHERE id = '$id'";
	mysqli_query($mysql, $query);
	
	$query = "DELETE FROM ba_people WHERE roomId = '$id'";
	mysqli_query($mysql, $query);
	}
	
?>