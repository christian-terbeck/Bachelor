<?php

include("mysql.php");

//-->Define variables

$labels = ["room_de" => "Raum", "room_en" => "Room", "room_es" => "Habitación",
"follow_instructions_de" => "Folgen Sie den Anweisungen auf dem Bildschirm.", "follow_instructions_en" => "Follow the instructions on your screen.", "follow_instructions_es" => "Siga las instrucciones en su pantalla.",
"location_de" => "Standort", "location_en" => "Location", "location_es" => "Ubicación",
"elevator_stairs_de" => "Aufzug", "elevator_stairs_en" => "Elevator", "elevator_stairs_es" => "Ascensor",
"destination_de" => "Ziel", "destination_en" => "Destination", "destination_es" => "Destino"];

//-->Get parameters

$languages = ["de", "en", "es"];

	if (isset($_GET["language"]))
	{
	$language = $_GET["language"];
	}
	else
	{
	$language = "en";
	}

	if (!in_array($language, $languages))
	{
	$language = "en";
	}
	
//-->Default start position is located at level 0

$mapWidth = 45; //whole image displays a length of 45m
$currentLevel = 0; //ground floor where display is located
$start = [$currentLevel, [67, 60]]; //start location of user right in front of main display
$relevantLevels = Array(); //store relevant levels in array
$relevantLevels[] = $currentLevel;

//-->Fetch

	if (isset($_GET["destination"]))
	{
	$destination = $_GET["destination"];
	}
	else
	{
	exit("Missing destination parameter");
	}

$query = "SELECT * FROM rooms WHERE id = '$destination'";
$result = mysqli_query($mysql, $query);
$check = mysqli_num_rows($result);

	if ($check > 0)
	{
	$room = mysqli_fetch_assoc($result);
	$roomLevel = $room["level"];
	
	$query = "SELECT * FROM levels WHERE id = '$roomLevel'";
	$result = mysqli_query($mysql, $query);
	$level = mysqli_fetch_assoc($result);
	
	$end = [$level["name"], [$room["doorX"], $room["doorY"]]]; //destination position
	
		if ($level["name"] != $currentLevel)
		{
		$relevantLevels[] = $level["name"];
		}
	}
	else
	{
	exit("Invalid destination parameter");
	}

