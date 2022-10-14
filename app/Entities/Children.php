<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the children table
*/
class Children extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Children"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['date_created','date_modified','status'];

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
public static $displayField = 'fullname';

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
public static $typeArray = ['staff_id' => 'int','fullname' => 'varchar','gender' => 'enum','birthdate' => 'date','status' => 'tinyint','date_modified' => 'timestamp','date_created' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','staff_id' => '','fullname' => '','gender' => '','birthdate' => '','status' => '','date_modified' => '','date_created' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['gender' => 'male','status' => '1','date_modified' => 'current_timestamp()','date_created' => 'current_timestamp()'];

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
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/children', 'edit' => 'edit/children'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getStaff_idFormField($value = ''){
	return getStaffOption($value);
		
}
public function getFullnameFormField($value = ''){
	return "<div class='form-group'>
				<label for='fullname'>Fullname</label>
				<input type='text' name='fullname' id='fullname' value='$value' class='form-control' required />
			</div>";
} 
public function getGenderFormField($value = ''){
	$arr =array('male'=>'Male','female'=>'Female');
       $option = buildOptionUnassoc2($arr,$value);
       return "<div class='form-group'>
       		<label for='gender' >Gender</label>
              <select name='gender' id='gender' class='form-control'>
              $option
              </select>
</div>";
} 
public function getBirthdateFormField($value = ''){
	return "<div class='form-group'>
				<label for='birthdate'>Birth Date</label>
				<input type='date' name='birthdate' id='birthdate' value='$value' class='form-control' required />
			</div>";
} 
public function getStatusFormField($value = ''){
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


 
}

?>
