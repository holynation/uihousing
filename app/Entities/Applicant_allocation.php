<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the applicant_allocation table
*/
class Applicant_allocation extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Applicant_allocation"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['dob','address','present_accommodation','applicant_status','date_modified','date_created'];

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
public static $displayField = 'dob';

/** 
* This array contains the fields that are unique
* @var array
*/
public static $uniqueArray = [];

/** 
* This is an associative array containing the fieldname and the datatype
* of the field
* @var array
*/
public static $typeArray = ['applicant_code' => 'varchar','staff_id' => 'int','category_id' => 'int','dob' => 'date','gender' => 'enum','address' => 'text','marriage' => 'enum','departments_id' => 'int','present_accommodation' => 'int','hall_location' => 'enum','applicant_status' => 'enum','date_modified' => 'timestamp','date_created' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','applicant_code' => '','staff_id' => '','category_id' => '','dob' => '','gender' => '','address' => '','marriage' => '','departments_id' => '','present_accommodation' => '','hall_location' => '','applicant_status' => '','date_modified' => '','date_created' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['applicant_status' => 'pending','date_modified' => 'current_timestamp()','date_created' => 'current_timestamp()'];

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
public static $documentField = []; 

/** 
* This is an associative array of fields showing relationship between
* entities
* @var array
*/
public static $relation = ['staff' => array('staff_id','id')
,'category' => array('category_id','id')
,'present_accommodation' => array('present_accommodation_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/applicant_allocation', 'edit' => 'edit/applicant_allocation'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getApplicant_codeFormField($value = ''){
	$value = ($value) ? $value : $this->generateApplicantCode();
	return "<div class='form-group'>
				<label for='applicant_code'>Applicant Code</label>
				<input type='text' name='applicant_code' id='applicant_code' value='$value' class='form-control' required readonly />
			</div>";
} 
public function getStaff_idFormField($value = ''){
	return getStaffOption($value);		
}
public function getCategory_idFormField($value = ''){
	$fk = ['table'=>'category','display'=>'category_name'];

		if(is_null($fk)){
			return $result = "<input type='hidden' name='category_id' id='category_id' value='$value' class='form-control' />";
		}

		if(is_array($fk)){
			
			$result ="<div class='form-group'>
			<label for='category_id'>Category</label>";
			$option = $this->loadOption($fk,$value);
			//load the value from the given table given the name of the table to load and the display field
			$result.="<select name='category_id' id='category_id' class='form-control'>
						$option
					</select>";
					$result.="</div>";
		return $result;
		}
		
}
public function getDobFormField($value = ''){
	return "<div class='form-group'>
				<label for='dob'>Dob</label>
				<input type='date' name='dob' id='dob' value='$value' class='form-control' required />
			</div>";
} 
public function getGenderFormField($value = ''){
	$arr =array('male'=>'Male','female'=>'Female');
       $option = buildOptionUnassoc2($arr,$value);
       return "<div class='form-group'>
       		<label for='gender'>Gender</label>
              <select name='gender' id='gender' class='form-control' required>
              $option
              </select>
</div>";
} 
public function getAddressFormField($value = ''){
	return "<div class='form-group'>
				<label for='address'>Enter your address</label>
				<textarea name='address' id='address' class='form-control' required>$value</textarea>
			</div>";
} 
public function getMarriageFormField($value = ''){
	$arr =array('single'=>'Single','married'=>'Married','others'=>'Others');
       $option = buildOptionUnassoc2($arr,$value);
       return "<div class='form-group'>
       		<label for='marriage'>Marriage Status</label>
              <select name='marriage' id='marriage' class='form-control' required>
              $option
              </select>
</div>";
} 
public function getDepartments_idFormField($value = ''){
	$fk = ['table'=>'departments','display'=>'name'];

		if(is_null($fk)){
			return $result = "<input type='hidden' name='department_id' id='department_id' value='$value' class='form-control' />";
		}

		if(is_array($fk)){
			
			$result ="<div class='form-group'>
			<label for='departments_id'>Department</label>";
			$option = $this->loadOption($fk,$value);
			//load the value from the given table given the name of the table to load and the display field
			$result.="<select name='departments_id' id='departments_id' class='form-control'>
						$option
					</select>";
					$result.="</div>";
		return $result;
		}
} 
public function getPresent_accommodationFormField($value = ''){
	$option = buildOptionFromQuery($this->db,"SELECT id,category_name as value from category order by value asc",null,$value,'chooose present accommodation');
	$result ="<div class='form-group'>
	<label for='present_accommodation_id'>Present Accommodation (If applicable)</label>";
	//load the value from the given table given the name of the table to load and the display field
	$result.="<select name='present_accommodation' id='present_accommodation' class='form-control'>
				$option
			</select>";
			$result.="</div>";
return $result;
		
}
public function getHall_locationFormField($value = ''){
	$arr =array('campus' => 'Campus','off_campus'=>'Off Campus');
	$option = buildOptionUnassoc2($arr,$value);
	return "<div class='form-group'>
	<label for='hall_location'>Hall Location</label>
		<select name='hall_location' id='hall_location' class='form-control'  >
		$option
		</select>
</div> ";
} 
public function getApplicant_statusFormField($value = ''){
	return "";
} 
public function getDate_modifiedFormField($value = ''){
	return "";
} 
public function getDate_createdFormField($value = ''){
	return "";
} 

protected function getStaff(){
	$query = 'SELECT * FROM staff WHERE id=?';
	if (!isset($this->array['staff_id'])) {
		return null;
	}
	$id = $this->array['staff_id'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\Staff($result[0]);
	return $resultObject;
}

protected function getCategory(){
	$query = 'SELECT * FROM category WHERE id=?';
	if (!isset($this->array['category_id'])) {
		return null;
	}
	$id = $this->array['category_id'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\Category($result[0]);
	return $resultObject;
}

protected function getPresent_accommodation(){
	$query = 'SELECT * FROM category WHERE id=?';
	if (!isset($this->array['present_accommodation_id'])) {
		return null;
	}
	$id = $this->array['present_accommodation_id'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\Category($result[0]);
	return $resultObject;
}

private function generateApplicantCode(){
	$orderStart = '100000011';
	$query = "select applicant_code as code from applicant_allocation order by ID desc limit 1";
	$result = $this->query($query);
	if($result && $result[0]['code']){
		[$label,$temp] = explode('UIH',$result[0]['code']);
		$orderStart = ($temp) ? $temp+1 : $orderStart;
	}
	return 'UIH'.$orderStart;
}

public static function init()
{
	return new Applicant_allocation;
}

public function getApplicantDistrix()
{
    $query = "select date_format(cast(a.date_created as date), '%M') as application_date,round(count(a.staff_id)) as total from applicant_allocation a where year(a.date_created) = year(curdate()) and a.applicant_status = 'pending' group by month(a.date_created)";
    $result = $this->query($query);
    return $result ? $result : [['application_date'=>'None', 'total' => 0]];
}

public function getGenderDistrix(){
	$query = "SELECT upper(gender) as label, count(gender) as value from applicant_allocation where year(date_created) = year(curdate()) group by gender order by label asc";
	$result = $this->query($query);
	return $result;
}

public function getStaffStatusDistrix(){
	$query = "SELECT upper(academic_status) as label, count(academic_status) as value from staff where year(date_created) = year(curdate()) group by academic_status order by label asc";
	$result = $this->query($query);
	return $result;
}


 
}

?>
