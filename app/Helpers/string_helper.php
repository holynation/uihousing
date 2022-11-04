<?php

if(!function_exists('toUserAgent'))
{
	function toUserAgent(object $agent){
		if ($agent->isBrowser()) {
		    $currentAgent = $agent->getBrowser() . ' ' . $agent->getVersion();
		} elseif ($agent->isRobot()) {
		    $currentAgent = $agent->getRobot();
		} elseif ($agent->isMobile()) {
		    $currentAgent = $agent->getMobile();
		}elseif ($agent->getAgentString() != '') {
		    $currentAgent = $agent->getAgentString();
		}
		else {
		    $currentAgent = 'Unidentified User Agent';
		}
		return $currentAgent;
	}
}

function deleteSpecialChar($str) {
	// replace all special characters by empty string 
	$res = str_replace( array( '%', '@', '\'', ';', '<', '>', '/','-','_'), ' ', $str);
	$res = str_replace(' ', '', $res);
	$res = strtoupper($res);
	return $res;
}

/**
 * [formatToUTC description]
 * @param  string|null $date [description]
 * @return [type]            [description]
 */
function formatToUTC(string $date=null){
	$date = $date ?? "now";
	$date = new \CodeIgniter\I18n\Time($date, 'UTC');
	$date = $date->format('Y-m-d H:i:s');
	return $date;
}

function discountOffPriceAmount($discPercentage, $sellPrice,&$discountAmount)
{
	if(!is_decimal($discPercentage)){
		$discPercentage = $discPercentage/100;
	}

	$discountAmount = round($sellPrice * $discPercentage, 2);
	return $sellPrice - $discountAmount;
}

/**
 * [getServiceCharge description]
 * @param  object  $scope      [description]
 * @param  int    $perValue [description]
 * @return int           [description]
 */
function getServiceCharge(object $scope, int $perValue=null){
	$query = "SELECT * from service_charge where status = '1' limit 1";
	$result = $scope->query($query);
	if($result->getNumRows() <= 0){
		return false;
	}
	$result = $result->getResultArray()[0];
	if($result['percentage'] <= 0 && $result['amount'] > 0){
		# this means that direct amount is used
		return $result['amount'];
	}else if($result['percentage'] > 0 && $result['amount'] <= 0){
		return ($result['percentage'] * $perValue);
	}else{
		# a rare case in which the service_charge is not set correctly
		return (0.05 * $perValue); # using 5% so we can detect something is wrong
	}
}

function is_decimal($val)
{
    return is_numeric( $val ) && floor( $val ) != $val;
}

function removeUnderscore($fieldname)
{
	$result = '';
	if (empty($fieldname)) {
		return $result;
	}
	$list = explode("_", $fieldname);

	for ($i = 0; $i < count($list); $i++) {
		$current = ucfirst($list[$i]);
		$result .= $i == 0 ? $current : " $current";
	}
	return $result;
}

function uniqueString($size = 20)
{
	$bytes = random_bytes($size);
	return bin2hex($bytes);
}

function generateReceipt()
{
	$rand = mt_rand(0x000000, 0xffffff); // generate a random number between 0 and 0xffffff
	$rand = dechex($rand & 0xffffff); // make sure we're not over 0xffffff, which shouldn't happen anyway
	$rand = str_pad($rand, 6, '0', STR_PAD_LEFT); // add zeroes in front of the generated string
	$code = date('Y') . "" . $rand;
	return strtoupper($code);
}
//this function returns the json encoded string based on the key pair paremter saved on it.
//
function createJsonMessage()
{
	$argNum = func_num_args();
	if ($argNum % 2 != 0) {
		throw new Exception('argument must be a key-pair and therefore argument length must be even');
	}
	$argument = func_get_args();
	$result = array();
	for ($i = 0; $i < count($argument); $i += 2) {
		$key = $argument[$i];
		$value = $argument[$i + 1];
		$result[$key] = $value;
	}
	return json_encode($result);
}

