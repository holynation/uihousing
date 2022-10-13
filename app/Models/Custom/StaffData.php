<?php 
/**
* This is the class that manages all information and data retrieval needed by the staff section of this application.
*/
namespace App\Models\Custom;

use CodeIgniter\Model;
use App\Models\WebSessionManager;
use App\Models\Mailer;
use CodeIgniter\HTTP\RequestInterface;

class StaffData extends Model
{
	private $staff;
	private $mailer;
	private $webSessionManager;
	protected $db;
	protected $request;

	public function __construct(RequestInterface $request=null)
	{
		helper(['string','array']);
		$this->db = db_connect();
		$this->request = $request;
		$this->webSessionManager = new WebSessionManager;
		$this->mailer = new Mailer;
	}

	public function setStaff($staff)
	{
		$this->staff = $staff;
	}

	public function loadDashboardInfo()
	{
		// get the iformatin for 
		$result = array();
		return $result;
	}

}
