<?php
namespace App\Controllers;

use App\Entities\Customer;
use App\Models\WebSessionManager;
use App\Models\ModelFormBuilder;
use App\Models\TableWithHeaderModel;
use App\Models\QueryHtmlTableModel;
use App\Models\QueryHtmlTableObjModel;
use App\Models\Custom\AdminData;
use App\Models\Custom\StaffData;
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
  private $staffData;
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

    $this->adminData = new AdminData();

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
  $role = false;
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

  // this approach is use so as to allow this page pass through using a path that is already permitted
  if ($page == 'permission') {
    $path ='vc/create/role';
  }

  if($page == 'view_model' || $page == 'view_more'
    || $page == 'children' || $page == 'occupant' || $page == 'print_application'
  ){
    $path = 'vc/admin/view_model/staff';
  }
  if (!$role->canView($path)) {
    echo show_access_denied();exit;
  }
  // caching this role pages
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
  $admin->ID = $this->webSessionManager->getCurrentUserProp('user_table_id');
  $admin->load();
  $data['admin'] = $admin;
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
  $modelType = $data['extra'][3]; // this is the index of the type
  $modelName = $data['entityName'];
  $newModel = loadClass($modelName);

  $result = $newModel->viewList($id,$modelType,1,true);
  $data['pageTitle'] = $this->getTitlePage($modelName).' '.$modelType ?? removeUnderscore($modelName);
  $data['modelStatus'] = (!$result) ? false : true;
  $data['modelPayload'] = $result;
  $data['modelName'] = $modelName;
  $data['modelInfo'] = false;
}

private function adminApplicant_allocation(&$data){
  $allocation = loadClass('applicant_allocation');
  $pager = service('pager');
  $page    = (int) ($this->request->getGet('page') ?? 1);
  $perPage = 20;
  $total   = $allocation::totalCount();

  $start =  ($page > 1) ? ($page * $perPage) : $page;
  $where = "where applicant_status = 'pending'";
  if($this->request->getGet('category')){
    $where .= " and category_id = '{$this->request->getGet('category')}'";
  }
  $payload = $allocation->all($count,false,$start,$perPage,'order by date_created desc',$where);

  // Call makeLinks() to make pagination links.
  $pager_links = $pager->makeLinks($page, $perPage, $total, 'front_full');

  $data['pager_links'] = $pager_links;
  $data['modelStatus'] = ($payload) ? true : false;
  $data['modelPayload'] = $payload;
  $data['db'] = $this->db;
}

private function adminAllocation(&$data){
  $allocation = loadClass('allocation');
  $payload = $allocation->all($count,false,0,null,'order by date_created desc');

  $data['modelStatus'] = $payload ? true : false;
  $data['modelPayload'] = $payload;
}

private function getTitlePage(string $modelName){
  $result = [
    'staff' => 'staff'
  ];
  return array_key_exists($modelName, $result) ? $result[$modelName] : null;
}

private function staff($page,&$data){
  $this->staffData = new StaffData;
  $staff = loadClass('staff');
  if($this->webSessionManager->getCurrentUserProp('user_type') == 'staff'){
    $staff->ID = $this->webSessionManager->getCurrentUserProp('user_type')=='admin'?$data['id']:$this->webSessionManager->getCurrentUserProp('user_table_id');
    $staff->load();
    $this->staffData->setStaff($staff);
    if($this->webSessionManager->getCurrentUserProp('has_change_password') == 0){
      $data['hasChangePassword'] = $this->webSessionManager->getCurrentUserProp('has_change_password');
    }
    $data['staff'] = $staff;
  }
  else{
    $this->admin('children',$data);
  }
}

private function staffDashboard(&$data){
  $data = array_merge($data,$this->staffData->loadDashboardInfo());
}

public function staffProfile(&$data)
{
  if ($this->webSessionManager->getCurrentUserProp('user_type')=='admin') {
    $this->admin('profile',$data);
    if (!isset($data['id']) || !$data['id']) {
      $this->show_404();exit;
    }
    $std = new Staff(array('ID'=>$data['id']));
    if (!$std->load()) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    $data['staff'] = $std;
  }
  $data['db'] = $this->db;
}

public function staffChildren(&$data){
  $data['tableWithHeaderModel'] = $this->tableWithHeaderModel;
  $data['modelFormBuilder'] = $this->modelFormBuilder;
}

private function staffApply(&$data){
  $allocation = loadClass('applicant_allocation');
  $id = $data['staff']->ID;
  $payload = $allocation->allNonObject($count,true,0,null,''," where staff_id='{$id}'");

  $data['modelStatus'] = $payload ? true : false;
  $data['modelPayload'] = $payload;
  $data['modelFormBuilder'] = $this->modelFormBuilder;
  $data['tableWithHeaderModel'] = $this->tableWithHeaderModel;
  $data['webSessionManager'] = $this->webSessionManager;
}

private function staffPrint_application(&$data){
  if ($this->webSessionManager->getCurrentUserProp('user_type')=='admin') {
    $this->admin('print_application',$data);
  }
  $id = $data['id'];
  $allocation = loadClass('applicant_allocation');
  $allocation->ID = $id;
  if(!$allocation->load()){
    return redirect()->back()->with('error', "Allocation data can't be found");
  }
  $data['allocation'] = $allocation;
}

public function staffTenant(&$data){
  $data['tableWithHeaderModel'] = $this->tableWithHeaderModel;
  $data['modelFormBuilder'] = $this->modelFormBuilder;
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
      $check = decode_password(trim($curr_password), $user->data()[0]['password']);
      if(!$check){
        echo createJsonMessage('status',false,'message','please type-in your password correctly...','flagAction',false);
        return;
      }
    }

    if ($new !== $confirm) {
      echo createJsonMessage('status',false,'message','new password does not match with the confirmation password','flagAction',false);exit;
    }
    $new = encode_password($new);
    $passDate = date('Y-m-d H:i:s');
      $query = "update user set password = '$new',has_change_password = '1' where ID=?";
      if ($this->db->query($query,array($id))) {
        $this->webSessionManager->setContent('has_change_password','1');
        
        $arr['status'] = true;
        $arr['message'] = 'operation successfull';
        $arr['flagAction'] = true;
        echo json_encode($arr);
        return;
      }
      else{
        $arr['status'] = false;
        $arr['message'] = 'error occured during operation...';
        $arr['flagAction'] = false;
        echo json_encode($arr);
        return;
      }
  }
  return false;

}

}
