<?php 
/**
* This is the class that manages all information and data retrieval needed by the admin section of this application.
*/
namespace App\Models\Custom;

use CodeIgniter\Model;
use App\Models\WebSessionManager;

class AdminData extends Model
{	
	protected $db;
	private $webSessionManager;

	public function __construct()
	{
		helper(['string','array']);
		$this->db = db_connect();
		$this->webSessionManager = new WebSessionManager;
	}

	public function loadDashboardData()
	{
		//check the permmission first
		$result = array();
		$hirers = loadClass('hirers');
		$owners = loadClass('owners');
		$equipments = loadClass('equipments');
		$equipRequest = loadClass('equip_request');
		$equipPayment = loadClass('equip_payment');
		$withdrawalRequest = loadClass('withdrawal_request');
		$equipOrder = loadClass('equip_order');
		$reviews = loadClass('reviews');

		$result['countData'] = array(
			'hirers'=> $hirers->totalCount("where status='1'"),
			'owners' => $owners->totalCount(),
			'equipments' => $equipments->totalCount("where status='1'"),
			'equipbooked' => $equipRequest->totalCount("where request_status='booked'"),
			'equipReceived' => $equipRequest->totalCount("where request_status='received'"),
			'equipReturned' => $equipRequest->totalCount("where request_status='returned'"),
			'payTotal' => $equipPayment->getTotalAmount() ?? 0,
			'payTotalDaily' => $equipPayment->getTotalAmount(true) ?? 0,
			'payoutAmount' => $withdrawalRequest->getTotalAmountPayout() ?? 0,
			'approvedOrder' => $equipOrder->totalCount("where order_status='accepted'"),
			'pendingOrder' => $equipOrder->totalCount("where order_status='pending'"),
			'reviews' => $reviews->totalCount()
		);
		$result['revenueDistrix'] = $equipPayment->getRevenueDistrix();
		$result['orderStatusDistrix'] = $equipOrder->getOrderStatusDistrix();
		$result['withdrawalStatusDistrix'] = $withdrawalRequest->getWithdrawalStatusDistrix();

		// print_r($result);exit;
		return $result;
	}

	public function getAdminSidebar($combine = false)
	{
		$role = loadClass('role');
		$role = new $role();
		// using $combine parameter to take into consideration path that're not captured in the admin sidebar
		$output = ($combine) ? array_merge($role->getModules(),$role->getExtraModules()) : $role->getModules();
		return $output;
	}

	public function getCanViewPages(object $role,$merge=false)
	{
		$result = array();
		$allPages = $this->getAdminSidebar($merge);
		$permissions = $role->getPermissionArray();
		
		foreach ($allPages as $module => $pages) {
			$has = $this->hasModule($permissions,$pages,$inter);
			$allowedModule = $this->getAllowedModules($inter,$pages['children']);
			$allPages[$module]['children'] = $allowedModule;
			$allPages[$module]['state'] = $has;
		}
		return $allPages;
	}

	private function getAllowedModules($includesPermission,$children)
	{
		$result = $children;
		$result=array();
		foreach($children as $key=>$child){
			if(is_array($child)){
				foreach($child as $childKey => $childValue){
					if (in_array($childValue, $includesPermission)) {
						$result[$key]=$child;
					}
				}
			}else{
				if (in_array($child, $includesPermission)) {
					$result[$key]=$child;
				}
			}
			
		}
		return $result;
	}

	private function hasModule($permission,$module,&$res)
	{
		if(is_array(array_values($module['children']))){
			$res =array_intersect(array_keys($permission), array_values_recursive($module['children']));
		}else{
			$res =array_intersect(array_keys($permission), array_values($module['children']));
		}
		
		if (count($res)==count($module['children'])) {
			return 2;
		}
		if (count($res)==0) {
			return 0;
		}
		else{
			return 1;
		}
	}

}

 ?>