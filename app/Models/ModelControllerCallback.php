<?php 
	/**
	* This is the class that contain the method that will be called whenever any data is inserted for a particular table.
	* the url path should be linked to this page so that the correct operation is performed ultimately. T
	*/
	namespace App\Models;

	use CodeIgniter\Model;
	use App\Models\WebSessionManager;
	use App\Models\Mailer;
	use CodeIgniter\I18n\Time;

	class ModelControllerCallback extends Model
	{
		protected $db;
		private $webSessionManager;
		private $mailer;

		function __construct()
		{
			helper(['string','url','array']);
			$this->webSessionManager = new WebSessionManager;
			$this->mailer = new Mailer;
			$this->db = db_connect();
		}

		public function onAdminInserted($data,$type,&$db,&$message)
		{
			//remember to remove the file if an error occured here
			//the user type should be admin
			$user = loadClass('user');
			if ($type=='insert') {
				// login details as follow: username = email, password = firstname(in lowercase)
				$password = encode_password(strtolower($data['firstname']));
				$param = array('user_type'=>'admin','username'=>$data['email'],'password'=>$password,'user_table_id'=>$data['LAST_INSERT_ID']);
				$std = new $user($param);
				if ($std->insert($db,$message)) {
					return true;
				}
				return false;
			}
			return true;
		}

		public function onAllocationInserted($data,$type,&$db,&$message)
		{
			//remember to remove the file if an error occured here
			//the user type should be admin
			if ($type == 'insert') {
				$id = $data['applicant_allocation_id'];
				$allocation = loadClass('applicant_allocation');
				$allocation->applicant_status = $data['allocation_status'];
				if(!$allocation->update($id)){
					$message = "something went wrong";
					return false;
				}
			}
			return true;
		}

	}
 ?>