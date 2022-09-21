<?php

namespace App\Controllers;

use App\Models\Mailer;
use CodeIgniter\I18n\Time;

/**
 * This is a class that handles webhook payment for paystack and stripe
 */
class Authhook extends BaseController
{
	private $mailer;

	protected $db;

	public function __construct()
	{

		helper(['string', 'url']);
		$this->mailer = new Mailer;
		$this->db = db_connect();
	}

	/**
	 * THis is a test method
	 * @return [type] [description]
	 */
	public function init_payment()
	{
		
		$key = getenv('paystackKey');
		$headers = array('Authorization'=>"Bearer $key",'Content-Type'=>'application/json');
		$nairaAmount = 2000;
		$amount = $nairaAmount*100;
		$metadata = [
			'equip_order_id' => 185,
			'receipt_ref' => 'NFbZa1t1wSeWerer' //generateHashRef('receipt')
		];
		$param = array('email'=>"holynationdevelopment@gmail.com",'amount'=>$amount,'metadata'=>json_encode($metadata));
		
		$param = json_encode($param);
		$url = 'https://api.paystack.co/transaction/initialize';
		$requestReport = request($url,'post',$headers,$param,true,$output);
		if (!$requestReport) {
			$response = 'unable to contact payment server';
			return false;
		}
		$output = json_decode($output,true);
		$output = $output['data'];
		// unset($output['data']);
		$output['amount']= $amount;
		echo json_encode($output);
		exit;
	}

	/**
	 * THis is a test method
	 * @return [type] [description]
	 */
	private function testPaystackDataEvent(string $ref){
		$data = array (
		  'event' => 'charge.success',
		  'data' => 
			  array (
			    'id' => 302961,
			    'domain' => 'live',
			    'status' => 'success',
			    'reference' => $ref,
			    'amount' => 10000,
			    'message' => NULL,
			    'gateway_response' => 'Approved by Financial Institution',
			    'paid_at' => '2016-09-30T21:10:19.000Z',
			    'created_at' => '2016-09-30T21:09:56.000Z',
			    'channel' => 'card',
			    'currency' => 'NGN',
			    'ip_address' => '41.242.49.37',
			    'metadata' => array(
			    	'equip_order_id' => 185,
			    	'receipt_ref'=> 'NFbZa1t1wSeWerer'
			    ),
			    'log' => 
			    array (
			      'time_spent' => 16,
			      'attempts' => 1,
			      'authentication' => 'pin',
			      'errors' => 0,
			      'success' => false,
			      'mobile' => false,
			      'input' => 
			      array (
			      ),
			      'channel' => NULL,
			      'history' => 
			      array (
			        0 => 
			        array (
			          'type' => 'input',
			          'message' => 'Filled these fields: card number, card expiry, card cvv',
			          'time' => 15,
			        ),
			        1 => 
			        array (
			          'type' => 'action',
			          'message' => 'Attempted to pay',
			          'time' => 15,
			        ),
			        2 => 
			        array (
			          'type' => 'auth',
			          'message' => 'Authentication Required: pin',
			          'time' => 16,
			        ),
			      ),
			    ),
			    'fees' => NULL,
			    'customer' => 
			    array (
			      'id' => 68324,
			      'first_name' => 'BoJack',
			      'last_name' => 'Horseman',
			      'email' => 'bojack@horseman.com',
			      'customer_code' => 'CUS_qo38as2hpsgk2r0',
			      'phone' => NULL,
			      'metadata' => NULL,
			      'risk_action' => 'default',
			    ),
			    'authorization' => 
			    array (
			      'authorization_code' => 'AUTH_f5rnfq9p',
			      'bin' => '539999',
			      'last4' => '8877',
			      'exp_month' => '08',
			      'exp_year' => '2020',
			      'card_type' => 'mastercard DEBIT',
			      'bank' => 'Guaranty Trust Bank',
			      'country_code' => 'NG',
			      'brand' => 'mastercard',
			      'account_name' => 'BoJack Horseman',
			    ),
			    'plan' => 
			    array (
			    ),
			  ),
		);
		return json_encode($data);
	}

