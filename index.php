<?php

require_once("./includes/db.php");
require_once("./includes/functions.php");

// $valsTab = callApi($pdo);
// test()

?>

<!-- API KEY(MARKETPLACE) : 14c9d3622d2fa33fa7df100d3d6b671a -->
<!-- API KEY(ALPHA VANTAGE) : 667NFLKBK2FPD0B3 -->
<!-- API KEY(FINHUB) : c40l5oaad3idvnt9uh8g -->
<!-- API KEY(QUANDL) : Wv3pRpo3qgxx2bvv_BJr -->

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Appli bourse</title>
	<link rel="stylesheet" href="./styles/main.css">
	
</head>
<body>
	<section class="flex">
		<div class="popup_add">
			<h2>Nouvelle action</h2>
			<form action="" method="get" class="ff">
				<div>
					<label for="name">Nom de l'action : </label>
					<input type="text" name="name" class="form_name" required>
				</div>
				<div>
					<label for="price">Coût de revient : </label>
					<input type="float" name="price" class="form_price" required>
				</div>
				<div>
					<label for="qt">Quantité : </label>
					<input type="number" name="qt" id="form_qt" required>
				</div>
				<div>
					<label for="type">Type : </label>
					<select type="text" name="type" class="form_type" required>
						<option value="Compte titre Sandrine">Compte titre Sandrine</option>
						<option value="Compte titre Philippe">Compte titre Philippe</option>
						<option value="PEA Sandrine">PEA Sandrine</option>
						<option value="PEA Philippe">PEA Philippe</option>
					</select>
				</div>
				<div>
					<label for="code">Code : </label>
					<input type="text" name="code" id="form_code" required>
				</div>
				<div>
					<input type="submit" name="input_action" value="Ajouter action">
					<?php 
					if(isset($_GET["input_action"])) :
						addAction( $pdo, $_GET["name"], $_GET["price"], $_GET["qt"], $_GET["code"], $_GET["type"]); 
						endif ?>
				</div>
			</form>
			<button type="button" class="shButton show" onclick="showActions()">
				Afficher mes actions
			</button>
</br>
			<div>
				<h2>Vos actions</h2>
				<div class="actual_actions">
					<?= displayAction($pdo) ?>
				</div>
			</div>
		</div>
		<div class="">
			<table>
				<thead>
					<tr>
						<td><h2>Nom de l'action</h2></td>
						<td><h2>Cours</h2></td>
						<td><h2>variation</h2></td>
						<td><h2>Variation %</h2></td>
						<td><h2>Coût de revient</h2></td>
						<td><h2>Valorisation initiale</h2></td>
						<td><h2>Valorisation</h2></td>
						<td><h2>Gain/perte</h2></td>
						<td><h2>%</h2></td>
						<td><h2>Compte</h2></td>
					</tr>
				</thead>
					<tbody>
					<?php tab($valsTab) ?>
				</tbody>
			</table>
		</div>
	</section>

	<script type="text/javascript" src="./scripts/app.js"></script>
</body>
</html>