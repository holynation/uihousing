<?php 
	/**
	* This class like other controller class will have full access control capability
	*/
namespace App\Controllers;

use App\Models\WebSessionManager;
use Exception;

class Actioncontroller extends BaseController
{
		private $uploadedFolderName = 'public/uploads';
		private $crudNameSpace = 'App\Models\Crud';
		protected $db;
		private $webSessionManager;

		/**
		 | NOTE: 1. To return things, it must be in json type using the provided function - createJsonMessage
		 */

		function __construct()
		{
			$this->db = db_connect();
			$this->webSessionManager = new WebSessionManager;
			// basically the admin should be the one accessing this module
			if ($this->webSessionManager->getCurrentUserprop('user_type')=='admin') {
				$role = loadClass('role');
				$role->checkWritePermission();
			}
		}

		// TODO: I WANNA WRAP EACH ACTION METHOD FOR BATCH OPERATION SUCH THAT WE CAN PERFORM MULTIPLE OPERATIONS ON THEM E.G MULTIPLE DELETE|DISABLE|ENABLE

		/**
		 * @param string 	$model
		 * @param int 		$id
		 * @return json|array
		 */
		public function disable(string $model,$id){
			$model = loadClass($model);
			//check that model is actually a subclass
			if ( empty($id)===false && is_subclass_of($model,$this->crudNameSpace)) {
				if($model->disable($id,$this->db)){
					echo createJsonMessage('status',true,'message',"action successfully performed",'flagAction',true);
				}else{
					echo createJsonMessage('status',false,'message',"action can't be performed",'flagAction',false);
				}
			}
			else{
				echo createJsonMessage('status',false,'message',"action can't be performed",'flagAction',false);
			}
		}

		/**
		 * @param string 	$model
		 * @param int 		$id
		 * @return json|array
		 */
		public function enable(string $model,$id){
			$tempModel = $model;
			$model = loadClass($model);
			//check that model is actually a subclass
			if ( !empty($id) && is_subclass_of($model,$this->crudNameSpace ) && $model->enable($id,$this->db)) {
				echo createJsonMessage('status',true,'message',"action successfully performed",'flagAction',true);
			}
			else{
				echo createJsonMessage('status',false,'message',"action can't be performed",'flagAction',false);
			}
		}

		public function view($model,$id){

		}

		/**
		 * @param string $model
		 * @return json|array
		 */
		public function truncate(string $model){
			if($model){
				$builder = $this->db->table($model);
				if($builder->truncate()){
					echo createJsonMessage('status',true,'message',"item successfully truncated...",'flagAction',true);
				}else{
					echo createJsonMessage('status',false,'message',"cannot truncate item...",'flagAction',false);
				}
			}	
		}

		/**
		 * @param string 		$model
		 * @param string 		$field
		 * @param string|int	$value
		 * @return json|array
		 */
		public function deleteModelByUserId(string $model,$field,$value){
			$db=$this->db;
		    $db->transBegin();
		    $query="delete from $model where $field=?";
		    if($db->query($query,[$value])){
		        $db->transCommit();
		        echo createJsonMessage('status',true,'message','item deleted successfully...','flagAction',true);
		        return true;
		    }
		    else{
		        $db->transRollback();
		        echo createJsonMessage('status',false,'message','cannot delete item(s)...','flagAction',true);
		        return false;
		    }
		}

		/**
		 * @param string 	$model
		 * @param string 	$extra - This is to remove any files attached to this single *  entity
		 * @param int 		$id
		 * @return json|array
		 */
		public function delete(string $model,$extra='',$id=''){
			# verifying this action before performing it
			$id = ($id == '') ? $extra : $id;
			$extra = ($extra != '' && $id != '') ? base64_decode(urldecode($extra)) : $id;
			# this extra param is a method to find a file and removing it from the server
			if($extra){
				$newModel = loadClass($model);
				$paramFile = $newModel::$documentField;
				$directoryName = $model.'_path';
				$filePath =  $this->uploadedFolderName.'/'.@$paramFile[$directoryName]['directory'].$extra;
				$filePath = ROOTPATH.$filePath;
				if(file_exists($filePath)){
					@chmod($filePath, 0777);
					@unlink($filePath); # remove the symlink only
				}
				$filePath = ROOTPATH.'/'.@$paramFile[$directoryName]['directory'].$extra;
				if(file_exists($filePath)){
					@chmod($filePath, 0777);
					@unlink($filePath); # remove the original file image
				}
			}
			$newModel = loadClass($model);
			# check that model is actually a subclass
			if ( !empty($id) && is_subclass_of($newModel,$this->crudNameSpace )&&$newModel->delete($id)) {
				$desc = "deleting the model $model with id {$id}";
				// $this->logAction($this->webSessionManager->getCurrentUserProp('ID'),$model,$desc);
				echo createJsonMessage('status',true,'message','item deleted successfully...','flagAction',true);
				return true;
			}
			else{
				echo createJsonMessage('status',false,'message','cannot delete item...','flagAction',true);
				return false;
			}
		}

		/**
		 * @param string 	$model
		 * @param string 	$value
		 * @param int 		$id
		 * @return json|array
		 */
		public function changeStatus(string $model, string $value ,int $id)
		{
			if($model == 'applicant_allocation'){
				$model = loadClass($model);
				$model->applicant_status = $value;
				$this->db->transBegin();
				if($model->update($id)){
					$allocation = loadClass('allocation');
					$allocation->applicant_allocation_id = $id;
					$allocation->status = 'approved';
					if(!$allocation->insert($this->db,$message)){
						$this->db->transRollback();
						$message = $message ?? "something went wrong";
						echo createJsonMessage('status',false,'message',$message,'flagAction',false);return;
					}
					$this->db->transCommit();
					echo createJsonMessage('status',true,'message',"You have successfully performed the action",'flagAction',true);
				}else{
					$this->db->transRollback();
					echo createJsonMessage('status',false,'message',"action can't be performed",'flagAction',false);
				}
			}
			else if($model == 'allocation'){
				$model = loadClass($model);
				$model->status = $value;
				$this->db->transBegin();
				if($model->update($id)){
					$this->db->transCommit();
					echo createJsonMessage('status',true,'message',"You have successfully performed the action",'flagAction',true);
				}else{
					$this->db->transRollback();
					echo createJsonMessage('status',false,'message',"action can't be performed",'flagAction',false);
				}
			}
		}

		/**
		 * @param string 	$model
		 * @param int 		$id
		 * @return json|array
		 */
		public function mail(string $model, $id)
		{
			return true;
		}

		private function logAction($user,$model,$description){
			$applicationLog = loadClass('application_log');
			$applicationLog->log($user,$model,$description);
		}

		private function getModelMail(string $model)
		{

		}


}
