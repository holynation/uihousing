<?php
/**
* The model for performing update, insert and delete for entities
*/
namespace App\Models;

use CodeIgniter\Model;
use App\Models\ModelControllerCallback;
use App\Models\ModelControllerDataValidator;
use App\Models\WebSessionManager;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\I18n\Time;

class EntityCreator
{
	public $outputResult = true;
	public $skipModelValidation = false;
	public $extraDataParam = [];
	private $insertedData = [];

	// RULE: date_created comes first,then date_modified or any named date
	// NOTE: it only accept two diff date,nothing more than that.
	private $modelTimestamp = [
		'default' => ['date_created', 'date_modified'],
	];

	private $modelControllerCallback;
	private $modelControllerDataValidator;
	private $webSessionManager;
	private $entitiesNameSpace = 'App\Entities\\';
	private $crudNameSpace = 'App\Models\Crud';
	private $_writableDirectory = ROOTPATH."public/uploads/";
	private $_rootUploadsDirectory = ROOTPATH."writable/uploads/";
	protected $db;
	protected $request;

	function __construct(RequestInterface $request)
	{
		$this->modelControllerCallback = new ModelControllerCallback;
		$this->modelControllerDataValidator = new ModelControllerDataValidator;
		$this->webSessionManager = new WebSessionManager;
		$this->db = db_connect();
		$this->request = $request;
	}

	private function checkPermission(){
		return true;
		//check that the user has permission to modify
		$cookie = getPageCookie();
		if (!in_array($this->webSessionManager->getCurrentUserProp('user_type'), array('student','admin','lecturer')) && @!$this->role->canModify($cookie[0],$cookie[1])) {
		  # who the access denied page.
			if (isset($_GET['a']) && $_GET['a']) {
				displayJson(false,'you do not  have permission to perform this action');exit;
			}
		  echo show_operation_denied();
		}
	}

	private function show_404(){
		throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
	}

	public function add($model,$filter=false,$raw=false){
		try{
			if (empty($model)) { //make sure empty value for model is not allowed.
				displayJson(false,'an error occured while processing information');
				return;

			}
			unset($_POST['MAX_FILE_SIZE']);
			// $this->log($model,"inserting $model");
			return $this->insertSingle($model,$filter,$raw);
		}
		catch(\Exception $e){
			$this->db->transRollback();
			displayJson(false,$e->getMessage());
			return;
		}

	}

	#this function is used to  document
	private function processFormUpload(string $model,array $parameter, $insertType=false){
		$oldModel = $model;
		$model = $this->entitiesNameSpace.$model;
		$paramFile= $model::$documentField;
		if (empty($paramFile)) {
			return $parameter;
		}
		$fields = array_keys($_FILES);
		foreach ($paramFile as $name => $value) {
			# if the field name is present in the fields, then upload the document
			if (in_array($name, $fields)) {
				# this is a precaution if no keys of this lable are not set in the array. Note $type and $size must always be defined
				$preserve = false;
				$max_width = 0;
				$max_height = 0;
				$directory="";
				extract($value);

				$method ="get".ucfirst($oldModel)."Directory";
				$uploadDirectoryManager = new \App\Models\UploadDirectoryManager;
				if (method_exists($uploadDirectoryManager, $method)) {
					$dir  = $uploadDirectoryManager->$method($parameter);
					if ($dir===false) {
						displayJson(false,'Error while uploading file');
						return false;
					}
					$directory.=$dir;
				}

				$currentUpload = $this->uploadFile($oldModel,$name,$type,$size,$directory,$message,$insertType,$preserve,$max_width,$max_height);
				if ($currentUpload==false) {
					displayJson(false,$message);
					return false;
				}
				$parameter[$name]=$message;
			}
			else{
				continue;
			}
		}
		return $parameter;
	}

