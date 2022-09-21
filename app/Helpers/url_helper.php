<?php 

//function for sending post http request using curl
function getCustomer()
{
    if (isset($_SERVER['current_user']) && $_SERVER['current_user']) {
        return $_SERVER['current_user'];
    }
    return false;
}

function sendPost($url,$post,&$errorMessage,$returnResult=false){
	$res = curl_init($url);
	curl_setopt($res, CURLOPT_POST,true);
	curl_setopt($res, CURLOPT_POSTFIELDS, $post);
	$certPath =str_replace(APPPATH."helpers\url_helper.php",'cacert.pem', __FILE__);
	curl_setopt($res, CURLOPT_CAINFO, $certPath);
	if ($returnResult) {
		curl_setopt($res, CURLOPT_RETURNTRANSFER, true);
	}
	$referer = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	curl_setopt($res, CURLOPT_REFERER, $referer);
	$result = curl_exec($res);
	$errorMessage = curl_error($res);
	curl_close($res);
	return $result;
}

function request($url,$type,$headers,$param,$return=false,&$output='',&$errorMessage=''){
    $res = curl_init($url);
    if ($type=='post') {
        curl_setopt($res, CURLOPT_POST,true);
        curl_setopt($res, CURLOPT_POSTFIELDS, $param);
    }
    else if($type=='get'){
        $getOption =array(
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET");
        curl_setopt_array($res, $getOption);
    }
    //check if the quest is a secure one
    if (strtolower(substr($url, 0,5))=='https') {
        // $certPath =str_replace( "application\helpers\MY_url_helper.php",'cacert.pem', __FILE__);
        // curl_setopt($res, CURLOPT_CAINFO, $certPath);
        curl_setopt($res, CURLOPT_SSL_VERIFYSTATUS, false);
        curl_setopt($res, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($res, CURLOPT_SSL_VERIFYPEER, false);
    }
    
    if ($return) {
        curl_setopt($res, CURLOPT_RETURNTRANSFER, true);
    }
    $formattedHeader = formatHeader($headers);
    curl_setopt($res, CURLOPT_HTTPHEADER, $formattedHeader);
    $result = curl_exec($res);
    $errorMessage = curl_error($res);
    $output = $result;
    curl_close($res);
    return !$errorMessage;
}

function makeOnlineTransaction($postParam,&$errorMessage=''){
    $curl = curl_init();
    $curlParam = $postParam;
    $sk = getenv('paystackTestKey');

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $curlParam,
      CURLOPT_HTTPHEADER => [
        "authorization: Bearer $sk", //replace this with your own test key
        "content-type: application/json",
        "cache-control: no-cache"
      ],
      CURLOPT_SSL_VERIFYSTATUS => false,
      CURLOPT_SSL_VERIFYPEER => false,

    ));

    $response = curl_exec($curl);
    $errorMsg = curl_error($curl);
    if($errorMsg){
        // die('Curl returned error: ' . $errorMessage);
        $errorMessage = 'Curl returned error: ' . $errorMsg;
    }
    return $response;
}

function makeNextTransaction($postParam,&$errorMessage=''){
    $curl = curl_init();
    $curlParam = $postParam;
    $sk = env('paystackTestKey');

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.paystack.co/transaction/charge_authorization",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $curlParam,
      CURLOPT_HTTPHEADER => [
        "authorization: Bearer $sk",
        "content-type: application/json",
        "cache-control: no-cache"
      ],
      CURLOPT_SSL_VERIFYSTATUS => false,
      CURLOPT_SSL_VERIFYPEER => false,

    ));

    // if (!defined('CURL_SSLVERSION_TLSV1_2')) define('CURL_SSLVERSION_TLSV1_2', 6);

    $response = curl_exec($curl);
    $errorMsg = curl_error($curl);
    if($errorMsg){
        // die('Curl returned error: ' . $errorMessage);
        $errorMessage = 'Curl returned error: ' . $errorMsg;
    }
    return $response;
}

function formatHeader($header)
{
    if (!$header) {
        return $header;
    }
    $keys = array_keys($header);
    if (is_numeric($keys[0])) {
        //if has numberic index, should mean the header has already been formated inthe right way
        return $header;
    }
    $result = array();
    foreach ($header as $key => $value) {
        $temp = "$key: $value";
        $result[]=$temp;
    }
    return $result;
}

