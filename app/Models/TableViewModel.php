<?php 
/**
* The model for generating table view for any model
*/
namespace App\Models;

use CodeIgniter\Model;
use App\Models\TableViewModel;

class TableViewModel extends Model
{
	private $uploadedFolderName = 'uploads'; // this name should be constant and not change at all
	private $modelFolderName= 'entities';
	private $statusArray = array(1=>'Active',0=>'Inactive');
	private $booleanArray = array(0 =>'No',1=>'Yes' );
	private $defaultPagingLength =50;
	public $export=false;
	private $crudNameSpace = 'App\Models\Crud';
	private $tableActionModel;
	protected $db;

	function __construct()
	{
		$this->tableActionModel = new TableViewModel;
		$this->db = db_connect();
		helper(['string','filesystem']);
	}
	//create  a method that load the table based on the specified paramterr
	/**
	This  method get the table view of a model(tablename)
	*the $model parameter is the name of the model to generate table view for
	*$page variable paging ability is included as a part of the generated html
	*/
	//model is the name of the class while data is the objects needed to be changed into a table.
	public function getFilteredTableHtml($model,$conditionArray,&$message='',$exclusionArray=array(),$action=null,$paged= true,$start=0,$length=NULL,$resolve=true,$sort=' order by ID desc '){
		if ($paged) {
			$length = $length?$length:$this->defaultPagingLength;
		}
		//use get function for the len and the start index for the sorting
		$start = (isset($_GET['p_start'])&& is_numeric($_GET['p_start']) )?$_GET['p_start']:$start;
		$length = (isset($_GET['p_len'])&& is_numeric($_GET['p_len']) )?$_GET['p_len']:$length;
		$newModel = loadClass("$model");
		$data = $this->export?$this->$newModel->getWhereNonObject($conditionArray,$message,0,null,$resolve,$sort):$this->$newModel->getWhere($conditionArray,$message,$start,$length,$resolve,$sort);
		return $this->export?$this->loadExportTable($model,$data):$this->loadTable($model,$data,$message,$exclusionArray,$action,$paged,$start,$length,$resolve);
	}
	public function loadExportTable($model,$data)
	{
		$res =array2csv($data);
	  	sendDownload($res,'text/csv',$model.'.csv');exit;
	}
	/**
	* $model is the name of the class to load , data is the list of object you want to display as table.
	*/
	public function convertToTable($model,$data,&$message='',$exclusionArray=array(),$action=null,$paged= true,$start=0,$length=NULL){
		loadClass($this->load,$model);
		return $this->loadTable($model,$data,$message='',$exclusionArray,$action,$paged,$start,$length);
	}

	private function loadTable($model,$data,$totalRow,$exclusionArray,$action,$paged,$start,$length,$removeId=true,$appendForm=array(),$multipleAction,$tableAttr=array()){
		if (!$this->validateModelNameAndAccess($model)) {
			return false;
		}
		$actionArray = $action===null?$model::$tableAction:$action;
		$documentField =$model::$documentField;
		// $totalRow=0;
		if (empty($data) || $data==false) {
			$link = base_url("vc/$model/create");
			return "<div class='empty-data alert alert-info text-light'>NO RECORD FOUND</div>";
		}
		$header = $this->getHeader($model,$exclusionArray,$removeId);
		$result=$this->openTable($tableAttr);
		$result.=$this->generateheader($header,$action,$appendForm,$multipleAction);
		$result.=$this->generateTableBody($model,$data,$exclusionArray,$actionArray,$appendForm,$multipleAction);
		$result.=$this->closeTable();
		if ($paged && $totalRow > $length) {
			$result.=$this->generatePagedFooter($totalRow,$start,$length);//for testing sake
		}
		return $result;
	}
	// the multipleAction arg is for performing multiple action using checkbox
	public function getTableHtml($model,&$message='',$exclusionArray=array(),$action=null,$paged= true,$start=0,$length=NULL,$resolve=true,$sort=' order by ID desc ',$where='',$appendForm=array(),$multipleAction=false,$tableAttr = array()){
		$newModel = loadClass("$model");

		if ($paged) {
			$length = $length?$length:$this->defaultPagingLength;
		}
		//use get function for the len and the start index for the sorting
		$start = (isset($_GET['p_start'])&& is_numeric($_GET['p_start']) )?(int)$_GET['p_start']:$start;
		$length = (isset($_GET['p_len'])&& is_numeric($_GET['p_len']) )?(int)$_GET['p_len']:$length;

		// $data = $this->export?$this->$model->allNonObject($message,$resolve,0,null,$sort):$this->$model->all($message,$resolve,$start,$length,$sort,$where);
		$data = $this->$newModel->all($message,$resolve,$start,$length,$sort,$where);

		$countAll = ''; // using this to count all the records in the model
		if($paged){
			$builder = $db->table("$model");
			$countAll = $builder->countAll();
		}
		// return $this->export?$this->loadExportTable($model,$data):$this->loadTable($model,$data,$message,$exclusionArray,$action,$paged,$start,$length,true,$appendForm,$multipleAction);
		return $this->export?$this->loadExportTable($model,$data):$this->loadTable($model,$data,$countAll,$exclusionArray,$action,$paged,$start,$length,true,$appendForm,$multipleAction,$tableAttr);
	}
	//the action array will contain the 
	private function generateActionHtml($data,$actionArray){//the id will be extracted from
		$result="<ul class='dropdown-menu' data-model=''>";
		$result.=$this->buildHtmlAction($data,$actionArray);
		$result.="</ul>";
		return $result;
	}