//the function to get the currently logged on use from the sessions
/**
 * check that non of the given paramter is empty
 * @return boolean [description]
 */
function isNotEmpty()
{
	$args = func_get_args();
	for ($i = 0; $i < count($args); $i++) {
		if (empty($args[$i])) {
			return false;
		}
	}
	return true;
}
//function to build csv file into a mutidimentaional array
function stringToCsv($string)
{
	$result = array();
	$lines = explode("\n", trim($string));
	for ($i = 0; $i < count($lines); $i++) {
		$current  = $lines[$i];
		$result[] = explode(',', trim($current));
	}
	return $result;
}

function array2csv($array, $header = false)
{
	$content = '';
	if ($array) {
		$content = strtoupper(implode(',', $header ? $header : array_keys($array[0]))) . "\n";
	}
	foreach ($array as $value) {
		$content .= implode(',', $value) . "\n";
	}
	return $content;
}

function endsWith($string, $end)
{
	$temp = substr($string, strlen($string) - strlen($end));
	return $end == $temp;
}

function verifyPassword($password)
{
	$minLength = 8;
	$numPattern = "/[0-9]/";
	$upperCasePattern = "/[A-Z]/";
	$lowerCasePattern = '/[a-z]/';
	return preg_match($numPattern, $password) && preg_match($upperCasePattern, $password) && preg_match($lowerCasePattern, $password) && strlen($password) >= $minLength;
}

function makeHash($string, $salt = '')
{
	return hash('sha256', $string . $salt);
}

function encode_password($password)
{
	return password_hash($password, PASSWORD_BCRYPT, array(
		'cost'  => 10
	));
}

function decode_password($userData, $fromDb)
{
	if ($userData != NULL) {
		return password_verify($userData, $fromDb);
	}
	return false;
}

function unique()
{
	return makeHash(uniqid());
}

function isValidEmail($string)
{
	if (filter_var($string, FILTER_VALIDATE_EMAIL) == FALSE) {
		return false;
	}
	return true;
}
function getFirstString($str, $uppercase = false)
{
	if ($str) {
		$value = substr($str, 0, 1);
		return ($uppercase) ? strtoupper($value) : strtolower($value);
	}
	return false;
}
function formatToNameLabel($string, $uppercase = false)
{
	if (!$string) return '';
	$splitName = explode(' ', $string);
	if (count($splitName) < 2) {
		return getFirstString($string, $uppercase);
	} else {
		$firstname = $splitName[0];
		$lastname = $splitName[1];
		return getFirstString($firstname, $uppercase) . '' . getFirstString($lastname, $uppercase);
	}
}

function isUniqueEmailAndPhone($scope, $email, $phone)
{

	$query = "select * from hirers where email=? or phone_number=?";
	$result = $scope->query($query, array($email, $phone));
	$result = $result->getResultArray();
	return count($result) == 0;
}

function getPasswordOTP($scope, $customer)
{
	$result = '';
	do {
		$result  = random_int(100000, 999999);
	} while (!isValidPasswordOTP($scope, $result));
	return $result;
}

function isValidPasswordOTP($scope, $otp)
{
	$query = "select * from password_otp where otp=? and status=1";
	$result = $scope->query($query, array($otp));
	$result = $result->getResultArray();
	return !$result;
}

function isValidState($state)
{
	$state = strtolower($state);
	$states = array('abia', 'adamawa', 'akwa ibom', 'awka', 'bauchi', 'bayelsa', 'benue', 'borno', 'cross river', 'delta', 'ebonyi', 'edo', 'ekiti', 'enugu', 'gombe', 'imo', 'jigawa', 'kaduna', 'kano', 'katsina', 'kebbi', 'kogi', 'kwara', 'lagos', 'nasarawa', 'niger', 'ogun', 'ondo', 'osun', 'oyo', 'plateau', 'rivers', 'sokoto', 'taraba', 'yobe', 'zamfara');
	return in_array($state, $states);
}