function base64UrlEncode($input){
    return str_replace(
        ['+', '/', '='],
        ['-', '_', ''],
        base64_encode($input)
    );
}

function base64UrlDecode($input){
	return str_replace(
		['-', '_', ''],
        ['+', '/', '='],
        base64_decode($input)
    );
}

function signInput($input,$key,$alg = 'sha256'){
	return hash_hmac($alg, $input, $key, true);
}

function decodeClaims($data){
	return json_decode(base64UrlDecode($data));
}

function verifySignature($data,$signature,$key,$alg = 'sha256'){
	$hash = hash_hmac($alg, $data, $key, true);

    if (function_exists('hash_equals')) {
        return hash_equals($signature,$hash);
    }

    return hash_compare($signature,$hash);
}

function hash_compare($hashed_value, $hashed_expected) {
    if (!is_string($hashed_value) || !is_string($hashed_expected)) {
        return false;
    }
   
    $len = strlen($hashed_value);
    if ($len !== strlen($hashed_expected)) {
        return false;
    }

    $status = 0;
    for ($i = 0; $i < $len; $i++) {
        $status |= ord($hashed_value[$i]) ^ ord($hashed_expected[$i]);
    }
    return $status === 0;
}

/**
* Get header Authorization
* */
function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }else {
    	$urlPath = apache_request_headers();
    	$headers = array_key_exists('Authorization', $urlPath)?$urlPath['Authorization']:(array_key_exists('authorization',$urlPath)?$urlPath['authorization']:false);
    }
    return $headers;
}

/**
 * get access token from header
 * */
function getBearerToken() {
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    throw new \Exception('Access Token Not found');
}

function generateJwt($payload,$key){
	// creating the token header
	$header = json_encode([
		'type' => 'JWT',
		'alg' => 'HS256'
	]);

	// Encode Header
	$base64UrlHeader = base64UrlEncode($header);
	$base64UrlPayload = base64UrlEncode(json_encode($payload));

	$signingInput = $base64UrlHeader.".".$base64UrlPayload;
	$signature = signInput($signingInput,$key);
	$base64UrlSignature = base64UrlEncode($signature);
	return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function validateJwt($jwt, $key){
    /**
     * You can add a leeway to account for when there is a clock skew times between
     * the signing and verifying servers. It is recommended that this leeway should
     * not be bigger than a few minutes.
     *
     */
	$leewayTime = 0;
	$timestamp = time();
    if (empty($key)) {
        throw new \Exception('Key can not be empty');
    }

    $token = explode('.', $jwt);
    if (count($token) != 3) {
        throw new \Exception('Wrong number of segments');
    }

    list($b64Header, $b64Payload, $b64Signature) = $token;
    // validating each segments of the token
    if(null === ($header = decodeClaims($b64Header))){
    	throw new \Exception('Invalid header encoding');
    }

    if(null === $payload = decodeClaims($b64Payload)){
    	throw new \Exception('Invalid claims encoding');
    }

    if (false === ($sig = base64UrlDecode($b64Signature))) {
        throw new \Exception('Invalid signature encoding');
    }

    if (empty($header->alg)) {
        throw new \Exception('The algorithm is empty');
    }

    // checking the signature
    $signInput = $b64Header.".".$b64Payload;
    if (!verifySignature($signInput, $sig, $key)) {
        throw new \Exception('Signature verification failed');
    }

    // Check that this token has been created before 'now'. This prevents
    // using tokens that have been created for later use.
    if (isset($payload->iat) && $payload->iat > ($timestamp + $leewayTime)) {
        throw new \Exception(
            'Cannot handle token prior to ' . date(DateTime::ISO8601, $payload->iat)
        );
    }
    // Check if this token has expired.
    if (isset($payload->exp) && ($timestamp - $leewayTime) >= $payload->exp) {
        throw new \Exception('It seems the token has expired');   
    }
    return $payload;

}

function getClientPlatform(){
    if(strpos(@$_SERVER['PATH_INFO'], 'api') !== FALSE || strpos(@$_SERVER['ORIG_PATH_INFO'], 'api')){
        return 'mobile';
    }else{
        return 'web';
    }
}


?>