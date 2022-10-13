<?php 
//this function checks if an array is sequential or not
//returns -1 if array is null 0 if not sequential and 1 if it is
function isSequential($array){
	if (empty($array)) {
		return -1;
	}
	if(isset($array[0]) && isset($array[count($array)-1])){
		return 1;
	}
	else{
		return 0;
	}
}

//this function can get a range of element from a given array
//useful for asssociative array will work for non-associative but will be slower. user subArray instead.
function subArrayAssoc($array,$start,$len){
	$count = count($array);
	$validity = $count - ($start + $len);//confirmed
	if ($validity < 0) {
		throw new \Exception("error occur validity=$validity count is $count", 1);
		
		return false;
	}
	//extract the array
	$keys = array_keys($array);
	$result = array();
	for ($i=$start; $i < ($start + $len); $i++) { 
		$key = $keys[$i];
		$result[$key] =$array[$key];
	}
	return $result;
}

function loadChoices($scope,$table)
{
	$oldTable = $table;
	$table = "App\\Entities\\".$table;
	$displayField = isset($table::$displayField)?$table::$displayField:false;
	$tableKey = getTableKey();
	if (!$displayField) {
		$displayField='name';
	}
	if ($oldTable=='customer') {
		$displayField=" concat_ws(' ',firstname,middlename,lastname) ";
	}
	if (is_array($displayField)) {
		$tempAdd = implode(',', $displayField);
		$displayField="concat_ws(' ',$tempAdd)";
	}
	$query="select {$tableKey} as id,$displayField as value from $oldTable";
	$result = $scope->query($query);
	$result = $result->getResultArray();
	return $result;
}

function getStructure($scope,$entity)
{
	$result = array();
	$entity = loadClass("$entity");
	$labels = $entity::$typeArray;
	$nullArray = $entity::$nullArray;
	$relation = $entity::$relation;
	$labelArray = $entity::$labelArray;
	$actions = $entity::$tableAction;
	$relationDictionary = getEntityDirectRelation($scope,$relation);
	foreach ($labels as $label => $value) {
		if ($label=='ID' || $label=='date_created' || $label=='customer_order_id' || $label=='delivery_id') {
			continue;
		}
		$param= array();
		
		if (!in_array($label, $nullArray)) {
			$param['required']=1;
		}
		$param['type']= getFieldType($label,$value);
		$title =(array_key_exists($label, $labelArray) && trim($labelArray[$label]))?$labelArray[$label]:$label;
		$param['label']=$title;
		$param['relation']='';
		if (array_key_exists($label, $relationDictionary)) {
			$param['relation']=$relationDictionary[$label];
			$param['choices']=loadChoices($scope,$param['relation']);
		}
		// $param['relation']=$this->getAllDirectRelation($label,$relation);
		$param['value']='';
		// if ($param['type']=='select' && $param['relation']) {
		// 	# code...
		// }
		$result[$label]=$param;
	}
	return $result;
}

function getBulkUploadFields($scope,$entity)
{
	$entity = loadClass("$entity");
	if (!property_exists($entity,'bulkUploadField')) {
		return false;
	}
	return $entity::$bulkUploadField;
}

function getFieldType($label,$value)
{
	if ($value=='varchar' || $value=='int') {
		if (endsWith($label,getForeignKeyAppend())) {
			return 'select';
		}
		if (strpos(strtolower($label), 'mail')!==false) {
			return 'email';
		}

		if (strpos(strtolower($label), 'phone')!==false) {
			return 'phone';
		}
		if (strpos(strtolower($label), 'date')!==false) {
			return 'date';
		}
		if (strpos(strtolower($label), 'path')!==false || strpos(strtolower($label), 'image')!==false || strpos(strtolower($label), 'file')!==false || strpos(strtolower($label), 'document')!==false) {
			return 'file';
		}
	}
	if ($value=='timestamp') {
		return 'date';
	}
	if ($value=='text') {
		return 'text';
	}

	return 'simple';
}

function getTableKey()
{
	return 'ID';
}

function getForeignKeyAppend()
{
	return '_id';
}
function getEntityDirectRelation($scope,$relations)
{
	$result = array();
	$tableKey = getTableKey();
	foreach ($relations as $label => $relation) {
		$temp = @$relation[0];
		if ($temp && is_string($temp) && $temp!=$tableKey) {
			$result[$temp]=$label;
		}
	}
	return $result;
}

