<?php

namespace App\Entities;

use App\Models\Crud;

class Role extends Crud
{
	protected static $tablename='Role';
	/* this array contains the field that can be null*/
	static $nullArray=array('status');
	static $compositePrimaryKey=[];
	static $uploadDependency = [];
	/*this array contains the fields that are unique*/
	static $displayField = 'role_title';
	static $uniqueArray=array('role_title' );
	/*this is an associative array containing the fieldname and the type of the field*/
	static $typeArray = array('role_title'=>'varchar','status'=>'tinyint');
	/*this is a dictionary that map a field name with the label name that will be shown in a form*/
	static $labelArray=array('ID'=>'','role_title'=>'','status'=>'');
	/*associative array of fields that have default value*/
	static $defaultArray = array('status'=>'1');
	//populate this array with fields that are meant to be disphpssociative array of field that should be regareded as document field. it will contain the setting for max size and data type.
	static $documentField = []; //array containing an associative array of field that should be regareded as document field. it will contain the setting for max size and data type.;
	static $relation=array('admin'=>array(array( 'ID', 'role_id', 1))
	,'permission'=>array(array( 'ID', 'role_id', 1))
	);
	static $tableAction=array('permissions'=>'vc/admin/permission','enable'=>'getEnabled','edit'=>'edit/role','delete'=>'delete/role');
	function __construct($array=array())
	{
		parent::__construct($array);
		$this->createSuperUser();
	}
	function getRole_titleFormField($value=''){
		return "<div class='form-group'>
		<label for='role_title' >Role Title</label>
			<input type='text' name='role_title' id='role_title' value='$value' class='form-control' required />
	</div> ";

	}
	function getStatusFormField($value=''){
		return "<div class='form-group'>
		<label class='form-checkbox'>Status</label>
		<select class='form-control' id='status' name='status' >
			<option value='1'>Yes</option>
			<option value='0' selected='selected'>No</option>
		</select>
		</div> ";

	}

	public function delete($id=null,&$db=null)
	{
		if ($id==null) {
			$id=$this->ID;
		}
		if ($id==1) {
			return false;
		}
		return parent::delete($id,$db);
	}	
	protected function getAdmin(){
		$query ='SELECT * FROM admin WHERE role_id=?';
		$id = $this->array['ID'];
		$db = db_connect();
		$result = $db->query($query,array($id));
		$result =$result->getResultArray();
		if (empty($result)) {
			return false;
		}
		$resultobjects = array();
		foreach ($result as  $value) {
			$resultObjects[] = new \App\Entities\Admin($value);
		}

		return $resultObjects;
	}
		
	protected function getPermission(){
		$query ='SELECT * FROM permission WHERE role_id=?';
		$id = $this->array['ID'];
		$db = db_connect();
		$result = $db->query($query,array($id));
		$result =$result->getResultArray();
		if (empty($result)) {
			return false;
		}
		$resultobjects = array();
		foreach ($result as  $value) {
			$resultObjects[] = new \App\Entities\Permission($value);
		}

		return $resultObjects;
	}
	public function getPermissionArray()
	{
		$query = "select * from permission where role_id=?";
		$result = $this->query($query,array($this->ID));
		$toReturn = array();
		if (!$result) {
			return array();
		}
		foreach ($result as $res) {
			$toReturn[$res['path']]=$res['permission'];
		}
		return $toReturn;
	}

	public function processPermission($update,$remove)
	{
		$db = db_connect();
		$id= $db->escape($this->ID);
		$removeQuery=$this->buildRemoveQuery($remove,$id);
		$updateQuery = $this->buildUpdateQuery($update,$id);
		$db->transBegin();
		if ($remove) {
			if (!$db->query($removeQuery)) {
				$db->transRollback();
				return false;
			}
		}
		if ($updateQuery) {
			if (!$db->query($updateQuery)) {
				$db->transRollback();
				return false;
			}
		}
		$db->transCommit();
		return true;
	}

