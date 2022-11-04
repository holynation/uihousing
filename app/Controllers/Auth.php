<?php

namespace App\Controllers;

use App\Models\WebSessionManager;
use App\Models\Mailer;

/**
 * 
 */
class Auth extends BaseController
{
	private $webSessionManager;
	private $mailer;
	private $loggedIn = false;
	private $appBaseUrl = 'https://';

	protected $db;

	public function __construct()
	{

		helper(['string', 'url','form']);
		$this->webSessionManager = new WebSessionManager;
		$this->mailer = new Mailer;
		$this->db = db_connect();
	}

	public function index()
	{
		$this->login();
	}

	public function signup($data = ''){
		$data = [];
		$data['db'] = $this->db;
		return view('housing/register', $data);
	}

	public function login($data = '')
	{
		return view('housing/login', [$data]);
	}

	public function forget($data = ''){
		return view('housing/forget_password', [$data]);
	}

	public function register(){
		if($this->inputValidate()){
			$data = $this->request->getPost(null);
			// print_r($data);exit;

			$staffnumber = trim($data['staff_number'] ?? "");
			$firstname = trim($data['firstname'] ?? "");
			$lastname = trim($data['lastname'] ?? "");
			$designation = trim($data['designation'] ?? "");
			$appointment_status = trim($data['appointment_status'] ?? "");
			$email = trim(@$data['email'] ?? "");
			$fpassword = trim($data['password']);
			$cpassword = trim($data['confirm_password']);

			if (!isNotEmpty($firstname,$lastname,$staffnumber,$designation,$appointment_status,$email,$fpassword,$cpassword)) {
				$arr['status'] = false;
				$arr['message'] = 'All field are required';
				echo json_encode($arr);
				return;
			}

			if(filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE){
				$arr['status'] = false;
				$arr['message'] = 'Email is not valid';
				echo json_encode($arr);
				return;
			}

			if($fpassword !== $cpassword){
				$arr['status'] = false;
				$arr['message'] = 'New password must match Confirm password...';
				echo json_encode($arr);
				return;
			}
			$user = loadClass('user');
			$result = $user->createUser($data);

			switch ($result) {
				case 1:
					$arr['status'] = true;
					$arr['message']= 'Your have successfully register.You can now sign-in';
					echo json_encode($arr);
					return;
				break;

				case 2:
				$arr['status'] = false;
					$arr['message']= "error occurred while registering";
					echo json_encode($arr);
					return;
				break;

				case 3:
					$arr['status'] = false;			
					$arr['message']= "email already registered on the platform.";
					echo json_encode($arr);
					return;
				break;

				case 4:
					$arr['status'] = false;			
					$arr['message']= "error occurred while registering,please try again";
					echo json_encode($arr);
					return;
				break;
			}
		}
		$this->signup();
	}

	public function web()
	{
		if ($this->inputValidate()) {
			$username = $this->request->getPost('email');
			$password = $this->request->getPost('password');
			$remember = null;
			$isAjax =  (isset($_POST['isajax']) && $_POST['isajax'] == "true") ? true : false;

			if (!isNotEmpty($username, $password)) {
				if ($isAjax) {
					echo createJsonMessage('status', false, 'message', "Please fill all field and try again");
					return;
				} else {
					$this->webSessionManager->setFlashMessage('error', 'Please fill all field and try again');
					redirect(base_url('auth/login'));
				}
			}
			$user = loadClass('user');
			$array = array('username' => $username, 'status' => 1);
			$user = $user->getWhere($array, $count, 0, 1, false);
			if ($user) {
				$checkPass = decode_password(trim($password), $user[0]->password);
				if (!$checkPass) {
					if ($isAjax) {
						$arr['status'] = false;
						$arr['message'] = "invalid username or password";
						echo  json_encode($arr);
						return;
					} else {
						$this->webSessionManager->setFlashMessage('error', 'invalid username or password');
						redirect(base_url('auth/login'));
					}
				}
				$user = $user[0];
				if($user->user_type != 'admin' && $user->user_type != 'staff'){
					$arr['status'] = true;
					$arr['message'] = 'Oops, invalid username or password';
					echo  json_encode($arr);
					return;
				}
				$baseurl = base_url() . '/';
				$this->webSessionManager->saveCurrentUser($user, true);
				$baseurl .= $this->getUserPage($user);
				if ($isAjax) {
					$arr['status'] = true;
					$arr['message'] = $baseurl;
					echo  json_encode($arr);
					return;
				} else {
					redirect($baseurl);
					exit;
				}
			}
			else {
				if ($isAjax) {
					$arr['status'] = false;
					$arr['message'] = 'Invalid username or password';
					echo json_encode($arr);
					exit;
				} else {
					$this->webSessionManager->setFlashMessage('error', 'invalid username or password');
					redirect(base_url('auth/login'));
				}
			}
		}

		$this->login();
	}

