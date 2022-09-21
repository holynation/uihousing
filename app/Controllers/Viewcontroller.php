<?php
namespace App\Controllers;

use App\Entities\Customer;
use App\Models\WebSessionManager;
use App\Models\ModelFormBuilder;
use App\Models\TableWithHeaderModel;
use App\Models\QueryHtmlTableModel;
use App\Models\QueryHtmlTableObjModel;
use CodeIgniter\I18n\Time;

class Viewcontroller extends BaseController{

  private $errorMessage; // the error message currently produced from this cal if it is set, it can be used to produce relevant error to the user.
  private $access = array();
  private $appData;

  private $modelFormBuilder;
  private $webSessionManager;
  private $tableWithHeaderModel;
  protected $db;
  private $adminData;
  private $customerData;
  private $companyData;

  private $crudNameSpace = 'App\Models\Crud';

  public function __construct(){
    helper(['array','string']);

    $this->db = db_connect();
    $this->webSessionManager = new WebSessionManager;
    $this->modelFormBuilder = new ModelFormBuilder;
    $this->tableWithHeaderModel = new TableWithHeaderModel;
    $this->queryHtmlTableModel = new QueryHtmlTableModel;
    $this->queryHtmlTableObjModel = new QueryHtmlTableObjModel;

    $this->adminData = new \App\Models\Custom\AdminData();

    if (!$this->webSessionManager->isSessionActive()) {
      header("Location:".base_url());exit;
    }
	}

// bootstrapping functions 
public function view($model,$page='index',$third='',$fourth=''){
  if ( !(file_exists(APPPATH."Views/$model/") && file_exists(APPPATH."Views/$model/$page".'.php')))
  {
    throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
  }
  # this number is the default arg that ID is the last arg i.e 3 = id

  $defaultArgNum = 4;
  if($defaultArgNum < func_num_args()){
    $data['extra'] = func_get_args();
    $data['entityName'] = ($fourth != '') ? $third : "";
  }else{
    $modelID = ($fourth == '') ? $third : $fourth;
    $data['id'] = urldecode($modelID);
    $data['entityName'] = ($fourth != '') ? $third : "";
  }
  $tempTitle = removeUnderscore($model);
  $title = $page=='index'?$tempTitle:ucfirst($page)." $tempTitle";
  $exceptions = array();//pages that does not need active session

  if (!in_array($page, $exceptions)) {
    if (!$this->webSessionManager->isSessionActive()) {
      redirect(base_url());exit;
    }
  }

  if (method_exists($this, $model)) {
    $this->$model($page,$data);
  }
  $methodName = $model.ucfirst($page);

  if (method_exists($this, $model.ucfirst($page))) {
    $this->$methodName($data);
  }

  $data['model'] = $page;
  $data['message'] = $this->webSessionManager->getFlashMessage('message');
  $data['webSessionManager'] = $this->webSessionManager;
  // sendPageCookie($model,$page);

  echo view("$model/$page", $data);
}

private function admin($page,&$data)
{
  $role_id=$this->webSessionManager->getCurrentUserProp('role_id');
  if (!$role_id) {
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  }

  $role=false;
  if ($this->webSessionManager->getCurrentUserProp('user_type')=='admin') {
    $admin = loadClass('admin');
    $admin->ID = $this->webSessionManager->getCurrentUserProp('user_table_id');
    $admin->load();
    $data['admin'] = $admin;
    $role = $admin->role;
    if(!$role){
      exit("Kindly ensure a role is assigned to this admin user");
    }
  }
  $data['currentRole'] = $role;
  if (!$role) {
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  }
  $path ='vc/admin/'.$page;

  # this approach is use so as to allow this page pass through using a path that is already permitted
  if ($page=='permission') {
    $path ='vc/create/role';
  }

  if($page == 'view_model' || $page == 'extend_equip_request' 
    || $page == 'equip_delivery_status' || $page == 'view_more'){
    $path = 'vc/admin/view_model/equip_request?type=approved';
  }
  if (!$role->canView($path)) {
    echo show_access_denied();exit;
  }
  # caching this role pages
  if(!$canView = cache('canView')){
    $canView = $this->adminData->getCanViewPages($role);
    cache()->save('canView',$canView,900); # cache for 15mins
  }
  $data['canView'] = $canView;
}

private function adminDashboard(&$data)
{
 $data = array_merge($data,$this->adminData->loadDashboardData());
}

private function adminPermission(&$data)
{
  $data['id'] = urldecode($data['id']);
  if (!isset($data['id']) || !$data['id'] || $data['id']==1) {
    $this->show_404();exit;
  }
  $role = loadClass('role');
  $newRole = new $role(array('ID'=>$data['id']));
  $newRole->load();
  $data['role'] = $newRole;
  $data['allPages'] = $this->adminData->getAdminSidebar(true);
  $sidebarContent = $this->adminData->getCanViewPages($data['role'],true);
  // print_r($sidebarContent);exit;
  $data['permitPages'] = $sidebarContent;
  $data['allStates'] = $data['role']->getPermissionArray();
  $data['pageTitle'] = 'permission';
  $data['modelName'] = 'Permission';
}

private function adminProfile(&$data)
{
  $admin = loadClass('admin');
  $admin = new $admin();
  $admin->ID=$this->webSessionManager->getCurrentUserProp('user_table_id');
  $admin->load();
  $data['admin']=$admin;
}

/**
 * This would ensure that custom query on table model is allowed based
 * on different type of the entity
 * @param  [type] &$data [description]
 * @return [type]        [description]
 */
private function adminView_model(&$data){
  $id = (is_numeric($data['id'])) ? $data['id'] : null;
  $model  = ($data['entityName']) ? $data['entityName'] : $data['id'];
  $newModel = loadClass($model);
  $modelType = '';
  $modelType = $_GET['type'] ?? "";
  
  $pageTitle = $this->getTitlePage($model) ?? removeUnderscore($model);
  if($model == 'owners' && $id){
    $pageTitle = 'Owners Equipments';
  }
  else if($model == 'hirers' && $id){
    $pageTitle = 'Hirers Bookings';
  }

  $result = $newModel->viewList($id,$modelType);
  $data['modelName'] = $model;
  $data['pageTitle'] = $pageTitle;
  $data['queryString'] = $result;
  $data['dataParam'] = $id;
  $data['queryHtmlTableObjModel'] = $this->queryHtmlTableObjModel;
}

private function adminView_more(&$data){
  $id = $data['extra'][4] ?? null;
  if(!$id){
    $data['modelStatus'] = false;
    $data['modelPayload'] = [];
    $data['pageTitle'] = null;
  }
  $modelType = $data['extra'][3]; # this is the index of the type
  $modelName = $data['entityName'];
  $newModel = loadClass($modelName);

  $result = $newModel->viewList($id,$modelType,1,true);
  $data['pageTitle'] = $this->getTitlePage($modelName).' '.$modelType ?? removeUnderscore($modelName);
  $data['modelStatus'] = (!$result) ? false : true;
  $data['modelPayload'] = $result;
  $data['modelName'] = $modelName;
  $data['modelInfo'] = false;

  if($modelName == 'equip_request'){
    $equip_request = loadClass('equip_request');
    $equip_request->ID = $id;
    if($equip_request->load()){
      $data['modelInfo'] = true;
      $data['equipHirers'] = $equip_request->hirers;
      $data['equipOwners'] = $equip_request->equipments->owners->hirers;
      $data['equipImages'] = $equip_request->equipments->equip_images;
    }
  }
  else if($modelName == 'equipments'){
    $entity = ($modelType == 'all') ? loadClass('equipments') : loadClass('equip_request');
    $entity->ID = $id;
    if($entity->load()){
      $data['modelInfo'] = true;
      $data['equipImages'] = ($modelType == 'all') ? $entity->equip_images : $entity->equipments->equip_images;
    }
  }
}

private function getTitlePage(string $modelName){
  $result = [
    'equip_request'=>'equipment bookings',
    'equipments' => $this->request->getGet('type').' '.'equipments',
  ];
  return array_key_exists($modelName, $result) ? $result[$modelName] : null;
}

private function adminExtend_equip_request(&$data){
  $id = $data['id'];
  if(!$id){
    $this->show_404();exit;
  }
  $extend = loadClass('extend_equip_request');
  $result = $extend->getEquipExtendedDetails($id);
  $hirers = $result->hirers ?? null;
  $data['modelStatus'] = $result ? true : false;
  $data['modelPayload'] = $result ? $result->toArray() : null;
  $data['hirers'] = $hirers ? $hirers->toArray() : null;
}

private function adminEquip_delivery_status(&$data){
  $id = $data['id'];
  if(!$id){
    $this->show_404();exit;
  }
  $delivery = loadClass('equip_delivery_status');
  $equipOrder = loadClass('equip_order');
  $result = $delivery->getDeliveryStatus($id,true);
  $equipOrder->ID = $id;
  if(!$equipOrder->load()){
    exit('order info not available');
  }
  $equip = $equipOrder->equip_request->equipments;
  $equipName = $equip->equip_name;
  $equipOwner = $equip->owners->hirers->fullname;
  $data['time'] = new Time;
  $data['modelStatus'] = (!empty($result)) ? true : false;
  $data['modelPayload']['deliveryInfo'] = (!empty($result)) ? $result : null;
  $data['modelPayload']['equipName'] = $equipName;
  $data['modelPayload']['ownerName'] = $equipOwner;
  $data['modelPayload']['equipOrder'] = $equipOrder;
}

private function adminEquipments(&$data){
  $equipImages = loadClass('equipments');
  $result = $equipImages->getAllEquips();
  $data['modelStatus'] = $result ? true : false;
  $data['modelPayload'] = $result ? $result : null;
  $data['scopeObject'] = $this->db;
}

private function adminKyc_document(&$data){
  $equipImages = loadClass('kyc_document');
  $result = $equipImages->getAllKycs();
  $data['modelStatus'] = $result ? true : false;
  $data['modelPayload'] = $result ? $result : null;
  $data['scopeObject'] = $this->db;
  $data['timeObject'] = new Time;
}

private function adminChats(&$data){
  
}

private function hirers($page,&$data){
  $customer = loadClass('hirers');
  $this->customer->ID = $this->webSessionManager->getCurrentUserProp('user_type')=='admin'?$data['id']:$this->webSessionManager->getCurrentUserProp('user_table_id');
  $customer->load();
  $this->customerData->setCustomer($customer);
  $data['customer']=$customer;
}

private function hirersDashboard(&$data){
  $data = array_merge($data,$this->customerData->loadDashboardInfo());
}

public function hirersProfile(&$data)
{
  if ($this->webSessionManager->getCurrentUserProp('user_type')=='admin') {
    $this->admin('profile',$data);
    if (!isset($data['id']) || !$data['id']) {
      $this->show_404();exit;
    }
    $std = new Customer(array('ID'=>$data['id']));
    if (!$std->load()) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    $data['customer']=$std;
  }
}

//function for loading edit page for general application
public function edit($model,$id){
  $userType = $this->webSessionManager->getCurrentUserProp('user_type');
  if($userType == 'admin'){
    $admin = loadClass('admin');
    $admin->ID = $this->webSessionManager->getCurrentUserProp('user_table_id');
    $admin->load();
    $role = $admin->role;
    $role->checkWritePermission();
    $role = true;
  }
  
  $ref = @$_SERVER['HTTP_REFERER'];
  if ($ref && !startsWith($ref,base_url())) {
    $this->show_404();
  }
  $exceptionList = array('user');//array('user','applicant','student','staff');
  if (empty($id) || in_array($model, $exceptionList)) {
    $this->show_404();
  }
  $formConfig = new \App\Models\FormConfig($role);
  $configData = $formConfig->getUpdateConfig($model);
  $exclude = ($configData && array_key_exists('exclude', $configData))?$configData['exclude']:[];

  # added this to switch owners details to hirers since they are related
  if($model == 'owners'){
    $owners = loadClass('owners');
    $owners->ID = $id;
    $owners->load();
    $hirers = $owners->hirers;
    $model = 'hirers';
    $id = $hirers->ID;
  }

  $formContent = $this->modelFormBuilder->start($model.'_edit')
      ->appendUpdateForm($model,true,$id,$exclude,'')
      ->addSubmitLink(null,false)
      ->appendSubmitButton('Update','btn btn-success')
      ->build();
  $result = $formContent;
  displayJson(true,$result);
  return;
}

private function show_404(){
  throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
}

# this method is for creation of form either in single or combine based on the page desire
public function create($model,$type='add',$data=null){
  if(!empty($type)){
    if($type=='add'){
      // this is useful for a page that doesn't follow normal procedure of a modal page
      $this->add($model,'add');
      return;
    }else{
      // this uses modal to show it content
      $this->add($model,$type,$data);
      return;
    }
  }
  return "please specify a type to be created (single page or combine page with view inclusive...)";
}

private function add($model,$type,$param=null)
{
  if (!$this->webSessionManager->isSessionActive()) {
    header("Location:".base_url());exit;
  }
  $role_id=$this->webSessionManager->getCurrentUserProp('role_id');
  $userType=$this->webSessionManager->getCurrentUserProp('user_type');
  if($userType == 'admin'){
    if (!$role_id) {
      $this->show_404();
    }
  }
  $role =false;
  if($userType == 'admin'){
    $admin = loadClass('admin');
    $admin->ID = $this->webSessionManager->getCurrentUserProp('user_table_id');
    $admin->load();
    $role = $admin->role;
    $data['admin']=$admin;
    $data['currentRole']=$role;
    $type = ($type == 'add') ? 'create' : $type;
    $path ="vc/$type/".$model;

    if (!$role->canView($path)) {
      echo show_access_denied();exit;
    }
    $type = ($type == 'create') ? 'add' : $type;
    $sidebarContent=$this->adminData->getCanViewPages($role);
    $data['canView']=$sidebarContent;
    $role = true;
  }else if($userType == 'customer'){
    $customer = loadClass('customer');
    $customer->ID = $this->webSessionManager->getCurrentUserProp('user_table_id');
    $customer->load();
    $role = true;
    $data['customer']=$customer;
  }

  if ($model==false) {
    $this->show_404();
  }

  $newModel = loadClass($model);
  $modelClass = new $newModel;
  if (!is_subclass_of($modelClass ,$this->crudNameSpace)) {
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
  }
  $formConfig = new \App\Models\FormConfig($role);
  $data['configData']=$formConfig->getInsertConfig($model);
  $data['model']=$model;
  $data['modelObj']=$newModel;
  $data['appConfig']=$this->appData;

  // defining some object parameters
  $data['db'] = $this->db;
  $data['webSessionManager'] = $this->webSessionManager;
  $data['queryHtmlTableObjModel'] = $this->queryHtmlTableObjModel;
  $data['tableWithHeaderModel'] = $this->tableWithHeaderModel;
  $data['modelFormBuilder'] = $this->modelFormBuilder;
  echo view("$type",$data);
}

public function changePassword()
{
  if(isset($_POST) && count($_POST) > 0 && !empty($_POST)){
    $curr_password = trim($_POST['current_password']);
    $new = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if (!isNotEmpty($curr_password,$new,$confirm)) {
      echo createJsonMessage('status',false,'message',"empty field detected.please fill all required field and try again");
      return;
    }
    
    $id= $this->webSessionManager->getCurrentUserProp('ID');
    $user = loadClass('user');
    if($user->findUserProp($id)){
      // $check = md5(trim($curr_password)) == $user->data()[0]['password'];
      $check = decode_password(trim($curr_password), $user->data()[0]['password']);
      if(!$check){
        echo createJsonMessage('status',false,'message','please type-in your password correctly...','flagAction',false);
        return;
      }
    }

    if ($new !==$confirm) {
      echo createJsonMessage('status',false,'message','new password does not match with the confirmation password','flagAction',false);exit;
    }
    // $new = md5($new);
    $new = encode_password($new);
    $passDate = date('Y-m-d H:i:s');
      $query = "update user set password = '$new',last_change_password = '$passDate' where ID=?";
      if ($this->db->query($query,array($id))) {
        $arr['status']=true;
        $arr['message']= 'operation successfull';
        $arr['flagAction'] = true;
        echo json_encode($arr);
        return;
      }
      else{
        $arr['status']=false;
        $arr['message']= 'error occured during operation...';
        $arr['flagAction'] = false;
        echo json_encode($arr);
        return;
      }
  }
  return false;
}

}
