<?php

$peopleQuery = "SELECT * FROM people WHERE room = '$room'";
$peopleResult = mysqli_query($mysql, $peopleQuery);

$roomQuery = "SELECT * FROM rooms WHERE id = '$room'";
$roomResult = mysqli_query($mysql, $roomQuery);
$room = mysqli_fetch_assoc($roomResult);

$level = $room["level"];

$levelQuery = "SELECT * FROM levels WHERE id = '$level'";
$levelResult = mysqli_query($mysql, $levelQuery);
$level = mysqli_fetch_assoc($levelResult);

$institute = $room["institute"];

$instituteQuery = "SELECT * FROM institutes WHERE id = '$institute'";
$instituteResult = mysqli_query($mysql, $instituteQuery);
$institute = mysqli_fetch_assoc($instituteResult);

echo '<div class="accordion-box__content-half">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td data-de="Etage" data-en="Level" data-es="Planta">Etage</td>
			<th>'.$level["name"].'</th>
		</tr>
		<tr>
			<td data-de="Raumnummer" data-en="Room No." data-es="Número plana">Raumnummer</td>
			<th>'.$room["no"].'</th>
		</tr>
		<tr>
			<td data-de="Institut" data-en="Institute" data-es="Instituto">Institut</td>
			<th data-de="'.$institute["name_de"].'" data-en="'.$institute["name_en"].'" data-es="'.$institute["name_es"].'">'.$institute["name_de"].'</th>
		</tr>
		<tr>
			<td data-de="Personen" data-en="People" data-es="Personas">Personen</td>
			<th>';
			
			while ($people = mysqli_fetch_assoc($peopleResult))
			{
			echo $people["name"].'<br />';
			}
		
			echo '</th>
		</tr>
		<tr>
			<td data-de="Öffnungs-/Sprechzeiten" data-en="Opening/office hours" data-es="Horarios de apertura">Öffnungs-/Sprechzeiten</td>
			<th>';
			
				if ($room["hoursStart"] > 0 || $room["hoursEnd"] > 0)
				{
				echo $room["hoursStart"].' &ndash; '.$room["hoursEnd"];
				}
				
		echo '</th>
		</tr>
		<tr>
			<td data-de="Beschreibung" data-en="Description" data-es="Descripción">Beschreibung</td>
			<th>'.$room["description"].'</th>
		</tr>
	</table>
</div>
<div class="accordion-box__content-half">
	<img class="qr-code" src="'.$baseUrl.'qr.php?language=de&destination='.$room["id"].'" alt="de" />
	<p data-de="Scannen Sie diesen QR-Code mit Ihrem Smartphone.<br />Die Wegbeschreibung wird anschließend auf Ihrem Endgerät angezeigt." data-en="Scan this QR code with your smartphone.<br />The directions are then displayed on your device." data-es="Escanee este código QR con su teléfono inteligente.<br />Las instrucciones se muestran en su dispositivo.">Scannen Sie diesen QR-Code mit Ihrem Smartphone.<br />Die Wegbeschreibung wird anschließend auf Ihrem Endgerät angezeigt.</p>
</div>';

?>