	public function forgetPassword(){
		if(isset($_POST) && count($_POST) > 0 && !empty($_POST)){
			if($_POST['task'] == 'reset'){
				$email = trim($this->request->getPost('email'));

				if (!isNotEmpty($email)) {
			        echo createJsonMessage('status',false,"message","empty field detected.please fill all required field and try again");
			        return;
			    }
				if(filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE){
					$arr['status'] = false;
					$arr['message'] = 'email is not valid';
					echo json_encode($arr);
					return;
				}
				$user = loadClass('user');
				$find = $user->find($email);
				if(!$find){
					$arr['status'] = false;
					$arr['message'] = 'the email address appears not to be on our platform...';
					echo json_encode($arr);
					return;
				}

				$sendMail = ($this->mailer->sendCustomerMail($email,'password_reset',3,$email)) ? true : false;
				$message = "A link to reset your password has been sent to $email.If you don't see it, be sure to check your spam folders too!";
				$arr['status'] = ($sendMail) ? true : false;
				$arr['message'] = ($sendMail) ? $message : 'error occured while performing the operation, please check your network and try again later.';
				echo json_encode($arr);
				return;
			}
			// this is for when resetting password
			else if ($_POST['task'] == 'update'){
				if(isset($_POST['email'], $_POST['email_hash']))
                {
                	if($_POST['email_hash'] !== sha1($_POST['email'] . $_POST['email_code'])){
                		// either a hacker or they changed their mail in the mail field, just die
	                    $arr['status'] = false;
						$arr['message'] = 'Oops,error updating your password';
						echo json_encode($arr);
						return;
                	}
                	$new = $this->request->getPost('password');
                	$confirm = $this->request->getPost('confirm_password');
                	$email = $this->request->getPost('email');
				    $dataID = $email;

				    if (!isNotEmpty($new,$confirm)) {
				        echo createJsonMessage('status',false,"message","empty field detected.please fill all required field and try again");
				        return;
				    }

				    if ($new !== $confirm) {
				       echo createJsonMessage('status',false,'message','new password does not match with the confirmation password');return;
				    }
				    $user = loadClass('user');
				    $updatePassword = $user->updatePassword($dataID,$new,'staff');
				    $customerName = $email;

				    if($updatePassword){
				    	$arr['status'] = true;
						$arr['message'] = 'your password has been reset! You may now login.';
						echo json_encode($arr);
						return;
				    }else{
						$arr['status'] = false;
						$arr['message'] = "error occured while updating your password. Please contact Administrator";
						echo json_encode($arr);
						return;
				    }
                    
                }
                
			}
		}
		$this->forget();
	}

	/**
	 * This is invoke when user click the verification link in their email account
	 * @param string $email
	 * @param string $hash
	 * @param string type
	 * @return array
	 */
	public function verify($email,$hash,$type){
		if(isset($email,$hash,$type)){
			$email = urldecode(trim($email));
			$email = str_replace(array('~az~','~09~'),array('@','.com'),$email);
			$hash = urldecode(trim($hash));
			$email_hash = sha1($email . $hash);
			$expireTime = rndDecode(@$_GET['tk']);
			$task = rndDecode(@$_GET['task']);
			$currentTime = time();
			if($task != 'verify'){
				$data['error'] = 'It seems like the link had broken, kindly re-click or copied the right link.';
				return view('verify',$data);
			}

			if(isTimePassed($currentTime,$expireTime)){
				$data['error'] = 'Oops an invalid or expired link was provided.Kindly reached out to the administrator';
				return view('verify',$data);
			}
			$user = loadClass('user');
			$tempUser = $user->find($email);
			$data = array();
			if(!$tempUser){
				$data['error'] = 'sorry we don\'t seems to have that email account on our platform.';
				return view('verify',$data);
			}

			$check = md5(appConfig('salt') . $email) == $hash;
			if(!$check){
				$data['error'] = 'there seems to be an error in validating your email account,try again later.';
				return view('verify',$data);return;
			}

			if($tempUser && $check){
				$mailType = appConfig('type');
				if($mailType[$type] == 'verify_account'){
					$id = $user->data()[0]['ID'];
					$userType = $user->data()[0]['user_type'];
					$result = $user->updateStatus($id,$email);
					$data['type'] = $mailType[$type];
					if($result){
						//sending the login details to users
						$param = ['customerName'=>$email];
						$template = $this->mailer->mailTemplateRender($param,'account_created_individual');
						$sendMail = ($this->mailer->sendCustomerMail($email,'welcome')) ? true : false;
						if($sendMail){
							$data['success'] = "Your Account has been verified, welcome on board.<br /><br />Thank you!";
						}else{
							$data['error'] = 'There is an error in network connection, please try again later...';
						}
						 
					}else{
						$data['error'] = 'There seems to be an error in performing the operation...';
					}
				}
				else if($mailType[$type] == 'forget'){
					$data['type'] = $mailType[$type];
					$data['email_hash'] = $email_hash;
					$data['email_code'] = $hash;
					$data['email'] = $email;
				}
				return view('verify',$data);
			}
		}
		$this->forget();
	}

	/**
	 * [inputValidate description]
	 * @return bool
	 */
	private function inputValidate(){
    	return isset($_POST) && count($_POST) > 0 && !empty($_POST) ?? false;
	}

	/**
	 * This is to return the user based dashboard
	 * 
	 * @param  string $user
	 * @return string
	 */
	private function getUserPage($user)
	{
		$link = array('admin' => 'vc/admin/dashboard','staff'=>'vc/staff/dashboard');
		$roleName = $user->user_type;
		return $link[$roleName];
	}

	public function logout()
	{
		$link = '';
		$base = base_url();
		$this->webSessionManager->logout();
		$path = $base . $link;
		header("location:$path");
		exit;
	}
}
