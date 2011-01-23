<?php

abstract class Model extends Application {
	protected static $primaryKey = 'id';
	protected static $defaultCondition = array(
		'status' => 'active'
	);
	protected static $defaultSorting = array(
		'created'
	);
	protected $fields = array();
	protected $requiredFields = array();
	protected $hiddenFields = array(
		'id',
		'status',
		'created',
		'modified'
	);
	protected $data = array();
	
	public static function getClassName() {
		return get_called_class();
	}
	
	public static function getTableName() {
		return strtolower(self::getClassName());
	}
	
	public static function __callStatic($method, $parameters) {
		list($operation, , , , $methodParts) = self::resolveMethod(get_called_class(), $method);
		if ($operation != 'find') return parent::__callStatic($method, $parameters);
		
		array_shift($methodParts[0]);
		$fieldNames = implode('', $methodParts[0]);
		
		$fields = explode('And', implode('', $methodParts[0]));
		foreach ($fields as $n => $field) {
			$fields[$n] = strtolower(substr($field, 0, 1)) . substr($field, 1);
		}
		
		$values = $parameters;
		$condition = NULL;
		if ((!is_array(pos($values)) && count($values) == count($fields) + 1) || (is_array(pos($values) && count($values) == 2))) {
			$condition = end($values);
			array_pop($values);
		}
		if (is_array(pos($values)) && count($values) == 1) $values = pos($values);
		
		return self::findBy($fields, $values, $condition);
	}
	
	protected static function resolveCondition($condition) {
		if (is_null($condition)) return self::getDefaultCondition();
		else return array_merge(self::getDefaultCondition(), $condition);
	}
	
	public static function findAll($condition = NULL, $sorting = NULL, $limit = NULL) {
		$Objects = array();
		$result = Database::select(self::getTableName(), self::resolveCondition($condition), $limit, !is_null($sorting) ? $sorting : self::getDefaultSorting());
		while ($row = Database::fetch($result)) {
			$modelName = self::getClassName();
			$Objects[] = new $modelName($row);
		}
		return $Objects;
	}
	
	public static function countAll($condition = NULL) {
		$result = Database::select(self::getTableName(), self::resolveCondition($condition));
		return Database::count($result);
	}
	
	public static function findFirst($condition = NULL, $sorting = NULL) {
		$Objects = self::findAll($condition, $sorting, 1);
		if ($Objects) return pos($Objects);
		else throw new Error('Record not found', $condition);
	}
	
	public static function find($primaryKeyValue, $condition = NULL) {
		$condition = array_merge(is_array($primaryKeyValue) ? $primaryKeyValue : array(self::getPrimaryKey() => $primaryKeyValue), self::resolveCondition($condition));
		$result = Database::select(self::getTableName(), $condition, 1);
		$row = Database::fetch($result);
		if (!$row) throw new Error('Record not found', array('Primary key value' => $primaryKeyValue, 'Condition' => $condition));
		
		$modelName = self::getClassName();
		return new $modelName($row);
	}
	
	public static function findBy($fields, $values, $condition = NULL) {
		$condition = array_merge(array_combine($fields, $values), self::resolveCondition($condition));
		$result = Database::select(self::getTableName(), $condition, 1);
		$row = Database::fetch($result);
		if (!$row) throw new Error('Record not found', array('Fields' => $fields, 'Values' => $values, 'Condition' => $condition));
		
		$modelName = self::getClassName();
		return new $modelName($row);
	}
	
	public function __call($method, $parameters) {
		list($operation, $property, $propertyExists, $propertyCapitalized) = $this->resolveMethod($this, $method);
		
		$isField = FALSE;
		$hasLoader = FALSE;
		
		if (method_exists($this, 'load' . $property) || property_exists($this, $property . 'Id')) {
			$hasLoader = TRUE;
		}
		
		if ($this->isField($property)) {
			$isField = TRUE;
		} else if ($this->isField($propertyCapitalized)) {
			$isField = TRUE;
			$property = $propertyCapitalized;
		}
		
		switch ($operation) {
			case 'get':
				if (($isField || $propertyExists) && $hasLoader) {
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
				if (($isField || $propertyExists) & $hasLoader) {
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
					$getterName = 'get' . $property . 'Id';
					return $this->$property = $property::find($this->$getterName());
				}
			case 'set':
				if ($isField) {
					return $this->setData($property, $parameters[0]);
				} else if ($propertyExists) {
					return $this->$property = $parameters[0];
				}
		}
		
		throw new FatalError('Undeclared property', $property);
	}
	
	public function __construct() {
		$requiredFields = $this->getRequiredFields();
		$arguments = func_get_args();
		
		if (func_num_args() == 1 && is_array(func_get_arg(0))) {
			$data = func_get_arg(0);
		} else {
			if (count($requiredFields) != count($arguments)) {
				throw new FatalError('Number of required fields does not match number of arguments', array('Arguments' => $arguments, 'Required fields' => $requiredFields));
			}
			
			$data = $arguments ? array_combine($requiredFields, $arguments) : array();
		}
		
		foreach ($requiredFields as $field) {
			if (!array_key_exists($field, $data)) {
				throw new FatalError('Required fields missing', array('Given fields' => array_keys($data), 'Required fields' => $requiredFields));
			}
		}
		
		foreach ($data as $property => $value) {
			if (!$this->isField($property)) continue;
			$setter = 'set' . ucfirst($property);
			$this->$setter($value);
		}
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
		if (is_null($field)) {
			$data = $this->data;
			foreach ($this->getHiddenFields() as $field) {
				unset($data[$field]);
			}
			return $data;
		} else if (isset($this->data[$field])) {
			return $this->data[$field];
		} else {
			return NULL;
		}
	}
	
	public function setData() {
		if (func_num_args() == 2) {
			return $this->data[func_get_arg(0)] = func_get_arg(1);
		} else if (func_num_args() == 1) {
			$data = func_get_arg(0);
			return $this->data = array_merge($this->data, $data);
		}
	}
	
	protected function prepareSaving() {
		if ($this->isField('status')) $this->setStatus('active');
		if ($this->isField('created')) $this->setData('created', 'NOW()');
	}
	
	public function save() {
		$this->prepareSaving();
		return Database::insert($this->getTableName(), $this->getData());
	}
	
	protected function prepareUpdating() {
		if ($this->isField('modified')) $this->setData('modified', 'NOW()');
	}
	
	public function update() {
		$this->prepareUpdating();
		return Database::update($this->getTableName(), $this->getData(), $this->getPrimaryKeyValue(), 1);
	}
	
	protected function prepareDeleting() {
		$this->prepareUpdating();
		if ($this->isField('status')) $this->setStatus('deleted');
	}
	
	public function delete() {
		$this->prepareDeleting();
		return Database::update($this->getTableName(), $this->getData(), $this->getPrimaryKeyValue(), 1);
	}
	
	public function saveSafely($condition = array('status' => NULL)) {
		$className = get_class($this);
		try {
			$Object = $className::find($this->getPrimaryKeyValue(), $condition);
			$Object->setData($this->getData());
			$Object->update();
		} catch (Error $Error) {
			$Object = new $className($this->getData());
			$Object->save();
		}
	}
	
	public function isModified() {
		return $this->isField('modified') && $this->getModified() != '0000-00-00 00:00:00';
	}
}