<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the category table
*/
class Category extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Category"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['status'];

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
public static $displayField = 'category_name';

/** 
* This array contains the fields that are unique
* @var array
*/
public static $uniqueArray = ['category_name'];

/** 
* This is an associative array containing the fieldname and the datatype
* of the field
* @var array
*/
public static $typeArray = ['category_name' => 'varchar','status' => 'tinyint'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','category_name' => '','status' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['status' => '1'];

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
public static $relation = [];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['enable'=>'getEnabled','delete' => 'delete/category', 'edit' => 'edit/category'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getCategory_nameFormField($value = ''){
	return "<div class='form-group'>
				<label for='category_name'>Category Name</label>
				<input type='text' name='category_name' id='category_name' value='$value' class='form-control' required />
			</div>";
} 
public function getStatusFormField($value = ''){
	return "<div class='form-group'>
				<label for='status'>Status</label>
				<input type='text' name='status' id='status' value='$value' class='form-control' required />
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


 
}

?>