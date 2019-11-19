<?php

include("mysql.php");

//-->Define variables

$labels = ["destination_de" => "Ziel", "destination_en" => "Destination", "destination_es" => "Destino"];

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
$start = [$currentLevel, [64, 60]]; //start location of user right in front of main display

//-->Fetch

	if (isset($_GET["destination"]))
	{
	$destination = $_GET["destination"];
	}
	else
	{
	exit("Missing destination parameter");
	}

$query = "SELECT * FROM ba_rooms WHERE id = '$destination'";
$result = mysqli_query($mysql, $query);
$check = mysqli_num_rows($result);

	if ($check > 0)
	{
	$room = mysqli_fetch_assoc($result);
	$roomLevel = $room["level"];
	
	$query = "SELECT * FROM ba_levels WHERE id = '$roomLevel'";
	$result = mysqli_query($mysql, $query);
	$level = mysqli_fetch_assoc($result);
	
	$end = [$level["name"], [$room["doorX"], $room["doorY"]]]; //destination position
	}
	else
	{
	exit("Invalid destination parameter");
	}

?>
<!DOCTYPE HTML>
<html>

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
				
				echo '<h1 class="overlay__head">Raum '.$room["no"].'</h1>';
				
				?>
				<div class="overlay__text">Folgen Sie den Anweisungen auf dem Bildschirm.</div>
			</div>
		</div>
		
		<div class="navigation">
	
			<section class="map">
			
				<div class="map__holder">
				
					<!--Map layers-->
				
					<?php
					
					$query = "SELECT * FROM ba_levels ORDER BY name ASC";
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
						
					?>
					
					<!--Navigation Elements-->
					
					<?php
					
					echo '<div class="map__marker" data-style="location" data-level="'.$start[0].'" data-x="'.$start[1][0].'" data-y="'.$start[1][1].'"></div>
					<div class="map__marker" data-style="destination" data-level="'.$end[0].'" data-x="'.$end[1][0].'" data-y="'.$end[1][1].'"></div>';
					
					?>
					
					<!--<div class="map__path" style="height: 6%; width: 1%; top: 51%; left: 62%;"></div>
					<div class="map__path" style="height: 1%; width: 9%; top: 51%; left: 62%;"></div>
					<div class="map__path" style="height: 6%; width: 1%; top: 45%; left: 70%;"></div>-->
					
				</div>
				
				<div class="map__levels">
				
					<?php
					
						$query = "SELECT * FROM ba_levels ORDER BY name DESC";
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

							echo '<div class="map__level clickable transition-fast" data-level="'.$row["name"].'" data-active="'.$active.'" onclick="switchLevel('.$row["name"].')">'.$row["name"].'</div>';
							}
					
					?>
				
				</div>
			
			</section>
			
			<section class="instruction">
			
				<div class="instruction__step" data-level="0">
					<div class="instruction__symbol fa fa-long-arrow-alt-right"></div>
					<div class="instruction__label">Nach rechts drehen</div>
				</div>
				<div class="instruction__distance" data-level="0">1 m</div>
				<div class="instruction__step" data-level="0">
					<div class="instruction__symbol fa fa-long-arrow-alt-up"></div>
					<div class="instruction__label">Geradeaus zum Fahrstuhl gehen</div>
				</div>
				<div class="instruction__distance" data-level="0">15 m</div>
				<div class="instruction__step">
					<div class="instruction__symbol instruction__symbol--special fa fa-caret-up"></div>
					<?php
					
					echo '<div class="instruction__label">In den '.$level["name"].'. Stock fahren<br /><small>Anschließend hier klicken:</small></div>';
					
					?>
				</div>
				<?php
					
					echo '<div class="instruction__button clickable" data-triggered="false" data-level="'.$level["name"].'" onclick="confirmNewLevel(this)"><span class="fa fa-check"></span><span class="fa fa-undo"></span><span data-text="done">'.$level["name"].'. Stock erreicht</span><span data-text="undo">Rückgängig</span></div>
					
					<div class="instruction__distance fa fa-arrows-alt-v"></div>
					<div class="instruction__step" data-level="'.$level["name"].'" style="display: none;">
						<div class="instruction__symbol fa fa-long-arrow-alt-up"></div>
						<div class="instruction__label">Den Fahrstuhl verlassen</div>
					</div>
					<div class="instruction__distance" data-level="'.$level["name"].'" style="display: none;">2 m</div>
					<div class="instruction__step" data-level="'.$level["name"].'" style="display: none;">
						<div class="instruction__symbol fa fa-long-arrow-alt-left"></div>
						<div class="instruction__label">Links abbiegen</div>
					</div>
					<div class="instruction__distance" data-level="'.$level["name"].'" style="display: none;">2 m</div>
					<div class="instruction__step" data-level="'.$level["name"].'" style="display: none;">
						<div class="instruction__symbol fa fa-long-arrow-alt-left"></div>
						<div class="instruction__label">Hinter dem Fahrstuhl links abbiegen</div>
					</div>
					<div class="instruction__distance" data-level="'.$level["name"].'" style="display: none;">2 m</div>
					<div class="instruction__step" data-level="'.$level["name"].'" style="display: none;">
						<div class="instruction__symbol fa fa-long-arrow-alt-up"></div>
						<div class="instruction__label">Geradeaus gehen</div>
					</div>
					<div class="instruction__distance" data-level="'.$level["name"].'" style="display: none;">10 m</div>
					<div class="instruction__step" data-level="'.$level["name"].'" style="display: none;">
						<div class="instruction__symbol fa fa-long-arrow-alt-right"></div>
						<div class="instruction__label">Raum '.$room["no"].' befindet sich auf der rechten Seite</div>
					</div>';
				
				?>
			
			</section>
			
		</div>
	
	</body>
	
</html>