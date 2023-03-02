<?php 

/**
* The controller that validate forms that should be inserted into a table based on the request url.
each method wil have the structure validate[modelname]Data
*/
namespace App\Models;

use CodeIgniter\Model;
use App\Models\WebSessionManager;
use CodeIgniter\I18n\Time;

class ModelControllerDataValidator extends Model
{
	protected $db;
	private $webSessionManager;
	
	function __construct()
	{
		helper('string');
		$this->db = db_connect();
		$this->webSessionManager = new WebSessionManager;
	}


}


?>