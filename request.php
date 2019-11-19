<?php

	if (isset($_POST) && isset($_POST["authorization_key"]) && isset($_POST["type"]) && isset($_POST["data"]))
	{
		if (isset($_POST["authorization_key"]) && $_POST["authorization_key"] == "1234")
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
						
						$query = "DESCRIBE ba_".$_POST["type"];
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
							for ($i = 0; $i < count($validTypes); $i++)
							{
								if ($_POST["type"] == $validTypes[$i])
								{
									if ($_POST["data"] == "all" || $_POST["data"] == "*")
									{
									$query = "SELECT * FROM ba_".$_POST["type"]." ORDER BY id ASC";
									}
									else
									{
										if ($criteria[1] == "LIKE")
										{
										$query = "SELECT * FROM ba_".$_POST["type"]." WHERE ".$criteria[0]." ".$criteria[1]." '%".$criteria[2]."%' ORDER BY id ASC";
										}
										else
										{
										$query = "SELECT * FROM ba_".$_POST["type"]." WHERE ".$criteria[0]." ".$criteria[1]." '".$criteria[2]."' ORDER BY id ASC";
										}
									}
									
								$tmpObject = ["status" => "success", "type" => $_POST["type"]];
								
								$result = mysqli_query($mysql, $query);
								
									while ($row = mysqli_fetch_assoc($result))
									{
									$tmpData = Array();
									
										foreach ($row as $key => $val)
										{ 
										$tmpData[$key] = $val; 
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