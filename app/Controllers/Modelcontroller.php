<?php

/**
 * The controller that link to the model.
 *all response in this class returns a json object return
 */

namespace App\Controllers;

use App\Models\WebSessionManager;
use App\Models\AccessControl;
use App\Models\ModelControllerCallback;
use App\Models\ModelControllerDataValidator;
use Exception;

class Modelcontroller extends BaseController
{
	private $_rootUploadsDirectory = ROOTPATH."writable/uploads/";
	private $_publicDirectory = ROOTPATH."public/uploads/";
	// RULE: date_created comes first,then date_modified or any named date
	// NOTE: it only accept two diff date,nothing more than that.
	private $_dateParam = array('default' => array('date_created', 'date_modified'));
	private $accessControl;
	private $webSessionManager;
	private $modelControllerCallback;
	private $modelControllerDataValidator;
	protected $db;
	private $crudNameSpace = 'App\Models\Crud';

	function __construct()
	{
		helper(['array', 'string']);

		$this->accessControl = new AccessControl; //for authentication authorization validation
		$this->modelControllerCallback = new ModelControllerCallback;
		$this->modelControllerDataValidator = new ModelControllerDataValidator;
		$this->webSessionManager = new WebSessionManager;
		$this->db = db_connect();

		if (!$this->webSessionManager->isSessionActive()) {
			header("Location:" . base_url());
			exit;
		}

		if ($this->webSessionManager->getCurrentuserProp('user_type') == 'admin') {
			$role = loadClass('role');
			// $role->checkWritePermission();
		}
	}

	// TODO: LOOK INTO  WHAT THIS METHOD IS ACTUALLY DOING IN REALITY
	//function that will enable the ajax call and return just the table content by passing the url link
	function tableContent($model, $start = 0, $len = 100, $paged = false)
	{
		if (!$this->isModel($model)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
			exit;
		}
		$this->load->model('tableViewModel');
		$html =  $this->tableViewModel->getTableHtml($model, $message, array(), array(), $paged, $start, $len);
		$data['tableData'] = $html == true ? $html : $message;
		$this->load->view('pages/modelTableView', $data);
	}

	function add($model, $filter = false, $parent = '', $noArrSkip = false)
	{
		//the parent field is optional
		try {
			if (empty($model)) { //make sure empty value for model is not allowed.
				echo createJsonMessage('status', false, 'message', 'an error occured while processing information', 'description', 'the model parameter is null so it must not be null');
				return;
			}

			unset($_POST['MAX_FILE_SIZE']);
			if ($model == 'many') {
				$this->insertMany($filter, $parent);
			} else {
				// $this->log($model,"inserting $model");
				$this->insertSingle($model, $filter, $noArrSkip);
			}
		} catch (\Exception $ex) {
			echo $ex->getMessage();
			$this->db->transRollback();
		}
	}

