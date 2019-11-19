<?php

	if (isset($_GET["language"]) && isset($_GET["destination"]))
	{
	require_once(__DIR__ ."/lib/phpqrcode/qrlib.php");

	$language = $_GET["language"];
	$destination = $_GET["destination"];

	ob_start("callback");

	$url = "https://christian-terbeck.de/projects/ba/navigation.php?language=".$language."&destination=".$destination;

	$debugLog = ob_get_contents();
	ob_end_clean();

	QRcode::png($url, false, QR_ECLEVEL_H, 20, 4);
	}

?>