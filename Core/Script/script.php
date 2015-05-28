<?php

namespace Core\Script;

include '../Entity/Form.php';
include '../Entity/Field.php';

header ( 'Content-Type: text/text' );

$currentTime = time ();
if ($dir = opendir ( '../temp' )) {
	while ( false !== ($file = readdir ( $dir )) ) {
		if (($currentTime - $file > 24 * 3600) && $file - 0 != $_POST ['formID'] && ! is_dir ( $file )) {
			unlink ( '../temp/' . $file );
		}
	}
}

if (isset ( $_POST ['formID'] ) && isset ( $_POST ['mode'] )) {
	$path = '../temp/' . $_POST ['formID'];
	
	if (file_exists ( $path )) {
		$form = unserialize ( file_get_contents ( $path ) );
		
		// On vérifie la cohérence des données
		for ($cpt = 0; $cpt < count($_POST['param']); $cpt++) {
			$field = $form->getField($_POST['param'][$cpt]);
			// Les champs 'date'
			if (stristr($field->type,"date")) {
				$_POST['values'][$cpt] = date_format(new \DateTime($_POST['values'][$cpt]),"Y-m-d");
			}
			// Les champs indexés
			if ($_POST['values'][$cpt] === 'select_value_null') {
				if ($field->required) {
					echo -1;
					die();
				}
				$_POST['values'][$cpt] = null;
			}
		}

		// Insertion dans la table
		if ($_POST ['mode'] == 'insert') { 
			$status = $form->insert ( $_POST ['values'] );
		} 
		// Update
		elseif ($_POST ['mode'] == 'update') {
			$status = $form->update ( $_POST ['values'] );
		}
		
		echo $status;
	} else {
		throw new Exception ();
	}
}

?>