	private function insertMany($filter)
	{
		$appended = '_id';
		//make sure the parent name exist
		if (!isset($_POST['parent_generated'])) {
			throw new Exception("is like you forgot to set a parent table for this form,kindly do and try again", 1);
		}
		//first validate the model
		$parentName = $_POST['parent_generated']; //remove the appended from the back
		unset($_POST['parent_generated']);
		$parent = $parentName . $appended;
		$prevCount = 0;
		$models = $this->validateModels('c', $message); //validate the models and return the model arrays on success of return false and return message
		$desc = implode(' , ', $models);
		// $this->log($desc,"attempting to insert $desc");
		if (!$models) {
			echo createJsonMessage('status', false, 'message', 'an error occured while processing information', 'description', $message);
			exit;
		}
		$inTable = array_key_exists($parentName, $models);
		$this->db->transBegin(); //start transaction
		$data = $this->request->getPost(null);
		$parentValue = @$data[$parent];
		$isFirst = true;
		$insertids = '';
		$message = '';
		foreach ($models as $model => $prop) {
			if (is_array($prop) || !is_int($prop)) {
				$this->db->transRollback();
				throw new Exception("invalid model properties");
			}
			$newModel = loadClass("$model");
			$data = $this->processFormUpload($model, $data, false);
			$parameter = $this->extractSubset($data, $newModel);
			$parameter = removeEmptyAssoc($parameter);
			if (!$this->validateModelData($model, 'insert', $parameter, $message)) {
				$this->db->transRollback();
				echo createJsonMessage('status', false, 'message', $message);
				return;
			}
			$parentSet = false;
			if ($parentName == $model || $isFirst) { //if this is the parent or the first table
				$newModel->setArray($parameter);
				if (!$newModel->insert($this->db, $message)) {
					//if tere is any problem with the current insertion just remove rollback the transaction and  exit with error that will be faster.
					$this->db->transRollback();
					echo createJsonMessage('status', false, 'message', $message);
					return false;
					// break;
				}
				$prevCount = $prop;
				if ($inTable) {
					$parentValue = $this->getLastInsertId(); //or another means of getting the parent value
					$insertids .= $parentValue . '#';
					$parentSet = true;
				}
				$isFirst = false;
				continue;
			}
			$ins = $this->getLastInsertId();
			$ins .= '#';
			$insertids .= $parentSet ? "" : $ins;
			$parameter[$parent] = $parentValue;
			if ($model == 'next_of_kin') {
				unset($parameter['guardian_ID']);
			}
			$newModel->setArray($parameter);
			$newModel->insert($this->db);
			$prevCount = $prop;
		}
		if ($this->db->transStatus() === FALSE) {
			$this->db->transRollback();
			$message = empty($message) ? 'error occured while inserting record' : $message;
			echo createJsonMessage('status', false, 'message', $message);
			// $this->log($desc,$message);
			return false;
		}
		//load the insert many method here before the db is committed so that the transaction is atomic.
		$data['LAST_INSERT_ID'] = $insertids;
		if ($this->afterManyInserts(array_keys($models), 'insert', $data, $this->db)) {
			$this->db->transCommit(); //end the transaction
			echo createJsonMessage('status', true, 'message', 'records inserted successfully', 'data', $parentValue);
			// $this->log($desc," $desc Inserted");
			return true;
		}
		$this->db->transRollback();
		echo createJsonMessage('status', false, 'message', 'error occured while inserting records');
		// $this->log($desc," error inserting $desc");
		return false;
	}
	// the models is the array of all the models inserted, type specify if it an update or an insert,
	// data is the data that was worked on. the filter post data.
	// the db is the database passed as reference.
	private function afterManyInserts($models, $type, $data, &$db)
	{
		//delegate to a method in the callback class
		$method = 'onInsertMany';
		if (method_exists($this->modelControllerCallback, $method)) {
			return $this->modelControllerCallback->$method($models, $type, $data, $db);
		}
		return true;
	}
	private function updateMany($filter)
	{
		//first validate the model
		$appended = '_id';
		//make sure the parent name exist
		if (!isset($_POST['parent_generated'])) {
			throw new Exception("is like you forgot to set a parent table for this form,kindly do and try again", 1);
		}
		//first validate the model
		$parentName = $_POST['parent_generated']; //remove the appended from the back
		unset($_POST['parent_generated']);
		unset($_POST['MAX_FILE_SIZE']);
		$parent = $parentName . $appended;
		$prevCount = 0;
		$models = $this->validateModels('u', $message); //validate the models and return the model arrays on success of return false and return message
		if (!$models) {
			echo createJsonMessage('status', false, 'message', $message);
			return;
		}
		// $inTable =array_key_exists($parentName, $models);
		$this->db->transBegin(); //start transaction
		$data = $this->request->getPost(null);
		$parentValue = isset($data[$parent]) ? $data[$parent] : false;
		$isFirst = true;
		foreach ($models as $model => $prop) {
			if (empty($prop) || !is_array($prop) || count($prop) != 2) {
				$this->db->transRollback();
				throw new Exception("invalid model properties");
			}
			//load the model
			$newModel = loadClass("$model");
			$data = $this->processFormUpload($model, $data, $prop[1]);
			$parameter = $this->extractSubset($data, $newModel);
			if (empty($parameter) || $this->validateModelData($model, 'update', $parameter, $message) == false) {
				$this->db->transRollback();
				if (empty($message)) {
					$message = 'error occured while performing operation';
				}
				throw new Exception($message, 1);
			}
			if ($parentName == $model || $isFirst) { //this is the first transaction
				$newModel->setArray($parameter);

				$newModel->update($prop[1], $this->db);
				$prevCount = $prop[0];
				$isFirst = false;
				continue;
			}
			if ($model == 'next_of_kin') {

				// print_r($parameter);
				// echo "got here";exit;
				unset($parameter['guardian_ID']);
			}

			$newModel->setArray($parameter);
			$newModel->update($prop[1], $this->db);
			$prevCount = $prop[0];
		}

		if ($this->db->transStatus() === FALSE) {
			echo createJsonMessage('status', true, 'message', 'error occured while updating record');
			return false;
		}
		if ($this->afterManyInserts(array_keys($models), 'update', $data, $this->db)) {
			$this->db->transCommit(); //end the transaction
			echo createJsonMessage('status', true, 'message', 'records updated successfully', 'data', $parentValue);
			return true;
		}
		$this->db->transRollback();
		echo createJsonMessage('status', false, 'message', 'error occured while updating record');
		return false;
	}

	//this function is used to  document
	private function processFormUpload(string $model, $parameter, $insertType = false)
	{
		$modelName = $model;
		$newModel = loadClass($model);
		$paramFile= $newModel::$documentField;

		if (empty($paramFile) || empty($_FILES)) {
			return $parameter;
		}
		$fields = array_keys($_FILES);
		foreach ($paramFile as $name => $value) {
			// $this->log($model,"uploading file $name");
			//if the field name is present in the fields the upload the document
			if (in_array($name, $fields)) {

				// list($type,$size,$directory,$preserve,@$max_width,@$max_height) = $value;
				// this is a precaution if no keys of this name are not set in the array
				$preserve = false;
				$max_width = 0;
				$max_height = 0;
				$directory = "";
				extract($value);

				$method = "get" . ucfirst($modelName) . "Directory";
				$uploadDirectoryManager = new \App\Models\UploadDirectoryManager;
				if (method_exists($uploadDirectoryManager, $method)) {
					$dir  = $uploadDirectoryManager->$method($parameter);
					if ($dir === false) {
						exit(createJsonMessage('status', false, 'message', 'Error while uploading file'));
					}
					$directory .= $dir;
				}

				$currentUpload = $this->uploadFile($modelName, $name, $type, $size, $directory, $message, $insertType, $preserve, $max_width, $max_height);
				if ($currentUpload == false) {
					return $parameter;
				}
				$parameter[$name] = $message;
			} else {
				continue;
			}
		}
		return $parameter;
	}

