<?php

header('Content-Type: text/text');

require './Form.php';
use form\php\Form;

$currentTime = time();
if($dir = opendir('../temp')) {
	while(false !== ($file = readdir($dir))) {
		if (($currentTime-$file > 24*3600) && $file-0 != $_POST['formID'] && !is_dir($file)) {
			unlink('../temp/' . $file);
		}
	}
}

if (isset($_POST['formID']) && isset($_POST['mode'])) {
	$path = '../temp/' . $_POST['formID'];
	
	if (file_exists($path)) {
		$form = unserialize(file_get_contents($path));
		
		// Insertion dans la table
		if($_POST['mode'] == 'insert') {
 			$status = $form->insert($_POST['values']);
		}
		// Update
		elseif ($_POST['mode'] == 'update') {
			$status = $form->update($_POST['values']);
		}
		
		echo "Resultat : " . $status;
	}else{
		throw new Exception();
	}
}
?>