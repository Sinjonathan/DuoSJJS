<?php

use Core\Exception\PluginPathException;

// Launch autoloader
require_once("Loader/SplClassLoader.php");
$loader = new SplClassLoader('Core', __DIR__);
$loader->register();

/**
 * Initialize the plugin information
 * @param String $pluginPath	: The location of the plugin in the project 
 * @throws PluginPathException	: Exception when pluginPath is not specified
 */
function loadFormMakerPluginInfo($pluginPath) {
	
	if (!empty($pluginPath)) {
		echo
		'<!-- jQuery UI -->
		<script src="'. $pluginPath .'/Core/Resources/lib/jquery-ui/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="'. $pluginPath .'/Core/Resources/lib/jquery-ui/jquery-ui.min.css">
		<link rel="stylesheet" href="'. $pluginPath .'/Core/Resources/lib/jquery-ui/jquery-ui.structure.min.css">
		<link rel="stylesheet" href="'. $pluginPath .'/Core/Resources/lib/jquery-ui/jquery-ui.theme.min.css">
		<!-- formulaire -->
		<script src="'. $pluginPath .'/Core/Script/form.js"></script>';
	}else{
		throw new PluginPathException('loadFormMakerPluginInfo');
	}
} 
?>