	private function uploadFile($model, $name, $type, $maxSize, $destination, &$message = '', $insertType = false, $preserve = false, $max_width = 0, $max_height = 0)
	{
		if (!$this->checkFile($name, $message)) {
			return false;
		}
		$filename = $_FILES[$name]['name'];
		$ext = strtolower(getFileExtension($filename));
		$fileSize = $_FILES[$name]['size'];
		$typeValid = is_array($type) ? in_array(strtolower($ext), $type) : strtolower($ext) == strtolower($type);
		if (!empty($filename) &&  $typeValid  && !empty($destination)) {
			if (!is_null($maxSize) && $fileSize > $maxSize) {
				// $message='file too large to be saved';return false;
				$calcsize = calc_size($maxSize);
				exit(createJsonMessage('status', false, 'message', "The file you are attempting to upload is larger than the permitted size ($calcsize)"));
			}
			$publicDestination = $this->_publicDirectory . $destination;
			if(!is_dir($publicDestination)){
				mkdir($publicDestination, 0777, true);
			}
			$publicDestination = $destination;
			$destination = $this->_rootUploadsDirectory . $destination;
			if (!is_dir($destination)) {
				mkdir($destination, 0777, true);
			}

			// using this is to check whether max_width or max_height was passed
			if (($max_width !== 0 && $max_height !== 0) || $max_width !== 0 || $max_height !== 0) {
				$config['max_width'] = $max_width;
				$config['max_height'] = $max_height;
				$temp_name = $_FILES[$name]['tmp_name'];

				if (!$this->isAllowedDimensions($temp_name, $max_width, $max_height)) {
					// $message = 'The image you are attempting to upload doesn\'t fit into the allowed dimensions.';return false;
					exit(createJsonMessage('status', false, 'message', "The image you are attempting to upload doesn't fit into the allowed dimensions (max_width:$max_width x max_height:$max_height)."));
				}
			}

			$naming = '';
			$new_name = $this->webSessionManager->getCurrentuserProp('user_table_id') . '_' . uniqid() . "_" . date('Y-m-d') . '.' . $ext;
			if ($insertType) {
				$getUpload = $this->getUploadID($model, $insertType, $name);
				if ($getUpload === 'insert') {
					// this means inserting
					$naming = ($preserve) ? $filename : $new_name;
				} else {
					$naming = basename($getUpload); # this means updating
				}
			} else {
				// this means inserting
				$naming = ($preserve) ? $filename : $new_name;
			}
			$destination .= $naming; # the test should be replaced by the name of the current user.
			$publicDestination .= $naming;
			if (move_uploaded_file($_FILES[$name]['tmp_name'], $destination)) {
				$destination = $this->createFileSymlink($publicDestination, $destination);
				$message = base_url($destination);
				return true;
			} else {
				$message = "error while uploading file. please try again";
				return false;
				// exit(createJsonMessage('status',false,'message','error while uploading file. please try again'));
			}
		} else {
			// $message = "error while uploading file. please try again";return false;
			exit(createJsonMessage('status', false, 'message', 'error while uploading file. please try again condition not satisfy'));
		}
		// $message='error while uploading file. please try again';return false;
		exit(createJsonMessage('status', false, 'message', 'error while uploading file. please try again'));
	}
	private function isAllowedDimensions($temp, $max_width = 0, $max_height = 0)
	{

		if (function_exists('getimagesize')) {
			$D = @getimagesize($temp);

			if ($max_width > 0 && $D[0] > $max_width) {
				return FALSE;
			}

			if ($max_height > 0 && $D[1] > $max_height) {
				return FALSE;
			}
		}

		return TRUE;
	}
	private function createFileSymlink(string $link, string $target){
        return createSymlink($link, $target);
	}
	private function getUploadID($model, $id, $name = '')
	{
		if ($id) {
			// return $id;
			// this means that it is updating
			$query = "select $name from $model where id = ?";
			$result = $this->db->query($query, array($id));
			$result = $result->getResultArray();

			// the return message 'insert' is a rare case whereby there is no media file at first
			// yet one want to add the media file through update action
			return (!empty($result[0][$name])) ? $result[0][$name] : 'insert';
		} else {
			// this means it is inserting
			$query = "select id from $model order by id desc limit 1";
			$result = $this->db->query($query);
			$result = $result->getResultArray();
			if ($result) {
				return $result[0]['id'];
			}
			return 1; //if no initial record
		}
	}
	private function checkFile($name, &$message = '')
	{
		$error = !$_FILES[$name]['name'] || $_FILES[$name]['error'];
		if ($error) {
			if ((int)$error === 2) {
				$message = 'file larger than expected';
				return false;
			}
			return false;
		}

		if (!is_uploaded_file($_FILES[$name]['tmp_name'])) {
			$this->db->transRollback();
			$message = 'uploaded file not found';
			return false;
		}
		return true;
	}


	//this function will return the last auto generated id of the last insert statement
	private function getLastInsertId()
	{
		return getLastInsertId($this->db);
	}
	private function DoAfterInsertion($model, $type, $data, &$db, &$message = '', &$redirect = '')
	{
		$method = 'on' . ucfirst($model) . 'Inserted';
		if (method_exists($this->modelControllerCallback, $method)) {
			return $this->modelControllerCallback->$method($data, $type, $db, $message, $redirect);
		}
		return true;
	}

	// the message variable will give the eror message if there is an error and the variable is passed
	private function validateModelData($model, $type, &$data, &$message = '')
	{
		$method = 'validate' . ucfirst($model) . 'Data';
		if (method_exists($this->modelControllerDataValidator, $method)) {
			$result = $this->modelControllerDataValidator->$method($data, $type, $message);
			return $result;
		}
		return true;
	}

	private function validateModels($method, &$message)
	{
		if (!isset($_POST['edu-submit'])) {
			$message = 'fatal error!';
			return false;
		}
		$jsonEncode = $_POST['combined-models'];
		unset($_POST['edu-submit'], $_POST['edu-reset'], $_POST['combined-models']);
		$arr = json_decode($jsonEncode, true);
		$keys = array_keys($arr);
		$allGood = $this->isAllModel($keys, $method, $message);
		if ($allGood) {
			return $arr;
		}
		return false;
	}

	private function isAllModel($keys, $method, $message)
	{
		for ($i = 0; $i < count($keys); $i++) {
			$model = $keys[$i];
			if (!$this->isModel($model)) {
				$message = "$model is not a valid model";
				return false;
			}
			// if (!$this->accessControl->moduleAccess($model,$method)) {
			// 	$message="access denied";
			// 	return false;
			// }
		}
		return true;
	}

