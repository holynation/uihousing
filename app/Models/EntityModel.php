<?php
/**
 * The class that managers entity related data generally
 */
namespace App\Models;

use CodeIgniter\Model;
use App\Models\EntityDetails;
use App\Models\EntityCreator;
use App\Models\FormConfig;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class EntityModel
{
	protected $db;
	private $entityDetails;
	private $crudNameSpace = 'App\Models\Crud';
	protected $request;
	protected $response;

	function __construct(RequestInterface $request=null,ResponseInterface $response=null)
	{
		$this->defaultLength = 20;
		$this->db = db_connect();
		$this->request = $request;
		$this->response = $response;
	}

	//process all the crud operations
	public function process($entity,$args,$apiType=null)
	{
		try {
			if (!$args) {
				#this handles /entity GET, will get list of entities and POST will insert a new one
				if ($_SERVER['REQUEST_METHOD']=='GET') {
					$param = $this->request->getGet(null);
					$result = $this->list($entity,$param);
					displayJson(true,'success',$result);
				}
				elseif ($_SERVER['REQUEST_METHOD']=='POST') {
					# check if the item allows processing of array element
					if (($customer = getCustomer())) {
						$_POST['hirers_id'] = $customer->ID;
						$_POST['user_id'] = $customer->user_id;
					}
					$result = $this->insert($entity,$apiType);
				}
				return;
			}

			if (count($args) == 1) {
				# this handles entity detail view  and update
				if (is_numeric($args[0])) {
					if ($_SERVER['REQUEST_METHOD']=='GET') {
						$param = $this->request->getGet(null);
						$id = $args[0];
						$result = $this->detail($entity,$id,$param);
						if(!$result){
							displayJson(false,'sorry no data available');return;
						}
						displayJson(true,'success',$result);
					}
					elseif ($_SERVER['REQUEST_METHOD']=='POST') {
						$values = $this->request->getPost(null);
						$id = $args[0];
						$this->update($entity,$apiType,$id,$values);
					}
					return;
				}
				else{
					if (strtolower($args[0]) == 'bulk_upload') {
						$message = '';
						$this->processBulkUpload($entity,$message);
						return;
					}
				}
				return;
			}

			if (count($args) == 2 && is_numeric($args[1])) {
				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					if (strtolower($args[0]) == 'delete') {
						$id = $args[1];
						if($this->delete($entity,$id)){
							displayJson(true,'success');
							return;
						}
						displayJson(false,'unable to delete item');
						return;
					}
					
					# handle the issue with the status
					if (strtolower($args[0])=='disable' || strtolower($args[0])=='remove' || strtolower($args[0])=='enable') {
						$operation = $args[0];
						$id = $args[1];
						$status = false;
						if ($operation=='disable' || $operation == 'remove') {
							$status =$this->disable($entity,$id);
						} else {
							$status =$this->enable($entity,$id);
						}
						displayJson($status,$status?'success':'unable to delete item');
						
					}
					
				}
				return;
			}

			return $this->response->setStatusCode(404)->setJSON(['status'=>false,'message'=>'resource not found']);
		} catch (\Exception $e) {
			displayJson(false,$e->getMessage());
			return;
		}

	}

	/**
	 * THis is to validate user URI
	 * @deprecated - Not used anymore
	 * @param  string $apiType [description]
	 * @return [type]          [description]
	 */
	private function validateUserURI(string $apiType=null)
	{
		$owners = ['equipments'];
		$customer = getCustomer();
		if((isset($customer->is_owner) && $apiType != 'owners')){
			return false;
		}
		return true;
	}

	private function validateHeader(string $entity,array $header)
	{
		$entity = loadClass($entity);
		return $entity::$bulkUploadField==$header;
	}

	private function processBulkUpload(string $entity)
	{
		$entity = loadClass($entity);
		$message = 'success';
		$content = $this->loadUploadedFileContent($message);
		if (!$content) {
			displayJson(false,'not uploaded content found');
			return false;
		}
		$content = trim($content);
		$array = stringToCsv($content);
		$header = array_shift($array);
		if (!$this->validateHeader($entity,$header)) {
			$message='column does not match, please check the column template and try again';
			displayJson(false,$message);
			return false;
		}
		$result = $entity->bulkUpload($header,$array,$message);
		displayJson($result,$message);
	}

	private function loadUploadedFileContent(string &$message){
		$filename = 'upload_form';
		$status = $this->checkFile($filename,$message);
		if (!$status) {
			return false;
		}
		if(!endsWith($_FILES[$filename]['name'],'.csv')){
			$message = "invalid file format";
			return false;
		}
		$path = $_FILES[$filename]['tmp_name'];
		$content = file_get_contents($path);
		return $content;
	}

	private function checkFile(string $name,string &$message=null){
		$error = !$_FILES[$name]['name'] || $_FILES[$name]['error'];
		if ($error) {
			if ((int)$error===2) {
				$message = 'file larger than expected';
				return false;
			}
			return false;
		}
		
		if (!is_uploaded_file($_FILES[$name]['tmp_name'])) {
			$this->db->transRollback();
			$message='uploaded file not found';
			return false;
		}
		return true;
	}

	private function delete(string $entity,int $id)
	{
		$this->db->transBegin();
		$entity = loadClass($entity);
		if(!$entity->delete($id,$this->db)){
			$this->db->transRollback();
			return false;
		}
		$this->db->transCommit();
		return true;
	}

	private function detail(string $entity,int $id,$param=null)
	{
		$entityDetails = new EntityDetails;
		$methodName = 'get'.ucfirst($entity).'Details';
		if (method_exists($entityDetails, $methodName)) {
			$result = $entityDetails->$methodName($id);
			return $result;
		}
		$entity = loadClass($entity);
		$entity->ID=$id;
		$res=$entity->load();
		if(!$res) return false;
		$result= $entity->toArray();
		return $result;
	}

	public function disable(string $model,int $id){
		$this->db->transBegin();
		$model = loadClass($model);
		//check that model is actually a subclass
		if ( !(empty($id)===false && is_subclass_of($model,$this->crudNameSpace ))) {
			return false;
		}
		return $model->disable($id,$this->db);
	}

	public function enable(string $model,int $id){
		$this->db->transBegin();
		$model = loadClass($model);
		//check that model is actually a subclass
		if ( !(empty($id)===false && is_subclass_of($model,$this->crudNameSpace))) {
			return false;
		}
		return $model->enable($id,$this->db);
	}

	/**
	 * This is to perform update on the entity
	 * @param  string     $entity [description]
	 * @param  int        $id     [description]
	 * @param  array|null $param  [description]
	 * @return [type]             [description]
	 */
	private function update(string $entity,?string $apiType,int $id,array $param=null)
	{
		$entityCreator = new EntityCreator($this->request);
		$tempEntity = $entity;
		$entity = loadClass($entity);
		# this is to capture entity that have owners_id|is_owners in it field
		if($apiType != null && $apiType == 'owners'){
			$customer = getCustomer();
			if(!isset($customer->owner_id)){
				displayJson(false, 'Oops, invalid operation.');return;
			}
			$data['owners_id'] = $customer->owner_id;
		}
		if (property_exists($entity, 'allowedFields')) {
			$allowParam = $entity::$allowedFields;
			if(!$this->validateAllowedParameter($param,$allowParam)){
				displayJson(false,'allowed parameters for update are:',['parameters'=>$allowParam]);
				return;
			}
		}
		return $entityCreator->update($tempEntity,$id,true,$param);
	}

	/**
	 * This is to perform insertion on the entity
	 * @param  string      $entity  [description]
	 * @param  string|null $apiType [description]
	 * @return [type]               [description]
	 */
	private function insert(string $entity, string $apiType = null)
	{
		$entityCreator = new EntityCreator($this->request);
		if (($customer = getCustomer())) {
			$data['hirers_id'] = $customer->ID;
			$data['user_id'] = $customer->user_id;
			# this is to capture entity that have owners_id|is_owners in it field
			if($apiType != null && $apiType == 'owners'){
				if(!isset($customer->owner_id)){
					displayJson(false, 'Oops, invalid operation.');return;
				}
				$data['owners_id'] = $customer->owner_id;
				$data['is_owners'] = 1;
				if($entity == 'equip_order'){
					$data['ip_address'] = $this->request->getIPAddress();
					$data['user_agent'] = toUserAgent($this->request->getUserAgent());
					$data['mac_address'] = getMacAddress();
				}
			}
			$entityCreator->extraDataParam = $data;
		}
		$entityCreator->add($entity);
	}

	/**
	 * [validateAllowedParameter description]
	 * @param  array  $param      [description]
	 * @param  array  $allowParam [description]
	 * @return [type]             [description]
	 */
	private function validateAllowedParameter(array $param, array $allowParam){
		foreach($param as $key => $value){
			if(!in_array($key,$allowParam)){
				return false;
			}
		}
		return true;
	}

	/**
	 * implementing the crud operation for list:view, update, detail,delete
	 * @param  string $entity [description]
	 * @param  string $query  [description]
	 * @return [type]         [description]
	 */
	private function buildWhereSearchString(string $entity, string $query)
	{
		$formConfig = new FormConfig(true,true);
		$config = $formConfig->getInsertConfig($entity);
		if (!$config) {
			return '';
		}
		$list= array_key_exists('search', $config)?$config['search']:false;
		if (!$list) {
			# use all the fields here then
			$entity = loadClass("$entity");
			$list = array_keys($entity::$labelArray);
		}
		$result='';
		$isFirst = true;
		foreach ($list as  $value) {
			if (!$isFirst) {
				$result.=' or ';
			}
			$isFirst =false;
			$result.="$value like '%$query%'";
		}
		return $result;
	}

	/**
	 * This is to get entity list
	 * @param  string $entity [description]
	 * @param  array  $param  [description]
	 * @return [type]         [description]
	 */
	private function list(string $entity,array $param)
	{
		# get the parameter for paging
		$start = array_key_exists('start', $_GET)?$param['start']:0;
		$len = array_key_exists('len', $_GET)?$param['len']:$this->defaultLength;
		$q = array_key_exists('q', $param)?$param['q']:false;
		$paging = array_key_exists('paging', $param)?$param['paging']:false;
		# the parameter to include structure
		$addStructure = (array_key_exists('st', $param) && $param['st'])?$param['st']:false;
		$filterList = $param;
		if ($q) {
			unset($filterList['q']);
		}
		unset($filterList['st']);
		unset($filterList['start']);
		unset($filterList['len']);
		unset($filterList['paging']);

		# perform some form of validation here to know what needs to be include in 
		# the list and also how to perform 
		$customer = getCustomer();
		$filterList = $this->validateFilters($entity,$filterList);
		if($filterList && $entity == 'equip_request' && $filterList['request_status'] == 'all'){
			unset($filterList['request_status']);
		}
		$newEntity = loadClass($entity);
		$usersExemption = ['equipments','reviews','kyc_document']; # using this to escape fetching list based on user's ID
		$allowOwnersFilter = ['equip_order','equipments'];
		
		if ($customer && !in_array($entity, $usersExemption)) {
			$labels = array_keys($newEntity::$labelArray);
			if (in_array('hirers_id', $labels)) {
				$filterList['hirers_id'] = $customer->ID;
			}
			if (in_array('notifier_id', $labels)) {
				$filterList['notifier_id'] = $customer->ID;
			}
			if (in_array('user_id', $labels)) {
				$filterList['user_id'] = $customer->user_id;
			}
		}
		# this is to allow filter for owners that needed it in their query model
		# Hence, it would be used in the where clause of the query
		if($customer && isset($customer->is_owner) && in_array($entity, $allowOwnersFilter)){
			$filterList['owners_id'] = $customer->owner_id;
			unset($filterList['hirers_id']);
		}
 
		$result = null;
		$totalLength = 0;
		$toReturn = array();
		$resolveForeign = ($customer) ? false : true;

		// print_r($filterList);exit;

		if ($q) {
			# this is for search query
			$this->createSearchLog($q);
			$queryString = $this->buildWhereSearchString($entity,$q);
			$tempR = $newEntity->allListFiltered($filterList,$totalLength,$start,$len,$resolveForeign,' order by ID desc ',$queryString);
			$toReturn['table_data'] = $tempR[0];
			$toReturn['paging'] = $tempR[1]; 
		}
		else{
			# setting paging on model
			if($paging){
				$newEntity->setModelPaging(true);
			}
			$tempR = $newEntity->allListFiltered($filterList,$totalLength,$start,$len,$resolveForeign,' order by ID desc ');
			$toReturn['table_data'] = $tempR[0];
			$toReturn['paging'] = $tempR[1]; 
		}
		# this is to know if the request is coming from the client API and
		# not directly from other client
		if ($customer) {
			$result= array();
			$result['totalLength'] = $toReturn['paging'];
			if (method_exists($newEntity, 'APIList')) {
				$temp = $newEntity->APIList($toReturn['table_data'],$customer,$param);
				$result['content'] = $temp;
				return $result;
			}
			$result['content'] = $toReturn['table_data'];		
			return $result;
		}

		# now add the filter information which is useful if loading for vuejs|react
		$formConfig = new FormConfig;
		$filters = $formConfig->getInsertConfig($entity);
		$filterContent = $this->getFilterContent($filters);
		$toReturn['filter_structure']=$filterContent;
		if ($addStructure) {
			$structure = getStructure($this,$entity);
			$toReturn['structure']=$structure;
			$temp = getBulkUploadFields($this,$entity);
			if ($temp) {
				$toReturn['bulk_structure']= $temp;

			}
		}
		return $toReturn;
	}

	/**
	 * [createSearchLog description]
	 * @param  string $query [description]
	 * @return [type]        [description]
	 */
	private function createSearchLog(string $query)
	{
		$db = $this->db;
		$builder = $db->table('search_log');
		$request = $this->request;
		$customer = getCustomer();
		$customer = $customer ? $customer->ID : null;
		$param = [
		    'hirers_id' => $customer,
		    'route' => $request->getServer('PATH_INFO'),
		    'query' => $query,
		    'date_created' => formatToUTC(),
		];
		$builder->insert($param);
	}

	/**
	 * [validateFilters description]
	 * @param  string 	$entity  [description]
	 * @param  array 	$filters [description]
	 * @return array          [description]
	 */
	private function validateFilters(string $entity,array $filters)
	{
		if (!$filters) {
			return  [];
		}
		$formConfig = new FormConfig(true,true);
		$filterSettings = $formConfig->getInsertConfig($entity);
		if (!$filterSettings) {
			return [];
		}

		$result = array();
		foreach ($filters as $key => $value) {
			if (!$value) {
				continue;
			}
			$realKey = $this->getRealKey($key,$filterSettings);
			if (!$realKey) {
				continue;
			}
			$result[$realKey]=$value;
		}
		return $result;
	}

	/**
	 * This is to get the filter values and compare against query param supplied
	 * @param  string $key            [description]
	 * @param  array  $filterSettings [description]
	 * @return [type]                 [description]
	 */
	private function getRealKey(string $key,array $filterSettings)
	{
		//check if there is 
		$result = false;
		foreach ($filterSettings['filter'] as $value) {
			if ($value['filter_display'] == $key || $value['filter_label'] == $key) {
				return 	$value['filter_label'];
			}
		}
		return $result;
	}

	/**
	 * [getFilterContent description]
	 * @param  array  $filters [description]
	 * @return [type]          [description]
	 */
	private function getFilterContent(array $filters)
	{
		$result = array();
		if (!$filters) {
			return $result;
		}
		if (!array_key_exists('filter', $filters)) {
			return $filters;
		}
		$mainFilter  = $filters['filter'];
		$tempFilter = array();
		foreach ($mainFilter as $filterItem) {
			$temp = array();
			$temp['title']=$filterItem['filter_display']?$filterItem['filter_display']:$filterItem['filter_label'];
			$temp['name']=$filterItem['filter_label'];
			if (array_key_exists('select_items', $filterItem)) {
				$temp['filter_item']=$filterItem['select_items'];
			}
			else{
				$temp['filter_item']=$this->getSelectItemFromQuery($filterItem['preload_query']);
			}
			$tempFilter[]=$temp;
		}
		if (array_key_exists('search', $filters)) {
			$result['search']=$filters['search'];
		}
		$result['filters']=$tempFilter;
		return $result;
		//now convert the sql to real data to be user
	}


	private function getSelectItemFromQuery(string $query)
	{
		if (!$query) {
			return [];
		}
		$result = $this->db->query($query);
		$result = $result->getResultArray();
		return $result;
	}
	
	private function getAllDirectRelation(string $label,array $relations)
	{
		$result = array();
		foreach ($relations as $key => $value) {
			if ($value[0]==$label) {
				$result[$key]=$value[1];
			}
		}
		return $result;
	}


}
?>