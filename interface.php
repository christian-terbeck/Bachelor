<?php

include_once("mysql.php");

?>
<!DOCTYPE HTML>
<html>

	<head>
	
		<title>GEO1 Informationssystem | Interface</title>
		
		<meta name="author" content="Christian Terbeck">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
		<!--<base href="">-->
		
		<!--Load styles-->
		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/interface.css">
		
		<!--Load fonts-->

		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" property="stylesheet" type="text/css">
		
		<!--Shortcut icon-->
		
		<!--<link rel="shortcut icon" type="image/x-icon" href="images/icon.ico">-->
		
		<!--Load scripts-->
		
		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<script src="js/interface.js"></script>
	
	</head>
	
	<body>
	
		<div class="wrapper">
		
			<div class="form">
				<h2 class="form__head">New Dataset</h2>
				<form name="data-form" action="" method="POST">
					<input type="hidden" name="sendData" />
					<input type="hidden" name="id" />
					<div class="form__row">
						<div class="form__label">Level</div>
						<select class="form__input" name="level" size="1">
							<option value="" disabled selected>Please select</div>
							<?php
							
							$query = "SELECT * FROM levels ORDER BY id ASC";
							$result = mysqli_query($mysql, $query);
							
								while ($row = mysqli_fetch_assoc($result))
								{
								echo "<option value='{$row["id"]}'>{$row["name"]}</div>";
								}
								
							?>
						</select>
					</div>
					<div class="form__row">
						<div class="form__label">Room No.</div>
						<input class="form__input" type="text" name="no" />
					</div>
					<div class="form__row">
						<div class="form__label">Institute</div>
						<select class="form__input" name="institute" size="1">
							<option value="" disabled selected>Please select</div>
							<?php
							
							$query = "SELECT * FROM institutes ORDER BY name_de ASC";
							$result = mysqli_query($mysql, $query);
							
								while ($row = mysqli_fetch_assoc($result))
								{
								echo "<option value='{$row["id"]}'>{$row["name_de"]}</option>";
								}
							
							?>
						</select>
					</div>
					<div class="form__row">
						<div class="form__label">Description</div>
						<textarea class="form__input" name="description" placeholder="optional"></textarea>
					</div>
					<div class="form__row">
						<div class="form__label">Category</div>
						<select class="form__input" name="category" size="1">
							<option value="" disabled selected>optional</div>
							<?php
							
							$query = "SELECT * FROM categories ORDER BY name_de ASC";
							$result = mysqli_query($mysql, $query);
							
								while ($row = mysqli_fetch_assoc($result))
								{
								echo "<option value='{$row["id"]}'>{$row["name_de"]}</option>";
								}
							
							?>
						</select>
					</div>
					<div class="form__row">
						<div class="form__label">Area start</div>
						<input class="form__input form__input--small" type="number" name="x1" placeholder="X" min="0" max="100" />
						<input class="form__input form__input--small" type="number" name="y1" placeholder="Y" min="0" max="100" />
					</div>
					<div class="form__row">
						<div class="form__label">Area end</div>
						<input class="form__input form__input--small" type="number" name="x2" placeholder="X" min="0" max="100" />
						<input class="form__input form__input--small" type="number" name="y2" placeholder="Y" min="0" max="100" />
					</div>
					<div class="form__row">
						<div class="form__label">Entrance</div>
						<input class="form__input form__input--small" type="number" name="doorX" data-name="doorx" placeholder="X" min="0" max="100" />
						<input class="form__input form__input--small" type="number" name="doorY" data-name="doory" placeholder="Y" min="0" max="100" />
					</div>
					<div class="form__row">
						<div class="form__label">People</div>
						<textarea class="form__input" name="people"></textarea>
					</div>
					<div class="form__row">
						<div class="form__label">Hours</div>
						<input class="form__input form__input--small" type="text" name="hoursStart" data-name="hoursstart" />
						<input class="form__input form__input--small" type="text" name="hoursEnd" data-name="hoursend" />
					</div>
					<div class="form__row" style="margin-top: 15px;">
						<div class="form__button clickable transition-fast" onclick="saveDataset('default')">Save</div>
						<div class="form__button clickable transition-fast" onclick="saveDataset('copy')">Save & Copy</div>
						<div class="form__button clickable transition-fast" onclick="deleteDataset()">Delete</div>
						<div class="form__button clickable transition-fast" style="float: left;" onclick="newDataset()">+New</div>
					</div>
				</form>
			</div>
			
			<?php
			
			$query = "SELECT * FROM rooms ORDER BY id DESC";
			$result = mysqli_query($mysql, $query);
			$amount = mysqli_num_rows($result);
			
			?>
			
			<div class="data">
				<h2 class="data__head">All Datasets (<span class="data__amount"><?php echo "$amount"; ?></span>)</h2>
				<div class="data__container">
					<?php
					
					include("datasets.php");
					
					?>
				</div>
			</div>
		
		</div>
	
	</body>
	
</html>