	//this method is called when a single insertion is to be made.
	private function  insertSingle($model, $filter, $noArrSkip)
	{
		$this->modelCheck($model, 'c');
		$message = '';
		$filter = (bool)$filter;
		$noArrSkip = (bool)$noArrSkip; // this is use to allow extra param array if needed later in the code
		$data = $this->request->getPost(null);
		$data = $this->processFormUpload($model, $data, false);
		unset($data["edu-submit"]);
		$newModel = loadClass("$model");
		$parameter = $data;
		// this is allow param not stated in the entity typeArray property to pass through without being removed from the array
		if (!$noArrSkip) {
			$parameter = $this->extractSubset($parameter, $newModel);
		}
		$parameter = removeEmptyAssoc($parameter);
		if ($this->validateModelData($model, 'insert', $parameter, $message) == false) {
			echo createJsonMessage('status', false, 'message', $message);
			return;
		}

		// using this to skip a param from the other param for insertion and later use in modelcallback function further processing in the code

		if (property_exists($newModel, 'skipParam')) {
			$skip = $newModel::$skipParam;
			if ($skip) {
				foreach ($skip as $sk) {
					if (array_key_exists($sk, $parameter)) {
						unset($parameter[$sk]);
					}
				}
			} // ended here
		}

		// check if date_modified or date_created is part of the entity
		$labelArray = array_keys($newModel::$labelArray);
		$dateLabel = "default";
		if (array_key_exists($model, $this->_dateParam)) {
			$dateLabel = $this->_dateParam[$model];
		}

		$dateParam = $this->_dateParam[$dateLabel];
		if (in_array($dateParam[0], $labelArray)) {
			$parameter[$dateParam[0]] = date('Y-m-d H:i:s');
		}
		if (in_array($dateParam[1], $labelArray)) {
			$parameter[$dateParam[1]] = date('Y-m-d H:i:s');
		}

		$newModel->setArray($parameter);
		if (!$this->validateModel($newModel, $message)) {
			echo createJsonMessage('status', false, 'message', $message);
			return;
		}
		$message = '';
		$this->db->transBegin();
		if ($newModel->insert($this->db, $message)) {
			$inserted = $this->getLastInsertId($this->db);
			$data['LAST_INSERT_ID'] = $inserted;

			if ($this->DoAfterInsertion($model, 'insert', $data, $this->db, $message, $redirect)) {
				$this->db->transCommit();
				if ($redirect != '') {
					$arr = array();
					$arr['status'] = true;
					$arr['message'] = $redirect;
					echo json_encode($arr);
					return;
				} else {
					$message = empty($message) ? 'Operation Successful ' : $message;
				}
				echo createJsonMessage('status', true, 'message', $message, 'data', $inserted);
				// $this->log($model,"inserted new $model information");//log the activity
				return;
			}
		}
		$this->db->transRollback();
		$message = empty($message) ? "an error occured while saving information" : $message;
		echo createJsonMessage('status', false, 'message', $message);
		// $this->log($model,"unable to insert $model information");
	}

	// private function log($model,$description){
	// 	$this->application_log->log($model,$description);
	// }

	function update($model, $id = '', $filter = false, $flagAction = false)
	{
		if (empty($id) || empty($model)) {
			echo createJsonMessage('status', false, 'message', 'an error occured while processing information', 'description', 'the model parameter is null so it must not be null');
			return;
		}
		if ($model == 'many') {
			$this->updateMany($filter);
		} else {
			$this->updateSingle($model, $id, $filter, $flagAction);
		}
	}

	private function updateSingle($model, $id, $filter, $flagAction = false)
	{
		$this->modelCheck($model, 'u');
		$newModel = loadClass("$model");
		$filter = (bool)$filter;
		$data = $this->request->getPost(null);
		unset($data["edu-submit"], $data["edu-reset"]);
		$data = $this->processFormUpload($model, $data, $id);
		//pass in the value needed by the model itself and discard the rest.
		$parameter = $this->extractSubset($data, $newModel);
		$this->db->transBegin();
		if ($this->validateModelData($model, 'update', $parameter, $message)) {
			// check if date_modified is part of the entity
			$labelArray = array_keys($newModel::$labelArray);
			$dateLabel = "default";
			if (array_key_exists($model, $this->_dateParam)) {
				$dateLabel = $this->_dateParam[$model];
			}

			$dateParam = $this->_dateParam[$dateLabel];
			if (in_array($dateParam[1], $labelArray)) {
				$parameter[$dateParam[1]] = date('Y-m-d H:i:s');
			}

			$newModel->setArray($parameter);
			if (!$newModel->update($id, $this->db)) {
				$this->db->transRollback();
				// $message="cannot perform update";
				$arr['status'] = false;
				$arr['message'] = 'cannot perform update';
				if ($flagAction) {
					$arr['flagAction'] = $flagAction;
				}
				echo json_encode($arr);
				return;
			}
			$data['ID'] = $id;
			if ($this->DoAfterInsertion($model, 'update', $data, $this->db, $message, $redirect)) {
				$this->db->transCommit();
				if ($redirect != '') {
					$arr = array();
					$arr['status'] = true;
					$arr['message'] = $redirect;
					echo json_encode($arr);
					return;
				} else {
					$message = empty($message) ? 'Operation Successful ' : $message;
				}
				$arr['status'] = true;
				$arr['message'] = $message;
				if ($flagAction) {
					$arr['flagAction'] = $flagAction;
				}
				echo json_encode($arr);
				return;
			} else {
				$this->db->transRollback();
				$arr['status'] = false;
				$arr['message'] = $message;
				if ($flagAction) {
					$arr['flagAction'] = $flagAction;
				}
				echo json_encode($arr);
				return;
			}
		} else {
			$this->db->transRollback();
			$arr['status'] = false;
			$arr['message'] = $message;
			if ($flagAction) {
				$arr['flagAction'] = $flagAction;
			}
			echo json_encode($arr);
			return;
		}
	}

