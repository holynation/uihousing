<?php 

/**
* The controller that validate forms that should be inserted into a table based on the request url.
each method wil have the structure validate[modelname]Data
*/
namespace App\Models;

use CodeIgniter\Model;
use App\Models\WebSessionManager;
use CodeIgniter\I18n\Time;

class ModelControllerDataValidator extends Model
{
	protected $db;
	private $webSessionManager;
	
	function __construct()
	{
		helper('string');
		$this->db = db_connect();
		$this->webSessionManager = new WebSessionManager;
	}

	public function validateHirersData(&$data,$type,&$message){
		if($type == 'update'){
			if(isset($data['kyc_name'])) {
				$kycName = $data['kyc_name'];
				$allowedKyc = [
					'international_passport' => 'International Passport',
					'voters_card' => 'Voters Card',
					'national_id_card' => 'National ID Card',
					'driver_license' => 'Driver License'
				];
				if(!isset($allowedKyc[$kycName])){
					$message = "Sorry, kindly use the allowed kyc for verification";
					return false;
				}
				$data['kyc_name'] = $allowedKyc[$kycName];
			}
		}
		return true;
	}

	public function validateService_chargeData(&$data,$type,&$message){
		if($type == 'insert'){
			if(isset($data['percentage']) && isset($data['amount'])){
				$message = "Sorry, you can only use one: either percentage or amount";
				return false;
			}

			if(isset($data['percentage']) && !is_decimal($data['percentage'])){
				$message = "percentage value must be a decimal";
				return false;
			}
		}
		return true;
	}

	public function validateEquipmentsData(&$data,$type,&$message){
		if($type == 'insert'){
			if(!isset($data['owners_id'])){
				$message = 'Oops, invalid operation.';
				return false;
			}
			if($data['avail_from'] || $data['avail_to']) {
				$data['avail_from'] = $this->formatToUTC($data['avail_from']);
				$data['avail_to'] = $this->formatToUTC($data['avail_to']);
			}
		}

		if($type == 'update'){
			if(isset($data['avail_from']) || isset($data['avail_to'])) {
				$data['avail_from'] = $this->formatToUTC($data['avail_from']);
				$data['avail_to'] = $this->formatToUTC($data['avail_to']);
			}
		}
		return true;
	}

	public function validateUsers_locationData(&$data,$type,&$message){
		if($type == 'insert'){
			$request = \Config\Services::request();
			$data['ip'] = $request->getIPAddress();
		}
		return true;
	}

	public function validateEquip_delivery_statusData(&$data,$type,&$message)
	{
		if($type == 'insert'){
			$allowedStatus = ['pending','picked_from_owner','delivered_hirer','in_use','picked_from_hirer','returned'];
			if(!in_array($data['delivery_status'], $allowedStatus)){
				$message = "Kindly use the allowed delivery_status value";
				return false;
			}

			if(!$data || !isset($data['equip_order_id'])){
				$message = 'equip_order_id is missing';return false;
			}

			$equip_order = loadClass('equip_order');
			$equip_order->ID = $data['equip_order_id'];
			if(!$equip_order->load()){
				$message = "equip_order doesn't exists";
				return false;
			}
			# validate against third party should not update status
			$customer = getCustomer();
			if($equip_order->hirers_id != $customer->ID){ # this would be the hirer
				if(isset($customer->owner_id) && ($customer->owner_id != $equip_order->owners_id)){ # this would be the owner
					$message = "Oops, you can't perform the operation";
					return false;
				}
			}

			$equipment_id = $equip_order->equip_request->equipments_id;
			if(!$equipment_id){
				$message = "something went wrong";
				return false;
			}
			$data['equipments_id'] = $equipment_id;
			$equip_delivery_status = loadClass('equip_delivery_status');
			$param = ['equip_order_id'=>$data['equip_order_id']];
			# this is to ensure that there is at least one data entry for the
			# equipments booked
			$result = $equip_delivery_status->getWhere($param,$count,0,1,false);
			if(!$result){
				$message = "Oops, it seems no available equipments booked";
				return false;
			}
		}

		return true;
	}

	public function validateReviewsData(&$data,$type,&$message)
	{
		if($type == 'insert'){
			if(!$data || !isset($data['equipments_id'])){
				$message = 'equipments_id is missing';return false;
			}

		}
		return true;
	}

	public function validateWithdrawal_requestData(&$data,$type,&$message)
	{
		if($type == 'insert'){
			$earnings = loadClass('earnings');
			$customer = getCustomer();
			if(!isset($customer->is_owner)){
				$message = "Oops, invalid operation on endpoint";
				return false;
			}
			$balance = $earnings->getEarningBalance($customer->owner_id);
			$withAmnt = $data['amount'];
			if($balance <= $withAmnt){
				$message = "Sorry, you don't have enough amount for withdrawal";
				return false;
			}
			$generateNumber = generateNumber($this->db,'withdrawal_request','request_number');
			$data['request_number'] = $generateNumber;
			$data['request_status'] = 'pending';
		}
		return true;
	}

