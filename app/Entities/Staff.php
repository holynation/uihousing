<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the staff table
*/
class Staff extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Staff"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['title_id','email','gender','marital_status','lga_of_state','state_of_origin','num_children','grade','date_first_app','date_present_app','office_address','phone_number','hall','staff_path','date_created','date_modified'];

/** 
* This are fields that must be unique across a row in a table.
* Similar to composite primary key in sql(oracle,mysql)
* @var array
*/
public static $compositePrimaryKey = [];

/** 
* This is to provided an array of fields that can be used for building a
* template header for batch upload using csv format
* @var array
*/
public static $uploadDependency = [];

/** 
* If there is a relationship between this table and another table, this display field properties is used as a column in the query.
* A field in the other table that displays the connection between this name and this table's name,something along these lines
* table_id. We cannot use a name similar to table id in the table that is displayed to the user, so the display field is used in
* place of it. To ensure that the other model queries use that field name as a column to be fetched with the query rather than the
* table id alone, the display field name provided must be a column in the table to replace the table id shown to the user.
* @var array|string
*/
public static $displayField = ['surname','firstname'];

/** 
* This array contains the fields that are unique
* @var array
*/
public static $uniqueArray = ['occupant_num','email'];

/** 
* This is an associative array containing the fieldname and the datatype
* of the field
* @var array
*/
public static $typeArray = ['occupant_num' => 'varchar','title_id' => 'int','surname' => 'varchar','firstname' => 'varchar','othername' => 'varchar','email' => 'varchar','gender' => 'enum','marital_status' => 'enum','state_of_origin' => 'varchar','lga_of_origin' => 'varchar','num_children' => 'int','academic_status' => 'enum','grade' => 'int','designation_id' => 'int','date_first_app' => 'date','date_present_app' => 'date','office_address' => 'varchar','phone_number' => 'varchar','hall' => 'enum','staff_path' => 'varchar','status' => 'tinyint','date_modified' => 'timestamp','date_created' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','title_id' => '','occupant_num' => '','surname' => '','firstname' => '','othername' => '','email' => '','gender' => '','marital_status' => '','lga_of_origin' => '','state_of_origin' => '','num_children' => '','academic_status' => '','grade' => '','designation_id' => '','date_first_app' => '','date_present_app' => '','office_address' => '','phone_number' => '','hall' => '','staff_path' => '','status' => '','date_modified' => '','date_created' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['academic_status' => 'others','status' => '1','date_modified' => 'current_timestamp()','date_created' => 'current_timestamp()'];

/** 
*  This is an array containing an associative array of field that should be regareded as document field.
* it will contain the setting for max size and data type. Example: populate this array with fields that
* are meant to be displayed as document in the format
* array('fieldname'=>array('type'=>array('jpeg','jpg','png','gif'),'size'=>'1048576','directory'=>'directoryName/','preserve'=>false,'max_width'=>'1000','max_height'=>'500')).
* the folder to save must represent a path from the basepath. it should be a relative path,preserve
* filename will be either true or false. when true,the file will be uploaded with it default filename
* else the system will pick the current user id in the session as the name of the file 
* @var array
*/
public static $documentField = ['staff_path'=>['type'=>['jpeg','jpg','png'],'size'=>'819200','directory'=>'staff/','preserve'=>false]]; 

/** 
* This is an associative array of fields showing relationship between
* entities
* @var array
*/
public static $relation = ['title' => array('title_id','id')
,'designation' => array('designation_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/staff', 'edit' => 'edit/staff'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getTitle_idFormField($value = ''){
	$fk = array('table'=>'title','display'=>'name'); 

		if(is_null($fk)){
			return $result = "<input type='hidden' name='title_id' id='title_id' value='$value' class='form-control' />";
		}

		if(is_array($fk)){
			
			$result ="<div class='form-group'>
			<label for='title_id'>Title</label>";
			$option = $this->loadOption($fk,$value);
			//load the value from the given table given the name of the table to load and the display field
			$result.="<select name='title_id' id='title_id' class='form-control'>
						$option
					</select>";
					$result.="</div>";
		return $result;
		}
		
}
public function getOccupant_numFormField($value = ''){
	return "<div class='form-group'>
				<label for='occupant_num'>Staff Number</label>
				<input type='text' name='occupant_num' id='occupant_num' value='$value' class='form-control' required readonly />
			</div>";
} 
public function getSurnameFormField($value = ''){
	return "<div class='form-group'>
				<label for='surname'>Surname</label>
				<input type='text' name='surname' id='surname' value='$value' class='form-control' required />
			</div>";
} 
public function getFirstnameFormField($value = ''){
	return "<div class='form-group'>
				<label for='firstname'>Firstname</label>
				<input type='text' name='firstname' id='firstname' value='$value' class='form-control' required />
			</div>";
} 
public function getOthernameFormField($value = ''){
	return "<div class='form-group'>
				<label for='othername'>Othername</label>
				<input type='text' name='othername' id='othername' value='$value' class='form-control' />
			</div>";
} 
public function getEmailFormField($value = ''){
	return "<div class='form-group'>
				<label for='email'>Email</label>
				<input type='text' name='email' id='email' value='$value' class='form-control'  />
			</div>";
} 
public function getGenderFormField($value = ''){
	$arr =array('male','female');
       $option = buildOptionUnassoc($arr,$value);
       return "<div class='form-group'>
       		<label for='gender' >Gender</label>
              <select name='gender' id='gender' class='form-control'>
              $option
              </select>
</div>";
} 
public function getMarital_statusFormField($value = ''){
	$arr =array('single','married','others');
	$option = buildOptionUnassoc($arr,$value);
	return "<div class='form-group'>
	<label for='marital_status' >Marital Status</label>
		<select  name='marital_status' id='marital_status'  class='form-control'  >
		$option
		</select>
</div> ";
} 
function getState_of_originFormField($value=''){
	$states = loadStates();
	$option = buildOptionUnassoc($states,$value);
	return "<div class='form-group'>
	<label for='state_of_origin' >State Of Origin</label>
		<select  name='state_of_origin' id='state_of_origin' value='$value' class='form-control autoload' data-child='lga_of_origin' data-load='lga'> 
		<option value=''>..select state..</option>
		$option
		</select>
</div> ";

}
function getLga_of_originFormField($value=''){
	$option='';
	if ($value) {
		$arr=array($value);
		$option = buildOptionUnassoc($arr,$value);
	}
	return "<div class='form-group'>
	<label for='lga_of_origin' >Lga Of Origin</label>
		<select type='text' name='lga_of_origin' id='lga_of_origin' value='$value' class='form-control'  >
		<option value=''></option>
		$option
		</select>
</div> ";

}
public function getNum_childrenFormField($value = ''){
	return "<div class='form-group'>
				<label for='num_children'>Number of Children</label>
				<input type='text' name='num_children' id='num_children' value='$value' class='form-control' />
			</div>";
} 
public function getAcademic_statusFormField($value = ''){
	$arr = array('student','academic','non_teaching','others');
	$option = buildOptionUnassoc($arr,$value);
	return "<div class='form-group'>
	<label for='academic_status' >Academic Status</label>
		<select  name='academic_status' id='academic_status'  class='form-control'  >
		$option
		</select>
</div> ";
} 
public function getGradeFormField($value = ''){
	return "<div class='form-group'>
				<label for='grade'>Grade</label>
				<input type='text' name='grade' id='grade' value='$value' class='form-control' required />
			</div>";
} 
public function getDesignation_idFormField($value = ''){
	$fk = array('table'=>'designation','display'=>'designation_name'); 

		if(is_null($fk)){
			return $result = "<input type='hidden' name='designation_id' id='designation_id' value='$value' class='form-control' />";
		}

		if(is_array($fk)){
			
			$result ="<div class='form-group'>
			<label for='designation_id'>Designation</label>";
			$option = $this->loadOption($fk,$value);
			//load the value from the given table given the name of the table to load and the display field
			$result.="<select name='designation_id' id='designation_id' class='form-control'>
						$option
					</select>";
					$result.="</div>";
		return $result;
		}
		
}
public function getDate_first_appFormField($value = ''){
	return "<div class='form-group'>
				<label for='date_first_app'>Date of First Appointment</label>
				<input type='date' name='date_first_app' id='date_first_app' value='$value' class='form-control' />
			</div>";
} 
public function getDate_present_appFormField($value = ''){
	return "<div class='form-group'>
				<label for='date_present_app'>Date Present Appointment</label>
				<input type='date' name='date_present_app' id='date_present_app' value='$value' class='form-control' />
			</div>";
} 
public function getOffice_addressFormField($value = ''){
	return "<div class='form-group'>
				<label for='office_address'>Office Address</label>
				<input type='text' name='office_address' id='office_address' value='$value' class='form-control' />
			</div>";
} 
public function getPhone_numberFormField($value = ''){
	return "<div class='form-group'>
				<label for='phone_number'>Phone Number</label>
				<input type='text' name='phone_number' id='phone_number' value='$value' class='form-control' />
			</div>";
} 
public function getHallFormField($value = ''){
	$arr =array('campus','off_campus');
	$option = buildOptionUnassoc($arr,$value);
	return "<div class='form-group'>
	<label for='hall'>Hall</label>
		<select name='hall' id='hall' class='form-control'  >
		$option
		</select>
</div> ";
} 
public function getStaff_pathFormField($value = ''){
	$path =  ($value != '') ? $value : "";
       return "<div class='row'>
                <div class='col-lg-8'>
                    <div class='form-group'>
                    <label for='staff_path' class='form-label'>Staff Profile</label>
                <input type='file' class='form-control' name='staff_path' id='staff_path' />
                <span class='form-text text-muted'>Max File size is 800KB. Supported formats: <code> jpeg,jpg,png</code></span></div></div>
                <div class='col-sm-4'><img src='$path' alt='staff profile' class='img-responsive' width='30%'/></div>
            </div><br>";
} 
public function getStatusFormField($value = ''){
	return "<div class='form-group'>
				<label for='status'>Status</label>
				<input type='text' name='status' id='status' value='$value' class='form-control' required />
			</div>";
} 
public function getDate_modifiedFormField($value = ''){
	return "";
} 
public function getDate_createdFormField($value = ''){
	return "";
} 

protected function getTitle(){
	$query = 'SELECT * FROM title WHERE id=?';
	if (!isset($this->array['title_id'])) {
		return null;
	}
	$id = $this->array['title_id'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\Title($result[0]);
	return $resultObject;
}

protected function getDesignation(){
	$query = 'SELECT * FROM designation WHERE id=?';
	if (!isset($this->array['designation_id'])) {
		return null;
	}
	$id = $this->array['designation_id'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\Designation($result[0]);
	return $resultObject;
}

protected function getStaff_department(){
	$query = 'SELECT * FROM staff_department WHERE staff_id=?';
	if (!isset($this->array['ID'])) {
		return null;
	}
	$id = $this->array['ID'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\Staff_department($result[0]);
	return $resultObject;
}

public function delete($id=null,&$db=null)
{  
    $db = $db ?? $this->db;
    $db->transBegin();
    if(parent::delete($id,$db)){
        $query="delete from user where user_table_id=? and user_type='staff'";
        if($this->query($query,array($id))){
        	if(!$this->removeModelImage($db,'staff','ID',$id)){
        		// this would mean it doesn't exists
        	}
            $db->transCommit();
            return true;
        }
        else{
            $db->transRollback();
            return false;
        }
    }
    else{
        $db->transRollback();
        return false;
    }
}

/**
 * This is to remove any media files related to the model hirers
 * 
 * @param  object $db        [description]
 * @param  string $modelName [description]
 * @param  string $fieldName [description]
 * @param  int    $id        [description]
 * @return [type]            [description]
 */
private function removeModelImage(object $db,string $modelName,string $fieldName,int $id){
	$result = $db->table($modelName)->getWhere([$fieldName=>$id]);
	if($result->getNumRows() > 0){
		$modelPath = $modelName."_path";
		$result = $result->getResultArray()[0][$modelPath];
		removeSymlinkWithImage($result);
		return true;
	}
	return false;
}

public function getStaffOption($value){
	$value = ($value != "") ? $value : "";
	$disable = ($value != '') ? "disabled" : ""; // this means edit function has passed down the value
	$where = ($value != '') ? " where ID= '$value' " : " where status = '1'";
	$db = db_connect();
	$query = "select id,concat(firstname,' ',surname,' ',othername) as value from staff $where order by value asc";
	$result ="<div class='form-group'>
		<label for='staff_id'>Staff Name</label>";
		$option = buildOptionFromQuery($db,$query,null,$value);
		//load the value from the given table given the name of the table to load and the display field
		$result.="<select name='staff_id' id='staff_id' class='form-control select' required>
					$option
				</select>";
		
	$result.="</div>";
	return $result;	
}

/**
 * Using this for admin table view based on the type to list out
 * This is to get data for manage equipments
 * @param  int    $id   [description]
 * @param  string $type [description]
 * @return [type]       [description]
 */
public function viewList(int $id=null, ?string $type,int $limit=20000,bool $runQuery=false){
	$query = null;
	$param = null;
	$whereClause = null;

	if($runQuery){
		$whereClause = ($id != null) ? " where a.ID = '$id'" : "";
		$query = "SELECT occupant_num as staff_number,b.name as title,concat(firstname,' ',surname,' ',othername) as fullname,email,phone_number,upper(gender) gender,marital_status,num_children as number_of_children,c.designation_name,academic_status,grade,date_first_app as date_of_first_appointment,date_present_app as date_of_present_appointment,hall,staff_path,if(a.status, 'Active', 'Inactive') status,a.date_created,a.date_modified from staff a left join title b on b.id = a.title_id left join designation c on c.id = a.designation_id $whereClause order by a.ID desc limit $limit";
		$result = $this->query($query);
		return (!empty($result)) ? $result[0] : false;
	}

	$query = "SELECT staff.ID,occupant_num as staff_number,concat(firstname,' ',surname,' ',othername) as fullname,email,phone_number,gender,marital_status,staff.status from staff order by staff.ID desc limit $limit";
		return $query;
}


 
}

?>