	function delete($model, $id = '')
	{
		if (isset($_POST['ID'])) {
			$id = $_POST['ID'];
		}
		if (empty($id)) {
			echo createJsonMessage('status', false, 'message', 'error occured while deleting information');
			return;
		}

		$this->modelCheck($model, 'd');
		$newModel = loadClass("$model");
		if ($newModel->delete($id)) {
			echo createJsonMessage('status', true, 'message', 'information deleted successfully');
		} else {
			echo createJsonMessage('status', false, 'message', 'error occured while deleting information');
		}
	}
	private function modelCheck($model, $method)
	{
		if (!$this->isModel($model)) {
			echo createJsonMessage('status', false, 'message', "{$model} is not an entity model");
			exit;
		}
		// echo "got here";
		// if (!$this->accessControl->moduleAccess($model,$method)) {
		// 	echo createJsonMessage('status',false,'message','operation access denied');
		// 	exit;
		// }
	}
	//this function checks if the argument id actually  a model
	private function isModel($model)
	{
		$model = loadClass("$model");
		if (!empty($model) && $model instanceof $this->crudNameSpace) {
			return true;
		}
		return false;
	}
	//check that the algorithm fit and that required data are not empty
	private function validateModel($model, &$message)
	{
		return $model->validateInsert($message);
	}
	
	//function to extract a subset of fields from a particular field
	private function extractSubset($array, $model)
	{
		//check that the model is instance of crud
		//take care of user upload substitute the necessary value for the username
		//dont specify username directly
		$result = array();
		if ($model instanceof $this->crudNameSpace) {
			$keys = array_keys($model::$labelArray);
			$valueKeys = array_keys($array);
			$temp = array_intersect($valueKeys, $keys);
			foreach ($temp as $value) {
				$result[$value] = $array[$value];
			}
		}
		if ($model == 'user') {
			$result = $this->processUser($array, $result);
		}
		return $result;
	}

	private function goPrevious($message, $path = '')
	{
		$location = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		if (empty($location) || !startsWith($location, base_url())) {
			$location = $path;
		}
		$this->session->set_flashdata('message', $message);
		header("location:$location");
	}

	//function for downloading data template
	function template($model)
	{
		//validate permission here too.
		if (empty($model)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
			exit;
		}
		$model = loadClass("$model");
		$model = new $model;
		if (!is_subclass_of($model, $this->crudNameSpace)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
			exit;
		}
		$exception = null;
		if (isset($_GET['exc'])) {
			$exception = explode('-', $_GET['exc']);
		}
		$model->downloadTemplate($exception);
	}
	function export($model)
	{
		$condition = null;
		$args  = func_get_args();
		if (count($args) > 1) {
			$method = 'export' . ucfirst($args[1]);
			if (method_exists($this, $method)) {
				$condition = $this->$method();
			}
		}
		if (empty($model)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
			exit;
		}
		$model = loadClass("$model");
		if (!is_subclass_of($model, $this->crudNameSpace)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException();
			exit;
		}
		$model->export($condition);
	}

	private function loadUploadedFileContent($filePath = false, $filename = '')
	{
		$filename = ($filename != '') ? $filename : 'bulk-upload';
		$status = $this->checkFile($filename, $message);
		if ($status) {
			if (!endsWith($_FILES[$filename]['name'], '.csv')) {
				echo "Invalid file format";
				exit;
			}
			$path = $_FILES[$filename]['tmp_name'];
			$content = file_get_contents($path);
			if ($filePath) {
				$res = move_uploaded_file($_FILES[$filename]['tmp_name'], $filePath);
				if (!$res) {
					exit("error occured while performing file upload");
				}
			}
			return $content;
		} else {
			echo "$message";
			exit;
		}
	}

	/**
	 * @param string $model
	 * @return \App\Views\upload_report
	 */
	public function modelFileUpload(string $model){
		$content = $this->loadUploadedFileContent();
		$content = trim($content);
		$array = stringToCsv($content);
		$header = array_shift($array);
		$defaultValues = null;
		$args = func_get_args();
		if (count($args) > 1) {
			$method = 'upload'.ucfirst($args[1]);
			if (method_exists($this, $method)) {
				$defaultValues = $this->$method();
				$keys = array_keys($defaultValues);
				for ($i=0; $i < count($keys); $i++) { 
					$header[]=$keys[$i];
				}
				foreach ($defaultValues as $field => $value) {
					replaceIndexWith($array,$field,$value);
				}
			}
		}
		//check for rarecases when the information in one of the fields needed to be replaces
		if (isset($_GET['rp'] ) && $_GET['rp']) {
			$funcName = $_GET['rp'];
			# go ahead and call the function make the change
			$funcName = 'replace'.ucfirst($funcName);
			if (method_exists($this, $funcName)) {
				//the function must accept the parameter as a reference
				$this->$funcName($header,$array);
			}
		}
		$db=null;
		$arr =array('admin');
		if (in_array($model, $arr)) {
			$this->db->transBegin();
			$db=$this->db;
		}
		$oldModel = $model;
		$model = loadClass($model);
		$result = $model->upload($header,$array,$message,$db);
		$data=array();
		$data['pageTitle']='file upload report';
		$data['backLink'] = $_SERVER['HTTP_REFERER'];
		if ($result) {
			$data['status']=true;
			$data['message']= ($message != '') ? $message : 'You have successfully performed the operation...';
			$data['model']=$oldModel;
			if ($result && in_array($oldModel, $arr)) {
				$db->transCommit();
			}
		}
		else{
			$data['status']=false;
			$data['message']=$message;
			$data['model']=$oldModel;
			if (!$result && in_array($oldModel, $arr)) {
				$db->transRollback();
			}
		}

		if ($this->webSessionManager->getCurrentuserProp('user_type')=='admin') {
			$data['canView']=$this->getAdminSidebar();
		}
		$data['webSessionManager'] = $this->webSessionManager;
		return view('uploadreport',$data);
	}

