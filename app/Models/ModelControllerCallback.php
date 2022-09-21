<?php 
	/**
	* This is the class that contain the method that will be called whenever any data is inserted for a particular table.
	* the url path should be linked to this page so that the correct operation is performed ultimately. T
	*/
	namespace App\Models;

	use CodeIgniter\Model;
	use App\Models\WebSessionManager;
	use App\Models\Mailer;
	use CodeIgniter\I18n\Time;

	class ModelControllerCallback extends Model
	{
		protected $db;
		private $webSessionManager;
		private $mailer;

		function __construct()
		{
			helper(['string','url','array']);
			$this->webSessionManager = new WebSessionManager;
			$this->mailer = new Mailer;
			$this->db = db_connect();
		}

		public function onAdminInserted($data,$type,&$db,&$message)
		{
			//remember to remove the file if an error occured here
			//the user type should be admin
			$user = loadClass('user');
			if ($type=='insert') {
				// login details as follow: username = email, password = firstname(in lowercase)
				$password = encode_password(strtolower($data['firstname']));
				$param = array('user_type'=>'admin','username'=>$data['email'],'password'=>$password,'user_table_id'=>$data['LAST_INSERT_ID']);
				$std = new $user($param);
				if ($std->insert($db,$message)) {
					return true;
				}
				return false;
			}
			return true;
		}

		public function onService_chargeInserted($data,$type,&$db,&$message,&$extra)
		{
			if($type == 'insert'){
				# this is to ensure there is only one active service charge
				$lastInsertId = $data['LAST_INSERT_ID'];
				$query = "update service_charge set status='0' where ID != '$lastInsertId' ";
				if(!$db->query($query)){
					$message = "error updating the service charge status";
					return false;
				}
			}

			return true;
		}

		public function onHirersInserted($data,$type,&$db,&$message,&$extra)
		{
			$modelName = "hirers";
			$uploadModel = "kyc_document";
			if($type == 'insert'){
				# insert here
			}

			if($type == 'update'){
				$lastInsertId = $data['ID'];
				$info = $data;
				
				if(!empty($_FILES)){
					# using this for kyc_document upload
					$uploadImage = $this->uploadModelImage($db,$lastInsertId,$modelName,$uploadModel,$info,$response);
					if(!$uploadImage){
						$message = $response;
						return false;
					}else{
						if(!is_numeric($response)){
							$extra['kyc_document_path'] = $response;
						}
					}
				}
			}

			return true;
		}

		public function onEquip_requestInserted($data,$type,&$db,&$message,&$extra)
		{	
			# this is for the admin section
			if($type == 'update'){
				if(isset($data['request_status'])){
					if(!isset($data['equip_order_id'])){
						$message = "something went wrong,reload page";
						return false;
					}
					$equipOrder = loadClass('equip_order');
					$equipOrder->ID = $data['equip_order_id'];
					if(!$equipOrder->load()){
						$message = "order is not available";
						return false;
					}
					$equipOrder->order_status = $data['request_status'];
					if(!$equipOrder->update()){
						$message = "booking status couldn't be updated";
						return false;
					}
				}
			}

			return true;
		}

		public function onEquipmentsInserted($data,$type,&$db,&$message,&$extra)
		{
			$modelName = "equipments";
			$uploadModel = "equip_images";
			if($type == 'insert'){
				$lastInsertId = $data['LAST_INSERT_ID'];

				if(!empty($_FILES)){
					# using this for equipment images
					$uploadImage = $this->uploadModelImage($db,$lastInsertId,$modelName,$uploadModel,null,$response);
					if(!$uploadImage){
						$message = $response;
						return false;
					}else{
						$this->createEquipStock($db,$lastInsertId,$data['quantity']);
					}
				}
			}

			if($type == 'update'){
				$lastInsertId = $data['ID'];

				if(!empty($_FILES)){
					$uploadImage = $this->uploadModelImage($db,$lastInsertId,$modelName,$uploadModel,null,$response);
					if(!$uploadImage){
						$message = $response;
						return false;
					}else{
						if(!is_numeric($response)){
							$equipment = loadClass('equipments');
							$equipment->ID = $lastInsertId;
							$equipment->load();
							$equipImages = $equipment->equip_images ?? [];
							$extra['equip_images'] = $equipImages;
						}
					}
				}
				# update the stock table
				if(isset($data['quantity'])){
					$builder = $db->table('equip_stock');
					$equip = $builder->getWhere(['equipments_id'=>$lastInsertId]);
					$totalUsed = $equip->getResultArray()[0]['total_used'];
					$totalLeft = ($data['quantity'] - $totalUsed);
					if($totalLeft <= 0){
						# this should not even happen
						$message = "Something went wrong";
						return false;
					}
					$builder->update(
						[
							'total_avail' => $data['quantity'],
							'total_left' => $totalLeft
						],['equipments_id'=>$lastInsertId]
					);
				}
			}

			return true;
		}

		/**
		 * [uploadModelImage description]
		 * @param  object $db            [description]
		 * @param  int    $equipments_id [description]
		 * @param  string &$response     [description]
		 * @return bool               [description]
		 */
		private function uploadModelImage(object $db,int $modelID,string $modelName,string $uploadModel,array $info = null,string &$response = null)
		{
			$validation =  \Config\Services::validation();
			$request = \Config\Services::request();
			$insertID=null; $updateFlag=false; $naming=null;

			$tempModel = strtolower($modelName);
			$fieldName = "equip_image_path";
			$uploadLabel = 'Equip Image';
			if($uploadModel == 'kyc_document'){
				$fieldName = 'kyc_document_path';
				$uploadLabel = "Kyc Image";
			}
			$validationRule = [
	            "$fieldName" => [
	                'label' => $uploadLabel,
	                'rules' => "uploaded[$fieldName]"
	                    . "|is_image[$fieldName]"
	                    . "|mime_in[$fieldName,image/jpg,image/jpeg,image/png]"
	                    . "|ext_in[$fieldName,png,jpg,jpeg]"
	                    . "|max_size[$fieldName,819200]", # 800kb
	            ],
	        ];
	        $validation->setRules($validationRule);
	        if (!$validation->run()) {
	            if ($validation->hasError($fieldName)) {
	            	$response = $validation->getError($fieldName);
	            	return false;
				}
	        }

	        $insertString = "";
	        if ($imagefile = $request->getFiles()) {
	        	$multipleImageFlag = false;
	        	$countImage = count($imagefile);
	            foreach ($imagefile[$fieldName] as $img) {
	                if (!$img->hasMoved()) {
	                	$multipleImageFlag = true;
                    	$uploadModelPath = $uploadModel."_path";
                    	$getUpload = $this->isModelImageExist($uploadModel,$modelID,$uploadModelPath,$tempModel);
                    	$naming = $img->getRandomName();
            			if ($getUpload !== 'insert') {
            				# this would mean an update
            				$this->removeMultiImages($db,$uploadModel,$getUpload,$uploadModelPath);
            				$updateFlag = true;
            			}

            			$modelPathSlash = $uploadModel.'/';
            			$targetPath = WRITEPATH . 'uploads/' . $modelPathSlash; 
            			$img->move($targetPath, $naming);
            			$naming = '';
            			$filepath = $targetPath.$img->getName();
            			$publicPath = ROOTPATH. 'public/uploads/'.$modelPathSlash;
            			if(!is_dir($publicPath)){
            				mkdir($publicPath, 0777, true);
            			}
            			
            			$filename = $modelPathSlash.basename($filepath);
            			$publicPath = createSymlink($filename, $filepath);
            			$publicPath = base_url($publicPath);

            			if($uploadModel == 'equip_images'){
            				$temp = "( {$db->escape($publicPath)}, {$db->escape($modelID)} )";
            			}
            			if($uploadModel == 'kyc_document'){
            				$temp = "( {$db->escape($publicPath)}, {$db->escape($info['kyc_name'])},{$db->escape($modelID)} )";
            			}
            			$insertString .= $insertString ? ",$temp" : $temp;
	                }else{
	                	$response = "The file has already been moved.";
	                	return false;
	                }
	            } # end foreach

	            if(!$multipleImageFlag){
                	$response = "Kindly ensure image is set for multiple uploads";
        			return;
                }

            	$query = '';
            	if($uploadModel == 'equip_images'){
		            $query = "insert ignore into equip_images (equip_images_path,equipments_id) values $insertString";
            	}
            	else if($uploadModel == 'kyc_document'){
		            $query = "insert ignore into kyc_document (kyc_document_path,document_name,hirers_id) values $insertString";
            	}
            	$insertID = $this->saveModelImage($db,$query,false);
                
                $response = ($updateFlag) ? "update" : $insertID;
                return true;
	        }
	        $response = "Kindly ensure image is set for multiple uploads";
	        return false;
		}

		/**
		 * This is to run the query string
		 * @param  object       $db         [description]
		 * @param  string       $query      [description]
		 * @param  bool|boolean $updateFlag [description]
		 * @return [type]                   [description]
		 */
		private function saveModelImage(object $db,string $query,bool $updateFlag=false)
		{
			$query = $db->query($query);
			if($query && !$updateFlag){
            	return $db->insertID();
            }else{
            	return;
            }
		}

		/**
		 * This would remove multi-images
		 * @param  object $db         [description]
		 * @param  array  $images     [description]
		 * @param  string $columnName [description]
		 * @return [type]             [description]
		 */
		private function removeMultiImages(object $db,string $uploadModel,array $images,string $columnName){
			$ids = '';
			foreach($images as $image){
				$this->removeImageSymlink($image[$columnName]);
				$temp = "{$image['id']}";
				$ids .= ($ids) ? " or id={$temp}" : " id={$temp}";
			}
			$query = "delete from $uploadModel where $ids";
			$deleted = $db->query($query);
			return;
		}

		/**
		 * This is to remove the image symlink and original image
		 * @param  string $image [description]
		 * @return void        [description]
		 */
		private function removeImageSymlink(string $image){
			return removeSymlinkWithImage($image);
		}

		/**
		 * This is to check if the model exist already for insertion or update
		 * 
		 * @param  string $uploadModel 	[description]
		 * @param  int    $modelID    	[description]
		 * @param  string $name  		[description]
		 * @param  string $modelName 	[description]
		 * @return string        		[description]
		 */
		private function isModelImageExist(string $uploadModel, int $modelID, string $name, $modelName)
		{
			if ($modelID) {
				# this means that it is updating
				$field = $modelName."_id";
				$query = "select id,$name from $uploadModel where $field = ? order by date_created desc";
				$result = $this->db->query($query, array($modelID));
				$result = $result->getResultArray();

				# the return message 'insert' is a rare case whereby there is no media file at first
				# yet one want to add the media file through update action
				return (!empty($result)) ? $result : 'insert';
			}
		}

		/**
		 * This is to create the equip stock
		 * @param  object $db            [description]
		 * @param  int    $equipments_id [description]
		 * @param  int    $quantity      [description]
		 * @return void                [description]
		 */
		private function createEquipStock(object $db, int $equipments_id, int $quantity)
		{
		# this would be updated when hirers make payment on the equipment
			$builder = $db->table('equip_stock');
			$param = [
				'equipments_id' => $equipments_id,
				'total_avail' => $quantity,
				'total_left' => $quantity
			];
			$builder->insert($param);
		}

		public function onEquip_orderInserted($data,$type,&$db,&$message,&$extra)
		{
			if($type == 'insert'){
				$lastInsertId = $data['LAST_INSERT_ID'];
				$bookingType = 'insert';
				$notifyType = 'order_create';
				if(!isset($data['prev_equip_order_id'])){
					# this is the normal equip_request
					$dateTimeStamp = $this->formatToUTC();
					$param = [
						'equip_order_id' => $lastInsertId,
						'hirers_id' => $data['hirers_id'],
						'equipments_id' => $data['equipments_id'],
						'quantity' => $data['quantity'],
						'rental_from' => $data['rental_from'],
						'rental_to' => $data['rental_to'],
						'delivery_location' => $data['delivery_location'],
						'date_created' => $dateTimeStamp,
						'date_modified' => $dateTimeStamp,
					];
					$builder = $db->table('equip_request');
					$builder->insert($param);
				}
				else{
					# this is the extended equip_request
					$dateTimeStamp = $this->formatToUTC();
					$param = [
						'prev_equip_order' => $data['prev_equip_order_id'],
						'equip_order_id' => $lastInsertId,
						'hirers_id' => $data['hirers_id'],
						'equip_request_id' => $data['equip_request_id'],
						'rental_from' => $data['rental_from'],
						'rental_to' => $data['rental_end'],
						'date_created' => $dateTimeStamp,
						'date_modified' => $dateTimeStamp,
					];
					$builder = $db->table('extend_equip_request');
					$builder->insert($param);
					$bookingType = 'extended';
					$notifyType = 'equip_extend';
				}
				$customer = getCustomer();
				# send mail notification
				if(!$this->bookingNotification($customer,$data,$bookingType)){
					$message = "something went wrong with the data";
					return false;
				}
				$data = ['equip_order_id'=>$lastInsertId,'fcm_token'=>$customer->fcm_token];
				$this->pushNotify($notifyType,$data,false);
				
				$extra = ['equip_order_id' => $lastInsertId];
			}

			if($type == 'update'){
				# performing update action here

				$lastInsertId = $data['ID'];
				$date = $this->formatToUTC();
				$param = [
					'quantity' => $data['quantity'],
					'rental_from' => $data['rental_from'],
					'rental_to' => $data['rental_to'],
					'delivery_location' => $data['delivery_location'],
					'date_modified' => $date,
				];
				$builder = $db->table('equip_request');
				$builder->update($param,['equip_order_id' => $lastInsertId]);
				$customer = getCustomer();
				if(!$this->bookingNotification($customer,$data,'update')){
					$message = "something went wrong with the data";
					return false;
				}
				$data = ['equip_order_id'=>$lastInsertId,'fcm_token'=>$customer->fcm_token];
				$this->pushNotify('order_update',$data,false);
			}
			return true;
		}

		public function onEquip_delivery_statusInserted($data,$type,&$db,&$message,&$extra)
		{
			if($type == 'insert'){
				# do something
				# this would be the action equiv to delivery_status to show their relationship
				
				/**
				 * pending -> init_payment/verify_payment -> equip_delivery_status
				 * booked -> verify_payment -> equip_request -> processing -> equip_order
				 * delivered_hirer -> received -> equip_request -> delivered-> -> equip_order
				 * picked_from_owner -> 
				 * in_use -> 
				 * picked_from_hirer -> 
				 * returned -> returned -> equip_request -> returned -> equip_order
				*/

				$deliveryStatus = $data['delivery_status'];
				if($deliveryStatus == 'delivered_hirer'){
					$param = ['request_status'=>'received'];
					$where = ['equip_order_id' => $data['equip_order_id']];
					$this->updateModelTable($db,'equip_request',$param,$where);

					$param = ['order_status'=>'delivered'];
					$where = ['id'=>$data['equip_order_id']];
					$this->updateModelTable($db,'equip_order',$param,$where);
					if($extendOrder = $this->getExtendOrder($data['equip_order_id'])){
						$where = ['id'=>$extendOrder['prev_equip_order']];
						$this->updateModelTable($db,'equip_order',$param,$where);
					}
				}

				if($deliveryStatus == 'returned'){
					$param = ['request_status'=>'returned'];
					$where = ['equip_order_id' => $data['equip_order_id']];
					$this->updateModelTable($db,'equip_request',$param,$where);

					$param = ['order_status'=>'returned'];
					$where = ['id'=>$data['equip_order_id']];
					$this->updateModelTable($db,'equip_order',$param,$where); # update normal order

					if($extendOrder = $this->getExtendOrder($data['equip_order_id'])){
						$where = ['id'=>$extendOrder['prev_equip_order']];
						$this->updateModelTable($db,'equip_order',$param,$where);
					}

					# update the stock after the equip had been returned
					if(!$this->updateStockAfterReturned($db,$data['equip_order_id'],$response)){
						$message = $response;
						return false;
					}
					# considering if mail notification should be sent to the owner
					
					$message = "equipment successfully returned";
				}
			}

			if($type == 'update'){
				# do something
			}

			return true;
		}

		private function getExtendOrder(object $db,int $equip_order_id){
			$builder = $db->table('extend_equip_request');
			$result = $builder->getWhere(['prev_equip_order'=>$equip_order_id]);
			if($result->getNumRows() > 0){
				return $result->getResultArray()[0];
			}
			return false;
		}

		/**
		 * This is to update the equip_stock after being returned
		 * and notify the owner about the return action
		 * @param  object $db             [description]
		 * @param  int    $equip_order_id [description]
		 * @param  [type] &$response       [description]
		 * @return [type]                 [description]
		 */
		private function updateStockAfterReturned(object $db,int $equip_order_id, &$response=null){
			$equip_order = loadClass('equip_order');
			$equip_stock = loadClass('equip_stock');
			$equip_order->ID = $equip_order_id;
			if(!$equip_order->load()){
				$response = "something went wrong with the order";
				return false;
			}

			$customer = $equip_order->owners;
			$quantity = $equip_order->quantity;
			$equipments_id = $equip->equipments_id;

			if($equip_order->order_status == 'normal'){
				if(!$equip_stock->updateStockValue($equip_order_id)){
					$response = "something went wrong while updating status";
					return false;
				}
			}

			# preping data to send email
			$data = [
				'equipments_id' => $equipments_id,
				'quantity' => $quantity,
				'order_number' => $equip_order->order_number,
				'total_amount' => $equip_order->total_amount
			];
			$notifyData = ['equip_order_id'=>$equip_order_id];
			$this->pushNotify('equip_return',$notifyData,false);

			return $this->bookingNotification($customer,$data,'returned','booking_request_status',false);
		}

		/**
		 * This is a wrapper for update on model
		 * @param  object $db    [description]
		 * @param  string $model [description]
		 * @param  array  $param [description]
		 * @param  array  $where [description]
		 * @return [type]        [description]
		 */
		private function updateModelTable(object $db,string $model,array $param, array $where){
			$builder = $db->table($model);
			$builder->update($param, $where);
		}

		/**
		 * This is to handle notification for owners
		 * @param  object $customer [description]
		 * @param  array  $data     [description]
		 * @param  string $type     [description]
		 * @return [type]           [description]
		 */
		private function bookingNotification(object $customer,array $data,string $type='insert',string $filename='booking_request',bool $hirers=false){
			$equip_request = loadClass('equip_request');
			return $equip_request->bookingNotification($customer,$data,$type,$filename,$hirers);
		}

		private function pushNotify(string $type,array $data,bool $isHirer=false){
			$equip_request = loadClass('equip_request');
			$equip_request->pushNotification($type,$data,$isHirer);
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