	private function validateExtendEquip(int $prev_equip_order_id,string $rental_end,&$response='',&$result=[])
	{
		if(!isset($prev_equip_order_id)){
			$response = 'prev_equip_order_id is missing';return false;
		}
		$equip_order = loadClass('equip_order');
		$equip_order->ID = $prev_equip_order_id;
		if(!$equip_order->load()){
			$message = "the original order is not available";
			return false;
		}
		$endDate = $equip_order->equip_request->rental_to;

		$prevDate = Time::parse(new time($endDate));
		$curDate = Time::parse(new time($rental_end));
		# check if prev date is greater than the current date
		if($prevDate->isAfter($curDate)){
			$response = "rental_end can't be lower than it previous end date";
			return false;
		}
		$result['equip_request_id'] = $equip_order->equip_request->ID;
		$result['equipments_id'] = $equip_order->equip_request->equipments_id;
		$result['rental_from'] = $endDate;
		$result['rental_to'] = $rental_end;
		$result['quantity'] = $equip_order->quantity;
		$result['order_type'] = 'extended';
		$result['prev_equip_order'] = $prev_equip_order_id;
		return true;
	}

	public function validateEquip_orderData(&$data,$type,&$message){
		if($type == 'insert' && !empty($data)){
			$data['order_number'] = generateNumber($this->db,'equip_order','order_number');
			if(isset($data['prev_equip_order_id'])){
				if(!$this->validateExtendEquip($data['prev_equip_order_id'],$data['rental_end'],$response,$result)){
					$message = $response;
					return false;
				}
				$data = array_merge($data,$result);
			}
			$equip = loadClass('equipments');
			$equip->ID = $data['equipments_id'];
			if(!$equip->load()){
				$message = "Sorry, Equipments not found";
				return false;
			}
			# no need to check equip avail since the equip is already with the hirer
			if(!isset($data['prev_equip_order_id'])){
				if(!$this->equipAvail($equip,$data)){
					$message = "Sorry, the equipments is not available at the moment";
					return false;
				}
			}
			# appending this new data value to the data array for insertion
			$data['total_amount'] = $this->calcEquipAmount($equip,$data['rental_from'],$data['rental_to'],$data['quantity']);
			$charge = getServiceCharge($this->db,$data['total_amount']);
			if(!$charge){
				$message = "service charge not available";
				return false;
			}
			$data['service_charge'] = $charge;
			$data['owners_id'] = $equip->owners_id;
		}

		if($type == 'update'){
			$equip_order = loadClass('equip_order');
			$equip_order->ID = $data['ID'];
			$equip_order->load();
			$equip = $equip_order->equip_request->equipments;

			if(!$this->equipAvail($equip,$data)){
				$message = "Sorry, the equipments is not available at the moment";
				return false;
			}
			$data['total_amount'] = $this->calcEquipAmount($equip,$data['rental_from'],$data['rental_to'],$data['quantity']);
			$data['equipments_id'] = $equip->ID;
			$data['order_number'] = $equip_order->order_number;
		}
		return true;
	}

	/**
	 * This is to validate equipment availability
	 * @param  object $equip [description]
	 * @param  array  $data  [description]
	 * @return [type]        [description]
	 */
	private function equipAvail(object $equip,array $data){
		$availFrom = $equip->avail_from;
		$availTo = $equip->avail_to;
		$availQty = $equip->equip_stock->total_left ?? null;
		if(!$availQty){
			return false;
		}
		$timeStart = Time::parse(new time($availFrom)); # from the database
		$timeStart1 = Time::parse(new time($data['rental_from'])); # from the user

		$timeEnd = Time::parse(new time($availTo)); # from the database
		$timeEnd1 = Time::parse(new time($data['rental_to'])); # from the user

		if(($timeStart1->isAfter($timeStart) || $timeStart1->equals($timeStart)) && ($timeEnd1->isBefore($timeEnd) || $timeEnd1->equals($timeEnd) )){
			if($data['quantity'] > $availQty){
				return false;
			}
			return true;
		}
		return false;
	}
	
	private function calcEquipAmount(object $equip,string $start, string $end,int $quantity){
		$cost = $equip->cost_of_hire;
		$costInterval = $equip->cost_of_hire_interval;

		$current = Time::parse(new time($start));
		$test    = Time::parse(new time($end));
		$diff = $current->difference($test);
		$days = abs($diff->getDays());
		$result = ( (($cost*$days)/$costInterval) * $quantity);
		return round($result, 2);
	}

	/**
	 * [formatToUTC description]
	 * @param  string|null $date [description]
	 * @return [type]            [description]
	 */
	private function formatToUTC(string $date=null){
		$date = $date ?? "now";
		$date = new Time($date, 'UTC');
		$date = $date->format('Y-m-d H:i:s');
		return $date;
	}
}


?>