	private function buildHtmlAction($data,$actionArray){//the link must be specified for the action that is to be performed
		$result='';
		foreach ($actionArray as $key => $value) {
			// this section is used for getting another column alongside the ID of that model
			// something like this vc/admin/edit/model_name/new_column/id
			if(is_array($value)){
				$temp = $value;
				$otherParam = $temp[1];
				$otherParam = $data->$otherParam;
				// check if the additional param include the default upload folder
				// this also means that the uploaded folder would start the directory of the path
				if(!empty($otherParam)){
					if(startsWith($otherParam,$this->uploadedFolderName)){
						$tempParam = explode('/',$otherParam);
						$otherParam = "/".urlencode(base64_encode($tempParam[2])); // this would be the file name used
					}
				}
				$value = $temp[0].$otherParam;
			}
			$currentid = $data->ID;
			$link = '';
			$classname = get_class($data);
			$critical='0';
			$label ='';
			$default=1;
			if(method_exists($this->tableActionModel,$value)){
				$tempObj = $this->tableActionModel->$value($data);
				if (empty($tempObj)) {
					continue;
				}
				$link = $tempObj['link'].'/'.$currentid;
				$critical = $tempObj['isCritical'];
				$label = $tempObj['label'];
				$default = $tempObj['ajax'];
			}
			else{
				if (is_array($value)) {
					$link = $value[0].'/'.$currentid;
					$critical = $value[1];
					$default = $value[2];
				}
				else{
					$criticalArray = array('delete','disable','reset password','remove');
					if (in_array(strtolower($key), $criticalArray)) {
						$critical =1;
					}
					$link = $value.'/'.$currentid;
				}
				$label = $key;
				$link =base_url($link);
			}
			$editClass='';
			if($label=='edit' || $label=='update'){
				$editClass = "data-ajax-edit='1'";
			}else if ($label == 'editor'){
				$editClass = "data-ajax-editor='1'";
			}else{
				$editClass ='';
			}
			$result.="<li data-item-id='$currentid' data-default='$default' data-critical='$critical' $editClass><a href='$link' class='dropdown-item text-center text-capitalize'>$label</a></li>";
		}
		return $result;
	}
	private function openTable($attr = array()){
		$attr = (!empty($attr)) ? attrToString($attr) : "";
		return "  <div class=\"box\"><div class=\"table-responsive no-padding\"><table $attr class='table'> \n";
	}
	private function generateTableBody($model,$data,$exclusionArray,$actionArray,$appendForm=array(),$multipleAction){
		$result ='<tbody>';
		for ($i=0; $i < count($data); $i++) { 
			$current= $data[$i];
			$result.=$this->generateTableRow($model,$current,$exclusionArray,$actionArray,@$_GET['p_start']+$i,$appendForm,$multipleAction);
		}
		$result.='</tbody>';
		return $result;
	}
	private function generateTableRow($model,$rowData,$exclusionArray,$actionArray,$index=false,$appendForm=array(),$multipleAction){
		$result="<tr data-row-identifier='{$rowData->ID}' class='best-content' id='best-content'>";

		// this is to add multiple checkbox functionality
		$id = $rowData->ID;
		if($multipleAction){
			// $id = $rowData->ID;
			$inputForm = "<label class='form-check-label'><input type='checkbox' class='form-check-input form-control' name='".$model.'Box'."[]' id='".$model.'Box'."[]' value='$id' /></label>";
			$result.="<td><div class='form-check form-check-flat'>$inputForm</div></td>";
		}

		if(!empty($appendForm)){
			extract($appendForm);
			// $id = $rowData->ID;
			$inputForm = "<label class='form-check-label'><input type='$type' class='form-check-input $class' name='".$name."[]' id='".$name."[]' value='$id' /></label>";
			$result.="<td><div class='form-check form-check-flat'>$inputForm</div></td>";
		}
		if ($index!==false) {
			$index+=1;
			$result.="<td>$index</td>";
		}
		$documentArray =array_keys($model::$documentField);
		$labels = array_keys($model::$labelArray);
		for ($i=0; $i <count($labels) ; $i++) { 
			$key =$labels[$i];
			if ($key=='ID' || in_array($key, $exclusionArray)) {
				continue;
			}

			$value = $rowData->$key;
		
			if (!empty($documentArray) && in_array($key, $documentArray)) {
				$link = 'javascript:void(0);';
				$fileMsg = 'no image yet';
				if($value != ""){
					$link = base_url($value);
				}

				$typeExt = getMediaType($link);
				if($typeExt == 'audio'){
					$fileMsg = 'Hear Audio';
					$value = "<a href='$link' target='_blank'>$fileMsg</a>";
				}else if($typeExt == 'image'){
					$fileMsg = 'View Image';
					$value = "<a href='$link' target='_blank'>$fileMsg</a>";
				}else if($typeExt == 'video'){
					$fileMsg = 'View Video';
					$value = "<a href='$link' target='_blank'>$fileMsg</a>";
				}else{
					$selector = $model . "_download_$id";
					$value = "<a href='$link' target='_blank' id='$selector'>View</a>";
				}
			}

			if ($model::$typeArray[$key]=='tinyint') {
				$value = $value?1:0;
				if ($key == 'status') {
					$value = (!$value)?$this->statusArray[$value]:$this->statusArray[$value];
				}
				else{
					$value = $this->booleanArray[$value];
				}
			}

			if(strpos($value,'href')){
				$value = $value;
			}else{
				$value = $value = wordwrap($value,50,"<br />\n",true);
			}
			
			$result.="<td>$value</td>";
		}
		if (!empty($actionArray) && $actionArray!==false) {
			$actionString=$this->generateActionHtml($rowData,$actionArray);
			$result.="<td class='action-column'><div class='list-icons'><div class='dropdown'><a href='#' class='list-icons-item' data-toggle='dropdown'><i class='icon-menu9'></i></a> 
				$actionString
			</div></div>
			</td>";
		}
		$result.="</tr>";
		return $result;
	}
	private function closeTable(){
		return '</table></div></div>';
	}
	private function generateHeader($header,$action,$appendForm=array(),$multipleAction){
		$sn = "<th>S/N</th>";
		$emptyHeader='';
		if(!empty($appendForm)){
			$emptyHeader .= "<th></th>";
		}
		if($multipleAction){
			$emptyHeader .= "<th></th>";
		}
		$result="<thead>
			<tr> $emptyHeader $sn";
		for ($i=0; $i < count($header); $i++) {
			$item = $header[$i]; 
			$result.="<th>$item</th>";
		}
		$actionText = '';
		if ($action!==false) {
			$actionText = "<th>Action</th>";
		}
		$result.="	$actionText
				</tr>
			</thead>";
		return $result;
	}
	//this function generate page footer will link to navigate through the pages
	public function generatePagedFooter($totalPage,$currentcount,$pageLength){
	 	$beginCount =10;
	 	
	 	if ($totalPage <= $pageLength) {
	 		return;
	 	}
	 	// $links = base_url('');
		$result="<div class='paging'>
		<div>
			<div class='form_group'>
				<label class='col-sm-3 col-form-label'>Page Size :</label>
				<div class='col-sm-9'>
					<input class='form-control' type='text' style='width:50px;display:inline_block;background-color:white;' id='page_size' value='$pageLength' disabled/>
				</div>
			</div>
		</div>
			<ul class='pagination rounded-separated pagination-danger'>
			";
		// $pageArray=$this->generatePageArray($totalPage,$pageLength);
		$totalPaged = ceil($totalPage/$pageLength);
		$currentIndex =$this->calculateCurrentIndex($currentcount,$pageLength);
		$start=0;
		if ($totalPaged > ($beginCount)) {
			$start=$currentIndex-(ceil($beginCount/2)+1) ;//half of the content before showing the current index
			$start =$start<0?0:$start;
			// $len = $start+$beginCount;
			$prev = $currentIndex> 0? ($currentIndex-1):0;
			$prev*=$pageLength;
			$disable = $prev==0?'disabled':'';
			$result.="<li data-start='$prev' data-length='$pageLength' class='page-item $disable'>«</li>";
			$len = $start+ceil($beginCount/2);
			for ($i=$start; $i < $len; $i++) { 
				$current =1+ $i;
				$itemClass ='page-item';
				if ($i==$currentIndex) {
					$itemClass ='active page-item';
				}
				$end = $current*$pageLength;
				$result.="<li data-start='$end' data-length='$pageLength' class='$itemClass '>
				<a class='page-link'> $current</a>
				</li>";
				// $start = $end;
			}
			$result.="<li data-start='' data-length='$pageLength' class='page-item  break'>
			<a class='page-link'>...</a>
			</li>";
			$len =floor($beginCount/2);
			$start = ($totalPaged-(1+$len));
			$len+=$start;
			for ($i=$start; $i < $len; $i++) { 
				$current =1+ $i;
				$itemClass ='page-item';
				if ($i==$currentIndex) {
					$itemClass ='page-item active';
				}
				$end = $current * $pageLength;
				$result.="<li data-start='$end' data-length='$pageLength' class='$itemClass '>
				<a class='page-link'>$current</a>
				</li>";
				// $start = $end;
			}
			$prev = $currentIndex < $totalPaged? ($currentIndex+1):$totalPaged-1;
			$last =$prev *$pageLength;
			$result.="<li data-start='$last' data-length='$pageLength' class='page-item'>
			<a class='page-link'>»</a>
			</li>";
			$len = $start+$beginCount;
		}
		else{
			for ($i=0; $i <= $totalPaged; $i++) { 
				$current =$i + 1;
				$itemClass ='page-item';
				if ($i==$currentIndex) {
					$itemClass ='page-item active';
				}
				$start =  ($current > 1) ? ($current * $pageLength) - $pageLength : 0;
				$end = $start * $pageLength;
				$result.="<li data-start='$end' data-length='$pageLength' class='$itemClass'>
				<a class='page-link' href='?p_start=".$start."&p_len=".$pageLength."'>$current</a>
				</li>";
				// $start = $end;
			}
		}
		$result.="<div class='clear'></div></ul>
		</div>";
		return $result."<div class='clear'></div>";
	}
	private function calculateCurrentIndex($current,$pageLength){
		return ceil($current/$pageLength);
	}
	private function generatePageArray($totalPage,$pageLength){
		$count = ceil(($totalPage/$pageLength));
		$result= array();
		for ($i=0; $i < $count ; $i++) { 
			$result[]=$i+1;
		}
		return $result;
	}
	//create another method to generate the needed javascript file for the paging this can be called independently of the getTableHtml function
	//this functin basically generates the javascript file needed to process  the action as well as the paging function search functionality will also be included automatically
	public function getJSData($actionArray){

	}
	private function getHeader($model,$exclusionArray,$removeid){
		$result = array();
		$labels = $model::$labelArray;
		foreach ($labels as $key => $value) {
			if ($key=='ID' || in_array($key, $exclusionArray)) {//dont include the header if its an id field or
				continue;
			}
			if (empty($value)) {
				if ($removeid && endsWith($key,'_id')) {
					$key = substr($key, 0,strlen($key)-strlen('_id'));
				}
				$result[] = removeUnderscore($key);
			}
			else{
				$result[]= ucfirst($value);
			}
		}
		return $result;
	}
	//this function validate the correctnes of the moe
	private function validateModelNameAndAccess($modelname){
		$message='';
		if (empty($modelname)) {
			throw new Exception("Empty model name not allowed"); 
			return false;
		}
		if (!is_subclass_of($modelname, $this->crudNameSpace)) {
			throw new Exception("Model is not a crud: make sure the correct name of the model is entered");
			return false;
		}
		if (!$this->validateAccess($modelname)) {
			throw new Exception("Access Denied");
			return false;
		}
		return true;
	}

	private function validateAccess($modelname){
		return true;//for now. change the implementation later//the implementation will make a call to the accessControl model
	}
}

?>