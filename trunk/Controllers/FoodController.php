<?php

class FoodController extends Controller {
	protected function getFood() {
		$Food = array();
		if (is_dir($dir = $this->getApplication()->getPath() . 'Models')) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== FALSE) {
					if (!in_array($file, array('.', '..', '.svn'))) {
						if (class_exists($name = strstr($file, '.', TRUE)) && is_subclass_of($name, 'Food')) {
							$Food[] = $name;
						}
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