	/**
	 * @return array
	 */
	private function getAdminSidebar()
	{
		$adminData = new \App\Models\Custom\AdminData;
		$admin = loadClass('admin');
		$admin = new $admin();
		$admin->ID= $this->webSessionManager->getCurrentuserProp('user_table_id');
		$admin->load();
		$role = $admin->role;
		return $adminData->getCanViewPages($role);
	}

	private function getDepartmentByName(string $name){
		$name = $this->db->escapeLikeString($name);
		$query = "SELECT id from departments where name like '%$name%' ESCAPE '!'";
		$result = $this->db->query($query);
		if($result->getNumRows() > 0){
			$result = $result->getResultArray()[0];
			return $result['id'];
		}
		return false;
	}

	private function getDesignationByName(string $name){
		$name = $this->db->escapeLikeString($name);
		$query = "SELECT id from designation where designation_name like '%$name%' ESCAPE '!'";
		$result = $this->db->query($query);
		if($result->getNumRows() > 0){
			$result = $result->getResultArray()[0];
			return $result['id'];
		}
		return false;
	}

	private function getStaffByNum(string $num){
		$query = "SELECT id from staff where occupant_num  = '$num'";
		$result = $this->db->query($query);
		if($result->getNumRows() > 0){
			$result = $result->getResultArray()[0];
			return $result['id'];
		}
		return false;
	}

	private function updateStaffInfo(array $data,$id){
		$query = $this->db->table('staff');
		$query = $query->update($data, ['id' => $id]);
		return $query;
	}

	private function designationMapName(string $name){
		$result = [
			'HEO' => 'Higher Executive Officer',
			'EO' => 'Executive Officer',
			'E.O.' => 'Executive Officer',
			'E. O.' => 'Executive Officer',
			'MIS' => 'Medical lab. Scientist',
			'ASO' => 'Asst. Security Officer',
			'S.W.S.' => 'Senior Workshop Supervisor',
			'PEO' => 'Principal Executive Ofiicer',
			'P.E.O.' => 'Principal Executive Ofiicer',
			'PEO II' => 'Principal Executive Officer II',
			'PEO I' => 'Principal Executive Officer I',
			'A.O.' => 'Administrative Officer'

		];
		if(array_key_exists($name, $result)){
			return $result[$name];
		}
		return $name;
	}

	private function hallMapping(string $name){
		$result = [
			'Off Campus' => 'off_campus',
			'Abadina' => 'campus',
		];
		if(array_key_exists($name, $result)){
			return $result[$name];
		}
		return 'off_campus';
	}

	private function formatToDate($name){
		$result = '';
		if(strpos($name, '/') !== false){
            $result = formatDateWithSlash($name);
		}
		else if(preg_match('~[A-Za-z]~', $name)){
			try{
				$result = formatToDateOnly(trim($name));
			}catch(Exception $e){
				$result = formatToUTC();
			}
		}
		return $result;
	}

	private function splitName(string $name){
		$fullname = str_replace('"','', $name); // replace , with single space
		$fullname = str_replace(',',' ', $name); // replace , with single space
		$fullname = str_replace('  ',' ', $fullname); // replace double with single space
		$splitName = explode(' ', $fullname);
		if (count($splitName) < 2) {
			print_r($name);
			echo "Name not valid\n";
			return [];
		}else{
			$lastname = $splitName[0];
			$firstname = strlen($splitName[1] <= 2 && count($splitName) > 2) ? $splitName[2] : $splitName[1];
			$middlename = count($splitName)>2?$splitName[2]:'';
			return [$lastname, $firstname, $middlename];
		}
	}

	private function createAccount(string $table,array $data){
		$query = $this->db->table($table);
		return $query->insert($data);
	}

	private function generateApplicantCode(){
		$orderStart = '100000011';
		$query = "select applicant_code as code from applicant_allocation order by ID desc limit 1";
		$result = $this->db->query($query);
		$result = $result->getResultArray();
		if($result && $result[0]['code']){
			[$label,$temp] = explode('UIH',$result[0]['code']);
			$orderStart = ($temp) ? $temp+1 : $orderStart;
		}
		return 'UIH'.$orderStart;
	}

	private function validateApplicant($staff){
		$query = "SELECT * from applicant_allocation a where a.staff_id = '$staff' and cast(date_created as date) = curdate() order by id desc limit 1";
		$result = $this->db->query($query);
		if($result->getNumRows() <= 0){
			return false;
		}
		return $result->getResultArray()[0];
	}

