<?php 

/**
 * This is the Model that manages Api specific request
 */
namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\WebSessionManager;
use App\Models\Mailer;
use Firebase\JWT\JWT;
use CodeIgniter\I18n\Time;

// TODO: I WANNA THINKA ABOUT ADDING STATUS TO QUERY ON ENTITY THAT MIGHT HAVE
// THEIR STATUS AS 0 TO MEAN THEY SHOULD NOT BE DISPLAY E.G OWNERS OF EQUIPMENTS

class ApiModel extends Model
{
	protected $request;
	protected $response;
	protected $db;
	private $mailer;
	private $webSessionManager;
	private $entitiesNameSpace = 'App\Entities';

	function __construct(RequestInterface $request=null, ResponseInterface $response=null)
	{
		helper(['url','string']);
		$this->db = db_connect();
		$this->request = $request;
		$this->response = $response;
		$this->webSessionManager = new WebSessionManager;
		$this->mailer = new Mailer;
	}

	/**
	 * This is both for mobile and web version
	 * @param $this->request->getPost(email) The user email
	 * @param $this->request->getPost(password) The user password
	 * @return JSON return a json having the user details & token
	 */
	public function login(){
        $username = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $fcmToken = $this->request->getPost('fcm_token');

        if (!($username || $password)) {
            $response= json_encode(array('status'=>false,'message'=>"invalid entry data"));
            echo $response;
            return;
        }
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
          $response= json_encode(array('status'=>false,'message'=>"invalid email address"));
          echo $response;
          return;
        }
        $user = loadClass('user');
        $array = array('username'=>$username,'status'=>1);
        $user = $user->getWhere($array,$count,0,null,false);
        if ($user==false) {
            $response = array('status'=>false,'message'=>'invalid email or account not verified yet');
            echo json_encode($response);
            return;             
        }
        else{
            $user = $user[0];
            if (!decode_password($password, $user->password)) {
                displayJson(false, 'invalid email or password');
                return; 
            }
            $baseurl = base_url().'/';
            if(($fcmToken) && !$this->updateUserFcmToken($user, $fcmToken)){
            	return false;
            }
            $userType = $this->webSessionManager->saveCurrentUser($user,true,true);
            if(!$userType){
            	displayJson(false, "Oops, sorry you can't login at the moment");
                return; 
            }
            unset($userType['password'],$userType['referral_code']);
            $baseurl.=$this->getUserPage($user);

            $key = getenv('jwtKey');
			$token = $this->generateToken($userType);
			$userType['ID'] = $userType['user_table_id'];
			unset($userType['user_table_id'], $userType['user_id']);
			$userType['kyc_updated'] = false;
			$userType['kyc_approved'] = false;
			if($kycDoc = $this->getKycDocument($userType['ID'])){
				$userType['kyc_updated'] = true;
				$userType['kyc_approved'] = $kycDoc['status'] == 0 ? false : true;
				$userType['kyc_name'] = $kycDoc['document_name'];
				$userType['kyc_document_path'] = $kycDoc['kyc_document_path'];
			}
			$payload['token'] = $token;
			$payload['details'] = $userType;
			displayJson(true, "You're successfully authenticated",$payload);
            return;
        }
    }

    /**
     * [generateToken description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    private function generateToken(array $data){
    	$key = getenv('jwtKey');
		$token = generateJwt($data,$key);
		return $token;
    }

    /**
     * This is to update fcm_token
     * @param  object $user  
     * @param  string $token
     * @return bool
     */
    private function updateUserFcmToken(object $user, string $token){
    	$user->fcm_token = $token;
    	if(!$user->update($user->ID)){
    		displayJson(false, 'sorry, something went wrong');
    		return false;
    	}
    	return true;
    }

    /**
     * This is both for mobile and web version
	 * @param fullname		the user fullname
	 * @param email 		the user email
	 * @param phone_number	the user phone number
	 * @param password 		the user password
	 * @return JSON if sucessfully created
	 */
    public function register()
	{
		//get all the information validate and create the necessary account
		$fullname= $this->request->getPost('fullname');
		if (!$fullname) {
			displayJson(false,"name can't be empty");
			return;
		}

		$email=$this->request->getPost('email');
		if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
			displayJson(false,"invalid email address");
			return;
		}

		$phone = $this->request->getPost('phone_number');

		if (!isUniqueEmailAndPhone($this->db,$email,$phone)) {
			displayJson(false,"email or phone number already exists");
			return;
		}

		//hash the password
		$password = trim($this->request->getPost('password'));
		$password = encode_password($password);

		$this->db->transBegin();
		$toInsert = array('fullname'=>$fullname,'email'=>$email,'phone_number'=>$phone);
		$hirers = loadClass('hirers');
		$hirers = new $hirers($toInsert);
		if (!$hirers->insert($this->db,$message)) {
			$this->db->transRollback();
			displayJson(false,"error occured: {$message}");
			return;		
		}
		$lastInsertId = getLastInsertId($this->db);
		$userType = (getClientPlatform() != 'mobile') ? 'hirers' : 'app_hirers';

		$data = array(
			'username' => $email,
			'password' => $password,
			'user_type' => $userType,
			'user_table_id' => $lastInsertId,
			'status' => '0'
		);
		$this->createUsers($data);
		$this->db->transCommit();

		$accountLink = $this->activationLink($email);
		$param = [
			'customerName'=>$fullname,
			'urlLink' => $accountLink
		];
		$template = $this->mailer->mailTemplateRender($param,'account_activation');

		$mailResponse = ($this->mailer->sendCustomerMail($email,'verify_account')) ? true : false;
		if(!$mailResponse){
			log_message("error", "WEB_REGISTRATION mail not sent to user {$lastInsertId}");
			$this->db->transRollback();
			displayJson(false,"an error occured while creating account 4");
			return;
		}
		displayJson(true,"Your have successfully register.Kindly Check your email for account confirmation.If not found in your inbox, try to check out your spam folder too.");
	}

	/**
	 * @param string $email
	 * @return string
	 */
	private function activationLink($email){
		$mailSalt = appConfig('salt');
        $encodeEmail = str_replace(array('@', '.com'), array('~az~', '~09~'), $email);
        $temp = md5($mailSalt . $email);
        $expire = rndEncode(time());
        $verifyTask = rndEncode('verify');
        $accountLink = base_url("account/verify/$encodeEmail/$temp/1?task=$verifyTask&tk=$expire");
        return $accountLink;
	}

	/**
	 * This is to create user
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	private function createUsers(array $data){
		try{
			$user = loadClass('user');
			$user = new $user($data);
			if(!$user->insert($this->db,$message)){
				$this->db->transRollback();
				displayJson(false,"error occured: {$message}");
				return;	
			}
		}catch(Exception $e){
			displayJson(false,"error occured: {$e->getMessage()}");
			return;	
		}
	}

	/**
	 * @param string $user
	 * @return array
	 */
	private function getUserPage($user){
		$link= array('app_hirers'=>'vc/hirers','hirers'=>'vc/hirers','admin'=>'vc/admin/dashboard');
		$roleName = $user->user_type;
		return $link[$roleName];
	}

	/**
	 * @return string
	 */ 
	private function validateUserType(){
		$userType = 'hirers'; # always return hirers since both mobile user and web hirers are using the same table
		return $userType;
	}

	/**
	 * This is both for mobile and web version
	 * @param email
	 * @return otp and expire time
	 */
	public function requestPasswordReset()
	{
		$email = trim($this->request->getPost('email'));
		if (!$email) {
			displayJson(false,'please provide email address');
			return;
		}
		$userType = $this->validateUserType();
		$userTemp = $userType;
		# this will just generate the token and send to the email address
		$userType = loadClass($userType);
		$userType=$userType->getWhere(array('email'=>$email),$count,0,null,false);
		$userType = is_array($userType)?$userType[0]:$userType;
		if (!$userType) {
			displayJson(false,'account with that email address does not exist');
			return;
		}
		# disable all previous OTP by the user
		$userType->disableAllPasswordOTPs();
		$otp = getPasswordOTP($this->db,$userType);
		if (!$otp) {
			displayJson(false,'an error occured');
			return;
		}
		# save the OTP and send the mail
		$password_otp = loadClass('password_otp');
		$password_otp->otp=$otp;
		$password_otp->user_table_id=$userType->ID;
		$password_otp->user_type=$userTemp;
		if (!$password_otp->insert()) {
			displayJson(false,'an error occured');
			return;
		}

		$mailer = new Mailer;
		$userName = $userType->fullname;
		$param = ['customerName'=>$userName,'otp'=>$otp];
		$template = $mailer->mailTemplateRender($param,'password_reset_change');
		$mailer->sendCustomerMail($email,'password_app_token');

		$result = array('code'=>$otp,'expired_in'=>'1h');
		displayJson(true,'success',$result);
		return;
	}

	/**
	 * This is both for mobile and web version
	 * @param otp 		the otp sent to user
	 * @param email 	the user email
	 * @param password 	the new password
	 * @return true 	true on sucess
	 */
	public function changePassword()
	{
		$email = trim($this->request->getPost('email'));
		$otp = trim($this->request->getPost('otp'));
		$password = trim($this->request->getPost('password'));
		if (!$email) {
			displayJson(false,'please provide email address');
			return;
		}
		if (!$otp) {
			displayJson(false,'no otp provided');
			return;
		}

		$userType = $this->validateUserType();
		$userTemp = $userType;

		$userType = loadClass($userType);
		$userType =$userType->getWhere(array('email'=>$email),$count,0,null,false);
		$userType = is_array($userType)?$userType[0]:$userType;

		if(!$this->validate_otp($otp,$email,true)){
			return false;
		}

		$password = encode_password($password);
		$newUser = loadClass('user');
		$temp = $newUser->getWhere(array('user_table_id'=>$userType->ID,'username'=>$email),$count,0,1,false);
		if (!$temp){
			displayJson(false,'sorry, an invalid operation...');
			return;
		}
		$userID = $temp[0]->ID;
		$passDate = $this->formatToUTC();
		$this->db->transBegin();
		$cust = new $newUser(array('ID'=>$userID,'password'=>$password,'last_change_password'=>$passDate));
		if (!$cust->update()) {
			$this->db->transRollback();
			displayJson(false,"an error occured while resetting password");
			return;		
		}

		$userType->disableAllPasswordOTPs();
		$mailer = new Mailer;
		$username = $userType->fullname;

		$param = ['customerName'=>$username];
		$template = $mailer->mailTemplateRender($param,'password_reset_success');
		$mailer->sendCustomerMail($email,'password_reset_success');

		$this->db->transCommit();
		displayJson(true,"password has been reset successfully");
		return;	
	}

	/**
	 * This is both for mobile and web version
	 * @param current_password 	$this->request->getPost('current_password');
	 * @param password 			$this->request->getPost('password')
	 * @param confirm_password 	$this->request->getPost('confirm_password')
	 * @return JSON 	
	 */
	public function update_password()
	{
	    $curr_password = $this->request->getPost('current_password');
	    $new = $this->request->getPost('password');
	    $confirm = $this->request->getPost('confirm_password');

	    if (!isNotEmpty($curr_password,$new,$confirm)){
	        displayJson(false,'empty field detected.please fill all required field and try again');
	        return;
	    }

	    $customer = getCustomer();
	    if(!$customer){
	    	displayJson(false,'invalid users');return;
	    }
	      
	    $id= $customer->user_id;
	    $user = loadClass('user');

	    if($user->findUserProp($id)){
	        $check = decode_password(trim($curr_password), $user->data()[0]['password']);
	        if(!$check){
	        	displayJson(false,'please type-in your password correctly');
	          	return;
	        }
	    }

	    if ($new !==$confirm) {
	        displayJson(false,'new password does not match with the confirmation password');
	        return;
	    }
		
	    $new = encode_password($new);
	    $date = $this->formatToUTC();
        $query = "update user set password = '$new', last_change_password='$date' where ID=?";
        if ($this->db->query($query,array($id))) {
          	displayJson(true,'operation successfull');
          	return;
        }
        else{
          	displayJson(false, 'error occured during operation');
          	return;
        }
	}

	/**
	 * @param $otp int This is the otp
	 * @param $email string This is the email
	 * @return JSON - Returning JSON based on the validation
	 */
	public function validate_otp($otp=null,$email=null,$return=false){
		$otp = $otp;
		if($this->request->getPost('otp')){
			$otp = $this->request->getPost('otp');
		}
		$email = $email ?? trim($this->request->getPost('email'));
		$userType = $this->validateUserType();
		$userTemp = $userType;

		$userType = loadClass($userType);
		$userType =$userType->getWhere(array('email'=>$email),$count,0,null,false);
		$userType = is_array($userType)?$userType[0]:$userType;
		if (!$userType) {
			displayJson(false,'invalid operation');
			return false;
		}
		if (!$this->verifyPasswordOTP($userType->ID,$otp,$userTemp)) {
			displayJson(false,'oops invalid code');
			return false;
		}
		if(!$return){
			displayJson(true,'otp successfully validated');
			return;
		}else{
			return true;
		}
	}

	/**
	 * @param  customer int
	 * @param  otp 		string
	 * @return array 	return array
	 */
	private function verifyPasswordOTP($user_table_id,$otp,$userType)
	{
		$query="select * from password_otp where user_table_id=? and otp=? and user_type = ? and status=0 and timestampdiff(MINUTE,date_created,current_timestamp) <=120 order by ID desc limit 1";
		$result = $this->db->query($query,[$user_table_id,$otp,$userType]);
		if($result->getNumRows() <= 0){
			return false;
		}
		$result = $result->getResultArray();
		return $result;
	}

	/**
	 * @return void
	 */
	public function logout(){
		$this->webSessionManager->logout();
		$_SERVER['current_user'] = [];
		displayJson(true,"You've successfully logout");
    }

    /**
     * THis is to set the kyc document object based on the $currentUserObject
     * @param object $currentUserObj [description]
     * @param object $customer       [description]
     */
    private function setKycDocument(object $currentUserObj,object $customer){
    	$kycDoc = $this->getKycDocument($customer->ID);
    	$currentUserObj->kyc_updated = false;
    	$currentUserObj->kyc_approved = false;
    	if($kycDoc){
    		$currentUserObj->kyc_updated = true;
    		$currentUserObj->kyc_approved = $kycDoc['status'] == 0 ? false : true;
    		$currentUserObj->kyc_name = $kycDoc['document_name'];
    		$currentUserObj->kyc_document_path = $kycDoc['kyc_document_path'];
    	}
    	return $currentUserObj;
    }

	/**
	 * [profile description]
	 * @param  array|null  $args    [description]
	 * @param  string|null $apiType [description]
	 * @return [type]               [description]
	 */
	public function profile(array $args=null, string $apiType=null)
	{
		# check for get and post to the able to perform the necessary update as required
		$customer = getCustomer();
		if ($_SERVER['REQUEST_METHOD']=='GET') {
			if($customer){
				$this->setKycDocument($customer, $customer);
			}
			unset($customer->user_id,$customer->owner_id);
			displayJson(true,"success",$customer);
			return;
		}
		# this would mean to update the profile
		if ($_SERVER['REQUEST_METHOD']=='POST') {
			$userType = $this->validateUserType();
			$userTemp = $userType;
			$userType = loadClass($userType);
			$entityCreator = new \App\Models\EntityCreator($this->request);
			# remove email,status,date_created,password,they should not be editable
			$nonEditable = array('email','date_created','status');
			$param  = $this->request->getPost(null);
			foreach ($nonEditable as $value) {
				if(array_key_exists($value, $param)){
					unset($param[$value]);
				}
			}
			$entityCreator->outputResult=false;
			$result = $entityCreator->update($userTemp,$customer->ID,true,$param);
			$entityCreator->outputResult=true;
			if (!$result) {
				// displayJson(false,"error occured");
				return;
			}
			$newCustomer = new $userType(array('ID'=>$customer->ID));
			$newCustomer->load();
			$message = "You've successfully updated your profile";
			if(isset($param['address']) && isset($param['kyc_name'])){ # using address to know if hirer wanna become owner
				$this->createOwner($customer->user_id, $customer->ID);
				$newCustomer->is_owner = true;
			}
			$newCustomer->fcm_token = $customer->fcm_token;
			$newCustomer->user_type = $customer->user_type;
			$newCustomer->user_id = $customer->user_id;
			if($newCustomer){
				$this->setKycDocument($newCustomer, $customer);
			}
			$myResult = (object)$newCustomer;
			$_SERVER['current_user'] = $myResult;
			$myResult = $myResult->toArray();
			displayJson(true,$message,$myResult);
			return;
		}
	}

	/**
	 * This is to create owner
	 * @param  int      $user_id   [description]
	 * @param  int|null $hirers_id [description]
	 * @return [type]              [description]
	 */
	private function createOwner(int $user_id, int $hirers_id=null){
		$builder = $this->db->table('owners');
		$builder->replace(array('user_id'=>$user_id,'hirers_id'=>$hirers_id,'status'=>'1'));
	}

	/**
	 * This is to get the kyc document of users
	 * @param  int    $hirers_id [description]
	 * @return array            [description]
	 */
	private function getKycDocument(int $hirers_id){
		$builder = $this->db->table('kyc_document');
		$result = $builder->getWhere(['hirers_id'=>$hirers_id]);
		if($result->getNumRows() <= 0){
			return false;
		}
		return $result->getResultArray()[0];
	}

	/**
	 * This is to allow switch between owners and hirers and vice-versa
	 * @return [type] [description]
	 */
	public function switch_users(){
		$customer = getCustomer();
		$toggleUser = $this->request->getPost('toggle');
		$hirers_id = $this->request->getPost('hirers_id');
		if($customer->ID != $hirers_id){
			displayJson(false,"Oops, invalid operation");
			return;
		}
		if(!in_array($toggleUser, ['hirers', 'owners'])){
			displayJson(false, 'Oops, go back and use the allowed value');
			return;
		}
		$customer->user_table_id = $customer->ID;
		$customer->user_type = "hirers";
		if($toggleUser == 'hirers'){
			unset($customer->is_owner,$customer->owner_id);
			$payload['token'] = $this->generateToken((array)$customer);
			if($customer){
				$this->setKycDocument($customer, $customer);
			}
			unset($customer->user_table_id,$customer->user_id);
			$payload['details'] = $customer;
        	displayJson(true,'successfully switched to a hirers', $payload);
			return;
		}
		$owner = loadClass('owners');
		$param = ['user_id' => $customer->user_id, 'hirers_id' => $hirers_id,'status' => '1'];
		$owner = $owner->getWhere($param,$count,0,1,false);
		if(!$owner){
			displayJson(false,"Oops, you're not an owner or yet to be verified.");
			return;
		}
		# regenerate the token to contain the owners_id
		if($owner){
			$owner = $owner[0];
            $customer->is_owner = true;
            $customer->owner_id = $owner->ID;
        }
        $payload['token'] = $this->generateToken((array)$customer);
        $customer->user_type = "owners";
        if($customer){
        	$this->setKycDocument($customer, $customer);
        }
		unset($customer->user_table_id,$customer->user_id,$customer->owner_id);
		$payload['details'] = $customer;
        displayJson(true,'successfully switched to an owner', $payload);return;
	}

	/**
	 * This is to validate if the user is an owner
	 * @param  string $role [description]
	 * @return [type]       [description]
	 */
	private function validateUserRole(string $role)
	{
		if($role != 'owners'){
			displayJson(false, "Oops, invalid operation on endpoint");return;
		}
	}

	/**
	 * This approve booking request and update the pickup date as well
	 * @param  array|null  $args    [description]
	 * @param  string|null $apiType [description]
	 * @return [type]               [description]
	 */
	public function equip_approval(array $args=null, string $apiType=null)
	{
		$request_status = $this->request->getPost('request_status');
		$pickDate = $this->request->getPost('pick_date');
		# this is to know if it's coming from extend_approval section
		$extension = $this->request->getPost('extension');
		$allowedStatus = ['rejected','accepted'];
		$this->validateUserRole($apiType);
		if(!in_array($request_status, $allowedStatus)){
			$allowedStatus = implode("|", $allowedStatus);
			displayJson(false, "Sorry, '{$request_status}' not allowed e.g [$allowedStatus]");return;
		}

		$id = trim($args[0]);
		$builder = $this->db->table('equip_order');
		$date = $this->formatToUTC();
		$orderParam = ['order_status' => $request_status,'date_modified'=>$date];
		$data = [];
		if(!$extension){
			$pickDate = $this->formatToUTC($pickDate);
			$orderParam['pickup_date'] = $pickDate;
		}
		$builder->update($orderParam,['id' => $id]);
		if(!$extension){
			$builder = $this->db->table('equip_request');
			$builder->update(['request_status' => $request_status,'date_modified'=>$date],['equip_order_id' => $id]);
			$this->createDeliveryStatus($id); # could be moved to init_payment|verify_payment method

			$parseDate = Time::parse($pickDate);
			$parseDate = $parseDate->toLocalizedString('MMM d, yyyy');
			$data = ['pick_date'=>$parseDate];
		}else{
			$builder = $this->db->table('extend_equip_request');
			$builder->update(['request_status' => $request_status,'date_modified'=>$date],['equip_order_id' => $id]);
		}
		# send notification to the hirer on the status of their order
		if(!$this->sendNotification($id,$request_status,$data)){
			displayJson(false, 'something went wrong with the data');return;
		}
		displayJson(true, "You've successfully {$request_status} the request");return;
	}

	/**
	 * This would create the equip_delivery_status upon acceptance.
	 * However,wanting to use this once equip_payment had been made on that equip_order.
	 * So there is tendency to remove this from equip_approval method
	 * 
	 * @param  int    $equip_order_id [description]
	 * @return void
	 */
	private function createDeliveryStatus(int $equip_order_id){
		$equip_order = loadClass('equip_order');
		$equip_order->ID = $equip_order_id;
		if(!$equip_order->load()){
			$message = "equip_order is missing";
			return false;
		}
		$equipRequest = $equip_order->equip_request;
		$equipment_id = $equipRequest->equipments_id;
		if(!$equipment_id){
			$message = "something went wrong";
			return false;
		}
		$hirers_id = $equipRequest->hirers_id;
		$dateCreated = $this->formatToUTC();
		$builder = $this->db->table('equip_delivery_status');
		$statusResult = $builder->getWhere(['equip_order_id' => $equip_order_id]);
		if($statusResult->getNumRows() <= 0){
			$data = [
				'equipments_id' => $equipment_id,
				'hirers_id' => $hirers_id,
				'delivery_status' => 'pending',
				'equip_order_id' => $equip_order_id,
				'date_created' => $dateCreated,
				'date_modified' => $dateCreated
			];
			$builder->insert($data);
		}
	}

	/**
	 * [pickup_date description]
	 * @deprecated - This has been merged together with the method equip_approval
	 * @param  array|null  $args    [description]
	 * @param  string|null $apiType [description]
	 * @return JSON               [description]
	 */
	public function pickup_date(array $args=null, string $apiType=null)
	{
		$pickDate = $this->request->getPost('pick_date');
		$this->validateUserRole($apiType);
		$id = $args[0];
		$builder = $this->db->table('equip_order');
		$pickDate = $this->formatToUTC($pickDate);
		$date = $this->formatToUTC();
		$builder->update(['pickup_date' => $pickDate,'date_modified'=>$date],['id' => $id]);

		$parseDate = Time::parse($pickDate);
		$parseDate = $parseDate->toLocalizedString('MMM d, yyyy');

		# send notification to the hirer on the status of their order
		$this->sendNotification($id,'pick_up', ['pick_date'=>$parseDate]);
		displayJson(true, 'operation successful');return;
	}

	/**
	 * This is to send notification to hirers
	 * @param  int    	$equip_order_id [description]
	 * @param  string 	$type           [description]
	 * @param  array 	$info
	 * @return bool                 [description]
	 */
	private function sendNotification(int $equip_order_id, string $type, array $info=null){
		$equip_order = loadClass('equip_order');
		$equip_request = loadClass('equip_request');
		$equip_order->ID = $equip_order_id;
		$equip_order->load();

		$equip = ($equip_order->order_type == 'normal') ? $equip_order->equip_request : $equip_order->extend_equip_request->equip_request;
		$hirers = $equip_order->hirers;
		$hirers->fcm_token = $hirers->user->fcm_token; # updating the hirers

		$data = [
			'equipments_id' => $equip->equipments_id,
			'quantity' => $equip->quantity,
			'order_number' => $equip_order->order_number,
			'total_amount' => $equip_order->total_amount
		];

		if($info != null && isset($info['pick_date']) && $equip_order->order_type == 'normal'){
			$data['pick_date'] = $info['pick_date'];
		}
		# send push notification
		$notifyType = ($type == 'accepted') ? 'order_accept' : 'order_reject';
		$notifyData = ['equip_order_id'=>$equip_order_id,'fcm_token'=>$hirers->fcm_token];
		$equip_request->pushNotification($notifyType,$notifyData,true);

		return $equip_request->bookingNotification($hirers, $data, $type,'booking_request_status',true);
	}

	/**
	 * This is to send chat to the receipient
	 * @return [type] [description]
	 */
	public function send_chat(){
		# validate devices info using CI validation library
		$validation = \Config\Services::validation();
		$validation->setRules([
		        'sender_id' => 'required',
		        'receiver_id' => 'required',
		        'message' => 'required'
		]);
		# validation on $this->request
		if(!$validation->withRequest($this->request)->run()){
			// handle the error here
			if ($validation->hasError('sender_id')) {
			    displayJson(false,$validation->getError('sender_id'));return;
			}else if($validation->hasError('receiver_id')){
				displayJson(false,$validation->getError('receiver_id'));return;
			}else if($validation->hasError('message')){
				displayJson(false,$validation->getError('message'));return;
			}
		}
	    $sender_id = $this->request->getPost('sender_id');
	    $receiver_id = $this->request->getPost('receiver_id');
	    $message = $this->request->getPost('message');

	    $customer = getCustomer();
	    if(!$customer){
	    	displayJson(false,'something went wrong');return;
	    }
	    $customerID = $customer->ID;
	    if($sender_id == $customerID){
	        $type = 'sent';
	    }else{
	        $type = 'received';
	    }

	    $builder = $this->db->table('inbox');
	    $getInbox = $builder->select('*')
	    ->groupStart()
	    	->groupStart()
	    		->where('user_id', $sender_id)
	    		->where('chat_with_id', $receiver_id)
	    	->groupEnd()
	    	->orGroupStart()
	    		->where('user_id', $receiver_id)
	    		->where('chat_with_id', $sender_id)
	    	->groupEnd()
	    ->groupEnd()
	    ->get();

	    $message_count = 0;
	    # check if inbox exist between users and increment their message count,update
	    # their last message
	    if($getInbox->getNumRows() > 0){
	    	$getInbox = $getInbox->getResult()[0];
	    	$date = $this->formatToUTC();
	    	$builder = $this->db->table('inbox')
	    	->groupStart()
	    		->groupStart()
	    			->where('user_id', $sender_id)
	    			->where('chat_with_id', $receiver_id)
	    		->groupEnd()
	    		->orGroupStart()
	    			->where('user_id', $receiver_id)
	    			->where('chat_with_id', $sender_id)
	    		->groupEnd()
	    	->groupEnd()
	    	->update([
	    		'user_id' => $customerID,
	            'chat_with_id' => $receiver_id,
	            'message_count' => $getInbox->message_count + 1,
	            'last_message' => $message,
	            'date_modified' => $date
	    	]);
	        $inbox_id = $getInbox->ID;
	    }
	    # create inbox since it's the first time
	    else{
	    	$date = $this->formatToUTC();
	    	$param = [
	            'user_id' => $customerID,
	            'chat_with_id' => $receiver_id,
	            'message_count' => $message_count + 1,
	            'last_message' => $message,
	            'date_modified' => $date,
	            'date_created' => $date
	        ];
	    	$builder = $this->db->table('inbox');
	        $builder->insert($param);
	        $inbox_id = $this->db->insertID();
	    }
	    $channel = "chat_".$inbox_id; # using their inbox_id has their channel name

	    # send an event that would broadcast the channel
	    $data = [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message,
            'type' => $type,
            'inbox_id' => $inbox_id
        ];
	    $sendMessage = $this->broadCastChannel($channel, $data);
	    if($sendMessage){
	        displayJson(true,'message sent');return;
	    }else{
	        displayJson(false,'message not sent');return;
	    }
	}

	/**
	 * This would trigger the pusher to the channel
	 * @param  string $channel
	 * @param  array  $data
	 * @return bool
	 */
	private function broadCastChannel(string $channel, array $data){
		$options = array(
		   'cluster' => getenv('pusherAppCluster'),
		   'useTLS' => true
		);
		$pusher = new \Pusher\Pusher(
			getenv('pusherKey'),
			getenv('pusherAppSecret'),
			getenv('pusherAppID'),
			$options
		);
		$eventName = "ev_equip_message";
		if($pusher->trigger($channel,$eventName,$data)){
			if(!$this->createChat($data)){
				return false;
			}
			return true;
		}else{
			return false;
		}

	}

	/**
	 * This would create the chat in the table
	 * @param  array  $data
	 * @return bool
	 */
	private function createChat(array $data){
		$data['date_created'] = $this->formatToUTC();
		$data['date_modified'] = $this->formatToUTC();

		$builder = $this->db->table('chats');
		$builder->insert($data);

		$hirers = loadClass('hirers');
		$hirers->ID = $data['receiver_id'];
		if(!$hirers->load()){
			return false;
		}
		$fcm_token = $hirers->user->fcm_token;
		$message = "You just received a new message on Equipro.Kindly check our app for details";
		sendPushNotification($fcm_token,'New chat message on Equipro',$message);
		return true;
	}

	/**
	 * This would get all chatted user with each other
	 * @return [type] [description]
	 */
	public function get_user_inbox(){
		$customer = getCustomer();
		$hirers = loadClass('hirers');

		$inbox = $this->db->table('inbox');
		$inbox->where('user_id', $customer->ID);
		$inbox->orWhere('chat_with_id', $customer->ID);
		$inbox->orderBy('date_created', 'DESC');
		$user_chats = $inbox->get();
		if($user_chats->getNumRows() <= 0){
			$payload = [];
			displayJson(false,"Sorry, you don't have any chat available yet",$payload);
			return;
		}
		$user_chats = $user_chats->getResultArray();
		$payload = [];
		foreach ($user_chats as $chat) {
		   if($chat['user_id'] == $customer->ID){
		       $new_data = [
		            'id' => $chat['ID'],
		            'user_id' => $chat['user_id'],
		            'chat_with_id' => $chat['chat_with_id'],
		            'message_count' => $chat['message_count'],
		            'last_message' => $chat['last_message'],
		            'date_created' => $chat['date_created'],
		            'date_modified' => $chat['date_modified'],
		            'chat_with' => $hirers->getHirersByID($chat['chat_with_id'])
		        ];
		    }
		    else if($chat['chat_with_id'] == $customer->ID){
		        $new_data = [
		            'id' => $chat['ID'],
		            'user_id' => $chat['chat_with_id'],
		            'chat_with_id' => $chat['user_id'],
		            'message_count' => $chat['message_count'],
		            'last_message' => $chat['last_message'],
		            'date_created' => $chat['date_created'],
		            'date_modified' => $chat['date_modified'],
		            'chat_with' => $hirers->getHirersByID($chat['user_id'])
		        ];
		    }
		    array_push($payload, $new_data);
		}
		displayJson(true, 'Chats fetched successfully', $payload);
	}

	/**
	 * This would get individual chat history
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function get_chat_details(array $args){
		$queryParam = false;
		if($this->request->getGet(null) != null){
			$queryParam = true;
		}

		$data = null;
		$chats = loadClass('chats');
		if($queryParam){
			$data = $this->get_chat_history($chats);
		}else{
			$inbox_id = trim(urldecode($args[0]));
			if(!is_numeric($inbox_id) || !$inbox_id){
				displayJson(false, 'ID is required and must be integer');
				return;
			}
			$data = $chats->getWhereNonObject(['inbox_id'=>$inbox_id],$count,0,null,false,'order by date_created desc ');
		}
        if(!$data){
        	$payload = [];
        	displayJson(false,'Oops, there is no available chat between the users',$payload);
        	return;
        }
        return displayJson(true,'Chat Details fetched successfully',$data);
    }

    /**
     * This would get chat based on both the hirers_id
     * @param  object $chats [description]
     * @return array|false        [description]
     */
    private function get_chat_history(object $chats){
    	$user_1 = $this->request->getGet('user_1');
    	$user_2 = $this->request->getGet('user_2');
    	if($user_1 == null || $user_2 == null){
    		displayJson(false, 'user_1 and user_2 queryparam value must be supplied');
    		return;
    	}
        $data = $chats->getChatDetails($user_1,$user_2);
        return $data;
    }

	/**
	 * [formatToUTC description]
	 * @param  string|null $date [description]
	 * @return [type]            [description]
	 */
	private function formatToUTC(string $date=null){
		return formatToUTC($date);
	}

	/**
	 * This would be used to initiate payment on a order
	 * @return [type] [description]
	 */
	public function init_payment()
	{
		$order_id = $this->request->getPost('equip_order_id');
		$customer = getCustomer();
		if (!$order_id) {
			displayJson(false,"invalid parameters");
			return;	
		}
		# validate that the hirers owns the order_id
		$query = "select * from equip_order where hirers_id=? and ID=?";
		$res = $this->db->query($query,array($customer->ID,$order_id));
		if ($res->getNumRows() <= 0) {
			displayJson(false,'invalid order specified');
			return;
		}
		$receiptHash = generateHashRef('receipt');
		$insert = array(
			'receipt_ref' => $receiptHash,
			'equip_order_id' => $order_id,
			'hirers_id' => $customer->ID,
			'payment_status' => 'pending',
			'date_created' => formatToUTC()
		);
		$equip_payment = loadClass('equip_payment');
		$item = new $equip_payment($insert);
		if (!$item->insert()) {
			displayJson(false,'there is a problem getting payment details');
			return;
		}
		unset($insert['hirers_id'],$insert['payment_status']);
		displayJson(true,'operation successful',$insert);
		return;
	}

	public function verify_payment(){
		$order_id = $this->request->getPost('equip_order_id');
		$equip_payment = loadClass('equip_payment');
		$value = $equip_payment->getWhere(["equip_order_id"=>$order_id,'payment_status'=>'success'],$count,0,1,false);
		if (!$value) {
			displayJson(false,'Payment not yet verified');
			return;
		}
		displayJson(true,'Paymenet successfully verified');
		return;
	}

}

?>