	private function testStripeDataEvent(){
		$data = array (
		  'id' => 'evt_1CiPtv2eZvKYlo2CcUZsDcO6',
		  'object' => 'event',
		  'api_version' => '2018-05-21',
		  'created' => 1530291411,
		  'data' => 
		  array (
		    'object' => 
		    array (
		      'id' => 'src_1CiPsl2eZvKYlo2CVVyt3LKy',
		      'object' => 'source',
		      'amount' => 1000,
		      'client_secret' => 'src_client_secret_D8hHhtdrGWQyK8bLM4M3uFQ6',
		      'created' => 1530291339,
		      'currency' => 'eur',
		      'flow' => 'redirect',
		      'livemode' => false,
		      'metadata' => 
		      array (
		      	'equip_order_id' => 185,
			    'receipt_ref'=> 'NFbZa1t1wSeWerer'
		      ),
		      'owner' => 
		      array (
		        'address' => NULL,
		        'email' => NULL,
		        'name' => NULL,
		        'phone' => NULL,
		        'verified_address' => NULL,
		        'verified_email' => NULL,
		        'verified_name' => 'Jenny Rosen',
		        'verified_phone' => NULL,
		      ),
		      'redirect' => 
		      array (
		        'failure_reason' => NULL,
		        'return_url' => 'https://minkpolice.com',
		        'status' => 'succeeded',
		        'url' => 'https://hooks.stripe.com/redirect/authenticate/src_1CiPsl2eZvKYlo2CVVyt3LKy?client_secret=src_client_secret_D8hHhtdrGWQyK8bLM4M3uFQ6',
		      ),
		      'sofort' => 
		      array (
		        'country' => 'DE',
		        'bank_code' => 'DEUT',
		        'bank_name' => 'Deutsche Bank',
		        'bic' => 'DEUTDE2H',
		        'iban_last4' => '3000',
		        'statement_descriptor' => NULL,
		        'preferred_language' => NULL,
		      ),
		      'statement_descriptor' => NULL,
		      'status' => 'chargeable',
		      'type' => 'sofort',
		      'usage' => 'single_use',
		    ),
		  ),
		  'livemode' => false,
		  'pending_webhooks' => 0,
		  'request' => 
		  array (
		    'id' => NULL,
		    'idempotency_key' => NULL,
		  ),
		  'type' => 'source.chargeable',
		);
		return $data;
	}

