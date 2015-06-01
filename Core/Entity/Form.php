<?php

namespace Core\Entity;

use Core\Entity\Field;
use Core\Driver\FormDriver;
use Core\Driver\FormDriverMYSQL;
use Core\Driver\FormDriverPGSQL;
use Core\Exception\FieldAlreadyDefinedException;
use Core\Exception\DriverNotSupportedException;
use Core\Exception\EmptyFieldListException;
use Core\Exception\FieldUnknownInDatabaseException;
use Core\Exception\PluginPathException;
use Core\Exception\FieldStatesCompatibilityException;

/**
 * The representation of a form
 * @author Sébastien JOLY
 * @author Jonathan SANTONI
 */
class Form {
	
	// Default parameters
	protected static $BDD_URL 		= "127.0.0.1";
	protected static $BDD_LOGIN 	= "root";
	protected static $BDD_PASSWORD 	= "";
	protected static $BDD_DATABASE 	= "";
	protected static $BDD_DRIVER	= "pgsql";
	protected static $LANGUAGE 		= "fr";
	protected static $ENCODING 		= "UTF-8";
	protected static $MODE			= "insert";
	
	// Database parameters
	public $url 		= "";
	public $login 		= "";
	public $password 	= "";
	public $database 	= "";
	public $table		= "";
	public $driver 		= "";
	public $language 	= "";
	public $encoding 	= "";
	public $mode		= "";
	
	// Form attributes
	public $id;
	public $fieldList;
	public $pluginPath;
	public $labelSize = array();
	public $inputSize = array();
	
	private $connect 	= null;
	private $concreteDriver = null;
	private $request	= "";
	private $error 		= "";
	
	/**
	 * Constructor of a Form
	 * @param String $pluginPath	: The location of the plugin in the project 
	 * @throws PluginPathException	: Exception when pluginPath is not specified
	 */
	function __construct($pluginPath) {

		$this->url 		= Form::$BDD_URL;
		$this->driver 	= Form::$BDD_DRIVER;
		$this->database = Form::$BDD_DATABASE;
		$this->login 	= Form::$BDD_LOGIN;
		$this->password = Form::$BDD_PASSWORD;
		$this->language = Form::$LANGUAGE;
		$this->encoding = Form::$ENCODING;
		$this->mode		= Form::$MODE;
		
		$this->fieldList = new \ArrayObject();
		
		if (!empty($pluginPath)) {
			$this->pluginPath = $pluginPath;
		}else{
			throw new PluginPathException('__construct');
		}
	}
		
	/**
	 * Initializing the connection to the database
	 * @param string $url			: URL of the database server
	 * @param string $driver		: Database type (MySQL , PostgreSQL , ...)
	 * @param string $database		: Database name
	 * @param unknown $table		: Database table
	 * @param string $login			: User login in database 
	 * @param string $password		: User password in database
	 * @return Form					: The initialized form
	 */
	public function init($url = "", $driver = "", $database = "",  $table, $login = "", $password = "") {

		if (!empty($url)) 		$this->url = $url;
		if (!empty($driver)) 	$this->driver = $driver;
		if (!empty($database)) 	$this->database = $database;
		if (!empty($login)) 	$this->login = $login;
		if (!empty($password)) 	$this->password = $password;
		
		$this->table = $table;
		
		$this->id = time();
		
		$this->setConcreteDriver();
		
		$this->initGridSize();
				
		return $this;
	}
	
	/**
	 * Initialize the boostrap parameters
	 */
	public function initGridSize() {
		$this->labelSize['col-xs-'] = 4;
		$this->labelSize['col-sm-'] = 4;
		$this->labelSize['col-md-'] = 4;
		$this->labelSize['col-lg-'] = 4;
		$this->inputSize['col-xs-'] = 8;
		$this->inputSize['col-sm-'] = 8;
		$this->inputSize['col-md-'] = 8;
		$this->inputSize['col-lg-'] = 8;
	}
	
	/**
	 * Setter for set all boostrap parameters
	 * @param integer $label	: The label bootstrap size
	 * @param integer $input	: The input boostrap size
	 */
	public function setAllGridColumn($label, $input) {
		foreach ($this->labelSize as $key => $value) {
			$this->labelSize[$key] = $label;
		}
		
		foreach ($this->inputSize as $key => $value) {
			$this->inputSize[$key] = $input;
		}
	}
	
