<?php 
/**
* model to manage table action information
*/
namespace App\Models;

use CodeIgniter\Model;

class TableActionModel extends Model
{

	/** 
	| NOTE: 1. The method name is determined by yourself. It must've been defined in | it entity file under tableAction property
	|
	 */

	/**
	 * function to generate the action link needed for the enable and disable field
	 * @param object $object
	 * @param string classname
	 * @return array
	 */
	public function getEnabled($object,$classname=''){
		if(is_array($object)){
			$object = (object) $object;
		}
		$classname = empty($classname)?lcfirst(get_class($object)):ucfirst($classname);
		$link = base_url("ac/disable/$classname");
		$label = "disable";

		if(strtolower($classname) == 'admin'){
			$label = 'Ban';
		}else if(strtolower($classname) == ('staff')){
			$label = "Ban";
		}

		$status = is_array($object)?$object['status']:$object->status;
		if (!$status) { # check if status is false
			$link = base_url("ac/enable/$classname");
			$label = "enable";
			if (strtolower($classname) == ('staff')) {
				$label = 'UnBan';
			}else if(strtolower($classname) == 'admin'){
				$label = 'UnBan';
			}
		}
		$link = strtolower($link);
		return $this->buildActionArray($label,$link,1,1);
	}

	/**
	 * @param object $object
	 * @param string classname
	 * @return array
	 */
	public function getMail($object,$classname=''){
		$classname = empty($classname)?lcfirst(get_class($object)):ucfirst($classname);
		$link = base_url("mail/$classname");
		$label = "Send Mail";

		$link = strtolower($link);
		return $this->buildActionArray($label,$link,1,1);
	}

	/**
	 * This is a method that ensure we can change the data value of an entity based on
	 * the allowed value in the database e.g enum values
	 * @param  [type] $object    [description]
	 * @param  string $classname [description]
	 * @return [type]            [description]
	 */
	public function getEnumStatus($object, $classname=''){
		if(is_array($object)){
			$object = (object) $object;
		}
		$classname = empty($classname)?lcfirst(get_class($object)):ucfirst($classname);
		$link = base_url("changeStatus/$classname");
		$label = "Change Status";
		if (strtolower($classname) == 'extend_equip_request') {
			$status = is_array($object)?$object['request_status']:$object->request_status;
			$status = strtolower($status);
			if ($status == 'rejected') {
				$label = "Approve Request";
				$link = base_url("changeStatus/$classname/approved");
			}else if ($status == 'approved') {
				$label = "Reject Request";
				$link = base_url("changeStatus/$classname/rejected");
			}
		}
		$link = strtolower($link);
		return $this->buildActionArray($label,$link,1,1);
	}

	/**
	 * function to return the array need
	 * @param string 	$label
	 * @param string 	$link
	 * @param int 		critical
	 * @param int 		$ajax
	 * @return array
	 */
	protected function buildActionArray($label,$link,$critical,$ajax){
		$result = array();
		$result['label'] = $label;
		$result['link'] = $link;
		$result['isCritical'] = $critical;
		$result['ajax'] = $ajax;
		return $result;
	}
}
