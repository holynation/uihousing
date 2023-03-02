<?php 

namespace App\Entities;

use App\Models\Crud;

/** 
* This class is automatically generated based on the structure of the table.
* And it represent the model of the category_location table
*/
class Category_location extends Crud {

/** 
* This is the entity name equivalent to the table name
* @var string
*/
protected static $tablename = "Category_location"; 

/** 
* This array contains the field that can be null
* @var array
*/
public static $nullArray = ['address'];

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
public static $displayField = 'address';

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
public static $typeArray = ['category_id' => 'int','address' => 'text','with_bq' => 'tinyint','zone_type' => 'varchar','status' => 'tinyint','date_created' => 'timestamp'];

/** 
* This is a dictionary that map a field name with the label name that
* will be shown in a form
* @var array
*/
public static $labelArray = ['ID' => '','category_id' => '','address' => '','with_bq' => '','zone_type' => '','status' => '','date_created' => ''];

/** 
* Associative array of fields in the table that have default value
* @var array
*/
public static $defaultArray = ['status' => '1','date_created' => 'current_timestamp()','with_bq' => '1'];

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
public static $relation = ['category' => array('category_id','id')
];

/** 
* This are the action allowed to be performed on the entity and this can
* be changed in the formConfig model file for flexibility
* @var array
*/
public static $tableAction = ['delete' => 'delete/category_location', 'edit' => 'edit/category_location'];

public function __construct(array $array = [])
{
	parent::__construct($array);
}
 
public function getCategory_idFormField($value = ''){
	$fk = array('table'=>'category','display'=>'category_name');

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
public function getAddressFormField($value = ''){
	return "<div class='form-group'>
				<label for='address'>Address</label>
				<input type='text' name='address' id='address' value='$value' class='form-control' required />
			</div>";
}
public function getWith_bqFormField($value = ''){
	$arr = array('0'=>'No','1'=>'Yes');
    $option = buildOptionUnassoc2($arr,$value);

       return "<div class='form-group'>
       		<label for='with_bq' >With BQ</label>
              <select name='with_bq' id='with_bq' class='form-select form-control form-control-sm' data-search='on' data-ui=''>
              $option
              </select>";
} 
public function getZone_typeFormField($value = ''){
	return "<div class='form-group mt-3'>
				<label for='zone_type'>Zone Type</label>
				<input type='text' name='zone_type' id='zone_type' value='$value' class='form-control' required />
			</div>";
} 
public function getStatusFormField($value = ''){
	return "";
} 
public function getDate_createdFormField($value = ''){
	return "";
} 

protected function getCategory(){
	$query = 'SELECT * FROM category WHERE id=?';
	if (!isset($this->array['ID'])) {
		return null;
	}
	$id = $this->array['ID'];
	$result = $this->query($query,[$id]);
	if (!$result) {
		return false;
	}
	$resultObject = new \App\Entities\Category($result[0]);
	return $resultObject;
}


 
}

?>