function isValidPhone($phone)
{
	$justNums = preg_replace("/[^0-9]/", '', $phone);
	if (strlen($justNums) == 13) $justNums = preg_replace("/^234/", '0', $justNums);

	//if we have 10 digits left, it's probably valid.
	return strlen($justNums) == 11;
}

function getLastInsertId($db)
{
	$query = "SELECT LAST_INSERT_ID() AS last"; //sud specify the table
	$result = $db->query($query);
	$result = $result->getResultArray();
	return $result[0]['last'];
}

//function migrated from  crud.php
function extractDbField($dbType)
{
	$index = strpos($dbType, '(');
	if ($index) {
		return substr($dbType, 0, $index);
	}
	return $dbType;
}

function extractDbTypeLength($dbType)
{
	$index = strpos($dbType, '(');
	if ($index) {
		$len = strlen($dbType) - ($index + 2);
		return substr($dbType, $index + 1, $len);
	}
	return '';
}

function getPhpType($dbType)
{
	$type = array('varchar' => 'string', 'text' => 'string', 'int' => 'integer', 'year' => 'integer', 'real' => 'double', 'float' => 'float', 'double' => 'double', 'timestamp' => 'date', 'date' => 'date', 'datetime' => 'date', 'time' => 'time', 'varbinary' => 'byte_array', 'blob' => 'byte_array', 'boolean' => 'boolean', 'tinyint' => 'boolean', 'bit' => 'boolean');
	$dbType = extractDbField($dbType);
	$dbType = strtolower($dbType);
	return $type[$dbType];
}

//function to build select option from array object with id and value key
function buildOption($array, $val='',$defaultValue='')
{
	if (empty($array)) {
		return '';
	}
	$optionValue = ($defaultValue != '') ? "$defaultValue" : "---choose option---";
	$result = "<option>$optionValue</option>";
	for ($i = 0; $i < count($array); $i++) {
		$current = $array[$i];
		$id = $current['id'];
		$value = $current['value'];
		$selected = $val == $id ? "selected='selected'" : '';
		$result .= "<option value='$id' $selected>$value</option> \n";
	}
	return $result;
}
function getRoleIdByName($db, $name)
{
	$query = "select id from role where role_name=?";
	$result = $db->query($query, array($name));
	$result = $result->getResultArray();
	return $result[0]['id'];
}
function buildOptionFromQuery($db, $query, $data = null, $val = '',$defaultValue='')
{
	$result = $db->query($query, $data);
	if ($result->getNumRows() == 0) {
		return '';
	}
	$result = $result->getResultArray();
	return buildOption($result,$val,$defaultValue);
}
//function to buiild select option from an array of numerical keys
function buildOptionUnassoc($array, $val = '')
{
	if (empty($array) || !is_array($array)) {
		return '';
	}
	$val = trim($val);
	$result = '';
	foreach ($array as $key => $value) {
		$current = trim($value);
		$selected = $val == $current ? "selected='selected'" : '';
		$result .= "<option $selected >$current</option>";
	}

	return $result;
}

function buildOptionUnassoc2($array,$val='',$defaultValue=''){
	if (empty($array) || !is_array($array)) {
		return '';
	}
	$val = trim($val);
	$optionValue = ($defaultValue != '') ? "$defaultValue" : "---choose option---";
	$result = "<option>$optionValue</option>";
	foreach ($array as $key => $value) {
		$current = trim($key);
		$selected = $val==$current?"selected='selected'":'';
		$result.="<option value='$current' $selected >". ucfirst($value)."</option>";
	} 
	return $result;
}

//function to tell if a string start with another string
function startsWith($str, $sub)
{
	$len = strlen($sub);
	$temp = substr($str, 0, $len);
	return $temp === $sub;
}

