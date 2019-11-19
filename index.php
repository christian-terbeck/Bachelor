<?php

include_once("mysql.php");

$baseUrl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$baseUrl = str_replace("index.php", "", $baseUrl);

$x = 3;

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
			<h2 class="header__subhead" data-de="Wonach suchen Sie?" data-en="What are you searching for?" data-es="Que estas buscando?">Wonach suchen Sie?</h1>
		
		</header>
		
		<nav class="nav">
			
			<ul class="nav__categories">
				<li class="clickable transition-fast" data-name="employees" data-active="true" onclick="switchCategory(this)">
					<span class="fa fa-users"></span>
					<span data-de="Mitarbeiter" data-en="Employees" data-es="Empleado">Mitarbeiter</span>
				</li>
				<li class="clickable transition-fast" data-name="rooms" data-active="false" onclick="switchCategory(this)">
					<span class="fa fa-door-closed"></span>
					<span data-de="Räume" data-en="Rooms" data-es="Habitaciones">Räume</span>
				</li>
				<li class="clickable transition-fast" data-name="institutes" data-active="false" onclick="switchCategory(this)">
					<span class="fa fa-university"></span>
					<span data-de="Institute" data-en="Institutes" data-es="Institutos">Institute</span>
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
					<img class="box-shadow" src="img/de.png" alt="Deutsch" title="Deutsch" />
				</li>
				<li class="clickable" onclick="setLanguage('en')">
					<img class="box-shadow" src="img/en.png" alt="English" title="English" />
				</li>
				<li class="clickable" onclick="setLanguage('es')">
					<img class="box-shadow" src="img/es.png" alt="Español" title="Español" />
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
						$query = "SELECT * FROM ba_people WHERE name LIKE '$alphabet[$i]%' ORDER BY name ASC";
						$result = mysqli_query($mysql, $query);
						$recordsFound = mysqli_num_rows($result);
						
							if ($recordsFound > 0)
							{						
							echo '<h3 class="content__tab" data-name="'.$alphabet[$i].'">'.mb_strtoupper($alphabet[$i]).'</h3>
							<div class="content__container">';
								
								while ($row = mysqli_fetch_assoc($result))
								{
								echo '<div class="accordion-box" data-active="false">
									<div class="accordion-box__head clickable" onclick="accordionBox(this)">
										<div class="accordion-box__symbol transition-fast fa fa-chevron-right"></div>
										<div class="accordion-box__label">'.$row["name"].'</div>
									</div>
									<div class="accordion-box__content">
										<br /><strong><i>ADD MORE CONTENT HERE.. INFO.. ROOM ETC.</i></strong><br /><br />
										<p data-de="Scannen Sie diesen QR-Code mit Ihrem Smartphone.<br />Die Wegbeschreibung wird anschließend auf Ihrem Endgerät angezeigt." data-en="Scan this QR code with your smartphone.<br />The directions are then displayed on your device." data-es="Escanee este código QR con su teléfono inteligente.<br />Las instrucciones se muestran en su dispositivo.">Scannen Sie diesen QR-Code mit Ihrem Smartphone.<br />Die Wegbeschreibung wird anschließend auf Ihrem Endgerät angezeigt.</p>
										<img class="qr-code" src="'.$baseUrl.'qr.php?language=de&destination='.$row["roomId"].'" alt="de" />
									</div>
								</div>';
								}
							
							echo '</div>';
							}
						}
					
					?>
				</div>
				
				<div class="content__page" data-name="rooms" data-active="false">
				
					<!--Categorized content overview-->
					
					<?php
					
					$levels = ["-1", "0", "1", "2", "3", "4", "5"];
					
					?>
					
					<ul class="content__shortlinks readonly">
						<li class="clickable transition-fast" data-name="000" onclick="tab(this)" data-de="Raum 0.01 - 0.99" data-en="Room 0.01 - 0.99" data-es="Habitación 0.01 - 0.99">Raum 0.01 - 0.99</li>
						<li class="clickable transition-fast" data-name="001" onclick="tab(this)" data-de="Raum 1 - 99" data-en="Room 1 - 99" data-es="Habitación 1 - 99">Raum 1 - 99</li>
						<li class="clickable transition-fast" data-name="100" onclick="tab(this)" data-de="Raum 100 - 199" data-en="Room 100 - 199" data-es="Habitación 100 - 199">Raum 100 - 199</li>
						<li class="clickable transition-fast" data-name="200" onclick="tab(this)" data-de="Raum 200 - 299" data-en="Room 200 - 299" data-es="Habitación 200 - 299">Raum 200 - 299</li>
						<li class="clickable transition-fast" data-name="300" onclick="tab(this)" data-de="Raum 300 - 399" data-en="Room 300 - 399" data-es="Habitación 300 - 399">Raum 300 - 399</li>
						<li class="clickable transition-fast" data-name="400" onclick="tab(this)" data-de="Raum 300 - 499" data-en="Room 400 - 499" data-es="Habitación 400 - 499">Raum 400 - 499</li>
						<li class="clickable transition-fast" data-name="500" onclick="tab(this)" data-de="Raum 500 - 599" data-en="Room 500 - 599" data-es="Habitación 500 - 599">Raum 500 - 599</li>
						<li class="clickable transition-fast" data-name="999" onclick="tab(this)" data-de="Spezielle Räume" data-en="Special rooms" data-es="Habitaciones especiales">Spezielle Räume</li>
					</ul>
				
				</div>
				
				<div class="content__page" data-name="institutes" data-active="false">
				
					<!--Categorized content overview-->
					
					<ul class="content__shortlinks readonly">
						<li class="clickable transition-fast" data-name="" onclick="tab(this)" data-de="Geographische Kommission" data-en="" data-es="">Geographische Kommission</li>
						<li class="clickable transition-fast" data-name="" onclick="tab(this)" data-de="Didaktik der Geographie" data-en="" data-es="">Didaktik der Geographie</li>
						<li class="clickable transition-fast" data-name="" onclick="tab(this)" data-de="Institut für Geographie" data-en="" data-es="">Institut für Geographie</li>
						<li class="clickable transition-fast" data-name="" onclick="tab(this)" data-de="Institut für Geoinformatik" data-en="" data-es="">Institut für Geoinformatik</li>
						<li class="clickable transition-fast" data-name="" onclick="tab(this)" data-de="Institut für Landschaftsökologie" data-en="" data-es="">Institut für Landschaftsökologie</li>
						<li class="clickable transition-fast" data-name="" onclick="tab(this)" data-de="Institut für Paläontologie" data-en="" data-es="">Institut für Paläontologie</li>
						<li class="clickable transition-fast" data-name="" onclick="tab(this)" data-de="Diverses" data-en="" data-es="">Diverses</li>
					</ul>
				
				</div>
				
				<div class="content__page" data-name="search" data-active="false">
					<ul>
						<li>Sucheingabefeld, das auch geleert werden muss, wenn der Tab gewechselt wird</li>
						<li>kurze Anweisung zum Eingabefeld... "Suchbegriff"</li>
						<li>Gefundene Personen</li>
						<li>Gefundene Räume</li>
						<li>Gefundene Institute</li>
						<li>Suche beginnt ab dem ersten Buchstaben, Ergebnisse werden entweder schon vorgeladen(vermutlich sinnvoller!) oder live aus der Datenbank abgefragt</li>
						<li>Suche evtl. mit Einzelbuchstaben (Touch-Tastatur), da dies deutlich einfacher auf einem großen Touchscreen bedienbar ist - dann natürlich kein Eingabefeld, sondern eine Zeichenkette anhand der gedrückten Buchstaben zusammensetzen</li>
						<li>evtl. nur Buchstaben verfügbar machen, die unter den gegebenen Umständen noch zu Ergebnissen führen</li>
					</ul>
				</div>
			
			</div>
		
		</div>
		
		<!--Go top button-->
		
		<div class="go-top go-top--inactive fa fa-chevron-up box-shadow clickable transition-fast" onclick="goTop()"></div>
	
	</body>

</html>