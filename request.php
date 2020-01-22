<?php

	if (isset($_POST) && isset($_POST["authorization_key"]) && isset($_POST["type"]) && isset($_POST["data"]))
	{
		if (isset($_POST["authorization_key"]) && $_POST["authorization_key"] == "GEO1")
		{
		$validTypes = ["categories", "institutes", "levels", "paths", "people", "rooms"];
		
			if (in_array($_POST["type"], $validTypes))
			{
				if ($_POST["data"] == "all" || $_POST["data"] == "*" || substr_count($_POST["data"], "+") === 2)
				{
				$operatorCheck = false;
					
					if ($_POST["data"] == "all" || $_POST["data"] == "*")
					{
					$operatorCheck = true;
					}
					else
					{
					$validOperators = ["=", ">", "<", ">=", "<=", "LIKE"];
					$criteria = explode("+", $_POST["data"]);
					
						if (in_array($criteria[1], $validOperators))
						{
						$operatorCheck = true;
						}
					}
				
					if ($operatorCheck === true)
					{
					include("mysql.php");
					
					//-->Check if field exists
					
					$fieldCheck = false;
					
						if ($_POST["data"] == "all" || $_POST["data"] == "*")
						{
						$fieldCheck = true;
						}
						else
						{
						$fields = Array();
						
						$query = "DESCRIBE ".$_POST["type"];
						$result = mysqli_query($mysql, $query);
						
							while ($row = mysqli_fetch_array($result))
							{
							$fields[] = $row[0];	
							}
							
							if (in_array($criteria[0], $fields))
							{
							$fieldCheck = true;
							}
						}
					
						if ($fieldCheck === true)
						{
						$foreignKeys = ["level", "room", "category", "institute"];
							
							for ($i = 0; $i < count($validTypes); $i++)
							{
								if ($_POST["type"] == $validTypes[$i])
								{
									if ($_POST["data"] == "all" || $_POST["data"] == "*")
									{
									$query = "SELECT * FROM ".$_POST["type"]." ORDER BY id ASC";
									}
									else
									{
										if ($criteria[1] == "LIKE")
										{
										$query = "SELECT * FROM ".$_POST["type"]." WHERE ".$criteria[0]." ".$criteria[1]." '%".$criteria[2]."%' ORDER BY id ASC";
										}
										else
										{
										$query = "SELECT * FROM ".$_POST["type"]." WHERE ".$criteria[0]." ".$criteria[1]." '".$criteria[2]."' ORDER BY id ASC";
										}
									}
									
								$tmpObject = ["status" => "success", "type" => $_POST["type"]];
								
								$result = mysqli_query($mysql, $query);
								
									while ($row = mysqli_fetch_assoc($result))
									{
									$tmpData = Array();
									
										foreach ($row as $key => $val)
										{
											if (in_array($key, $foreignKeys))
											{
											$tmpData2 = Array();
												
												if ($key == "level")
												{
												$query2	= "SELECT * FROM levels WHERE id = '$val'";
												}
												else if ($key == "room")
												{
												$query2	= "SELECT * FROM rooms WHERE id = '$val'";
												}
												else if ($key == "category")
												{
												$query2	= "SELECT * FROM categories WHERE id = '$val'";
												}
												else if ($key == "institute")
												{
												$query2	= "SELECT * FROM institutes WHERE id = '$val'";
												}
												
											$result2 = mysqli_query($mysql, $query2);
											$row2 = mysqli_fetch_assoc($result2);
											
												foreach ($row2 as $key2 => $val2)
												{
												$tmpData2[$key2] = $val2;
												}
											
											$val = $tmpData2;
											}
											
										$tmpData[$key] = $val;
										}
										
										if ($_POST["type"] == "rooms")
										{
										$tmpData2 = Array();
										$tmpId = $row["id"];
										
										$query2 = "SELECT name FROM people WHERE room = '$tmpId' ORDER BY name ASC";
										$result2 = mysqli_query($mysql, $query2);
										
											while ($row2 = mysqli_fetch_assoc($result2))
											{										
											$tmpData2[] = $row2["name"];
											}
											
										$tmpData["people"] = $tmpData2;
										}

									$tmpObject[] = $tmpData; 
									}
								
								break;
								}
							}
						}
						else
						{
						$tmpObject = ["status" => "error", "message" => "Invalid field name (valid inputs: ".implode(", ", $fields).")"];
						}
					}
					else
					{
					$tmpObject = ["status" => "error", "message" => "Invalid operator used (valid inputs: ".implode(", ", $validOperators).")"];
					}
				}
				else
				{
				$tmpObject = ["status" => "error", "message" => "Invalid 'data' (valid inputs: 'all', '*', or 'FIELDNAME+OPERATOR+VALUE')"];
				}
			}
			else
			{
			$tmpObject = ["status" => "error", "message" => "Invalid 'type' (valid inputs: ".implode(", ", $validTypes).")"];
			}
		}
		else
		{
		$tmpObject = ["status" => "error", "message" => "Access denied (invalid 'authorization_key')"];
		}
	}
	else
	{
	$tmpObject = ["status" => "error", "message" => "No data specified ('type' and 'data' need to be transmitted)"];
	}
	
//-->Create and return object
	
$tmpObject = json_encode($tmpObject, JSON_FORCE_OBJECT);

//-->Send output header (type: json object)

header("Content-Type: application/json");

echo "$tmpObject";

?>