?>
<!DOCTYPE HTML>
<?php echo '<html lang="'.$language.'">'; ?>

	<head>
	
		<title>GEO1 Informationssystem | Navigation</title>
		
		<meta name="author" content="Christian Terbeck">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<base href="https://christian-terbeck.de/projects/ba/">
		
		<!--Load styles-->
		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/navigation.css">
		
		<!--Load fonts-->

		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" property="stylesheet" type="text/css">
		
		<!--Shortcut icon-->
		
		<!--<link rel="shortcut icon" type="image/x-icon" href="images/icon.ico">-->
		
		<!--Load scripts-->
		
		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<script src="js/script.js"></script>
	
	</head>
	
	<body>
	
		<div class="overlay box-shadow transition-slow" data-active="true">
			<div class="overlay__content">
				<div class="overlay__animation fas fa-circle-notch fa-spin"></div>
				<?php
				
				echo '<h1 class="overlay__head">'.$labels["room_".$language].' '.$room["no"].'</h1>
				<div class="overlay__text">'.$labels["follow_instructions_".$language].'</div>
				<div class="overlay__hints">
					<div class="overlay__hint">
						<div class="overlay__hint-symbol fa fa-location-arrow"></div>
						<div class="overlay__hint-label">'.$labels["location_".$language].'</div>
					</div>
					<div class="overlay__hint">
						<div class="overlay__hint-symbol fa fa-arrows-alt-v"></div>
						<div class="overlay__hint-label">'.$labels["elevator_stairs_".$language].'</div>
					</div>
					<div class="overlay__hint">
						<div class="overlay__hint-symbol fa fa-map-pin"></div>
						<div class="overlay__hint-label">'.$labels["destination_".$language].'</div>
					</div>
				</div>';
				
				?>
			</div>
		</div>
		
		<div class="navigation">
	
			<section class="map">
			
				<div class="map__holder" data-orientation="270">
				
					<!--Map layers-->
				
					<?php
					
					$query = "SELECT * FROM levels ORDER BY name ASC";
					$result = mysqli_query($mysql, $query);
					
						while ($row = mysqli_fetch_assoc($result))
						{
							if ($row["name"] == $currentLevel)
							{
							$active = "true";
							}
							else
							{
							$active = "false";
							}
							
						echo '<div class="map__plan" data-level="'.$row["name"].'" data-active="'.$active.'">
						
							<img class="map__image" src="img/levels/'.$row["name"].'.png" alt="Level '.$row["name"].'" />
						
						</div>';
						}
						
					/*
						
					echo '<!--Rooms-->';
					
					$query = "SELECT * FROM rooms WHERE level = '2'";
					$result = mysqli_query($mysql, $query);
					
						while ($row = mysqli_fetch_assoc($result))
						{
						$x1 = (float) $row["x1"];
						$y1 = (float) $row["y1"];
						$x2 = (float) $row["x2"];
						$y2 = (float) $row["y2"];
						
						$height = $y2 - $y1;
						$width = $x2 - $x1;
						
						echo '<div class="map__room" style="top: '.$y1.'%; left: '.$x1.'%; height: '.$height.'%; width: '.$width.'%;"></div>';
						}
					*/
					
					?>
					
					<!--Canvas Layer-->
					
					<canvas id="canvas" class="map__canvas"></canvas>
					
					<!--Level label-->
					
					<div class="map__label"><span></span></div>
					
					<!--Navigation Elements-->
					
					<?php
					
					echo '<div class="map__marker" data-style="location" data-level="'.$start[0].'" data-x="'.$start[1][0].'" data-y="'.$start[1][1].'">
						<div class="map__marker-icon fa fa-location-arrow"></div>
					</div>
					<div class="map__marker" data-style="destination" data-level="'.$end[0].'" data-x="'.$end[1][0].'" data-y="'.$end[1][1].'">
						<div class="map__marker-icon fa fa-map-pin"></div>
					</div>';
					
					//-->Load relevant paths
					
						for ($i = 0; $i < count($relevantLevels); $i++)
						{
						$query = "SELECT paths.* FROM paths, levels WHERE paths.level = levels.id && levels.name = '$relevantLevels[$i]'";
						$result = mysqli_query($mysql, $query);
						
							while ($path = mysqli_fetch_assoc($result))
							{
							$levelId = $path["level"];
							
							$query2 = "SELECT name FROM levels WHERE id = '$levelId'";
							$result2 = mysqli_query($mysql, $query2);
							$row2 = mysqli_fetch_assoc($result2);
							
							$level = $row2["name"];
								
								if ($path["x1"] == $path["x2"])
								{
									if ($path["y2"] > $path["y1"])
									{
									$pathLength = $path["y2"] - $path["y1"];
									}
									else
									{
									$pathLength = $path["y1"] - $path["y2"];
									}
								
								echo '<div class="map__path" data-level="'.$level.'" data-x1="'.$path["x1"].'" data-y1="'.$path["y1"].'" data-x2="'.$path["x2"].'" data-y2="'.$path["y2"].'" style="height: '.$pathLength.'%; width: 1%; top: '.$path["y1"].'%; left: '.$path["x1"].'%;"></div>';
								}
								else if ($path["y1"] == $path["y2"])
								{
									if ($path["x2"] > $path["x1"])
									{
									$pathLength = $path["x2"] - $path["x1"];
									}
									else
									{
									$pathLength = $path["x1"] - $path["x2"];
									}
								
								echo '<div class="map__path" data-level="'.$level.'" data-x1="'.$path["x1"].'" data-y1="'.$path["y1"].'" data-x2="'.$path["x2"].'" data-y2="'.$path["y2"].'" style="height: 1%; width: '.$pathLength.'%; top: '.$path["y1"].'%; left: '.$path["x1"].'%;"></div>';
								}
							}
						}
						
						//-->Get all stairs and elevators
						
						// $query = "SELECT * FROM rooms WHERE category = '1' || category = '2'"; //all stairs and elevators
						$query = "SELECT * FROM rooms WHERE category = '2'"; //just use the elevator(s) for now
						$result = mysqli_query($mysql, $query);
						
							while ($row = mysqli_fetch_assoc($result))
							{
							echo '<div class="map__level-switch" data-x="'.$row["doorX"].'" data-y="'.$row["doorY"].'"></div>';
							}
					
					?>
					
				</div>
				
				<div class="map__levels">
				
					<?php
					
						$query = "SELECT * FROM levels ORDER BY name DESC";
						$result = mysqli_query($mysql, $query);
						
							while ($row = mysqli_fetch_assoc($result))
							{
								if ($row["name"] == $currentLevel)
								{
								$active = "true";
								}
								else
								{
								$active = "false";
								}

							// echo '<div class="map__level clickable transition-fast" data-level="'.$row["name"].'" data-active="'.$active.'" onclick="switchLevel('.$row["name"].')">'.$row["name"].'</div>';
							echo '<div class="map__level readonly transition-fast" data-level="'.$row["name"].'" data-active="'.$active.'">'.$row["name"].'</div>'; //switching level disabled - just confuses the user
							}
					
					?>
				
				</div>
			
			</section>
			
			<section class="instruction"></section>
			
		</div>
	
	</body>
	
</html>