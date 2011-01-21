<?php

abstract class Model extends Application {
	protected $tableName = NULL;
	protected $primaryKey = 'id';
	protected $fields = array();
	protected $requiredFields = array();
	protected $data = array();
	
	public function __call($method, $parameters) {
		preg_match_all('/(^|[A-Z]{1})([a-z]+)/', $method, $methodParts);
		if (!isset($methodParts[0][0]) || !isset($methodParts[0][1])) throw new Exception('Invalid method format', $method);
		
		$operation = $methodParts[0][0];
		array_shift($methodParts[0]);
		
		$name = implode('', $methodParts[0]);
		$property = strtolower(substr($name, 0, 1)) . substr($name, 1);
		$propertyCapitalized = ucfirst($property);
		
		$propertyExists = FALSE;
		$isField = FALSE;
		$hasLoader = FALSE;
		
		if (property_exists($this, $property)) {
			$propertyExists = TRUE;
		}
		
		if ($this->isField($property)) {
			$isField = TRUE;
		} else if ($this->isField($propertyCapitalized)) {
			$isField = TRUE;
			$property = $propertyCapitalized;
		} else if ($this->isField($propertyCapitalized . 'Id')) {
			$isField = TRUE;
			$property = $propertyCapitalized . 'Id';
			$hasLoader = TRUE;
		}
		
		switch ($operation) {
			case 'get':
				if ($isField && $hasLoader) {
					$loaderName = 'load' . $property;
					if (is_null($this->$property)) $this->$loaderName();
					return $this->$property;
				} else if ($isField) {
					return $this->getData($property);
				} else if ($propertyExists) {
					return $this->$property;
				}
			case 'is':
				if ($isField) {
					return $this->getData($property) == 'yes';
				} else if ($propertyExists) {
					return $this->$property == 'yes';
				}
			case 'has':
				if ($isField & $hasLoader) {
					$loaderName = 'load' . $property;
					if (is_null($this->$property)) $this->$loaderName();
					return is_object($this->$property);
				} else if ($isField) {
					return $this->getData($property) != FALSE;
				} else if ($propertyExists) {
					return $this->$property != FALSE;
				}
			case 'load':
				if ($isField & $hasLoader) {
					$finderName = $property . 'Finder';
					$getterName = 'get' . $property . 'Id';
					$this->$property = $finderName::find($this->$getterName());
					return;
				}
			case 'set':
				if ($isField) {
					$this->setData($property, $parameters[0]);
					return;
				} else if ($propertyExists) {
					$this->$property = $parameters[0];
					return;
				}
		}
		
		throw new Exception('Undeclared property', $property);
	}
	
	public function __construct() {
		parent::__construct();
		
		$requiredFields = $this->getRequiredFields();
		$arguments = func_get_args();
		
		if (func_num_args() == 1 && is_array(func_get_arg(0))) {
			$data = func_get_arg(0);
		} else {
			if (count($requiredFields) != count($arguments)) {
				throw new Exception('Number of required fields does not match number of arguments', array('$arguments' => $arguments, '$requiredFields' => $requiredFields));
			}
			
			$data = $arguments ? array_combine($requiredFields, $arguments) : array();
		}
		
		foreach ($requiredFields as $field) {
			if (!array_key_exists($field, $data)) {
				throw new Exception('Required fields missing', array('array_keys($data)' => array_keys($data), '$requiredFields' => $requiredFields));
			}
		}
		
		foreach ($data as $property => $value) {
			if (!$this->isField($property)) continue;
			$setter = 'set' . ucfirst($property);
			$this->$setter($value);
		}
	}
	
	public function getClassName() {
		return get_class($this);
	}
	
	protected function getPrimaryKeyValue() {
		$primaryKeyValue = array();
		if (is_string($this->getPrimaryKey())) {
			$getterName = 'get' . ucfirst($this->getPrimaryKey());
			$primaryKeyValue[$this->getPrimaryKey()] = $this->$getterName();
		} else if (is_array($this->getPrimaryKey())) {
			foreach ($this->getPrimaryKey() as $field) {
				$getterName = 'get' . ucfirst($field);
				$primaryKeyValue[$field] = $this->$getterName();
			}
		}
		return $primaryKeyValue;
	}
	
	public function isField($field) {
		return in_array($field, $this->fields);
	}
	
	public function getData($field = NULL) {
		if (is_null($field)) return $this->data;
		else if (isset($this->data[$field])) return $this->data[$field];
		else return NULL;
	}
	
	public function setData() {
		if (func_num_args() == 2) {
			$this->data[func_get_arg(0)] = func_get_arg(1);
		} else if (func_num_args() == 1) {
			$data = func_get_arg(0);
			$this->data = array_merge($this->data, $data);
		}
	}
	
	protected function prepareSaving() {
		if ($this->isField('status')) $this->setStatus('active');
		if ($this->isField('created')) {
			$this->db->set('created', 'NOW()', FALSE);
			unset($this->data['created']);
		}
	}
	
	public function save() {
		$this->prepareSaving();
		return $this->db->insert($this->getTableName(), $this->getData());
	}
	
	protected function prepareUpdating() {
		if ($this->isField('modified')) {
			$this->db->set('modified', 'NOW()', FALSE);
			unset($this->data['modified']);
		}
		if ($this->isField('modifications')) {
			$this->db->set('modifications', 'modifications + 1', FALSE);
			unset($this->data['modifications']);
		}
	}
	
	public function update() {
		$this->prepareUpdating();
		$this->db->where($this->getPrimaryKeyValue());
		$this->db->limit(1);
		return $this->db->update($this->getTableName(), $this->getData());
	}
	
	protected function prepareDeleting() {
		$this->prepareUpdating();
		if ($this->isField('status')) $this->setStatus('deleted');
	}
	
	public function delete() {
		$this->prepareDeleting();
		$this->db->where($this->getPrimaryKeyValue());
		$this->db->limit(1);
		return $this->db->update($this->getTableName(), $this->getData());
	}
	
	//TODO: check this!
	public function saveSafely($condition = array('status' => NULL)) {
		$modelName = $this->getClassName();
		$finderName = $modelName . 'Finder';
		try {
			$Object = $finderName::find($this->getPrimaryKeyValue(), $condition);
			$Object->setData($this->getData());
			$Object->update();
		} catch (Exception $Exception) {
			$Object = new $modelName($this->getData());
			$Object->save();
		}
	}
	
	public function isModified() {
		return $this->isField('modified') && $this->getModified() != '0000-00-00 00:00:00';
	}
}