//this function will return a subArray of an array(most useful if the array is not associative)
function subArray($array,$start,$len){
	$count = count($array);
	echo "the count is $count";
	$validity = $count - ($start + $len);
	if ($validity < 0) {
		return false;
	}
	$result = array();
	for ($i=$start; $i < ($start + $len); $i++) { 
		$result[] =$array[$i];
	}
	return $result;
}

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
}
function replaceIndexWith(&$array,$index,$value){
	for ($i=0; $i < count($array); $i++) { 
		$array[$i][]=$value;;// $array[$i][$index]=$value;
	}
}
//this function check if an element is empty in 
function checkEmpty($array,$except=array()){
	if (empty($array)) {
		return false;
	}
	foreach ($array as $key => $value) {
		if (!in_array($value, $except) && empty($value) ) {
			return $key;
		}
	}
	return false;
}

//function to get get the last insert index given the database object
function getLastInsert($db){
	$query = 'select last_insert_id() as last';
	$result = $db->query($query);
	$result = $result->getResultArray();
	return $result[0]['last'];
}

function query($db,$query,$data=array()){
	$result =$db->query($query,$data);
	if (is_bool($result)) {
		return $result;
	}
	return $result->getResultArray();
}
//function to help return to the previous page while setting error message

//function to load states
function loadStates(){
	$list =scandir('assets/states');
	$result = array();
	//process the list well before they are returned
	helper('string');
	for ($i=0; $i < count($list); $i++) { 
		$current = $list[$i];
		if (startsWith($current,'.')) {
			continue;
		}
		$result[]=trim($current);
	}
	return $result;
}
function loadLga($state){
	$path = ENVIRONMENT == 'development' ? ROOTPATH."public/assets/states/$state" : ROOTPATH."assets/states/$state";
	if (!file_exists($path)) {
		return '';
	}
	$content = file_get_contents($path);
	$content = trim($content);
	$result = explode("\n", $content);
	for ($i=0; $i < count($result); $i++) { 
		$result[$i]=trim($result[$i]);
	}
	return $result;
}

function removeDuplicateValues($array){
	$result = array();
	foreach ($array as  $value) {
		if (in_array($value, $result)) {
			continue;
		}
		$result[]=$value;
	}
	return $result;
}

function arrayToCsvString($array,$header=null){
	$result = "";
	$key = $header==null?array_keys($array[0]):$header;
	array_unshift($array, $key);
	for ($i=0; $i < count($array); $i++) { 
		$current = $array[$i];
		$result.=singleRowToCsvString($current);
	}
	return $result;
}
function singleRowToCsvString($row){
	$result='';
	$result.=implode(',', $row);
	$result.="\n";
	return $result;
}

//function to extract a section of columns from a two dimsntional array
function copyMultiArrayWithIndex($indexArray,$data){
	$result= array();
	for ($i=0; $i < count($data); $i++) { 
		$current = $data[$i];
		$result[]=extractArrayPortion($indexArray,$current);
	}
	return $result;
}

function extractArrayPortion($index,$data){
	if (max($index) >= count($data)) {
		# there will be an error just throw exception or exit
		exit('error occur while performing operation');
	}
	$result = array();
	for ($i=0; $i < count($index); $i++) { 
		$result[]=$data[$index[$i]];
	}
	return $result;
}

function convertToAssoc($data,$first,$second){
	$result=array();
	for ($i=0; $i < count($data); $i++) { 
		$key = $data[$i][$first];
		$value = $data[$i][$second];
		$result[$key]=$value;
	}
	return $result;
}
function removeEmptyAssoc($arr)
{
	$result = array();
	foreach ($arr as $key => $value) {
		if (trim($value)!=='') {
			$result[$key]=$value;
		}
	}
	return $result;
}
function removeEmptyArrayElement($arr){
	$result=array();
	for ($i=0; $i < count($arr); $i++) { 
		if (trim($arr[$i])=='') {
			continue;
		}
		$result[]= $arr[$i];
	}
	return $result;
}

//this function initialize n number of array with the
function initArray($size,$default)
{
	$result= array();
	for ($i=0; $i < $size; $i++) { 
		$result[$i]=$default;
	}
	return $result;
}

function removeValue($arr,$val)
{
	$result = array();
	foreach ($arr as $value) {
		if ($value==$val) {
			continue;
		}
		$result[]=$value;
	}
	return $result;
}
function array_values_recursive($array)
{
	$arrayValues = array();
	foreach ($array as $value)
	{
	    if (is_scalar($value) OR is_resource($value))
	    {
	        $arrayValues[] = $value;
	    }
	    elseif (is_array($value))
	    {
	        $arrayValues = array_merge($arrayValues, array_values_recursive($value));
	    }
	}
	return $arrayValues;
}

/**
 * This would be deprecated for the new notification system
 * @param  string      $label  [description]
 * @param  string|null $value  [description]
 * @param  string|null $object [description]
 * @return [type]              [description]
 */
