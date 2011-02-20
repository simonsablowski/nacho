<?php

abstract class SqlDatabase extends Database {
	protected static $requiredFields = array(
		'type',
		'host',
		'name',
		'user',
		'password'
	);
	
	protected static function getRequiredFields() {
		return self::$requiredFields;
	}
	
	protected static function format($value) {
		return sprintf(is_int($value) ? "%d" : ($value == 'NOW()' ? "%s" : "'%s'"), self::escape($value));
	}
	
	protected static function addCondition(&$statement, $condition) {
		if (is_null($condition)) return;
		
		$statement .= sprintf("\nWHERE ");
		$and = FALSE;
		foreach ($condition as $field => $value) {
			if (is_null($value)) continue;
			$statement .= $and ? "\nAND " : '';
			$statement .= sprintf("`%s` = %s", $field, self::format($value));
			$and = TRUE;
		}
	}
	
	protected static function addLimit(&$statement, $limit) {
		if (is_null($limit)) return;
		
		$statement .= sprintf("\nLIMIT %s", $limit);
	}
	
	protected static function addSorting(&$statement, $sorting) {
		if (is_null($sorting)) return;
		
		$statement .= sprintf("\nORDER BY ");
		$comma = FALSE;
		foreach ($sorting as $column) {
			$statement .= $comma ? ', ' : '';
			$statement .= sprintf("`%s`", $column);
			$comma = TRUE;
		}
	}
	
	public static function select($tableName, $condition = NULL, $limit = NULL, $sorting = NULL, $columns = NULL) {
		$statement = sprintf("SELECT ");
		if (!is_array($columns)) {
			$statement .= '*';
		} else {
			$comma = FALSE;
			foreach ($columns as $column) {
				$statement .= $comma ? ', ' : '';
				$statement .= sprintf("`%s`", $column);
				$comma = TRUE;
			}
		}
		$statement .= sprintf(" FROM `%s`", $tableName);
		self::addCondition($statement, $condition);
		self::addSorting($statement, $sorting);
		self::addLimit($statement, $limit);
		
		return self::query($statement);
	}
	
	public static function insert($tableName, $data) {
		$statement = sprintf("INSERT INTO `%s` SET", $tableName);
		$n = 0;
		foreach ($data as $field => $value) {
			$statement .= sprintf("\n`%s` = %s", $field, self::format($value));
			$statement .= $n + 1 < count($data) ? ',' : '';
			$n++;
		}
		
		return self::query($statement);
	}
	
	public static function update($tableName, $data, $condition, $limit = NULL) {
		$statement = sprintf("UPDATE `%s` SET", $tableName);
		$n = 0;
		foreach ($data as $field => $value) {
			$statement .= sprintf("\n`%s` = %s", $field, self::format($value));
			$statement .= $n + 1 < count($data) ? ',' : '';
			$n++;
		}
		self::addCondition($statement, $condition);
		
		return self::query($statement);
	}
	
	public static function delete($tableName, $condition, $limit = NULL) {
		$statement = sprintf("DELETE FROM `%s`", $tableName);
		self::addCondition($statement, $condition);
		
		return self::query($statement);
	}
}