<?php 

	/**
	* This class  is automatically generated based on the structure of the table. And it represent the model of the user table.
	*/ 

namespace App\Entities;

use App\Models\Crud;

class User extends Crud {

private $_data;

protected static $tablename = "User"; 
/* this array contains the field that can be null*/ 
static $nullArray = array('auth_type','last_logout');
static $compositePrimaryKey = array();
static $uploadDependency = array();
/*this array contains the fields that are unique*/ 
static $displayField = 'username';// this display field properties is used as a column in a query if a their is a relationship between this table and another table.In the other table, a field showing the relationship between this name having the name of this table i.e something like this. table_id. We cant have the name like this in the table shown to the user like table_id so the display field is use to replace that table_id.However,the display field name provided must be a column in the table to replace the table_id shown to the user,so that when the other model queries,it will use that field name as a column to be fetched along the query rather than the table_id alone.;
static $uniqueArray = array();
/* this is an associative array containing the fieldname and the type of the field*/ 
static $typeArray = array('username' => 'varchar','password' => 'varchar','user_type' => 'varchar','program_type'=>'enum','user_table_id' => 'int','last_login' => 'timestamp','activity_log'=>'tinyint','last_logout' => 'timestamp','fcm_token'=>'varchar','last_change_password'=>'timestamp','date_created' => 'timestamp','status' => 'tinyint');
/*this is a dictionary that map a field name with the label name that will be shown in a form*/ 
static $labelArray = array('ID' => '','username' => '','password' => '','user_type' => '','activity_log'=>'','user_table_id' => '','last_login' => '','last_logout' => '','fcm_token'=>'','last_change_password'=>'','date_created' => '','status' => '');
/*associative array of fields that have default value*/ 
static $defaultArray = array('last_login' => 'current_timestamp()','date_created' => 'current_timestamp()','status' => '1');
 // populate this array with fields that are meant to be displayed as document in the format array("fieldname"=>array("filetype","maxsize",foldertosave","preservefilename"))
//the folder to save must represent a path from the basepath. it should be a relative path,preserve filename will be either true or false. when true,the file will be uploaded with it default filename else the system will pick the current user id in the session as the name of the file.
static $documentField = array(); //array containing an associative array of field that should be regareded as document field. it will contain the setting for max size and data type.;
static $relation = array('user_table' => array('user_table_id','id')
);
static $tableAction = array('delete' => 'delete/user', 'edit' => 'edit/user');
function __construct($array = array())
{
	parent::__construct($array);
}
 
function getUsernameFormField($value = ''){
	return "<div class='form-group'>
				<label for='username'>Username</label>
				<input type='text' name='username' id='username' value='$value' class='form-control' required />
			</div>";
} 
 function getPasswordFormField($value = ''){
	return "<div class='form-group'>
				<label for='password'>Password</label>
				<input type='text' name='password' id='password' value='$value' class='form-control' required />
			</div>";
} 
 function getUser_typeFormField($value = ''){
	return "<div class='form-group'>
				<label for='user_type'>User Type</label>
				<input type='text' name='user_type' id='user_type' value='$value' class='form-control' required />
			</div>";
} 
 function getUser_table_idFormField($value = ''){
	$fk = null; 
 	//change the value of this variable to array('table'=>'user_table','display'=>'user_table_name'); if you want to preload the value from the database where the display key is the name of the field to use for display in the table.[i.e the display key is a column name in the table specify in that array it means select id,'user_table_name' as value from 'user_table' meaning the display name must be a column name in the table model].It is important to note that the table key can be in this format[array('table' => array('user_table', 'another table name'))] provided that their is a relationship between these tables. The value param in the function is set to true if the form model is used for editing or updating so that the option value can be selected by default;

		if(is_null($fk)){
			return $result = "<input type='hidden' name='user_table_id' id='user_table_id' value='$value' class='form-control' />";
		}

		if(is_array($fk)){
			
			$result ="<div class='form-group'>
			<label for='user_table_id'>User Table Id</label>";
			$option = $this->loadOption($fk,$value);
			//load the value from the given table given the name of the table to load and the display field
			$result.="<select name='user_table_id' id='user_table_id' class='form-control'>
						$option
					</select>";
		}
		$result.="</div>";
		return $result;
}
 function getLast_loginFormField($value = ''){
	return "<div class='form-group'>
				<label for='last_login'>Last Login</label>
				<input type='text' name='last_login' id='last_login' value='$value' class='form-control' required />
			</div>";
} 
 function getLast_logoutFormField($value = ''){
	return "<div class='form-group'>
				<label for='last_logout'>Last Logout</label>
				<input type='text' name='last_logout' id='last_logout' value='$value' class='form-control' required />
			</div>";
}
function getFcm_tokenFormField($value = ''){
	return "<div class='form-group'>
				<label for='fcm_token'>Last Logout</label>
				<input type='text' name='fcm_token' id='fcm_token' value='$value' class='form-control' required />
			</div>";
} 
 function getDate_createdFormField($value = ''){
	return "<div class='form-group'>
				<label for='date_created'>Date Created</label>
				<input type='text' name='date_created' id='date_created' value='$value' class='form-control' required />
			</div>";
} 
 function getStatusFormField($value = ''){
	return "<div class='form-group'>
				<label for='status'>Status</label>
				<input type='text' name='status' id='status' value='$value' class='form-control' required />
			</div>";
} 

protected function getUser_table(){
	$query ='SELECT * FROM user_table WHERE id=?';
	if (!isset($this->array['ID'])) {
		return null;
	}
	$id = $this->array['ID'];
	$db = db_connect();
	$result = $db->query($query,array($id));
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	require_once('User_table.php');
	$resultObject = new \App\Entities\User_table($result[0]);
	return $resultObject;
}

//this function will return the last auto generated id of the last insert statement
public function getLastInsertId(){
	return getLastInsertId($this->db);
}

public function updatePassword($dataID=null,$password=null,$type='')
{
	if(isset($dataID,$password)){
		$password = encode_password(trim($password));
		// $password = md5(trim($password));
		$field = (is_numeric($dataID)) ? 'user_table_id' : 'username';
		$dateChange = date('Y-m-d H:i:s');
		$query = "update user set password = '$password',last_change_password='$dateChange' where $field=? and user_type=?";
		$db=db_connect();
		$db->transBegin();
		
		$param = array($dataID,$type);
		if($db->query($query,$param)){
			$db->transCommit();
			return true;
		}else{
			$db->transRollback();
			return false;
		}
	}
}

public function updateStatus($id,$email){
	if(isset($id,$email)){
		$query = "update user set status = '1' where ID = ? and username=?";
		$db=db_connect();
		$db->transBegin();
		$param = array($id,$email);
		if($this->query($query,$param)){
			$db->transCommit();
			return true;
		}else{
			$db->transRollback();
			return false;
		}
	}
}

public function find($user = null){
	if($user){
		$field = (is_numeric($user)) ? 'username' : 'username';
		$db = db_connect();
		$builder = $db->table('user');
	   $data = $builder->getWhere(array($field => $user));
	 
	   if($data->getNumRows() > 0){
	   	  $this->_data = $data->getResultArray(); // setting the data value of a user to making it public
	   	   return true;
	   }	
	}

 	return false;		
}

public function findBoth($user = null){
	if($user){
		$field = (isValidEmail($user)) ? 'username' : 'username_2';
		$db = db_connect();
		$builder = $db->table('user');
	   $data = $builder->getWhere(array($field => $user));
	   if($data->getNumRows() > 0){
	   	  $this->_data = $data->getResultArray(); // setting the data value of a user to making it public
	   	   return true;
	   }	
	}
 	return false;		
}

public function findUserProp($user = null){
	if($user){
		$field = (is_numeric($user)) ? 'ID' : 'username';
		$db= db_connect();
		$builder = $db->table('user');
	   $data = $builder->getWhere(array($field => $user));
	 
	   if($data->getNumRows() > 0){
	   	  $this->_data = $data->getResultArray(); // setting the data value of a user to making it public
	   	   return true;
	   }	
	}

 	return false;		
}

public function memberLogin(){
	// this handles the remember me function
	if($this->dataExists()){
		$userID = $this->data()->ID;
		$newUser = new User();
		$userRes = $newUser->getWhere(array('ID'=>$userID,'status'=>'1'),$count,0,1,false);
		$userRes = $userRes[0];
		$this->webSessionManager->saveCurrentUser($userRes,true);
	}
}

public function dataExists(){
   return (!empty($this->_data)) ? true : false;
}

public function data(){
 	return $this->_data;
}

public function getRealUserData(int $uid)
{
	$user = new User();
	$user->Id = $uid;
	$user->load();
	$user = $user->toArray();
	$userType = $user['user_type'];

   $moreInfo = [];
   if($userType == 'app_hirers'){
   	$userType = 'hirers';
   }
   $userType = loadClass($userType);

   $moreInfo = $userType->getWhere(array('id'=>$uid,'status'=>1),$c,0,null,false);
   if (!$moreInfo) {
      displayJson(false,'sorry an unexpected error occured');return;
   }
   return array_merge($user,$moreInfo[0]);
}




}

?>