	/**
	 * Setter for boostrap Xs parameters
	 * @param integer $label	: The label bootstrap size
	 * @param integer $input	: The input boostrap size
	 */
	public function setGridColumnXs($label, $input) {
		$this->labelSize['col-xs-'] = $label;
		$this->inputSize['col-xs-'] = $input;
	}
	
	/**
	 * Setter for boostrap Sm parameters
	 * @param integer $label	: The label bootstrap size
	 * @param integer $input	: The input boostrap size
	 */
	public function setGridColumnSm($label, $input) {
		$this->labelSize['col-sm-'] = $label;
		$this->inputSize['col-sm-'] = $input;
	}
	
	/**
	 * Setter for boostrap Md parameters
	 * @param integer $label	: The label bootstrap size
	 * @param integer $input	: The input boostrap size
	 */
	public function setGridColumnMd($label, $input) {
		$this->labelSize['col-md-'] = $label;
		$this->inputSize['col-md-'] = $input;
	}
	
	/**
	 * Setter for boostrap Lg parameters
	 * @param integer $label	: The label bootstrap size
	 * @param integer $input	: The input boostrap size
	 */
	public function setGridColumnLg($label, $input) {
		$this->labelSize['col-Lg-'] = $label;
		$this->inputSize['col-Lg-'] = $input;
	}
	
	/**
	 * Get the label boostrap size
	 * @return string	: The labelSize
	 */
	public function getLabelSize() {
		$labelsPrint = " ";
	
		foreach ($this->labelSize as $key => $value) {
			$labelsPrint .= $key . $value . " ";
		}
	
		return $labelsPrint;
	}
	
	/**
	 * Get the input boostrap size
	 * @return string	: The inputSize
	 */
	public function getInputSize() {
		$inputsPrint = " ";
	
		foreach ($this->inputSize as $key => $value) {
			$inputsPrint .= $key . $value . " ";
		}
	
		return $inputsPrint;
	}
	
	/**
	 * Connecting to the database via the PDO tool
	 * @throws \PDOException	: The catched PDOException
	 */
	private function connect() {
		try {
			$this->connect = new \PDO($this->driver . ':host=' . $this->url . ';dbname=' . $this->database . '', $this->login, $this->password);
			$this->connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}catch (\PDOException $e) {
			echo $e->getMessage();
			throw new \PDOException($e->getMessage());
		}
	}
	
	/**
	 * Get a list of form fields
	 * @return \ArrayObject 	: The list of the form field
	 */
	private function getFieldList() {
		return $this->fieldList;
	}
	
	/**
	 * Returns the field whose name is passed as a parameter
	 * @param String $name						: The name of the field in the database
	 * @throws FieldUnknownInDatabaseException	: Exception if the field not exist
	 * @return \ArrayObject						: A list with the field or null if is not found
	 */
	public function getField($name) {
		foreach ($this->fieldList as $field)
			if ($field->name == $name)
				return $field;
		//throw new FieldUnknownInDatabaseException($name);
		return null;
	}
	
	/**
	 * Add a field in the form
	 * @param String $name						: The name of the field in the database
	 * @param String $label						: The label of the field
	 * @param string $defaultValue				: The default value of the field
	 * @throws FieldUnknownInDatabaseException 	: Exception when the field is not in the database
	 * @throws FieldAlreadyDefinedException		: Exception when the field is already defined in the form
	 * @return Field							: The field added
	 */
	public function addField($name, $label, $defaultValue = null) {
		
		$this->connect();
		$request = $this->concreteDriver->getFieldType($name);
		$response = $this->prepareExecute($request['request'], $request['parameters']);
		
		if($response->count() === 0) {
			throw new FieldUnknownInDatabaseException($name);
		}
		
		// If the field already exist with this name, make an exception
		$field = $this->getField($name);
		if ($field instanceof Field) {
			throw new FieldAlreadyDefinedException($field->name);
		
		// Else create it
		}else{
			// If is a primary key 
			if ($this->concreteDriver->isIndex($name)) {
				$field = new Field($this, $this->table, $name, $label, $response[0]['column_type'], true);
				$field->required = true;
				
				if($this->mode === 'update') {
					$field->disabled = true;
				}
				
				if ($this->concreteDriver->isAutoGenerated($name)) {
					$field->autogenerated = true;
					$field->hidden = true;
				}
			}else{
				$field = new Field($this, $this->table, $name, $label, $response[0]['column_type'], false);
			}
			$field->defaultValue = trim($defaultValue);
			$this->fieldList->append($field);
		}
		
		return $field;
	}
	
