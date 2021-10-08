<?php

require_once("./includes/db.php");
require_once("./includes/functions.php");

$abstr = null;
/* Fonction renvoyant l'adresse de la page actuelle */


$filterArr = [
	// "Alpha" => ["mode_alpha", "Mode Alpha", "Mode Alphabétique"],
	// "Croissant" => ["mode_croissant", "Mode Croissant", "Mode Croissant"],
	// "Décroissant" => ["mode_décroissant", "Mode Décroissant", "Mode Décroissant"],
	"PEAS" => ["PEAS", "PEA Sandrine", "PEA Sandrine"],
	"PEAP" => ["PEAP", "PEA Philippe", "PEA Philippe"],
	"CTS" => ["CTS", "Compte titre Sandrine", "Compte titre Sandrine"],
	"CTP" => ["CTP", "Compte titre Philippe", "Compte titre Philippe"],
	"Gain" => ["gain", "Gain", "Gain"],
	"Perte" => ["perte", "Perte", "Perte"],
];

$filters = getFilters();

$mode = filter_input(INPUT_GET, "mode");
$filtre = filter_input(INPUT_GET, "filtre");
if($mode){
	setcookie("mode", $mode, time() + (60 * 60 * 24 * 365));
	setcookie("filtre", $filtre, time() + (60 * 60 * 24 * 365));
}
$modeFilter = $mode ?? filter_input(INPUT_COOKIE, "mode");
$filtreFilter = $filter ?? filter_input(INPUT_COOKIE, "filter");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Appli bourse</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet"> 
	<link rel="stylesheet" href="./styles/main.css">
</head>
<body>
	<section class="page">
		<section class="gauche">
			<!-- Filtrage -->
			<?php
				displayFilter($filterArr);
				if(isset($filters) && isset($_GET["active_filter"])) :
					$valsTab = callApi($pdo, $filters, $_GET["active_filter"]);
				elseif(isset($filters)) :
					$valsTab = callApi($pdo, $filters, $abstr);
				else :
					$valsTab = callApi($pdo, $abstr, $abstr);
				endif
			?>
			<div><a class="reload" href="http://localhost/bourse-gh/">Recharger la page</a></div>
			<!-- Recherche par mot clé -->
			<!-- Ajouter une action -->
			<div>
				<h2>Ajouter une action :</h2>
				<form action="" method="get" class="form_new_action">
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
						<label for="comment"> Mots clés : </label>
						<input type="text" name="comment" id="form_code">
					</div>
					<div>
						<input type="submit" name="input_action" value="Ajouter action">
						<?php 
						if(isset($_GET["input_action"])) :
							addAction( $pdo, $_GET["name"], $_GET["price"], $_GET["qt"], $_GET["code"], $_GET["type"], $_GET["comment"]); 
						endif ?>
					</div>
				</form>
			</div>
			<!-- Vendre des actions -->
			<div>
				<h2>Vendre une action :</h2>
				<form>
					<div>
						<label for="sell_code"> Code de l'action : </label>
						<input type="text" name="sell_code" id="form_sell_code" required>
					</div>
					<div>
						<label for="sell_compte">Type : </label>
						<select type="text" name="sell_compte" class="form_type" required>
							<option value="Compte titre Sandrine">Compte titre Sandrine</option>
							<option value="Compte titre Philippe">Compte titre Philippe</option>
							<option value="PEA Sandrine">PEA Sandrine</option>
							<option value="PEA Philippe">PEA Philippe</option>
						</select>
					</div>
					<div>
						<label for="sell_qt"> Quantité : </label>
						<input type="number" name="sell_qt" id="form_sell_qt" required>
					</div>
						<div>
						<input type="submit" name="sell_action" value="Vendre une action">
						<?php
							if(isset($_GET["sell_action"])) :
								sellAction($pdo, $_GET["sell_code"], $_GET["sell_qt"], $_GET["sell_compte"], $valsTab);
							endif ?>
					</div>
				</form>
			</div>
			<!-- Actions possédées -->
			<button type="button" class="shButton show" onclick="showActions()">
					Afficher mes actions
				</button>
				<div>
					<h2>Vos actions</h2>
					<div class="actual_actions">
						<?= displayAction($pdo) ?>
					</div>
				</div>
				<!-- Actions vendues -->
				<button type="button" class="shButtonSell show" onclick="showActionsSell()">
					Afficher mes actions vendues
				</button>
				<div>
					<h2>Vos actions vendues</h2>
					
				</div>
		</section>
		<!--  -->

		<section class=droite>
			<div class="blank"></div>
			<button type="button" class="buttonTools unactive" onclick="showTools()">
				Cacher les outils
			</button>
			<div class="actions_tab">
				<table>
					<thead>
						<tr>
							<td><h2>Nom de l'action</h2></td>
							<td><h2>Code de l'action</h2></td>
							<td><h2>Quantité</h2></td>
							<td><h2>Cours</h2></td>
							<td><h2>Variation</h2></td>
							<td><h2>Coût de revient</h2></td>
							<td><h2>Valorisation initiale</h2></td>
							<td><h2>Valorisation</h2></td>
							<td><h2>Gain/perte</h2></td>
							<td><h2>Compte</h2></td>
							<td><h2>Mots clés</h2></td>
							<td><h2>News</h2></td>
						</tr>
					</thead>
						<tbody>
							<?php 
								fillTab($valsTab, $mode) 
							?>
						</tbody>
				</table>
			</div>
			<div class="actual_actions_sell">
						<table>
							<thead>
								<tr>
									<td><h2>Nom de l'action</h2></td>
									<td><h2>Prix d'achat</h2></td>
									<td><h2>Quantité vendue</h2></td>
									<td><h2>Compte</h2></td>
									<td><h2>Code</h2></td>
									<td><h2>Cours au moment de la vente</h2></td>
									<td><h2>Variation au moment de la vente</h2></td>
									<td><h2>Valorisation initiale</h2></td>
									<td><h2>Valorisation au moment de la vente</h2></td>
									<td><h2>Gain/Perte</h2></td>
								</tr>
							</thead>
							<tbody>
								<?= displayActionSell($pdo) ?>
							</tbody>
						</table>
					</div>
		</section>
	</section>
	<script type="text/javascript" src="./scripts/app.js"></script>
</body>
</html>