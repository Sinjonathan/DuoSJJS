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
		
		// Check data coherance
		for ($cpt = 0; $cpt < count($_POST['param']); $cpt++) {
			$field = $form->getField($_POST['param'][$cpt]);
	
			if (stristr($field->type,"date")) {
				
				if( $_POST['values'][$cpt] == date('Y-m-d',strtotime($_POST['values'][$cpt])) )
				{
					$dateTime = \DateTime::createFromFormat('Y-m-d', $_POST['values'][$cpt]);
				}else{
					$dateTime = \DateTime::createFromFormat('d/m/Y', $_POST['values'][$cpt]);
				}
				
				$_POST['values'][$cpt] = date_format($dateTime,"Y-m-d");
			}
			// Indexed field
			if ($_POST['values'][$cpt] === 'select_value_null') {
				if ($field->required) {
					echo -1;
					die();
				}
				$_POST['values'][$cpt] = null;
			}
		}

		// Insertion
		if ($_POST ['mode'] == 'insert') { 
			$status = $form->insert ( $_POST ['values'] );
		} 
		// Update
		elseif ($_POST ['mode'] == 'update') {
			$status = $form->update ( $_POST ['values'] );
		}
		
		echo $status;
	} else {
		throw new \Exception();
	}
}

?>