<?php 

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\I18n\Time;

class ApiAuth implements FilterInterface
{

    /**
     * [before description]
     * @param  RequestInterface $request   [description]
     * @param  [type]           $arguments [description]
     * @return [type]                      [description]
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here
        $response = service('response');
        if (!$this->validateHeader($request)) {
            $this->logRequest($request);
            return $response->setStatusCode(405)->setJSON(['status'=>false,'message'=>'denied']);
        }
        $proceed = $this->canProceed($request,$request->getUri()->getSegments());
        if ($proceed === false) {
            $this->logRequest($request);
            return $response->setStatusCode(405)->setJSON(['status'=>false,'message'=>'denied']);
        }
        else if((int)$proceed === 403){
            $this->logRequest($request);
            return $response->setStatusCode(403)->setJSON(['status'=>false,'message'=>'Oops, user banned']);
        }
        $this->logRequest($request,'1');
    }

    /**
     * [after description]
     * @param  RequestInterface  $request   [description]
     * @param  ResponseInterface $response  [description]
     * @param  [type]            $arguments [description]
     * @return [type]                       [description]
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
        $response->setHeader('Content-Type','application/json');
    }

    /**
     * This is to validate request header
     * @param  object $request [description]
     * @return [type]          [description]
     */
    private function validateHeader(object $request)
    {
        $apiKey = getenv('xAppKey');
        return (array_key_exists('HTTP_X_API_KEY', $_SERVER) && $request->getServer('HTTP_X_API_KEY')==$apiKey) || (array_key_exists('HTTP_X_APP_KEY', $_SERVER) && $request->getServer('HTTP_X_APP_KEY')==$apiKey);
    }

    /**
     * This is to validate request
     * @param  object $request [description]
     * @param  array  $args    [description]
     * @return [type]          [description]
     */
    private function canProceed(object $request,array $args)
    {
        $isExempted = $this->isExempted($request, $args);
        if ($isExempted) {
            return true;
        }
        return $this->validateAPIRequest();
    }

    private function validateAPIRequest()
    {
        helper(['url','string']);
        try{
            $token = getBearerToken();
            $jwtKey = getenv('jwtKey');
            JWT::$leeway = 60; # $leeway in seconds
            $decodedToken = JWT::decode($token,new Key($jwtKey, 'HS256'));
            $id = $decodedToken->user_table_id; # the real hirers_id and any other users
            $userType = $decodedToken->user_type;
            $userType = loadClass($userType);
            $tempUser  = new $userType(array('ID'=>$id));
            if (!$tempUser->load() || !$tempUser->status) {
                return 403; # this could be mean user is ban
            }

            $newUser = (object)$tempUser->toArray();
            if(isset($decodedToken->user_type)){
                $newUser->user_type = $decodedToken->user_type;
                # if decodedToken->user_id exists,means it's coming from switch endpoint,
                # else, it's coming from auth endpoints
                $newUser->user_id = isset($decodedToken->user_id) ? $decodedToken->user_id : $decodedToken->ID;
                $newUser->fcm_token = $decodedToken->fcm_token;
                # this is to append owners_id to the newUser object
                if(isset($decodedToken->is_owner)){
                    $newUser->is_owner = true;
                    $newUser->owner_id = $decodedToken->owner_id;
                }
            }
            // print_r($newUser);exit;
            $_SERVER['current_user'] = $newUser;
            return true;

        }
        catch(\Exception $e){
            return false;
        }
    }

    /**
     * [isOwner description]
     * @deprecated no longer using this since the switch would come from the client
     * @param  int   $user_id
     * @return bool
     */
    private function isOwner(int $user_id){
        $db = db_connect();
        $builder = $db->table('owners');
        $query = $builder->getWhere(['user_id'=>$user_id]);
        return ($query->getNumRows() > 0) ? $query->getRow() :false;
    }

    /**
     * This is to exempt certain request from the jwt auth
     * @param  object  $request   
     * @param  array   $arguments 
     * @return boolean  
     */
    private function isExempted(object $request,array $arguments)
    {
        $exemptionList = ['POST/signup','POST/reset_password',
            'POST/change_password','POST/auth',
            'POST/logout',
            'POST/validate_otp',
        ];
        $argument = $arguments[1];
        if($argument == 'owners'){
            $argument = $arguments[2];
        }
        $argPath = strtoupper($request->getMethod()).'/'.$argument;
        return in_array($argPath, $exemptionList);
    }

    private function logRequest($request, $status = '0')
    {
        $uri =  $request->getUri();
        $uri = '/'.$uri->getPath();
        $db = db_connect();
        $builder = $db->table('activity_log');
        $customer = getCustomer();
        $customer = $customer ? $customer->ID : null;
        $time = Time::createFromTimestamp($request->getServer('REQUEST_TIME'));
        $time = $time->format('Y-m-d H:i:s');
        $param = [
            'hirers_id' => $customer,
            'host' => $request->getServer('HTTP_HOST'),
            'route' => $uri,
            'user_agent' => $request->getUserAgent(),
            'ip_address' => $request->getIPAddress(),
            'date_created' => $time,
            'status' => $status,
        ];
        $builder->insert($param);
    }
}