	public function upload_applicant(){
		$content = $this->loadUploadedFileContent(false, 'bulk_applicant');
		$content = trim($content);
		$array = stringToCsv($content);
		$header = array_shift($array);

		$staffIndex = array_search('staff_num', $header);
		$nameIndex = array_search('name', $header);
		$departIndex = array_search('department', $header);
		$designIndex = array_search('designation', $header);
		$appointIndex = array_search('appointment_date', $header);
		$gradeIndex = array_search('grade', $header);
		$hallIndex = array_search('present_all', $header);
		$dateAppIndex = array_search('date_of_application', $header);

		$insertString = '';
		$query = "insert ignore into staff (occupant_num,surname,firstname,othername,marital_status,grade,designation_id,date_first_app,hall) values ";
		$this->db->transBegin();
		foreach($array as $data){
			$staffNum = trim($data[$staffIndex]);
			$name = $data[$nameIndex];
			if(count($data) == 12 && isset($data[3])){ // incase name split into 2 column
				$name .= ' '.$data[3];
			}
			$name = $this->splitName(trim($name));
			if(count($name) <= 0){
				continue;
			}
			$lastname = $name[0];
			$firstname = $name[1];
			$middlename = $name[2];
			if(!$staffNum){
				echo "{$lastname} no staff number \n";
				continue;
			}
			$hall = $this->hallMapping(trim($data[$hallIndex]));
			$department = $this->getDepartmentByName(trim($data[$departIndex]));
			$appDate = $this->formatToDate(trim($data[$dateAppIndex]));
			// $appDate = trim($data[$dateAppIndex]);
			// $appDate = formatToUTC();

			if($id = $this->getStaffByNum($staffNum)){
				$this->updateStaffInfo(['hall' => $hall],$id);

				// create user auth account
				$userData = ['username'=>$staffNum,'password'=>encode_password($staffNum),'user_type'=>'staff','user_table_id'=>$id,'status'=>'1'];
				$builder = $this->db->table('user');
				$result = $builder->getWhere(['username'=>$staffNum]);
				if($result->getNumRows() > 0){
					continue;
				}

				if(!$this->createAccount('user',$userData)){
					$this->db->transRollback();
					echo "{$staffNum} user account not created\n";
				}
				
				// create applicant
				$hall = 'campus';
				$applicantData = ['applicant_code'=>$this->generateApplicantCode(),'staff_id'=>$id,'category_id'=>'1','marriage'=>'married','departments_id'=>$department,'hall_location'=>$hall,'date_created'=>$appDate,'applicant_status'=>'approved'];

				if($applicant = $this->validateApplicant($id)){
					$data = [
						'applicant_allocation_id' => $applicant['id'],
						'status' => 'approved'
					];

					// update the applicant_allocation table first and insert into allocation
					$this->db->table('applicant_allocation')
						->update(['applicant_status'=>'approved'],
							['id'=>$applicant['id']]);

					if(!$this->createAccount('allocation',$data)){
						$this->db->transRollback();
						echo "{$staffNum} allocation not created\n";
					}
				}else{
					if(!$this->createAccount('applicant_allocation',$applicantData)){
						$this->db->transRollback();
						echo "{$staffNum} allocation not created\n";
					}
					$allocationID = lastInsertId();
					$data = ['applicant_allocation_id'=>$allocationID, 'status' => 'approved'];
					if(!$this->createAccount('allocation',$data)){
						$this->db->transRollback();
						echo "{$staffNum} allocation not created\n";
					}
				}
				
				$this->db->transCommit();
				echo "{$staffNum} created \n";
			}else{
				$designation = $this->getDesignationByName($this->designationMapName(trim($data[$designIndex])));
				$appointmentDate = $this->formatToDate(trim($data[$appointIndex]));
				$grade = trim($data[$gradeIndex]);
				$hall = 'campus';
				
				if(strpos($staffNum, '/') === false){ // checking if '/' exist
					$insertString = '( '.$this->db->escape($staffNum).', '.$this->db->escape($lastname).', '.$this->db->escape($firstname).', '.$this->db->escape($middlename).', "married",'.$this->db->escape($grade).', '.$this->db->escape($designation).', '.$this->db->escape($appointmentDate).', '.$this->db->escape($hall).') on duplicate key update occupant_num = values(occupant_num),surname = values(surname),firstname = values(firstname),othername = values(othername),marital_status = values(marital_status),grade = values(grade),designation_id = values(designation_id),date_first_app = values(date_first_app),hall = values(hall) ';
				}else{
					$insertString = "(".$this->db->escapeString($staffNum).", ".$this->db->escapeString($lastname).", ".$this->db->escapeString($firstname).", ".$this->db->escapeString($middlename).", 'married',".$this->db->escapeString($grade).", ".$this->db->escapeString($designation).", ".$this->db->escapeString($appointmentDate).", ".$this->db->escapeString($hall)." ) on duplicate key update occupant_num = values(occupant_num),surname = values(surname),firstname = values(firstname),othername = values(othername),marital_status = values(marital_status),grade = values(grade),designation_id = values(designation_id),date_first_app = values(date_first_app),hall = values(hall) ";
				}
				$query .= $insertString;
				// echo $query;exit;

				$result = $this->db->query($query);
				if (!$result) {
					$this->db->transRollback();
					echo "{$staffNum} not inserted\n";
				}else{
					$lastInsertId = getLastInsertId($this->db);
					// creating user account username => staff_num and password => staff_num
					$userData = ['username'=>$staffNum,'password'=>encode_password($staffNum),'user_type'=>'staff','user_table_id'=>$lastInsertId,'status'=>'1'];

					$builder = $this->db->table('user');
					$result = $builder->getWhere(['username'=>$staffNum]);
					if($result->getNumRows() > 0){
						continue;
					}

					if(!$this->createAccount('user',$userData)){
						$this->db->transRollback();
						echo "{$staffNum} user account not created\n";
					}

					// creating applicant
					$applicantData = ['applicant_code'=>$this->generateApplicantCode(),'staff_id'=>$lastInsertId,'category_id'=>'1','marriage'=>'married','departments_id'=>$department,'hall_location'=>$hall,'date_created'=>$appDate,'applicant_status'=>'approved'];

					if($applicant = $this->validateApplicant($lastInsertId)){
						$data = ['applicant_allocation_id'=>$applicant['id'], 'status' => 'approved'];

						// update the applicant_allocation table first and insert into allocation
						$this->db->table('applicant_allocation')
							->update(['applicant_status'=>'approved'],
								['id'=>$applicant['id']]);

						if(!$this->createAccount('allocation',$data)){
							$this->db->transRollback();
							echo "{$staffNum} allocation not created\n";
						}
					}else{
						if(!$this->createAccount('applicant_allocation',$applicantData)){
							$this->db->transRollback();
							echo "{$staffNum} allocation not created\n";
						}
						$allocationID = lastInsertId();
						$data = ['applicant_allocation_id'=>$allocationID, 'status' => 'approved'];
						if(!$this->createAccount('allocation',$data)){
							$this->db->transRollback();
							echo "{$staffNum} allocation not created\n";
						}
					}
					$this->db->transCommit();
					echo "{$staffNum} created\n";
				}
			}
		}

		echo "Successfully uploaded";
	}

