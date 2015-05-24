<?php

namespace form\php;

/**
 * Driver de base de données (MySQL, pgSQL,...)
 * @author Sébastien JOLY
 * @author Jonathan SANTONI
 */
abstract class FormDriver {
	/**
	 * Call this function to update the type
	 * @return array the SQL function
	 */
	//public abstract function getSQL();
	
	/**
	 * Call this function to have all function
	 * @return array the SQL function
	 */
	//public abstract function getAllColumn();
	
	/**
	 * Retourne le type du champs passé en paramètre
	 */
	public abstract function getFieldType($name);
	
	/**
	 * Change le formulaire de la classe
	 * @param unknown $form
	 */
	public abstract function setForm($form);
	
	/**
	 * Retourne le formulaire de la class
	 * @return form
	 */
	public abstract function getForm();
	
	/**
	 * Return the name of the column for this row
	 * @param unknown $aRow
	 */
	//public abstract function getColumnName($aRow);
	
	/**
	 * Return the name of the column for this row
	 * @param unknown $aRow
	 */
	//public abstract function getColumnType($aRow);
	
	/**
	 * Return the name of the column for this row
	 * @param unknown $aRow
	 */
	//public abstract function getColumnLength($aRow);
	
	/**
	 * Call this function to know if name is index of table
	 * @param unknown $name
	 * @return boolean
	 */
	public abstract function isIndex($name);
}