function getMediaType($path,$arr = false){
	$file = new \CodeIgniter\Files\File($path);
	if($file = @$file->getMimeType()){
		$media = explode('/', $file);
		return ($arr) ? $media : $media[0];
	}
	return null;
}

function createSymlink(string $link, string $target){
	$link = "uploads/{$link}";
    if(!is_link($link)){
        symlink($target, $link);
    }
    return $link;
}

function removeSymlinkWithImage(string $image){
	if(startsWith($image, base_url())){
		$image = str_replace(base_url(),'',$image);
	}
	$image = ltrim($image,"/"); # remove image from the public directory
	if(file_exists($image)){
		@chmod($image, 0777);
		@unlink($image);
	}
	$image = ROOTPATH.ltrim("writable/".$image); # remove image from the writable directory
	if(file_exists($image)){
		@chmod($image, 0777);
		@unlink($image);
	}
}

function showUploadErrorMessage($webSessionManager, $message, $isSuccess = true, $ajax = false)
{
	if ($ajax) {
		echo $message;
		exit;
	}
	$referer = $_SERVER['HTTP_REFERER'];
	$base = base_url();
	if (startsWith($referer, $base)) {
		$webSessionManager->setFlashMessage('flash_status', $isSuccess);
		$webSessionManager->setFlashMessage('message', $message);
		header("location:$referer");
		exit;
	}
	echo $message;
	exit;
}
function loadClass($classname, $namespace = null)
{
	if (!class_exists($classname)) {
		$modelName = is_null($namespace) ? "App\\Entities\\" . ucfirst($classname) : $namespace . "\\" . ucfirst($classname);
		return new $modelName;
	}
}

// function to get date difference
function getDateDifference($first, $second)
{
	$interval = date_diff(date_create($first), date_create($second));
	return $interval;
}

//function to get is first function is greater than the second
function isDateGreater($first, $second)
{
	$interval = getDateDifference($first, $second);
	return $interval->invert;
}

// function to send download request of a file to the browser
function sendDownload($content, $header, $filename)
{
	$content = trim($content);
	$header = trim($header);
	$filename = trim($filename);
	header("Content-Type:$header");
	header("Content-disposition: attachment;filename=$filename");
	echo $content;
	exit;
}


