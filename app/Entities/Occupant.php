<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the occupant table
*/
class Occupant extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Occupant"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['title_id','email','lga_of_state','state_of_origin','date_first_app','date_present_app','staff_path'];

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
public static $displayField = 'title_id';

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
public static $typeArray = ['title_id' => 'int','occupant_num' => 'varchar','surname' => 'varchar','firstname' => 'varchar','othername' => 'varchar','email' => 'varchar','gender' => 'enum','marital_status' => 'enum','lga_of_state' => 'varchar','state_of_origin' => 'varchar','num_children' => 'int','academic_status' => 'enum','grade' => 'int','designation_id' => 'int','date_first_app' => 'date','date_present_app' => 'date','office_address' => 'varchar','phone_number' => 'varchar','hall' => 'enum','staff_path' => 'varchar','status' => 'tinyint','date_modified' => 'timestamp','date_created' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','title_id' => '','occupant_num' => '','surname' => '','firstname' => '','othername' => '','email' => '','gender' => '','marital_status' => '','lga_of_state' => '','state_of_origin' => '','num_children' => '','academic_status' => '','grade' => '','designation_id' => '','date_first_app' => '','date_present_app' => '','office_address' => '','phone_number' => '','hall' => '','staff_path' => '','status' => '','date_modified' => '','date_created' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['gender' => 'male','marital_status' => 'married','academic_status' => 'others','hall' => 'off_campus','status' => '1','date_modified' => 'current_timestamp()','date_created' => 'current_timestamp()'];

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
public static $relation = ['title' => array('title_id','id')
,'designation' => array('designation_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/occupant', 'edit' => 'edit/occupant'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getTitle_idFormField($value = ''){
	$fk = null; 
 	//change the value of this variable to array('table'=>'title','display'=>'title_name'); if you want to preload the value from the database where the display key is the name of the field to use for display in the table.[i.e the display key is a column name in the table specify in that array it means select id,'title_name' as value from 'title' meaning the display name must be a column name in the table model].It is important to note that the table key can be in this format[array('table' => array('title', 'another table name'))] provided that their is a relationship between these tables. The value param in the function is set to true if the form model is used for editing or updating so that the option value can be selected by default;

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
				<label for='occupant_num'>Occupant Num</label>
				<input type='text' name='occupant_num' id='occupant_num' value='$value' class='form-control' required />
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
				<input type='text' name='othername' id='othername' value='$value' class='form-control' required />
			</div>";
} 
public function getEmailFormField($value = ''){
	return "<div class='form-group'>
				<label for='email'>Email</label>
				<input type='text' name='email' id='email' value='$value' class='form-control' required />
			</div>";
} 
public function getGenderFormField($value = ''){
	return "<div class='form-group'>
				<label for='gender'>Gender</label>
				<input type='text' name='gender' id='gender' value='$value' class='form-control' required />
			</div>";
} 
public function getMarital_statusFormField($value = ''){
	return "<div class='form-group'>
				<label for='marital_status'>Marital Status</label>
				<input type='text' name='marital_status' id='marital_status' value='$value' class='form-control' required />
			</div>";
} 
public function getLga_of_stateFormField($value = ''){
	return "<div class='form-group'>
				<label for='lga_of_state'>Lga Of State</label>
				<input type='text' name='lga_of_state' id='lga_of_state' value='$value' class='form-control' required />
			</div>";
} 
public function getState_of_originFormField($value = ''){
	return "<div class='form-group'>
				<label for='state_of_origin'>State Of Origin</label>
				<input type='text' name='state_of_origin' id='state_of_origin' value='$value' class='form-control' required />
			</div>";
} 
public function getNum_childrenFormField($value = ''){
	return "<div class='form-group'>
				<label for='num_children'>Num Children</label>
				<input type='text' name='num_children' id='num_children' value='$value' class='form-control' required />
			</div>";
} 
public function getAcademic_statusFormField($value = ''){
	return "<div class='form-group'>
				<label for='academic_status'>Academic Status</label>
				<input type='text' name='academic_status' id='academic_status' value='$value' class='form-control' required />
			</div>";
} 
public function getGradeFormField($value = ''){
	return "<div class='form-group'>
				<label for='grade'>Grade</label>
				<input type='text' name='grade' id='grade' value='$value' class='form-control' required />
			</div>";
} 
public function getDesignation_idFormField($value = ''){
	$fk = null; 
 	//change the value of this variable to array('table'=>'designation','display'=>'designation_name'); if you want to preload the value from the database where the display key is the name of the field to use for display in the table.[i.e the display key is a column name in the table specify in that array it means select id,'designation_name' as value from 'designation' meaning the display name must be a column name in the table model].It is important to note that the table key can be in this format[array('table' => array('designation', 'another table name'))] provided that their is a relationship between these tables. The value param in the function is set to true if the form model is used for editing or updating so that the option value can be selected by default;

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
				<label for='date_first_app'>Date First App</label>
				<input type='text' name='date_first_app' id='date_first_app' value='$value' class='form-control' required />
			</div>";
} 
public function getDate_present_appFormField($value = ''){
	return "<div class='form-group'>
				<label for='date_present_app'>Date Present App</label>
				<input type='text' name='date_present_app' id='date_present_app' value='$value' class='form-control' required />
			</div>";
} 
public function getOffice_addressFormField($value = ''){
	return "<div class='form-group'>
				<label for='office_address'>Office Address</label>
				<input type='text' name='office_address' id='office_address' value='$value' class='form-control' required />
			</div>";
} 
public function getPhone_numberFormField($value = ''){
	return "<div class='form-group'>
				<label for='phone_number'>Phone Number</label>
				<input type='text' name='phone_number' id='phone_number' value='$value' class='form-control' required />
			</div>";
} 
public function getHallFormField($value = ''){
	return "<div class='form-group'>
				<label for='hall'>Hall</label>
				<input type='text' name='hall' id='hall' value='$value' class='form-control' required />
			</div>";
} 
public function getStaff_pathFormField($value = ''){
	return "<div class='form-group'>
				<label for='staff_path'>Staff Path</label>
				<input type='text' name='staff_path' id='staff_path' value='$value' class='form-control' required />
			</div>";
} 
public function getStatusFormField($value = ''){
	return "<div class='form-group'>
				<label for='status'>Status</label>
				<input type='text' name='status' id='status' value='$value' class='form-control' required />
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

protected function getTitle(){
	$query = 'SELECT * FROM title WHERE id=?';
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
	$resultObject = new \App\Entities\Title($result[0]);
	return $resultObject;
}

protected function getDesignation(){
	$query = 'SELECT * FROM designation WHERE id=?';
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
	$resultObject = new \App\Entities\Designation($result[0]);
	return $resultObject;
}


 
}

?>