	private function uploadFile(string $model,string $name,$type,$maxSize,$destination,&$message='',$insertType=false,$preserve=false,int $max_width=0,int $max_height=0){
		if (!$this->checkFile($name,$message)) {
			return false;
		}
		$filename=$_FILES[$name]['name'];
		$ext = getFileExtension($filename);
		$fileSize = $_FILES[$name]['size'];
		$typeValid = is_array($type)?in_array(strtolower($ext), $type):strtolower($ext)==strtolower($type);
		if (!empty($filename) &&  $typeValid  && !empty($destination)) {
			if (!is_null($maxSize) && $fileSize > $maxSize) {
				$calcsize = calc_size($maxSize);
				$message = "The file you are attempting to upload is larger than the permitted size ($calcsize)";
				return false;
			}
			$publicDestination = $this->_writableDirectory . $destination;
			if(!is_dir($publicDestination)){
				mkdir($publicDestination, 0777, true);
			}
			$publicDestination = $destination;
			$destination=$this->_rootUploadsDirectory.$destination;
			if (!is_dir($destination)) {
				mkdir($destination,0777,true);
			}
			# using this is to check whether max_width or max_height was passed
			if(($max_width !== 0 && $max_height !== 0) || $max_width !== 0 || $max_height !== 0){
                $temp_name = $_FILES[$name]['tmp_name'];

                if (!$this->isAllowedDimensions($temp_name,$max_width,$max_height))
                {
                    $message = "The image doesn't fit into the allowed dimensions (max_width:$max_width x max_height:$max_height).";
                    return false;
                }
			}
			$naming= '';
			$new_name = uniqueString(13)."_".date('Y-m-d').".".$ext;
			if($insertType){
				$getUpload = $this->getUploadID($model,$insertType,$name);
				if($getUpload === 'insert'){
					// this means inserting
					$naming = ($preserve) ? $filename : $new_name; 
				}else{
					$naming = basename($getUpload); // this means updating
				}
				
			}else{
				// this means inserting
				$naming = ($preserve) ? $filename : $new_name; 
			}
			$destination.=$naming;
			$publicDestination .= $naming;
			if(move_uploaded_file($_FILES[$name]['tmp_name'], $destination)){
				$destination = $this->createFileSymlink($publicDestination, $destination);
				$message=base_url($destination);
				return true;
			}
			else{
				$message = "error while uploading file. please try again";return false;
			}
		}
		else{
			$message = "error while uploading file. please try again";return false;
		}
		$message='error while uploading file. please try again';
		return false;
	}

