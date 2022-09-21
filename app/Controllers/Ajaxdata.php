<?php
	/**
	* model for loading extra data needed by pages through ajax
	*/
namespace App\Controllers;

use App\Models\WebSessionManager;
use Exception;

class Ajaxdata extends BaseController
	{
		protected $db;
		private $webSessionManager;

		function __construct()
		{			
			helper(['string']);
			$this->db = db_connect();
			$this->webSessionManager = new WebSessionManager;
			$exclude=array('changePassword','savePermission','approve','disapprove');
			$page = $this->getMethod($segments);
			if ($this->webSessionManager->getCurrentUserProp('user_type')=='admin' && in_array($page, $exclude)) {
				$role = loadClass('role');
				$role->checkWritePermission();
			}
		}

		private function getMethod(&$allSegment)
		{
			$path = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$base = base_url();
			$left = ltrim($path,$base);
			$result = explode('/', $left);
			$allSegment=$result;
			return $result[0];
		}

		private function returnJSONTransformArray($query,$data=array(),$valMessage='',$errMessage=''){
			$newResult=array();
			$result = $this->db->query($query,$data);
			if($result->getNumRows() > 0){
				$result = $result->getResultArray();
				if($valMessage != ''){
					$result[0]['value'] = $valMessage;
				}
				return json_encode($result[0]);
			}else{
				if($errMessage != ''){
					$dataParam = array('value' => $errMessage);
					return json_encode($dataParam);
				}
				return json_encode(array());
			}
		}

		private function returnJSONFromNonAssocArray($array){
			//process if into id and value then
			$result =array();
			for ($i=0; $i < count($array); $i++) {
				$current =$array[$i];
				$result[]=array('id'=>$current,'value'=>$current);
			}
			return json_encode($result);
		}

		protected function returnJsonFromQueryResult($query,$data=array(),$valMessage='',$errMessage=''){
			$result = $this->db->query($query,$data);
			if ($result->getNumRows() > 0) {
				$result = $result->getResultArray();
				if($valMessage != ''){
					$result[0]['value'] = $valMessage;
				}
				// print_r($result);exit;
				return json_encode($result);
			}
			else{
				if($errMessage != ''){
					$dataParam = array('value' => $errMessage);
					return json_encode($dataParam);
				}
				return "";
			}
		}

		public function savePermission()
		{	
			if (isset($_POST['role'])) {
				$role = $_POST['role'];
				if (!$role) {
					echo createJsonMessage('status',false,'message','error occured while saving permission','flagAction',false);
				}
				$newRole = loadClass('role');
				try {
					$removeList = json_decode($_POST['remove'],true);
					$updateList = json_decode($_POST['update'],true);
					$newRole->ID=$role;
					$result=$newRole->processPermission($updateList,$removeList);
					echo createJsonMessage('status',$result,'message','permission updated successfully','flagAction',true);
				} catch (Exception $e) {
					echo createJsonMessage('status',false,'message','error occured while saving permission','flagAction',false);
				}
				
			}
		}
		
	}
