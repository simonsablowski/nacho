<?php

class FoodController extends Controller {
	protected function getFood() {
		$Food = array();
		
		$Directory = dir($this->getApplication()->getPath() . 'models');
		while ($file = $Directory->read()) {
			if (($SpecialFood = strstr($file, '.', TRUE)) && class_exists($SpecialFood)
				&& ($ReflectionClass = new ReflectionClass($SpecialFood)) && $ReflectionClass->isSubclassOf('Food') && !$ReflectionClass->isAbstract()) {
				$Food[] = $SpecialFood;
			}
		}
		$Directory->close();
		
		return $Food;
	}
	
	public function index() {
		$this->displayView('Food.index.php', array(
			'Food' => $this->getFood()
		));
	}
}