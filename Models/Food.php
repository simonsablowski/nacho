<?php

abstract class Food extends Model {
	protected static $name = NULL;
	protected static $nutritionFacts = NULL;
	
	final public static function getName() {
		return !is_null($name = parent::getName()) ? $name : get_called_class();
	}
}