<?php

class StaticController extends Controller {
	public function show($view) {
		$this->displayView($view . '.php');
	}
}