<?php

namespace App\Controllers;

class Equipro extends BaseController {

	public function index()
	{
		return $this->home();
	}

	public function home(){
		helper('form');
		$data = array();
		echo view('equipro/login',$data);
	}

}