	/**
	 * Add fields to the form whose values ​​are indexed in the manner of a foreign key
	 * @param String $name					: The name of the field in the database
	 * @param String $label					: The label of the field
	 * @param String $ref_table				: The name of the table in which the reference is
	 * @param String $ref_column_index		: The indexes stored in the column in the current table
	 * @param String $ref_column_values		: The corresponding values ​​in the index to display in the drop-down list
	 * @throws \PDOException				: PDOException
	 * @throws FieldAlreadyDefinedException	: Exception when the field is already defined
	 * @return Field						: The indexed field
	 */
	public function addIndexedField($name, $label, $ref_table, $ref_column_index, $ref_column_values) {
	
		try{
			$this->connect();
	
			$req = $this->connect->prepare("SELECT DISTINCT ".$ref_column_index." , ".$ref_column_values." FROM ".$ref_table);
			$req->execute();
			$results = $req->fetchAll(\PDO::FETCH_ASSOC);
	
		}catch (\PDOException $e) {
			$this->connect = null;
			echo $e->getMessage();
			throw new \PDOException($e->getMessage());
		}
	
		// If the field already exist with this name, make an exception
		$field = $this->getField($name);
		if ($field instanceof Field) {
			throw new FieldAlreadyDefinedException($field->name);
		
		// Else created it
		}else{
			$field = new Field($this, $this->table, $name, $label, 'select', false);
			$field->indexed = true;
			foreach ($results as $result) {
				$field->indexedValues[$result[$ref_column_index]] = $result[$ref_column_values];
			}
			$this->fieldList->append($field);
		}
	
		return $field;
	}
	
	/**
	 * Returns information about the database
	 */
	public function printInfoBDD() {
		print("__________Connexion__________<br/><br/>");
		print("URL : ".$this->url."<br/>");
		print("Database Type : ".$this->driver."<br/>");
		print("Language : ".$this->language."<br/>");
		print("Encodage : ".$this->encoding."<br/>");
		print("Database name : ".$this->database."<br/>");
		print("Table name : ".$this->table."<br/>");
		print("Login : ".$this->login."<br/>");
		print("Password : ".$this->password."<br/>");
		print("_____________________________");
	}
	
	/**
	 * Prepare and execute a request
	 * @param array $request		:  
	 * @param array $parameters		: 
	 * @return \ArrayObject			: The result of the request
	 */
	public function prepareExecute($request, $parameters) {
		try {
			if ($this->connect) {
				$req = $this->connect->prepare($request);
				$req->execute($parameters);
				$data = new \ArrayObject();
	
				if (substr($request, 0, strlen("SELECT")) == "SELECT") {
					while ($row = $req->fetch()) {
						$data->append($row);
					}
				}
				$req->closeCursor();
			}
		}
		catch (\Exception $e) {
			$this->connect = null;
			$this->error = 'Exécution de la requete: ' . $e->getMessage();
		}
		
		return $data;
	}
	
	/**
	 * Initilize driver 
	 * @throws DriverNotSupportedException	: Exception when a driver is unknown
	 */
	private function setConcreteDriver() {
		if ($this->concreteDriver == null) {
			switch ($this->driver) {
				case "pgsql" :
					$this->concreteDriver = new FormDriverPGSQL();
					break;
				case "mysql" :
					$this->concreteDriver = new FormDriverMYSQL();
					break;
				default :
					throw new DriverNotSupportedException( $this->driver );
					break;
			}
			$this->concreteDriver->setForm( $this );
		}
	}
	
	/**
	 * Setter of database table
	 * @param String $table		: The table name
	 */
	public function setTable($table) {
		$this->table = $table;
	}
	