	/**
	 * Validating paystack signature header
	 * @return [type] [description]
	 */
	private function validatePaystackHook()
	{
		if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !array_key_exists('HTTP_X_PAYSTACK_SIGNATURE', $_SERVER) ){
			echo "Oops, invalid operation";
		    exit();
		}
		$input = $this->request->getBody();
		define('PAYSTACK_SECRET_KEY', getenv('paystackKey'));
		# validate event do all at once to avoid timing attack
		if($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY)){
		    echo "Oops, invalid operation";
		    exit();
		}

		if(!$this->validatePaystackWhitelist()){
			echo "Operation not authorized";
			exit();
		}

		http_response_code(200);
		return json_decode($input);
	}

	/**
	 * This is validate the whitelist ip of paystack used for webhook
	 * @return bool [description]
	 */
	private function validatePaystackWhitelist()
	{
		$whitelist = [
			'52.31.139.75','52.49.173.169',
			'52.214.14.220','127.0.0.1',
			'0.0.0.0','102.89.41.59'
		];

		if (!in_array($this->request->getIPAddress(), $whitelist))
		{
			return false;
		}
		return true;
	}

	/**
	 * This is only invoke by the webhook from payment gateway(paystack|stripe)
	 * @param string $paymentType - Valeue to differentiate the payment gateway
	 * @return bool|string
	 */
	public function verifyTransaction(string $paymentType)
	{
		$equip_order_id = null;
		if($paymentType == 'paystack'){
			$event = $this->validatePaystackHook();
			$equip_order_id = $this->verifyPaystackPayment($event);
			// $event = $this->testPaystackDataEvent('or5us7pibv');
			// $equip_order_id = $this->verifyPaystackPayment(json_decode($event));
		}
		else if($paymentType == 'stripe'){
			$equip_order_id = $this->verifyStripePayment();
		}
		# update the necessary table that need to be updated
		if(is_numeric($equip_order_id)){
			$this->updateOrderRelatedModel($equip_order_id);
			echo "Successfully verified payment";
			exit();
		}
		echo "Verification not successful";
		exit();
	}

	/**
	 * This would update the necessary tables in the database
	 * equip_request, equip_order, earnings, equip_stock
	 * @param  int    $equip_order_id [description]
	 * @return [type]                 [description]
	 */
	private function updateOrderRelatedModel(int $equip_order_id)
	{
		$equip_stock = loadClass('equip_stock');
		$equip_order = loadClass('equip_order');
		$equip_order->ID  = $equip_order_id;
		if(!$equip_order->load()){
			echo "something went wrong with the order";
			exit();
		}
		$owners_id = $equip_order->owners_id;
		$amount = $equip_order->total_amount;
		if($this->createEarningAmount($owners_id,$amount,$equip_order_id)){
			# update both equip_order and equip_request status accordingly
			# stock should be updated for normal order and not extended order
			# since the equipment is still with the hirer in the first place
			if($equip_order->order_status == 'normal'){
				$query = "UPDATE equip_order eo, equip_request er set eo.order_status='processing',er.request_status = 'booked',eo.payment_status='1' where eo.id = er.equip_order_id and er.equip_order_id = '$equip_order_id'";
				$result = $this->db->query($query);
				$equipStock = $equip_stock->updateStockValue($equip_order,$equip_order_id);
				if(!$equipStock){
					echo "something went wrong while updating equip_stock";
					exit();
				}
			}
			else{
				# this would update the extended order
				$query = "UPDATE equip_order eo set eo.order_status='processing',eo.payment_status='1' where eo.id = '$equip_order_id'";
				$result = $this->db->query($query);
			}
		}
		return true;
	}

	/**
	 * This would create the earning of owners using equip_order_id
	 * @param  [type] $owners_id [description]
	 * @param  [type] $amount    [description]
	 * @return [type]            [description]
	 */
	private function createEarningAmount($owners_id,$amount,$equipOrderID){
		$builder = $this->db->table('earnings');
		$result = $builder->getWhere(['owners_id'=>$owners_id,'equip_order_id'=>$equipOrderID]);
		if($result->getNumRows() <= 0){
			# insert data
			$builder->set('owners_id', $owners_id);
			$builder->set('equip_order_id', $equipOrderID);
			$builder->set('amount', $amount);
			$builder->insert();
			return true;
		}
		return false;
	}

	/**
	 * This is to verify the order id sent alongside with the webhook
	 * @param  int    $equip_order_id [description]
	 * @param  string $receiptRef     [description]
	 * @return [type]                 [description]
	 */
	private function verifyOrderPayment(int $equip_order_id,string $receiptRef)
	{
		$equip_payment = loadClass('equip_payment');
		$value = $equip_payment->getWhere(["equip_order_id"=>$equip_order_id,"receipt_ref"=>$receiptRef],$count,0,1,false);
		if (!$value) {
			echo 'invalid reference number';
			exit();
		}
		# check if the payment has already been verified and saved in the database
		$value = $value[0];
		# confirming if payment had aleady been verified
		if ($value->payment_status == 'success' && $value->payment_date) {
			return $equip_order_id;
		}
		return $value;
	}

	/**
	 * Verifying paystack transaction using it webhook and insert data to db
	 * Hence, returning the equip_order_id for further process
	 * @param object 	$event
	 * @return int|string
	 */
	private function verifyPaystackPayment(object $event)
	{
		if($event->event != 'charge.success'){
			echo 'something went wrong with the payment gateway';
			exit();
		}
		$eventData = $event->data;
		$reference = $eventData->reference;
		if (!$reference) {
			echo 'an error occcured, cannot find payment reference';
			exit();
		}
		if(empty($eventData->metadata)){
			echo "something went wrong with the data provided";
			exit();
		}
		$receiptRef = $eventData->metadata->receipt_ref;
		$equip_order_id = $eventData->metadata->equip_order_id;
		$orderValue = $this->verifyOrderPayment($equip_order_id,$receiptRef);

		if(is_numeric($orderValue)){
			return $equip_order_id;
		}

		$url = "https://api.paystack.co/transaction/verify/$reference";
		$key = getenv('paystackKey');
		$header = array('Authorization'=>'Bearer '.$key,'Cache-Control'=>'no-cache');
		$result = request($url,'get',$header,array(),true,$output,$error);
		if (!$result) {
			echo "Curl Error:".$error;
			exit();
		}
		$rawInput = $output;
		$output = json_decode($output);

		//start a database transaction here
		if ($output->status && $output->data->status == 'success') {
			$orderValue = $this->orderValueObj($orderValue,'success',$output,$rawInput);
			if (!$orderValue->update()) {
				echo 'error occured while verifying payment, please try again';
				exit();
			}
			return $equip_order_id;
		}
		else if($output->status && $output->data->status == 'failed'){
			$orderValue = $this->orderValueObj($orderValue,'failed',$output,$rawInput);
			if (!$orderValue->update()) {
				echo 'error occured while verifying payment, please try again';
				exit();
			}
		}
		echo "Paystack Response:".$output->data->gateway_response;
		exit();
	}

	/**
	 * THis would update eqyip_payment object entity
	 * @param  object $orderValue    [description]
	 * @param  string $paymentStatus [description]
	 * @param  object $output        [description]
	 * @param  [type] $rawInput      [description]
	 * @return [type]                [description]
	 */
	private function orderValueObj(object $orderValue,string $paymentStatus,object $output,$rawInput)
	{
		$outputData = $output->data;
		$outputAuth = $outputData->authorization;
		$orderValue->payment_status = $paymentStatus;
		$orderValue->reference_number = $outputData->reference;
		$orderValue->payment_date = $outputData->paid_at;
		$orderValue->transaction_message = $outputData->gateway_response;
		$orderValue->payment_log = $rawInput;
		$orderValue->payment_method = $outputData->channel;
		$orderValue->transaction_number = $outputAuth->authorization_code;
		$orderValue->gateway_reference = "";
		$orderValue->payment_channel = 'paystack';
		$orderValue->amount = $outputData->amount;
		$orderValue->date_modified = formatToUTC();

		return $orderValue;
	}

	/**
	 * This is a test method
	 * @return [type] [description]
	 */
	public function verifyStripePaymentTest(){
		// testing the data here
		$event = json_encode($this->testStripeDataEvent());
		$event = json_decode($event);

		$paymentIntent = $event->data->object;
		$equip_order_id = $this->stripeVerifyAction($paymentIntent,'success');
		return $equip_order_id;
	}

	/**
	 * This is to verify the webhook and it authenticity
	 * Hence, returning equip_order_id for further process
	 * @return int|null [description]
	 */
	private function verifyStripePayment()
	{
		\Stripe\Stripe::setApiKey(getenv('stripeKey'));
		$endpoint_secret = getenv('stripeSecret');

		$payload = $this->request->getBody();
		$event = null;
		$equip_order_id = null;

		try {
		  $event = \Stripe\Event::constructFrom(
		    json_decode($payload, true)
		  );
		} catch(\UnexpectedValueException $e) {
		  # Invalid payload
		  echo 'Webhook error while parsing basic request.';
		  http_response_code(400);
		  exit();
		}
		if ($endpoint_secret) {
		  # Only verify the event if there is an endpoint secret defined
		  # Otherwise use the basic decoded event
		  $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
		  try {
		    $event = \Stripe\Webhook::constructEvent(
		      $payload, $sig_header, $endpoint_secret
		    );
		  } catch(\Stripe\Exception\SignatureVerificationException $e) {
		    # Invalid signature
		    echo 'Webhook error while validating signature.';
		    http_response_code(400);
		    exit();
		  }
		}

		# Handle the event
		switch ($event->type) {
		  case 'payment_intent.succeeded':
		    $paymentIntent = $event->data->object;
		    // handlePaymentIntentSucceeded($paymentIntent);
		    $equip_order_id = $this->stripeVerifyAction($paymentIntent,'success');
		    break;

		  case 'payment_intent.payment_failed':
		    $paymentIntent = $event->data->object;
		    // handlePaymentIntentSucceeded($paymentIntent);
		    $this->stripeVerifyAction($paymentIntent,'failed');
		    break;

		  default:
		    // Unexpected event type
		    error_log('Received unknown event type');
		}
		http_response_code(200);
		return $equip_order_id;
	}

	/**
	 * This would verify and insert data into the database
	 * Hence returning equip_order_id for further process
	 * @param  object $data [description]
	 * @param  string  $paymentStatus
	 * @return int|string       [description]
	 */
	private function stripeVerifyAction(object $data,string $paymentStatus){
		if(empty($data->metadata)){
			echo "something went wrong with the data provided";
			exit();
		}
		$equip_order_id = $data->metadata->equip_order_id;
		$receiptRef = $data->metadata->receipt_ref;
		$orderValue = $this->verifyOrderPayment($equip_order_id,$receiptRef);

		if(is_numeric($orderValue)){
			return $equip_order_id;
		}

		$paymentDate = Time::createFromTimestamp($data->created);
		$paymentDate = $paymentDate->format('Y-m-d H:i:s');

		//start a database transaction here
		if ($data->amount) {
			$orderValue->payment_status = $paymentStatus;
			$orderValue->reference_number = $data->id;
			$orderValue->payment_date = $paymentDate;
			$orderValue->transaction_message = "stripe: {$paymentStatus}";
			$orderValue->payment_log = json_encode($data);
			$orderValue->payment_method = $data->payment_method ?? $data->payment_method_types[0] ?? 'card';
			$orderValue->transaction_number = $data->client_secret;
			$orderValue->gateway_reference = $data->redirect->url;
			$orderValue->payment_channel = 'stripe';
			$orderValue->amount = $data->amount;
			$orderValue->date_modified = formatToUTC();

			if (!$orderValue->update()) {
				echo 'error occured while verifying payment, please try again';
				exit();
			}
			return $equip_order_id;
		}
	}

}