	private function isAllowedDimensions($temp,$max_width=0,$max_height=0)
	{

		if (function_exists('getimagesize'))
		{
			$D = @getimagesize($temp);

			if ($max_width > 0 && $D[0] > $max_width)
			{
				return FALSE;
			}

			if ($max_height > 0 && $D[1] > $max_height)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	private function createFileSymlink(string $link, string $target){
        return createSymlink($link, $target);
	}

	private function getUploadID($model,$id,$name='')
	{
		if ($id) {
			# this means that it is updating
			$query="select $name from $model where id = ?";
			$result = $this->db->query($query,array($id));
			$result=$result->getResultArray();
			
			# the return message 'insert' is a rare case whereby there is no media 
			# file at first, yet one want to add the media file through update action
			return (!empty($result[0][$name])) ? $result[0][$name] : 'insert';
		}
		else{
			# this means it is inserting
			$query="select id from $model order by id desc limit 1";
			$result = $this->db->query($query);
			$result=$result->getResultArray();
			if ($result->getNumRows() > 0) {
				return $result[0]['id'];
			}
			return 1; //if no initial record
		}
	}

	private function checkFile(string $name,&$message=''){
		$error = !$_FILES[$name]['name'] || $_FILES[$name]['error'];
		if ($error) {
			if ((int)$error===2) {
				$message = 'file larger than expected';
				return false;
			}
			$message = "check if file [{$name}] is uploaded";
			return false;
		}
		if (!is_uploaded_file($_FILES[$name]['tmp_name'])) {
			$this->db->transRollback();
			$message='uploaded file not found';
			return false;
		}
		return true;
	}

	//this function will return the last auto generated id of the last insert statement
	private function getLastInsertId(){
		$query = "SELECT LAST_INSERT_ID() AS last";//sud specify the table
		$result =$this->db->query($query);
		$result = $result->getResultArray();
		return $result[0]['last'];

	}

	private function DoAfterInsertion($model,$type,$data,&$db,&$message='',&$extra=[]){
		$method = 'on'.ucfirst($model).'Inserted';
		if (method_exists($this->modelControllerCallback, $method)) {
			return $this->modelControllerCallback->$method($data,$type,$db,$message,$extra);
		}
		return true;
	}

	// the message variable will give the eror message if there is an error and the variable is passed
	private function validateModelData(string $model,string $type,array &$data,&$message=''){
		$method = 'validate'.ucfirst($model).'Data';
		if (method_exists($this->modelControllerDataValidator, $method)) {
			$result =$this->modelControllerDataValidator->$method($data,$type,$message);
			return $result;
		}
		return true;
	}

	private function validateModels($method,&$message){
		$arr = json_decode($jsonEncode,true);
		$keys = array_keys($arr);
		$allGood = $this->isAllModel($keys,$method,$message);
		if ($allGood) {
			return $arr;
		}
		return false;
	}

	private function isAllModel($keys,$method,$message){
		for ($i=0; $i < count($keys); $i++) {
			$model = $keys[$i];
			if (!$this->isModel($model) ) {
				$message ="$model is not a valid model";
				return false;
			}
		}
		return true;
	}

	private function insertSingle(string $model, bool $filter=false, bool $raw=false){
		$this->modelCheck($model,'c');
		$message ='';
		$data = $raw ? $_POST : $this->request->getPost(null);
		if(!empty($_FILES) && !empty($_FILES['name'])){
			$data = $this->processFormUpload($model,$data,false);
		}
		# injecting extra data to the data param during post request
		# $request->getPost() when initiated
		if(!empty($this->extraDataParam)){
			$data = array_merge($data,$this->extraDataParam);
		}
		$newModel = loadClass($model);
		$parameter = $this->extractSubset($data,$newModel);
		$parameter = removeEmptyAssoc($parameter);

		if(!$this->skipModelValidation){
			if ($this->validateModelData($model,'insert',$data,$message)==false) {
				if (!$this->outputResult) {
					return false;
				}
				displayJson(false,$message);
				return;
			}
		}

		# ensuring to populate model timestamp
		if ($tempParameter = $this->createModelTimestamp($newModel, $model)){
			if(!empty($tempParameter)){
				$parameter = array_merge($parameter,$tempParameter);
			}
		}

		# this is to ensure data being passed from validateModelData is merged
		# with the rest of the data if available
		if($tempData = $this->getHiddenParameter($newModel, $data)){
			if(!empty($tempData)){
				$parameter = array_merge($parameter,$tempData);
			}
		}
		
		# needed to separate them so that i can set the model param and to be use in validation
		$newModel->setArray($parameter);
		if(!$this->skipModelValidation){
			if (!$this->validateModel($newModel,$message)) {
				if (!$this->outputResult) {
					return false;
				}
				displayJson(false,$message);
				return;
			}
		}
		$message = '';
		$this->db->transBegin();
		if($newModel->insert($this->db,$message)){
			$inserted = $this->getLastInsertId();
			$data['LAST_INSERT_ID']= $inserted;
			if($this->DoAfterInsertion($model,'insert',$data,$this->db,$message,$extra)){
				$this->skipModelValidation = false;
				$this->db->transCommit();
				$this->extraDataParam = [];
				$this->insertedData = $data;
				$message = empty($message)?'operation successfull ':$message;
				if (!$this->outputResult) {
					return true;
				}
				$inserted = $extra ?? $inserted;
				displayJson(true,$message,$inserted);
				// $this->log($model,"inserted new $model information");//log the activity
				return;
			}
		}
		$this->db->transRollback();
		$message = empty($message)?"an error occured while saving information":$message;
		if (!$this->outputResult) {
			return false;
		}
		displayJson(false,$message);return;
		// $this->log($model,"unable to insert $model information");
	}

	/**
	 * This is to upto create timestamp on model
	 * @param  object $model 
	 * @param  [type] $label [description]
	 * @return [type]        [description]
	 */
	public function createModelTimestamp(object $model,string $label,string $type='insert'){
		$parameter = [];
		$labelArray = array_keys($model::$labelArray);
		$dateLabel = "default";
		if (array_key_exists($label, $this->modelTimestamp)) {
			$dateLabel = $this->modelTimestamp[$label];
		}

		$dateParam = $this->modelTimestamp[$dateLabel];
		$dateString = 'now';
		if (in_array($dateParam[0], $labelArray) && $type == 'insert') { # date_created
			$date = new Time($dateString, 'UTC');
			$parameter[$dateParam[0]] = $date->format('Y-m-d H:i:s');
		}
		if (in_array($dateParam[1], $labelArray)) { # date_modified
			$date = new Time($dateString, 'UTC');
			$parameter[$dateParam[1]] = $date->format('Y-m-d H:i:s');
		}
		return $parameter;
	}

	/**
	 * This is to get parameter not originally in the request
	 * @param  object $model [description]
	 * @param  array  $data  [description]
	 * @return [type]        [description]
	 */
	private function getHiddenParameter(object $model, array $data){
		return array_intersect_key($data, $model::$labelArray);
	}

	/**
	 * @return array Returning inserted data
	 */
	public function getInsertedData(){
		return $this->insertedData ?? [];
	}

	private function log($model,$description){
		$this->application_log->log($model,$description);
	}

	public function update(string $model,$id='',$filter=false,$param=false){
		if (empty($id) || empty($model)) {
			if (!$this->outputResult) {
				return false;
			}
			displayJson(false,'an error occured while processing information');
			return;
		}
		return $this->updateSingle($model,$id,$filter,$param);
	}

	private function updateSingle($model,$id,$filter=false,$param=false){
		$this->modelCheck($model,'u');
		$newModel = loadClass("$model");
		$data = $param?$param:$this->request->getPost(null);
		if(!empty($_FILES)){
			$res = $this->processFormUpload($model,$data,$id);
			if(!$res){
				return false;
			}
			$data = $res;
		}
		# filter the value needed by the model itself and discard the rest.
		$parameter = $this->extractSubset($data,$newModel);

		# ensuring to populate model timestamp
		if ($tempParameter = $this->createModelTimestamp($newModel,$model,'update')){
			if(!empty($tempParameter)){
				$parameter = array_merge($parameter,$tempParameter);
			}
		}
		$this->db->transBegin();
		$parameter['ID']=$id;
		$data['ID'] = $id;
		if (!$this->validateModelData($model,'update',$data,$message)){
			displayJson(false,$message);
			return ;
		}

		# this is to ensure data being passed from validateModelData is merged
		# with the rest of the data if available
		if($tempData = $this->getHiddenParameter($newModel, $data)){
			if(!empty($tempData)){
				$parameter = array_merge($parameter,$tempData);
			}
		}

		$newModel->setArray($parameter);
		if ($newModel->update($id,$this->db)) {
			$data['ID']=$id;
		if($this->DoAfterInsertion($model,'update',$data,$this->db,$message,$extra)){
			$this->db->transCommit();
			$message = empty($message)?'operation successfull':$message;
			if (!$this->outputResult) {
				return true;
			}
			$data = (!empty($extra)) ? array_merge($data, $extra) : $data;
			displayJson(true,$message,$data);
			return;
		}
		else{
			// echo $message;exit;
			$this->db->transRollback();
			if (!$this->outputResult) {
				return false;
			}
			displayJson(false,$message);
			return;
		}
		}
		else{
			$this->db->transRollback();
			if (!$this->outputResult) {
				return false;
			}
			displayJson(false,$message);
			return;
		}
	}

	//innplement deleter where function here.
	public function delete($model,$id=''){
		if (isset($_POST['ID'])) {
			$id = $_POST['ID'];
		}
		if (empty($id)) {
			return false;
		}

		$this->modelCheck($model,'d');
		$model = loadClass("$model");
		return $model->delete($id);
	}
	
	private function modelCheck($model,$method){
		if (!$this->isModel($model)) {
			displayJson(false,'error occured while deleting information');
			exit;
		}
	}

	//this function checks if the argument id actually  a model
	private function isModel(string $model){
		$model = loadClass($model);
		if (!empty($model) && $model instanceof $this->crudNameSpace) {
			return true;
		}
		return false;
	}

	//check that the algorithm fit and that required data are not empty
	private function validateModel($model,&$message){
		return $model->validateInsert($message);
	}
		//function to extract a subset of fields from a particular field
	private function extractSubset($array, $model){
		//check that the model is instance of crud
		//take care of user upload substitute the necessary value for the username
		//dont specify username directly
		
		$result =array();
		if ($model instanceof $this->crudNameSpace) {
			$keys = array_keys($model::$labelArray);
			$valueKeys = array_keys($array);
			$temp =array_intersect($valueKeys, $keys);
			foreach ($temp as $value) {
				$result[$value]= $array[$value];
			}
		}
		return $result;
	}

	//function for downloading data template
	public function template($model){
		//validate permission here too.
		if (empty($model)) {
			$this->show_404();
		}
		$model = loadClass("$model");
		if (!is_subclass_of($model, $this->crudNameSpace)) {
			$this->show_404();
		}
		$exception = null;
		if (isset($_GET['exc'])) {
			$exception = explode('-', $_GET['exc']);
		}
		$model->downloadTemplate($exception);
	}

	public function export($model){
		$condition = null;
		$args  =func_get_args();
		if (count($args) > 1) {
			$method = 'export'.ucfirst($args[1]);
			if (method_exists($this, $method)) {
				$condition = $this->$method();
			}
		}
		if (empty($model)) {
			$this->show_404();
		}
		$model = loadClass("$model");
		if (!is_subclass_of($model, $this->crudNameSpace)) {
			$this->show_404();
		}
		$model->export($condition);
	}

	// just create a the template function below to generate the needed paramter.
	public function sFile($model){
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
				// $header = array_merge($header,);
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
		$model = loadClass("$model");

		$result = $model->upload($header,$array,$message);
		$data=array();
		$data['pageTitle']='file upload report';
		if ($result) {
			$data['status']=true;
			$data['message']=$message;
			$data['model']=$model;
			$data['insert_info']=$this->db->conn_id->info;
		}
		else{
			$data['status']=false;
			$data['message']=$message;
			$data['model']=$model;
		}
		echo view('uploadreport',$data);
	}

}