	/**
	 * Return the HTML form
	 * @throws EmptyFieldListException	: Exception when try to access on a empty field list
	 */
	public function show() {
		
		if(count($this->fieldList) == 0) {
			throw new EmptyFieldListException("show");
		}
		
		$html = '<div id="alert-error" class="alert-failure" style="display:none;">Error : SQL constraints violation.</div>';
		$html .= '<div id="alert-success-insert" class="alert-success" style="display:none;">1 line added in "'.$this->table.'".</div>';
		$html .= '<div id="alert-failure-insert" class="alert-failure" style="display:none;">Error : Insert failure. Please check the database constraints.</div>';
		$html .= '<div id="alert-success-update" class="alert-success" style="display:none;">1 line updated in "'.$this->table.'".</div>';
		$html .= '<div id="alert-failure-update" class="alert-failure" style="display:none;">Error : Update failure. Please check the database constraints.</div>';

		$html .= '<br /><form id="'.$this->id.'" class="form-horizontal" method="post" action="" onsubmit="return validateForm()">';
		foreach ($this->fieldList as $field)
			$html .= $field->showField();
		
		$html .= '<div class="form-group"><div class="col-sm-offset-4 col-sm-10 col-md-offset-4 col-md-10">';
		$html .= '<button type="submit" class="btn btn-primary" name="request" id="request" value="'.$this->mode.'">Envoyer</button></div>';
		$html .= '</div></div>';
		$html .= '<input type="hidden" id="formID" name="formID" value="'.$this->id.'">';
		$html .= '<input type="hidden" id="pluginPath" name="pluginPath" value="'.$this->pluginPath.'">';
		$html .= '</form>';
		print($html);
		
		$this->check();
	}
	
	/**
	 * Insertion in database
	 * @param array $values		: The values to insert
	 * @return integer 			: The insertion status
	 */
	public function insert($values) {
		
		$request = "INSERT INTO " . $this->table . " (";
		$cptField = 0;
		$boolPosition = array();
		foreach ($this->fieldList as $field) {
			$request .= $field->name . ", ";

			if(stristr($field->type,"boolean") || stristr($field->type,"tinyint")){
				array_push($boolPosition, $cptField);
			}
			
			$cptField++;
		}
		
		$request = substr($request, 0, strlen($request)-2);
		$request .= ") VALUES (";
		
		for($i = 0; $i < sizeof($values); $i++){
			if(in_array($i, $boolPosition))
				$request .= $values[$i].", ";
			else
				$request .= "'".str_replace("'", "''", $values[$i])."', ";
		}
			
		$request = substr($request, 0, strlen($request)-2);
		$request .= ");";
		
		return $this->connect->exec($request);
	}

	/**
	 * Update in database
	 * @param array $values		: The values to insert
	 * @return integer 			: The insertion status
	 */
	public function update($values) {

		$request = "UPDATE " . $this->table . " SET ";
		$requestCondition = " WHERE ";
		$cptField = 0;

		foreach ($this->fieldList as $field) {
			if(!$field->primaryKey) {
				if(stristr($field->type,"boolean") || stristr($field->type,"tinyint"))
					$request .= $field->name . " = " . $values[$cptField] . ", ";
				else
					$request .= $field->name . " = " . "'".str_replace("'", "''", $values[$cptField])."', ";
			} else {
				$requestCondition .= $field->name . " = " . "'".str_replace("'", "''", $values[$cptField]) ."', ";
			}
			$cptField++;
		}

		$request = substr($request, 0, strlen($request)-2);
		$requestCondition = substr($requestCondition, 0, strlen($requestCondition)-2);
		$request .= $requestCondition  . ";";

		return $this->connect->exec($request);
	}
	
	/**
	 * Setter for hidden option
	 * @param String $fieldname						: The field name
	 * @param boolean $bool							: The new hidden value
	 * @throws FieldStatesCompatibilityException	: Exception throw when invalid compatibility
	 */
	public function setHidden($fieldname, $bool) {
		if ($bool) {
			if ($this->getField($fieldname)->required && $this->getField($fieldname)->defaultValue == null) {
				throw new FieldStatesCompatibilityException($fieldname,"hidden = true", "required field without default value");
			}
		}else{
			if ($this->mode === "update" && $this->getField($fieldname)->autogenerated) {
				throw new FieldStatesCompatibilityException($fieldname,"hidden = false", "autogenerated primary key in update form");
			}
		}
		$this->getField($fieldname)->hidden = $bool;
	}
	
