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
public static $nullArray = ['dob','address','applicant_status'];

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
public static $typeArray = ['staff_id' => 'int','category_id' => 'int','dob' => 'date','gender' => 'enum','address' => 'text','applicant_status' => 'enum','date_modified' => 'timestamp','date_created' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','staff_id' => '','category_id' => '','dob' => '','gender' => '','address' => '','applicant_status' => '','date_modified' => '','date_created' => ''];

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
 
public function getStaff_idFormField($value = ''){
	$fk = null; 
 	//change the value of this variable to array('table'=>'staff','display'=>'staff_name'); if you want to preload the value from the database where the display key is the name of the field to use for display in the table.[i.e the display key is a column name in the table specify in that array it means select id,'staff_name' as value from 'staff' meaning the display name must be a column name in the table model].It is important to note that the table key can be in this format[array('table' => array('staff', 'another table name'))] provided that their is a relationship between these tables. The value param in the function is set to true if the form model is used for editing or updating so that the option value can be selected by default;

		if(is_null($fk)){
			return $result = "<input type='hidden' name='staff_id' id='staff_id' value='$value' class='form-control' />";
		}

		if(is_array($fk)){
			
			$result ="<div class='form-group'>
			<label for='staff_id'>Staff</label>";
			$option = $this->loadOption($fk,$value);
			//load the value from the given table given the name of the table to load and the display field
			$result.="<select name='staff_id' id='staff_id' class='form-control'>
						$option
					</select>";
					$result.="</div>";
		return $result;
		}
		
}
public function getCategory_idFormField($value = ''){
	$fk = null; 
 	//change the value of this variable to array('table'=>'category','display'=>'category_name'); if you want to preload the value from the database where the display key is the name of the field to use for display in the table.[i.e the display key is a column name in the table specify in that array it means select id,'category_name' as value from 'category' meaning the display name must be a column name in the table model].It is important to note that the table key can be in this format[array('table' => array('category', 'another table name'))] provided that their is a relationship between these tables. The value param in the function is set to true if the form model is used for editing or updating so that the option value can be selected by default;

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
				<input type='text' name='dob' id='dob' value='$value' class='form-control' required />
			</div>";
} 
public function getGenderFormField($value = ''){
	return "<div class='form-group'>
				<label for='gender'>Gender</label>
				<input type='text' name='gender' id='gender' value='$value' class='form-control' required />
			</div>";
} 
public function getAddressFormField($value = ''){
	return "<div class='form-group'>
				<label for='address'>Address</label>
				<input type='text' name='address' id='address' value='$value' class='form-control' required />
			</div>";
} 
public function getApplicant_statusFormField($value = ''){
	return "<div class='form-group'>
				<label for='applicant_status'>Applicant Status</label>
				<input type='text' name='applicant_status' id='applicant_status' value='$value' class='form-control' required />
			</div>";
} 
public function getDate_modifiedFormField($value = ''){
	return "<div class='form-group'>
				<label for='date_modified'>Date Modified</label>
				<input type='text' name='date_modified' id='date_modified' value='$value' class='form-control' required />
			</div>";
} 
public function getDate_createdFormField($value = ''){
	return "<div class='form-group'>
				<label for='date_created'>Date Created</label>
				<input type='text' name='date_created' id='date_created' value='$value' class='form-control' required />
			</div>";
} 

protected function getStaff(){
	$query = 'SELECT * FROM staff WHERE id=?';
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
	$resultObject = new \App\Entities\Staff($result[0]);
	return $resultObject;
}

protected function getCategory(){
	$query = 'SELECT * FROM category WHERE id=?';
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
	$resultObject = new \App\Entities\Category($result[0]);
	return $resultObject;
}


 
}

?>
