<?php 
/**
* This is the class that manages all information and data retrieval needed by the admin section of this application.
*/
namespace App\Models\Custom;

use CodeIgniter\Model;
use App\Models\WebSessionManager;
use App\Entities\Allocation;
use App\Entities\Applicant_allocation;
use App\Entities\Staff;
use App\Entities\Children;
use App\Entities\Tenant;
use App\Entities\Departments;

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
		$result = [];

		$result['countData'] = [
			'approvedAllocation' => Allocation::totalCount("where status='approved'"),
			'applicant' => Applicant_allocation::totalCount(),
			'staff' => Staff::totalCount(),
			'children' => Children::totalCount(),
			'tenant'=> Tenant::totalCount(),
			'departments'=> Departments::totalCount(),
		];
		$result['applicantDistrix'] = Applicant_allocation::init()->getApplicantDistrix();
		$result['genderDistrix'] = Applicant_allocation::init()->getGenderDistrix();
		$result['staffStatusDistrix'] = Applicant_allocation::init()->getStaffStatusDistrix();

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