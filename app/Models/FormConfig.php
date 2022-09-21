<?php 
/**
* this class help save the configuration needed by the form in order to use a single file for all the form code.
* you only need to include the configuration data that matters. the default value will be substituted for other configuration value that does not have a key  for a particular entity.
*/
namespace App\Models;

use App\Models\WebSessionManager;

class FormConfig
{
	private $insertConfig=[];
	private $updateConfig;
	private $webSessionManager;
	public $currentRole;
	private $apiEntity = false;
	
	function __construct(bool $currentRole=false,bool $apiEntity=false)
	{
		$this->currentRole=$currentRole;
		$this->apiEntity = $apiEntity;
		$this->webSessionManager = new WebSessionManager;
		if ($currentRole) {
			$this->buildInsertConfig();
			$this->buildUpdateConfig();
		}
		
	}

	/**
	 * this is the function to change when an entry for a particular entitiy needed to be addded. this is only necessary for entities that has a custom configuration for the form.Each of the key for the form model append insert option is included. This option inculde:
	 * form_name the value to set as the name and as the id of the form. The value will be overridden by the default value if the value if false.
	 * has_upload this field is used to determine if the form should include a form upload section for the table form list
	 * hidden this  are the field that should be pre-filled. This must contain an associative array where the key of the array is the field and the value is the value to be pre-filled on the value.
	 * showStatus field is used to show the status flag on the form. once the value is true the status field will be visible on the form and false otherwise.
	 * exclude contains the list of entities field name that should not be shown in the form. The filed for this form will not be display on the form.
	 * submit_label is the label that is going to be displayed on the submit button
	 * 	table_exclude is the list of field that should be removed when displaying the table.
	 * table_action contains an associative arrays action to be displayed on the action table and the link to perform the action.
	 * the query paramete is used to specify a query for getting the data out of the entity
	 * upload_param contains the name of the function to be called to perform
	 * 
	 */ 
	private function buildInsertConfig()
	{
		if($this->apiEntity){
			$this->insertConfig = array
			(
				'equipments' => array(
					'search' => array('equip_name')
				),
				'activity_log' => array(
					'search' => array('events')
				),
				'equip_request' => array(
					'search' => array('request_status')
				)
				//add new entry to this array
			);
		}
		else{
			$this->insertConfig = array
			(
				'hirers'=>array
				(
					'show_add' => false,
					'exclude'=>array(),
					'table_exclude'=>array(),
					'header_title'=>'Manage registered hirers(s)',
					'table_title'=>'Manage registered hirers(s)',
					'has_upload'=>false,
					'hidden'=>array(),
					'show_status'=>false,
					'search'=>array('fullname'),
					'search_placeholder'=>array('Search...'),
					'order_by' => array('fullname'),
					// 'query'=>"select hirers.ID,fullname,hirers.phone_number,email,hirers_path,status from hirers"
				),
				'admin'=>array
				(
					'table_title' => 'Admin Table',
					'show_status' => true,
					'show_add' => true,
					'table_exclude' => array('middlename'),
					'header_title' => 'Manage Admin(s)'
					// 'search'=>array('phone_number','firstname','middlename','lastname'),
					// 'search_placeholder'=> array('phone num','admin name'),
				),
				'role'=>array(
					'query'=>'select * from role where ID<>1'
				),
				'extend_equip_request' => array(
					'table_exclude' => ['prev_equip_order'],
					'query' => "SELECT extend_equip_request.ID,fullname as hirers_name, ( select concat_ws(' ',equip.equip_name,'(#',eqo.order_number,')') from equip_order eqo join equip_request eqr on eqr.equip_order_id = eqo.id join equipments equip on equip.id = eqr.equipments_id where extend_equip_request.prev_equip_order = eqo.id ) as prev_order,concat('#',equip_order.order_number) as order_number ,extend_equip_request.rental_from,extend_equip_request.rental_to,extend_equip_request.request_status,extend_equip_request.date_modified,extend_equip_request.date_created from extend_equip_request join equip_order on equip_order.id = extend_equip_request.equip_order_id join hirers on extend_equip_request.hirers_id = hirers.id order by extend_equip_request.ID desc "
				),
				'equipments' => array(
					'table_exclude' => array('owners_id'),
					'query' => "SELECT equipments.ID,fullname as owners_name,equip_name,cost_of_hire,cost_of_hire_interval,avail_from,avail_to,quantity,description,equipments.latitude,equipments.longitude,equipments.address,equipments.status,equipments.date_modified,equipments.date_created from equipments join owners on owners.id=equipments.owners_id join hirers on hirers.id=owners.hirers_id order by equipments.ID desc "
				),
				'reviews' => array(
					'table_exclude' => array('is_owners'),
				),
				'service_charge' => array(
					'show_add' => true,
				),
				//add new entry to this array
			);
		}
	}