function padNumber($n, $value)
{
	$value = '' + $value; //convert the type to string
	$prevLen = strlen($value);
	// if ($prevLen > $n) {
	// 	throw new Exception("Error occur while processing");

	// }
	$num = $n - $prevLen;
	for ($i = 0; $i < $num; $i++) {
		$value = '0' . $value;
	}
	return $value;
}
function getFileExtension($filename)
{
	$index = strripos($filename, '.', 0); //start from the back
	if ($index === -1) {
		return '';
	}
	return substr($filename, $index + 1);
}
//function to determine if a string is a file path
function isFilePath($str)
{
	$recognisedExtension = array('doc', 'docx', 'pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'csv');
	$extension = getFileExtension($str);
	return (startsWith($str, 'uploads') && strpos($str, '/') && in_array($extension, $recognisedExtension));
}

//function to pad a string by a number of zeros
function padwithZeros($str, $len)
{
	$str .= '';
	$count = $len - strlen($str);
	for ($i = 0; $i < $count; $i++) {
		$str = '0' . $str;
	}
	return $str;
}
function generatePassword()
{
	return randStrGen(10);
}
function randStrGen($len)
{
	$result = "";
	$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
	$charArray = str_split($chars);
	for ($i = 0; $i < $len; $i++) {
		$randItem = array_rand($charArray);
		$ra = mt_rand(0, 10);
		$result .= "" . $ra > 5 ? $charArray[$randItem] : strtoupper($charArray[$randItem]);
	}
	return $result;
}

//function to get the recent page cookie information
function getPageCookie()
{
	$result = array();
	if (isset($_COOKIE['equipro'])) {
		$content = $_COOKIE['equipro'];
		$result = explode('-', $content);
	}
	return $result;
}

//function to save the page cookie
function sendPageCookie($module, $page)
{
	$content = $module . '-' . $page;
	setcookie('equipro', $content, 0, '/', '', false, true);
}
function show_access_denied()
{
	$viewName = "App\\Views\\access_denied";
	view($viewName);
}

function show_operation_denied()
{
	$viewName = "App\\Views\\operation_denied";
	view($viewName);
}
//function to replace the first occurrence of a string
function replaceFirst($toReplace, $replacement, $string)
{
	$pos = stripos($string, $toReplace);
	if ($pos === false) {
		return $string;
	}
	$len = strlen($toReplace);
	return substr_replace($string, $replacement, $pos, $len);
}

function displayJson($status, $message, $payload = false,$return=false)
{
	$param = array('status' => $status, 'message' => $message, 'payload' => $payload);
	$result = json_encode($param);
	if($return){
		return $result;
	}
	echo $result;
}
function formatToLocalCurrency($value = null)
{
	return "&#8358;$value"; // this is a naira currency
}
function attrToString($attributes = array())
{
	if (is_array($attributes)) {
		$atts = '';

		foreach ($attributes as $key => $val) {
			$atts .= ' ' . $key . '="' . $val . '"';
		}

		return $atts;
	}
}
function getStaffOption($value = '')
{
	$staff = loadClass('staff');
	return $staff->getStaffOption($value);
}
function getUserOption($value = '')
{
	$user = loadClass('user');
	return $user->getUserIdOption($value);
}
function getTitlePage($page = '')
{
	$formatted = " $page | Daabo ";
	return ($page != '') ? " $formatted " : " Daabo";
}

function getIDByName($scope, $table, $column, $value)
{
	$query = "select ID from $table where $column=?";
	$result = $scope->query($query, [$value]);
	$result = $result->getResultArray();
	if (!$result) {
		return false;
	}
	return $result[0]['ID'];
}

function getEquipImages($scope, $ID)
{
	$query = "SELECT equip_images_path as image_path from equip_images where equipments_id=?";
	$result = $scope->query($query, [$ID]);
	$result = $result->getResultArray();
	if (!$result) {
		return false;
	}
	return $result;
}

function rndEncode($data, $len = 16)
{
	return urlencode(base64_encode(randStrGen($len) . $data));
}

function rndDecode($data, $len = 16)
{
	$hash = base64_decode(urldecode($data));
	return substr($hash, $len);
}

function refEncode($data = '')
{
	// the ref code should not be more than 30 characters
	return randStrGen(25);
}

function generateHashRef($type = '')
{
	$hash = randStrGen(8) . randStrGen(10) . date("s"); //  the total should be 20 in character
	$ref = randStrGen(13);
	$result = array('receipt' => $hash, 'reference' => $ref);
	return $result[$type];
}

/**
 * This is to generate random character on a model
 * @param  string $table  [description]
 * @param  string $column [description]
 * @return [type]         [description]
 */
function generateNumber(object $db, string $table,string $column)
{
	$orderStart='1000000011';
	$query="select max($column) as model_hash from $table";
	$result = $db->query($query);
	if($result->getNumRows() > 0){
		$result = $result->getResultArray()[0];
		$temp = $result['model_hash'];
		return ($temp) ? $temp+1 : $orderStart;
	}else{
		return $orderStart;
	}
}

function checkRewardClaim($db, $user_id)
{
	$sql  = "select user_id from claimed_reward where user_id=?";
	$res = $db->query($sql, array($user_id));
	return ($res->getNumRows() > 0) ? true : false;
}

function formatToDateOnly($dateTime)
{
	$date = new DateTime($dateTime);
	return $date->format('Y-m-d');
}

function formatDateWithSlash(string $date){
	$tempDate = explode("/",trim($date));
    $day = ($tempDate[0] <= 12) ? $tempDate[1] : $tempDate[0];
    $month = ($tempDate[0] <= 12) ? $tempDate[0] : $tempDate[1];
    $year = $tempDate[2] ?? '';
    return $year."-".$month."-".$day;
}

function isTimePassed($start, $end, $limit = 30)
{
	$expiration = "+$limit minutes";
	$expTime = strtotime($expiration, $end);
	if ($start <= $expTime) {
		return false; // means the first is less than the second
	}
	return true;
}

function nextPaymentDate($date, $daysToAdd)
{
	$date = new DateTime($date);
	$interval = 'P' . $daysToAdd . 'D';
	$date->add(new DateInterval($interval));
	return $date->format('Y-m-d H:i:s');
}
function dateFormatter($posted)
{
	if ($posted) {
		$date = strftime("%d %B, %Y", strtotime($posted));
		return $date;
	}
	return false;
}
function dateTimeFormatter($posted, $hourFormat = 24)
{
	if ($posted) {
		$date = strftime("%d %b %Y", strtotime($posted));
		return $date . ', ' . localTimeRead($posted, $hourFormat);
	}
	return false;
}
function dateFormatToTimeline($posted)
{
	if ($posted) {
		$date = strftime("%b %d, %Y", strtotime($posted));
		return $date;
	}
	return false;
}

function formatDate()
{
	$d = new DateTime();
	return $d->format("Y-m-d H:i:s");
}

function calc_size($file_size)
{
	$_size = '';
	$kb = 1024;
	$mb = 1048576;
	$gb = 1073741824;

	if (empty($file_size)) {
		return '';
	} else if ($file_size < $kb) {
		return $_size . "B";
	} elseif ($file_size > $kb and $file_size < $mb) {
		$_size = round($file_size / $kb, 2);
		return $_size . "KB";
	} elseif ($file_size >= $mb and $file_size < $gb) {
		$_size = round($file_size / $mb, 2);
		return $_size . "MB";
	} else if ($file_size >= $gb) {
		$_size = round($file_size / $gb, 2);
		return $_size . "GB";
	} else {
		return NULL;
	}
}

// '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
// '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
// '%m Month %d Day'                                            =>  3 Month 14 Day
// '%d Day %h Hours'                                            =>  14 Day 11 Hours
// '%d Day'                                                        =>  14 Days
// '%h Hours %i Minute %s Seconds'                                =>  11 Hours 49 Minute 36 Seconds
// '%i Minute %s Seconds'                                        =>  49 Minute 36 Seconds
// '%h Hours                                                    =>  11 Hours
// '%a Days                                                        =>  468 Days

function dateDiffFormat($date_1, $date_2, $differenceFormat = '%a')
{
	$datetime1 = date_create($date_1);
	$datetime2 = date_create($date_2);
	$interval = date_diff($datetime1, $datetime2);
	return $interval->format($differenceFormat);
}

function localTimeRead($dateTime, $hourFormat = 24)
{
	$format = ($hourFormat == 24) ? "G" : "g";
	$date = date_create($dateTime);
	return date_format($date, "$format:i a");
}

function calcPercentageDiff($startVal, $endVal)
{
	// using percentage decrease formula
	// percentage decrease = ((starting value-ending value)/starting value) * 100
	// if ans is negative, it expresses a rate of increase, otherwise a decrease
	$diff = (($startVal - $endVal) / $startVal);
	return round(($diff * 100), 2);
}

function appConfig($mailKey)
{
	$mailLink = array(
		'salt' => '_~2y~12~T31xd7x7b67FO',
		'type' => array(
			1 => 'verify_account', 2 => 'verify_success',
			3 => 'forget', 4 => 'forget_success',
		),
		'company_name' => 'UIHousing',
		'company_address' => 'University of Ibadan',
		'company_email' => 'info@ui.edu.ng',
		'footer_link' => "ui.edu.ng"
	);
	return $mailLink[$mailKey];
}
