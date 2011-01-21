<?php

abstract class Finder {
	protected static $tableName = NULL;
	protected static $primaryKey = 'id';
	protected static $defaultCondition = array('status' => 'active');
	protected static $defaultSorting = array('created DESC');
	
	//TODO: create database abstraction libraries for supporting multiple sql rdms as well as xquery and other database types and get rid of codeigniter libraries
	public function __call($method, $parameters) {
		preg_match_all('/(^|[A-Z]{1})([a-z]+)/', $method, $methodParts);
		if (!isset($methodParts[0][0]) || !isset($methodParts[0][1])) throw new Exception('Invalid method format', $method);
		
		$operation = $methodParts[0][0];
		array_shift($methodParts[0]);
		
		if ($operation == 'find') {
			array_shift($methodParts[0]);
			$fieldNames = implode('', $methodParts[0]);
			
			$fields = explode('And', implode('', $methodParts[0]));
			foreach ($fields as $n => $field) {
				$fields[$n] = strtolower(substr($field, 0, 1)) . substr($field, 1);
			}
			
			$values = $parameters;
			$condition = NULL;
			if ((!is_array(pos($values)) && count($values) == count($fields) + 1)
					|| (is_array(pos($values) && count($values) == 2))) {
				$condition = end($values);
				array_pop($values);
			}
			if (is_array(pos($values)) && count($values) == 1) $values = pos($values);
			
			return self::findBy($fields, $values, $condition);
		} else {
			$name = implode('', $methodParts[0]);
			$property = strtolower(substr($name, 0, 1)) . substr($name, 1);
			if (!property_exists($this, $property)) throw new Exception('Undeclared property', $property);
			
			switch ($operation) {
				case 'get':
					return self::$property;
				case 'is':
					return self::$property == 'yes';
				case 'set':
					self::$property = $parameters[0];
					return;
			}
		}
	}
	
	public static function getClassName() {
		return substr(get_class($this), 0, strrpos(get_class(self), 'Finder'));
	}
	
	public static function findAll($condition = NULL, $sorting = NULL, $offset = 0, $limit = NULL) {
		if (is_null($condition)) $condition = self::getDefaultCondition();
		else $condition = array_merge(self::getDefaultCondition(), $condition);
		if (is_null($sorting)) $sorting = self::getDefaultSorting();
		
		foreach ($condition as $field => $value) {
			if (is_null($value)) continue;
			self::db->where($field, $value);
		}
		if (!is_null($limit)) self::db->limit($limit, $offset);
		if ($sorting) self::db->order_by(implode(', ', $sorting));
		
		$Objects = array();
		foreach (self::db->get(self::getTableName())->result_array() as $row) {
			$modelName = self::getClassName();
			$Objects[] = new $modelName($row);
		}
		return $Objects;
	}
	
	public static function countAll($condition = NULL) {
		if (is_null($condition)) $condition = self::getDefaultCondition();
		else $condition = array_merge(self::getDefaultCondition(), $condition);
		
		foreach ($condition as $field => $value) {
			if (is_null($value)) continue;
			self::db->where($field, $value);
		}
		
		return self::db->count_all_results(self::getTableName());
	}
	
	public static function findFirst($condition = NULL, $sorting = NULL) {
		$Objects = self::findAll($condition, $sorting, 0, 1);
		if ($Objects) return $Objects[0];
		else throw new Exception('Record not found', $condition);
	}
	
	public static function find($primaryKeyValue, $condition = NULL) {
		if (is_null($condition)) $condition = self::getDefaultCondition();
		else $condition = array_merge(self::getDefaultCondition(), $condition);
		if (!is_array($primaryKeyValue)) $primaryKeyValue = array(self::getPrimaryKey() => $primaryKeyValue);
		
		self::db->where($primaryKeyValue);
		foreach ($condition as $field => $value) {
			if (is_null($value)) continue;
			self::db->where($field, $value);
		}
		self::db->limit(1);
		
		$row = self::db->get(self::getTableName())->row_array();
		if (!$row) throw new Exception('Record not found', array('$primaryKeyValue' => $primaryKeyValue, '$condition' => $condition));
		
		$modelName = self::getClassName();
		return new $modelName($row);
	}
	
	public static function findBy($fields, $values, $condition = NULL) {
		if (is_null($condition)) $condition = self::getDefaultCondition();
		else $condition = array_merge(self::getDefaultCondition(), $condition);
		
		foreach ($fields as $n => $field) {
			self::db->where($field, $values[$n]);
		}
		foreach ($condition as $field => $value) {
			if (is_null($value)) continue;
			self::db->where($field, $value);
		}
		
		$row = self::db->get(self::getTableName())->row_array();
		if (!$row) throw new Exception('Record not found', array('$fields' => $fields, '$values' => $values, '$condition' => $condition));
		
		$modelName = self::getClassName();
		return new $modelName($row);
	}
}