	private function buildUpdateQuery($update,$id)
	{
		$query="insert into permission(role_id,path,permission) values ";
		$additional='';
		$db = db_connect();
		foreach ($update as $value) {
			$path = $db->escape($value['path']);
			$permission = $db->escape($value['permission']);
			$additional.=$additional?",($id,$path,$permission)":"($id,$path,$permission)";
		}
		if (!$additional) {
			return false;
		}
		return $query.$additional.' on duplicate key update permission=values(permission) ';
	}

	private function buildRemoveQuery($remove,$id)
	{
		$content = implode(',', $remove);
		if ($content) {
			$content=str_replace(',', "','", $content);
			$content = "'$content'";
		}
		$result="delete from permission where path in ($content) and role_ID={$this->ID}";
		return $result;
	}

	public function canView($path)
	{
		$db = db_connect();
		$path = $db->escape($path);
		$query = "select * from permission where role_id=? and $path like concat('%',path,'%')";
		$result = $this->query($query,[$this->ID]);
		return $result;
	}

	public function canWrite($path)
	{
		$db = db_connect();
		$path = $db->escape($path);
		$query = "select * from permission where role_id=? and $path like concat('%',path,'%') and permission='w'";
		$result = $this->query($query,[$this->ID]);
		return $result;
	}

	public function checkWritePermission(){
		$admin = loadClass('admin');
		$webSessionManager = new \App\Models\WebSessionManager;
		$admin->ID = $webSessionManager->getCurrentUserProp('user_table_id');
		$admin->load();
		$role = $admin->role;
		//get the page referer and use it as the
		$path = @$_SERVER['HTTP_REFERER'];
		$path = $this->extractBase($path);
		if (!$role->canWrite($path)) {
		  echo createJsonMessage('status',false,'message','sorry,you do not have permission to perform operation');exit;
		}
	}

	private function extractBase($path)
	{
		$base =base_url();
		$ind = strpos($path, $base);
		if ($ind===false) {
			return false;
		}
		$result = substr($path, $ind+strlen($base));
		return $result;
	}


	public function createSuperUser()
	{
		$db = db_connect();
		$db->transBegin();
		$query="insert into role(ID,role_title) values(1,'superadmin') on duplicate key update role_title=values(role_title)";
		if ($this->query($query)) {
			$modules = $this->getModules();
			$q="insert into permission(role_id,path,permission) values(?,?,?) on duplicate key update permission=values(permission)";
			$role_id=1;
			foreach ($modules as $val) {
				foreach ($val['children'] as $child) {
					if(is_array($child)){
						foreach($child as $childValue){
							if(!$this->query($q,array($role_id,$childValue,'w'))){
								$db->transRollback();
								return false;
							}
						}
					}else{
						if (!$this->query($q,array($role_id,$child,'w'))) {
							$db->transRollback();
							return false;
						}
					}
				}
			}
			$db->transCommit();
			return true;
		}
		else{
			$db->transRollback();
			return false;
		}
	}

	public function getModules(){
		$result=array(
			'Manage Occupants' => array(
				'class' => 'building-house',
				'children' => array(
					'Occupant Department' => 'vc/create/occupant_department',
					'Applicant Allocation' => 'vc/create/applicant_allocation',
					'Allocation' => 'vc/create/allocation',
				)
			),
			'Users Management'=>array(
				'class'=>'bx-user-plus',
				'children'=>array(
					'Manage Occupant'=>'vc/create/occupant',
					'Manage Children'=>'vc/create/children',
					'Manage Tenant'=>'vc/create/boy_quarters',
					'Upload Users' => 'vc/create/users',
				)
			),
			'Admin Management'=>array(
				'class'=>'bx-user',
				'children'=>array(
					'Manage Admin (s)'=>'vc/create/admin',
					'Role'=>'vc/create/role',
				)
			),
			'App Setting' => array(
				'class' => 'bx-dock-top',
				'children' => array(
					'Title' => 'vc/create/title',
					'Department' => 'vc/create/departments',
					'Category' => 'vc/create/category',
					'Designation' => 'vc/create/designation'
				)
			),

		);
		return $result;
	}

	public function getExtraModules(){
		$result = array(
			'Extra Section' => array(
				'class' => 'bx-layout',
				'children' => array(

				)
			)
		);
		return $result;
	}

}
?>