	public function upload_applicant_fresh(){
		$content = $this->loadUploadedFileContent(false, 'bulk_applicant');
		$content = trim($content);
		$array = stringToCsv($content);
		$header = array_shift($array);

		$staffIndex = array_search('staff_num', $header);
		$nameIndex = array_search('name', $header);
		$departIndex = array_search('department', $header);
		$designIndex = array_search('designation', $header);
		$appointIndex = array_search('appointment_date', $header);
		$gradeIndex = array_search('grade', $header);
		$hallIndex = array_search('present_all', $header);
		$dateAppIndex = array_search('date_of_application', $header);

		$insertString = '';
		$query = "insert ignore into staff (occupant_num,surname,firstname,othername,marital_status,grade,designation_id,date_first_app,hall) values ";
		$this->db->transBegin();
		foreach($array as $data){
			$staffNum = trim($data[$staffIndex]);
			$name = $data[$nameIndex];
			if(count($data) == 12 && isset($data[3])){ // incase name split into 2 column
				$name .= ' '.$data[3];
			}
			$name = $this->splitName(trim($name));
			if(count($name) <= 0){
				continue;
			}
			$lastname = $name[0];
			$firstname = $name[1];
			$middlename = $name[2];
			if(!$staffNum){
				echo "{$lastname} no staff number \n";
				continue;
			}
			$hall = $this->hallMapping(trim($data[$hallIndex]));
			$department = $this->getDepartmentByName(trim($data[$departIndex]));
			// $appDate = $this->formatToDate(trim($data[$dateAppIndex]));
			// $appDate = trim($data[$dateAppIndex]);
			$appDate = formatToUTC($appDate);

			if($id = $this->getStaffByNum($staffNum)){
				$this->updateStaffInfo(['hall' => $hall],$id);

				// create user auth account
				$userData = ['username'=>$staffNum,'password'=>encode_password($staffNum),'user_type'=>'staff','user_table_id'=>$id,'status'=>'1'];
				$builder = $this->db->table('user');
				$result = $builder->getWhere(['username'=>$staffNum]);
				if($result->getNumRows() > 0){
					continue;
				}

				if(!$this->createAccount('user',$userData)){
					$this->db->transRollback();
					echo "{$staffNum} user account not created\n";
				}
				
				// create applicant
				$applicantData = ['applicant_code'=>$this->generateApplicantCode(),'staff_id'=>$id,'category_id'=>'1','marriage'=>'married','departments_id'=>$department,'hall_location'=>$hall,'date_created'=>$appDate];

				$builder = $this->db->table('applicant_allocation');
				$result = $builder->getWhere(['staff_id'=>$id]);
				if($result->getNumRows() > 0){
					continue;
				}
				
				if(!$this->createAccount('applicant_allocation',$applicantData)){
					$this->db->transRollback();
					echo "{$staffNum} allocation not created\n";
				}
				$this->db->transCommit();
				echo "{$staffNum} created \n";
			}else{
				$designation = $this->getDesignationByName($this->designationMapName(trim($data[$designIndex])));
				$appointmentDate = $this->formatToDate(trim($data[$appointIndex]));
				$grade = trim($data[$gradeIndex]);

				// if ($insertString) {
				// 	$insertString .= ',';
				// }
				
				if(strpos($staffNum, '/') === false){ // checking if '/' exist
					$insertString = '( '.$this->db->escape($staffNum).', '.$this->db->escape($lastname).', '.$this->db->escape($firstname).', '.$this->db->escape($middlename).', "married",'.$this->db->escape($grade).', '.$this->db->escape($designation).', '.$this->db->escape($appointmentDate).', '.$this->db->escape($hall).') on duplicate key update occupant_num = values(occupant_num),surname = values(surname),firstname = values(firstname),othername = values(othername),marital_status = values(marital_status),grade = values(grade),designation_id = values(designation_id),date_first_app = values(date_first_app),hall = values(hall) ';
				}else{
					$insertString = "(".$this->db->escapeString($staffNum).", ".$this->db->escapeString($lastname).", ".$this->db->escapeString($firstname).", ".$this->db->escapeString($middlename).", 'married',".$this->db->escapeString($grade).", ".$this->db->escapeString($designation).", ".$this->db->escapeString($appointmentDate).", ".$this->db->escapeString($hall)." ) on duplicate key update occupant_num = values(occupant_num),surname = values(surname),firstname = values(firstname),othername = values(othername),marital_status = values(marital_status),grade = values(grade),designation_id = values(designation_id),date_first_app = values(date_first_app),hall = values(hall) ";
				}
				$query .= $insertString;
				// echo $query;exit;

				$result = $this->db->query($query);
				if (!$result) {
					$this->db->transRollback();
					echo "{$staffNum} not inserted\n";
				}else{
					$lastInsertId = getLastInsertId($this->db);
					// creating user account username => staff_num and password => staff_num
					$userData = ['username'=>$staffNum,'password'=>encode_password($staffNum),'user_type'=>'staff','user_table_id'=>$lastInsertId,'status'=>'1'];

					$builder = $this->db->table('user');
					$result = $builder->getWhere(['username'=>$staffNum]);
					if($result->getNumRows() > 0){
						continue;
					}
					if(!$this->createAccount('user',$userData)){
						$this->db->transRollback();
						echo "{$staffNum} user account not created\n";
					}

					// creating applicant
					$applicantData = ['applicant_code'=>$this->generateApplicantCode(),'staff_id'=>$lastInsertId,'category_id'=>'1','marriage'=>'married','departments_id'=>$department,'hall_location'=>$hall,'date_created'=>$appDate];

					$builder = $this->db->table('applicant_allocation');
					$result = $builder->getWhere(['staff_id'=>$lastInsertId]);
					if($result->getNumRows() > 0){
						continue;
					}
					if(!$this->createAccount('applicant_allocation',$applicantData)){
						$this->db->transRollback();
						echo "{$staffNum} allocation not created\n";
					}
					$this->db->transCommit();
					echo "{$staffNum} created\n";
				}
			}
		}

		echo "Successfully uploaded";
	}
}
