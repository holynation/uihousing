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

		helper(['string', 'url']);
		$this->webSessionManager = new WebSessionManager;
		$this->mailer = new Mailer;
		$this->db = db_connect();
	}

	public function index()
	{
		$this->login();
	}

	public function login($data = '')
	{
		return view('equipro/login', [$data]);
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
			$find = $user->findBoth($username);
			if ($find) {
				$checkPass = decode_password(trim($password), $user->data()[0]['password']);
				if (!$checkPass) {
					if ($isAjax) {
						$arr['status'] = false;
						$arr['message'] = "invalid email or password";
						echo  json_encode($arr);
						return;
					} else {
						$this->webSessionManager->setFlashMessage('error', 'invalid email or password');
						redirect(base_url('auth/login'));
					}
				}
				$array = array('username' => $username, 'status' => 1, 'user_type' => 'admin');
				$user = $user->getWhere($array, $count, 0, null, false);
				if ($user == false) {
					if ($isAjax) {
						$arr['status'] = false;
						$arr['message'] = "Invalid email or password";
						echo json_encode($arr);
						return;
					} else {
						$this->webSessionManager->setFlashMessage('error', 'invalid email or password');
						redirect(base_url('/auth/login'));
					}
				} else {
					$user = $user[0];
					if($user->user_type != 'admin'){
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
			} else {
				if ($isAjax) {
					$arr['status'] = false;
					$arr['message'] = 'Invalid email or password';
					echo json_encode($arr);
					exit;
				} else {
					$this->webSessionManager->setFlashMessage('error', 'invalid email or password');
					redirect(base_url('auth/login'));
				}
			}
		}

		$this->login();
	}

	/**
	 * This is to return the user based dashboard
	 * 
	 * @param  string $user
	 * @return string
	 */
	private function getUserPage($user)
	{
		$link = array('admin' => 'vc/admin/dashboard','hirers'=>'vc/hirers/dashboard');
		$roleName = $user->user_type;
		return $link[$roleName];
	}

	/**
	 * [inputValidate description]
	 * @return bool
	 */
	private function inputValidate(){
    	return isset($_POST) && count($_POST) > 0 && !empty($_POST) ?? false;
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
							$builder = $this->db->table('notification_setting');
							$builder->replace(array('user_id'=>$id));
							$data['success'] = "Your Account has been verified, welcome on board.<br /><br />Thank you!";
						}else{
							$data['error'] = 'There is an error in network connection, please try again later...';
						}
						 
					}else{
						$data['error'] = 'There seems to be an error in performing the operation...';
					}
				}else if($mailType[$type] == 'forget'){
					$data['type'] = $mailType[$type];
					$data['email_hash'] = $email_hash;
					$data['email_code'] = $hash;
					$data['email'] = $email;
				}
				return view('verify',$data);
			}
			
		}
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
