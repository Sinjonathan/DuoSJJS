<?php
// Lancement de l'autoloader
require_once("Loader/SplClassLoader.php");
$loader = new SplClassLoader('Core', __DIR__);
$loader->register();

function loadFormMakerPluginInfo($pathFromSrc) {
	
	echo 
		'<!-- jQuery UI -->
		<script src="'. $pathFromSrc .'/Core/Resources/lib/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="'. $pathFromSrc .'/Core/Resources/lib/jquery-ui/jquery-ui.min.css">
		<link rel="stylesheet" href="'. $pathFromSrc .'/Core/Resources/lib/jquery-ui/jquery-ui.structure.min.css">
		<link rel="stylesheet" href="'. $pathFromSrc .'/Core/Resources/lib/jquery-ui/jquery-ui.theme.min.css">
		<!-- formulaire -->
		<script src="'. $pathFromSrc .'/Core/Script/form.js"></script>';
} 
?>