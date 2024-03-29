<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the allocation table
*/
class Allocation extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Allocation"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['date_created','date_modified'];

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
public static $displayField = 'email';

/** 
* This array contains the fields that are unique
* @var array
*/
public static $uniqueArray = ['applicant_allocation_id'];

/** 
* This is an associative array containing the fieldname and the datatype
* of the field
* @var array
*/
public static $typeArray = ['applicant_allocation_id' => 'int','category_location_id' => 'int','allocation_status' => 'enum','date_modified' => 'timestamp','date_created' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','applicant_allocation_id' => '','category_location_id'=>'','allocation_status' => '','date_modified' => '','date_created' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['allocation_status' => 'pending','date_modified' => 'current_timestamp()','date_created' => 'current_timestamp()'];

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
public static $relation = ['applicant_allocation' => array('applicant_allocation_id','id')
,'category' => array('category_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/allocation', 'edit' => 'edit/allocation'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getApplicant_allocation_idFormField($value = ''){
	$fk = null; 
 	//change the value of this variable to array('table'=>'staff','display'=>'staff_name');

		if(is_null($fk)){
			return $result = "<input type='hidden' name='applicant_allocation_id' id='applicant_allocation_id' value='$value' class='form-control' />";
		}

		if(is_array($fk)){
			
			$result ="<div class='form-group'>
			<label for='applicant_allocation_id'>Applicant</label>";
			$option = $this->loadOption($fk,$value);
			//load the value from the given table given the name of the table to load and the display field
			$result.="<select name='applicant_allocation_id' id='applicant_allocation_id' class='form-control'>
						$option
					</select>";
					$result.="</div>";
		return $result;
		}
		
}
public function getCategory_location_idFormField($value = ''){
	$query = "SELECT id,concat(address,' (', zone_type ,')') as value from category_location where status = '1' order by value asc";
	$option = buildOptionFromQuery($this->db,$query,null,$value,'chooose location');
	$result ="<div class='form-group'>
	<label for='category_location_id'>Address Location</label>";
	//load the value from the given table given the name of the table to load and the display field
	$result.="<select name='category_location_id' id='category_location_id' class='form-control'>
				$option
			</select>";
			$result.="</div>";
return $result;
		
}
public function getAllocation_statusFormField($value = ''){
	$arr =array('rejected' => 'Rejected','approved'=>'Approved');
	$option = buildOptionUnassoc2($arr,$value);
	return "<div class='form-group mt-4'>
	<label for='allocation_status'>Allocation Status</label>
		<select name='allocation_status' id='allocation_status' class='form-control'  >
		$option
		</select>
	</div> ";
} 
public function getDate_modifiedFormField($value = ''){
	return "";
} 
public function getDate_createdFormField($value = ''){
	return "";
} 

protected function getApplicant_allocation(){
	$query = 'SELECT * FROM applicant_allocation WHERE id=?';
	if (!isset($this->array['applicant_allocation_id'])) {
		return null;
	}
	$id = $this->array['applicant_allocation_id'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\Applicant_allocation($result[0]);
	return $resultObject;
}

protected function getCategory_location(){
	$query = 'SELECT * FROM category_location WHERE id=?';
	if (!isset($this->array['category_location_id'])) {
		return null;
	}
	$id = $this->array['category_location_id'];
	$db = $this->db;
	$result = $db->query($query,[$id]);
	$result = $result->getResultArray();
	if (empty($result)) {
		return false;
	}
	$resultObject = new \App\Entities\Category_location($result[0]);
	return $resultObject;
}


 
}

?>