function notifyText(string $label,string $value=null, string $object=null){
	$result = [
		'booking_request' => "You have a new booking request order on our platform",
		'booking_request_update' => "You've a new updated booking request on our platform",
		'booking_request_accepted' => "Your booking request with the order ID - {$value} was accepted.Proceed to make payment",
		'booking_request_rejected' => "Your booking request with the order ID - {$value} was rejected.",
		'booking_request_pickup' => "The pick up date for the order ID - {$value} has been scheduled",
		'booking_request_extended' => "{$value} made a request to extend the equipment hiring.",
		'booking_request_returned' => "{$value} successfully returned the equipment {$object}",
	];
	return $result[$label];
}

/**
 * This represent entity_type in the notification_object table
 * following the format where the
 * key  => alias name
 * value => entityname_actiontype e.g equip_order_created
 * @param  string $label [description]
 * @return string         [description]
 */
function notifyEntityType(string $label) : string
{
	$result = [
		'order_create' 	=> 'equip_order_created',
		'order_update' 	=> 'equip_order_updated',
		'order_accept' 	=> 'equip_order_accepted',
		'order_reject' 	=> 'equip_order_rejected',
		'order_pay' 	=> 'equip_order_paid',
		'equip_extend' 	=> 'equip_order_extended',
		'equip_return' 	=> 'equip_order_returned',
		'delivery_hirer' => 'equip_delivery_status_toHirer',
		'chat_msg' 		=> 'chats_message',
	];
	return $result[$label];
}

/**
 * @deprecated - This is not used yet
 * @param  [type] $value [description]
 * @return [type]        [description]
 */
function bookingDays($value){
	$result = array(
		'week' => 7,
		'month' => 30,
		'day' => 1
	);
	return $result[$value];
}

/**
 * @deprecated - This is not used yet
 * @param  [type] $event [description]
 * @param  string $value [description]
 * @return [type]        [description]
 */
function eventLog($event, $value=''){
	$result = [
		'booking' => "Booking request sent {$value}", # value: pending,rejected,approved
		'extended' => "An extension on hire equipment",
		'payment' => "Payment made - {$value}", # value: normal, extended
		'chat' => "Chat {$value}", # value: initiated|sender, response
		'review' => "Review on equipment",
		'post_equip' => "Creation of equipment",
		'withdrawal' => "Withdrawal request",
		'login' => "Account login",
		'logout' => "Account logout"
	];
	return $result[$value];
}

function listAPIEntities($db)
{
	// this is to exempt some table to be accessed from the mobile end
	$exemptions = array('user','role','admin','permission','password_otp','activity_log');
	$query='show tables';
	$dbResult = $db->query($query);
	$dbResult = $dbResult->getResultArray();
	$result=array();
	foreach ($dbResult as $res) {
		$temp = reset($res);
		if (in_array($temp, $exemptions)) {
			continue;
		}
		$result[]=$temp;
	}
	return $result;
}

function listEntities($db)
{
	$query='show tables';
	$dbResult = $db->query($query);
	$dbResult = $dbResult->getResultArray();
	$result=array();
	foreach ($dbResult as $res) {
		$result[]=reset($res);
	}
	return $result;
}

function getAPIEntityTranslation()
{
	# this gets the mobile translation from the database
	$result = array(
		'reset_password'=>'requestPasswordReset',
		'change_password'=>'changePassword',
		'signup'=>'register',
		'auth'=>'login',
		'payment' => 'users_payment_details',
		'location' => 'users_location',
		'equip_booking' => 'equip_order',
		'extend_booking' => 'equip_order',
		'rentals' => 'equip_request',
		'extend_request' => 'extend_equip_request',
		'delivery_status' => 'equip_delivery_status',
		'toggle_users' => 'switch_users',
		'verify_document' => 'kyc_document'
	);
	return $result;
}

function getDeliveryStatus($label){
	$result = [
		'pending' => 'Pending',
		'picked_from_owner' => 'Picked up from owner',
		'delivered_hirer' => 'Delivered to hirer',
		'in_use' => 'Equipment in use',
		'picked_from_hirer' => 'Picked up from hirer',
		'returned' => 'Returned'
	];
	return $result[$label];
} 

function getEntityTranslation()
{
	# this gets the web translation from the database
	$result = array(
		'reset_password'=>'requestPasswordReset',
		'change_pass'=>'changePassword',
		'signup'=>'register',
		'auth'=>'login',
	);
	return $result;
}
//the set of methods that are allowed for the api to access and the translation for those that has translation
function getDeniedApiMethods()
{
	$result = array('user','customer_login');
}

?>
