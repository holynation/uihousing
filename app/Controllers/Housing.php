<?php

namespace App\Controllers;

class Housing extends BaseController {

	public function index()
	{
		helper('form');
		return $this->home();
	}

	public function home(){
		$data = array();
		echo view('housing/login',$data);
	}

	public function register(){
		
	}

}