	/**
	 * Setter for required option
	 * @param String $fieldname						: The field name
	 * @param boolean $bool							: The new required value
	 * @throws FieldStatesCompatibilityException	: Exception throw when invalid compatibility
	 */
	public function setRequired($fieldname, $bool) {
		if ($bool) {
			if ($this->getField($fieldname)->disabled && $this->getField($fieldname)->defaultValue == null) {
				throw new FieldStatesCompatibilityException($fieldname,"required = true", "disabled field without default value");
			}elseif ($this->getField($fieldname)->hidden && $this->getField($fieldname)->defaultValue == null) {
				throw new FieldStatesCompatibilityException($fieldname,"required = true", "hidden field without default value");
			}
		}else{
			if ($this->getField($fieldname)->primaryKey) {
				throw new FieldStatesCompatibilityException($fieldname,"required = false", "primary key");
			}
		}
		$this->getField($fieldname)->required = $bool;
	}
	
	/**
	 * Setter for required all field
	 * @param boolean $bool			: The new required value
	 */
	public function setAllRequired($bool) {
		foreach ($this->fieldList as $field) {
			$this->setRequired($field->name, $bool);
		}
	}

	/**
	 * Setter for disabled option
	 * @param String $fieldname						: The field name
	 * @param boolean $bool							: The new disabled value
	 * @throws FieldStatesCompatibilityException	: Exception throw when invalid compatibility
	 */
	public function setDisabled($fieldname, $bool) {
		if ($bool) {
			if ($this->getField($fieldname)->required && $this->getField($fieldname)->defaultValue == null) {
				throw new FieldStatesCompatibilityException($fieldname,"disabled = true", "required field without default value");
			}
		}else{
			if ($this->mode === "update" && $this->getField($fieldname)->primaryKey) {
				throw new FieldStatesCompatibilityException($fieldname,"disabled = false", "primary key in update form");
			}
		}
		$this->getField($fieldname)->disabled = $bool;
	}
	
	/**
	 * Load value from a given index
	 * @param String $id_column			: The name of the id column
	 * @param Integer $id_value			: The value of the id column
	 * @throws EmptyFieldListException	: Exception when try to access on a empty field list
	 * @throws \PDOException			: PDOException
	 */
	public function loadValuesFromIndex($id_column, $id_value) {
		$fields = "";
		$tables = "";
		
		if(count($this->fieldList) == 0) {
			throw new EmptyFieldListException("loadValuesFromIndex");
		}
				
		foreach ($this->fieldList as $field) {
			$fields .= $field->table . "." . $field->name . ", ";
			
			if(strpos($tables,$field->table.", ") === false)
				$tables .= $field->table . ", ";
		}
		$fields = substr($fields, 0, strlen($fields)-2);
		$tables = substr($tables, 0, strlen($tables)-2);
		
		$request = "SELECT ".$fields." FROM ".$tables." WHERE $id_column = '".$id_value."';";

		try {
			$req = $this->connect->query($request);
		}catch (\PDOException $e){
			echo $e->getMessage();
			throw new \PDOException($e->getMessage());	
		}
		
		$row = $req->fetch();
		
		foreach ($this->fieldList as $field) {
			$this->getField($field->name)->defaultValue = trim($row[$field->name]);
		}	
	}
	
	/**
	 * Set the form to update mode and load value from an index
	 * @param String $id_column			: The name of the id column
	 * @param Integer $id_value			: The value of the id column
	 */
	public function isUpdateForm($id_column, $id_value) {
		
		$this->loadValuesFromIndex($id_column, $id_value);
		$this->mode = "update";
	
		// If primary key not already printed, add it to the list
		$field = $this->getField($id_column);
		if($field === null ) {
			$this->addField($id_column,"ID for update", $id_value);
		}
		
		$this->setDisabled($id_column, true);
		if ($this->getField($id_column)->autogenerated) { $this->setHidden($id_column, true); }
	}

	/**
	 * Serialize the Form object
	 * @return multitype:string
	 */
	public function __sleep() {
		return array(
			'url',
			'login',
			'password',
			'database',
			'table',
			'driver',
			'language',
			'encoding',
			'mode',
			'fieldList'
		);
	}
	
	/**
	 * Call after wakeup
	 */
	public function __wakeup() {
		$this->connect();
	}
	
	/**
	 * Call when Form object is serialized
	 */
	public function check() {
		file_put_contents($this->pluginPath . "/Core/temp/".$this->id, serialize($this));
	}
}