	/**
	 * This is to get the entity filter for a model using certain pattern
	 * @example 'entity_name'=>array(
	 * array(
	 * 'filter_label'=>'request_status', # this is the field to call for the filter
	 * 'filter_display'=>'active_status' # this is the query param supplied
	 * )),
	 * @param  string $tablename [description]
	 * @return [type]            [description]
	 */
	private function getFilter(string $tablename)
	{	
		$result = [];
		if($this->apiEntity){
			$result = array(
				'equip_request'=>array(
					array(
						'filter_label'=>'request_status',
						'filter_display'=>'active_status'
					)
				),
				'equip_delivery_status'=>array(
					array(
						'filter_label'=>'equipments_id',
						'filter_display'=>'equipments_id'
					)
				),
				'reviews'=>array(
					array(
						'filter_label'=>'equipments_id',
						'filter_display'=>'equipments_id'
					),
					array(
						'filter_label'=>'hirers_id',
						'filter_display'=>'hirers_id'
					)
				),
				'equipments'=>array(
					array(
						'filter_label'=>'latitude',
						'filter_display'=>'lat'
					),
					array(
						'filter_label'=>'longitude',
						'filter_display'=>'lng'
					)
				),
				'equip_order'=>array(
					array(
						'filter_label'=>'equipments_id',
						'filter_display'=>'equipments_id'
					)
				),
				'kyc_document'=>array(
					array(
						'filter_label'=>'hirers_id',
						'filter_display'=>'hirers_id'
					)
				),
			);
		}
		else{
			$result = array(
				'equip_request'=>array(
					array(
						'filter_label'=>'request_status',
						'filter_display'=>'request_status',
						'preload_query'=>'',
					)
					
				)
			);
		}

		if (array_key_exists($tablename, $result)) {
			return $result[$tablename];
		}
		return false;
	}

	/**
	 * This is the configuration for the edit form of the entities.
	 * exclude take an array of fields in the entities that should be removed from the form.
	 */
	private function buildUpdateConfig()
	{
		$userType = $this->webSessionManager->getCurrentUserProp('user_type');
		$exclude = [];
		if($userType == 'customer'){
			$exclude = array('email','customer_path');
		}
		$this->updateConfig = array
		(
			'equip_request' => array(
				'exclude' => array('date_created','date_modified'),
			),
			'reviews' => array(
				'exclude' => array('date_created'),
			),
			'kyc_document' => array(
				'exclude' => array('kyc_document_path')
			)
		//add new entry to this array
		);
	}

	public function getInsertConfig(?string $entities)
	{
		if (array_key_exists($entities, $this->insertConfig)) {
			$result=$this->insertConfig[$entities];
			if (($fil=$this->getFilter($entities))) {
				$result['filter']=$fil;
			}
			$this->apiEntity = false;
			return $result;
		}
		if (($fil=$this->getFilter($entities))) {
			return array('filter'=>$fil);
		}
		return false;
	}

	public function getUpdateConfig(?string $entities)
	{
		if (array_key_exists($entities, $this->updateConfig)) {
			$result=$this->updateConfig[$entities];
			if (($fil=$this->getFilter($entities))) {
				$result['filter']=$fil;
			}
			return $result;
		}
		if (($fil = $this->getFilter($entities))) {
			return array('filter'=>$fil);
		}
		return false;
	}
}
 ?>