<?php
namespace App\Auth;

use Cake\Auth\FormAuthenticate;

class NoHashAuthenticate extends FormAuthenticate 
{
	function _password($data){
		return $data;
	}
}
