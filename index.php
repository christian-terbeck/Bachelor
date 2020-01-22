<?php

include_once("mysql.php");

$baseUrl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$baseUrl = str_replace("index.php", "", $baseUrl);

?>
<!DOCTYPE HTML>
<html>

	<head>
	
		<title>GEO1 Informationssystem | Home</title>
		
		<meta name="author" content="Christian Terbeck">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
		<?php echo '<base href="'.$baseUrl.'">'; ?>
		
		<!--Load styles-->
		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
		<link rel="stylesheet" href="css/style.css">
		
		<!--Load fonts-->

		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" property="stylesheet" type="text/css">
		
		<!--Shortcut icon-->
		
		<!--<link rel="shortcut icon" type="image/x-icon" href="images/icon.ico">-->
		
		<!--Load scripts-->
		
		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<script src="js/script.js"></script>
	
	</head>
	
	<body>
		
		<header class="header">
		
			<h1 class="header__head" data-de="GEO1 Informationssystem" data-en="GEO1 information system" data-es="GEO1 sistema de información">GEO1 Informationssystem</h1>
			<h2 class="header__subhead" data-de="Wonach suchen Sie?" data-en="What are you searching for?" data-es="Qué está buscando?">Wonach suchen Sie?</h1>
		
		</header>
		
		<nav class="nav">
			
			<ul class="nav__categories">
				<li class="clickable transition-fast" data-name="employees" data-active="true" onclick="switchCategory(this)">
					<span class="fa fa-users"></span>
					<span data-de="Mitarbeiter" data-en="Employees" data-es="Empleados">Mitarbeiter</span>
				</li>
				<li class="clickable transition-fast" data-name="rooms" data-active="false" onclick="switchCategory(this)">
					<span class="fa fa-door-closed"></span>
					<span data-de="Räume" data-en="Rooms" data-es="Habitaciones">Räume</span>
				</li>
				<li class="clickable transition-fast" data-name="institutes" data-active="false" onclick="switchCategory(this)">
					<span class="fa fa-university"></span>
					<span data-de="Institute" data-en="Institutes" data-es="Departamentos">Institute</span>
				</li>
				<li class="clickable transition-fast" data-name="search" data-active="false" onclick="switchCategory(this)">
					<span class="fa fa-search"></span>
					<span data-de="Manuelle Suche" data-en="Manual search" data-es="Búsqueda manual">Manuelle Suche</span>
				</li>
			</ul>
			
		</nav>
		
		<aside class="aside">
			
			<ul class="aside__items">
				<li class="clickable" onclick="setLanguage('de')">
					<img class="box-shadow" src="img/flags/de.png" alt="Deutsch" title="Deutsch" />
				</li>
				<li class="clickable" onclick="setLanguage('en')">
					<img class="box-shadow" src="img/flags/en.png" alt="English" title="English" />
				</li>
				<li class="clickable" onclick="setLanguage('es')">
					<img class="box-shadow" src="img/flags/es.png" alt="Español" title="Español" />
				</li>
			</ul>
		
		</aside>
	
		<div class="wrapper">
			
			<div class="content">
			
				<div class="content__page" data-name="employees" data-active="true">
					
					<!--Categorized content overview-->
					
					<?php
					
					$alphabet = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "ä", "ö", "ü"];
					
					echo '<ul class="content__shortlinks readonly">';
					
						for ($i = 0; $i < count($alphabet); $i++)
						{
						echo '<li class="clickable transition-fast" data-name="'.$alphabet[$i].'" onclick="tab(this)">'.mb_strtoupper($alphabet[$i]).'</li>';
						}
						
					echo '</ul>
				
					<!--Accordion boxes-->';
					
						for ($i = 0; $i < count($alphabet); $i++)
						{
						$query = "SELECT * FROM people WHERE name LIKE '$alphabet[$i]%' ORDER BY name ASC";
						$result = mysqli_query($mysql, $query);
						$recordsFound = mysqli_num_rows($result);
						
							if ($recordsFound > 0)
							{						
							echo '<h3 class="content__tab" data-name="'.$alphabet[$i].'">'.mb_strtoupper($alphabet[$i]).'</h3>
							<div class="content__container">';
								
								while ($row = mysqli_fetch_assoc($result))
								{
								$room = $row["room"];
								
								echo '<div class="accordion-box" data-active="false">
									<div class="accordion-box__head clickable" onclick="accordionBox(this)">
										<div class="accordion-box__symbol transition-fast fa fa-chevron-right"></div>
										<div class="accordion-box__label">'.$row["name"].'</div>
									</div>
									<div class="accordion-box__content">';
										
										include("room_view.php");
										
									echo '</div>
								</div>';
								}
							
							echo '</div>';
							}
						}
					
					?>
				</div>
				
				<div class="content__page" data-name="rooms" data-active="false">
					
					<!--Accordion Boxes-->
					
					<?php
					
					$levels = ["-1", "0", "1", "2", "3", "4", "5"];
					$levelLabels = [["Raum 0.01 - 0.99", "Room 0.01 - 0.99", "Habitación 0.01 - 0.99"], ["Raum 1 - 99", "Room 1 - 99", "Habitación 1 - 99"], ["Raum 100 - 199", "Room 100 - 199", "Habitación 100 - 199"], ["Raum 200 - 299", "Room 200 - 299", "Habitación 200 - 299"], ["Raum 300 - 399", "Room 300 - 399", "Habitación 300 - 399"], ["Raum 400 - 499", "Room 400 - 499", "Habitación 400 - 499"], ["Raum 500 - 599", "Room 500 - 599", "Habitación 500 - 599"]];
					
					echo '<!--Categorized content overview-->
					
					<ul class="content__shortlinks readonly">';
					
						for ($i = 0; $i < count($levels); $i++)
						{
						echo '<li class="clickable transition-fast" data-name="'.$levels[$i].'" onclick="tab(this)" data-de="'.$levelLabels[$i][0].'" data-en="'.$levelLabels[$i][1].'" data-es="'.$levelLabels[$i][2].'">'.$levelLabels[$i][0].'</li>';
						}
					
					echo '</ul>
					
					<!--Accordion Boxes-->';
					
						for ($i = 0; $i < count($levels); $i++)
						{
						$query = "SELECT rooms.* FROM rooms, levels WHERE rooms.no != '' && rooms.level = levels.id && levels.name = '$levels[$i]' ORDER BY rooms.no ASC";
						$result = mysqli_query($mysql, $query);
						$recordsFound = mysqli_num_rows($result);
						
							if ($recordsFound > 0)
							{						
							echo '<h3 class="content__tab" data-name="'.$levels[$i].'" data-de="'.$levelLabels[$i][0].'" data-en="'.$levelLabels[$i][1].'" data-es="'.$levelLabels[$i][2].'">'.$levelLabels[$i][0].'</h3>
							<div class="content__container">';
								
								while ($row = mysqli_fetch_assoc($result))
								{
								$room = $row["id"];
								
								echo '<div class="accordion-box" data-active="false">
									<div class="accordion-box__head clickable" onclick="accordionBox(this)">
										<div class="accordion-box__symbol transition-fast fa fa-chevron-right"></div>
										<div class="accordion-box__label" data-de="Raum '.$row["no"].'" data-en="Room '.$row["no"].'" data-es="Habitación '.$row["no"].'">Raum '.$row["no"].'</div>
									</div>
									<div class="accordion-box__content">';
										
										include("room_view.php");
										
									echo '</div>
								</div>';
								}
							
							echo '</div>';
							}
						}
					
					?>
				
				</div>
				
				<div class="content__page" data-name="institutes" data-active="false">
				
					<?php
				
					echo '<!--Categorized content overview-->
					
					<ul class="content__shortlinks readonly">';
						
						$query = "SELECT * FROM institutes ORDER BY id ASC";
						$result = mysqli_query($mysql, $query);
						
							while ($row = mysqli_fetch_assoc($result))
							{
							echo '<li class="clickable transition-fast" data-name="institute-'.$row["id"].'" onclick="tab(this)" data-de="'.$row["name_de"].'" data-en="'.$row["name_en"].'" data-es="'.$row["name_es"].'">'.$row["name_de"].'</li>';
							}
						
					echo '</ul>
						
					<!--Accordion Boxes-->';
					
					$result = mysqli_query($mysql, $query);
					
						while ($row = mysqli_fetch_assoc($result))
						{
						$tmpInstitute = $row["id"];
						
						$query2 = "SELECT * FROM rooms WHERE institute = '$tmpInstitute' ORDER BY no ASC";
						$result2 = mysqli_query($mysql, $query2);
						$recordsFound = mysqli_num_rows($result2);
						
							if ($recordsFound > 0)
							{						
							echo '<h3 class="content__tab" data-name="institute-'.$row["id"].'" data-de="'.$row["name_de"].'" data-en="'.$row["name_en"].'" data-es="'.$row["name_es"].'">'.$row["name_de"].'</h3>
							<div class="content__container">';
								
								while ($row2 = mysqli_fetch_assoc($result2))
								{
								$room = $row2["id"];
								
								echo '<div class="accordion-box" data-active="false">
									<div class="accordion-box__head clickable" onclick="accordionBox(this)">
										<div class="accordion-box__symbol transition-fast fa fa-chevron-right"></div>
										<div class="accordion-box__label" data-de="Raum '.$row2["no"].'" data-en="Room '.$row2["no"].'" data-es="Habitación '.$row2["no"].'">Raum '.$row2["no"].'</div>
									</div>
									<div class="accordion-box__content">';
										
										include("room_view.php");
										
									echo '</div>
								</div>';
								}
							
							echo '</div>';
							}
						}
						
					?>
				
				</div>
				
				<div class="content__page" data-name="search" data-active="false">
				
					<?php
					
					$keyboard = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "ß", "CLEAR", "\n", "Q", "W", "E", "R", "T", "Z", "U", "I", "O", "P", "Ü", "\n", "A", "S", "D", "F", "G", "H", "J", "K", "L", "Ö", "Ä", "\n", "Y", "X", "C", "V", "B", "N", "M"];
					
					echo '<ul class="content__keyboard readonly">';
					
						for ($i = 0; $i < count($keyboard); $i++)
						{
							if ($keyboard[$i] == "CLEAR")
							{
							echo '<li class="clickable transition-fast fa fa-long-arrow-alt-left" data-name="CLEAR" onclick="clearKeyboard()" style="padding: 10px 20px;"></li>';	
							}
							else if ($keyboard[$i] == "\n")
							{
							echo '<br />';
							}
							else
							{
							echo '<li class="clickable transition-fast" data-name="'.mb_strtolower($keyboard[$i]).'" onclick="keyboard(this)">'.$keyboard[$i].'</li>';
							}
						}
						
					echo '<br /><li class="clickable transition-fast" data-name=" " data-de="Leerzeichen" data-en="Space" data-es="Espacio" onclick="keyboard(this)" style="padding: 10px 50px;">Leerzeichen</li>
					</ul>';
					
					$query = "SELECT * FROM rooms WHERE no != '' ORDER BY level ASC, no ASC";
					$result = mysqli_query($mysql, $query);
					$recordsFound = mysqli_num_rows($result);
					
						if ($recordsFound > 0)
						{
						echo '<h3 class="content__tab" data-name="search" style="display: none;"></h3>
						<div class="content__container" data-name="search">';
						
							while ($row = mysqli_fetch_assoc($result))
							{
							$room = $row["id"];
							$people = Array();
							
							$peopleQuery = "SELECT * FROM people WHERE room = '$room' ORDER BY name ASC";
							$peopleResult = mysqli_query($mysql, $peopleQuery);
							
								while ($peopleRow = mysqli_fetch_assoc($peopleResult))
								{
								$people[] = $peopleRow["name"];
								}
								
							$label_de = "Raum ".$row["no"];
							$label_en = "Room ".$row["no"];
							$label_es = "Habitación ".$row["no"];
							
								if (count($people) > 0)
								{
								$label_de .= " &ndash; ".implode(", ", $people);
								$label_en .= " &ndash; ".implode(", ", $people);
								$label_es .= " &ndash; ".implode(", ", $people);
								}
								
							$institute = $row["institute"];
							
							$instituteQuery = "SELECT * FROM institutes WHERE id = '$institute'";
							$instituteResult = mysqli_query($mysql, $instituteQuery);
							$instituteRow = mysqli_fetch_assoc($instituteResult);
							
							$label_de .= " (".$instituteRow["name_de"].")";
							$label_en .= " (".$instituteRow["name_en"].")";
							$label_es .= " (".$instituteRow["name_es"].")";
							
							echo '<div class="accordion-box" data-active="false" style="display: none;">
								<div class="accordion-box__head clickable" onclick="accordionBox(this)">
									<div class="accordion-box__symbol transition-fast fa fa-chevron-right"></div>
									<div class="accordion-box__label" data-de="'.$label_de.'" data-en="'.$label_en.'" data-es="'.$label_es.'">'.$label_de.'</div>
								</div>
								<div class="accordion-box__content">';
									
									include("room_view.php");
									
								echo '</div>
							</div>';
							}
							
						echo '</div>';
						}
						
					?>
					
				</div>
			
			</div>
		
		</div>
		
		<!--Go top button-->
		
		<div class="go-top go-top--inactive fa fa-chevron-up box-shadow clickable transition-fast" onclick="goTop()"></div>
	
	</body>

</html>