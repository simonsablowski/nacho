<?php

class FoodController extends Controller {
	protected function getFood() {
		$Food = array();
		if (is_dir($dir = $this->getApplication()->getPath() . 'Models')) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== FALSE) {
					if (($SpecialFood = strstr($file, '.', TRUE)) && class_exists($SpecialFood) && is_subclass_of($SpecialFood, 'Food')) {
						$Food[] = $SpecialFood;
					}
				}
				closedir($dh);
			}
		}
		return $Food;
	}
	
	public function index() {
		$this->displayView('Food.index.php', array(
			'Food' => $this->getFood()
		));
	}
}