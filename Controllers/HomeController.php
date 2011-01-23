<?php

class HomeController extends Controller {
	public function index() {
		$this->displayView('Home.index.php');
	}
}