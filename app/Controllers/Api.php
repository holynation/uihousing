<?php 

namespace App\Controllers;

use App\Models\WebSessionManager;
use App\Models\EntityModel;
use App\Models\ApiModel;
use App\Models\WebApiModel;

class Api extends BaseController
{
    protected $db;
    private $webSessionManager;
    private $entityModel;
    private $apiModel;
    private $webApiModel;

    function __construct()
    {
        helper(['array','string']);
        $this->db = db_connect();
        $this->webSessionManager = new WebSessionManager;
    }

    // TODO: also add a way to limit the http method an entity should allowed to prevent unnecessary method allowance
    // also, find a way to block any guessed entity name coming from the endpoint
    // such that entity is not accessible using the apis endpoint 

    public function mobileApi(string $entity)
    {
        $dictionary = getAPIEntityTranslation();
        $method = array_key_exists($entity, $dictionary)?$dictionary[$entity]:$entity;
        $entities = listAPIEntities($this->db);
        $args = array_slice(func_get_args(),1);
        $apiType=null;

        # incase of extra uri args before the real endpoint name
        if($method == 'owners'){
            $apiType = $this->formatURIType($method,$args);
        }
        # this check if the method is equivalent to any entity model to get it equiv result
        $method = array_key_exists($method, $dictionary)?$dictionary[$method]:$method;
        if (in_array($method, $entities)) {
            $entityModel = new EntityModel($this->request,$this->response);
            $entityModel->process($method,$args,$apiType);
            return;
        }
        # define the set of methods in another model called ApiMOdel
        $apiModel = new ApiModel($this->request,$this->response);
        if (method_exists($apiModel, $method)) {
            $apiModel->$method($args,$apiType);
            return;
        }else{
            # method no dey exist for this place 00
            return $this->response->setStatusCode(405)->setJSON(['status'=>false,'message'=>'denied']);
        }
    }

    /**
     * @deprecated  - This is not in use at the moment
     * @param  string $entity [description]
     * @return [type]         [description]
     */
    public function webApi(string $entity)
    {
        $dictionary = getEntityTranslation();
        $method = array_key_exists($entity, $dictionary)?$dictionary[$entity]:$entity;
        $entities = listEntities($this->db);
        $args = array_slice(func_get_args(),1);
        $apiType=null;

        # incase of extra uri args before the real endpoint name
        if($method == 'owners'){
            $apiType = $this->formatURIType($method,$args);
        }

        $method = array_key_exists($method, $dictionary)?$dictionary[$method]:$method;
        # this check if the method is equivalent to any entity model to get it equiv result
        if (in_array($method, $entities)) {
            $entityModel = new EntityModel($this->request,$this->response);
            $entityModel->process($method,$args,$apiType);
            return;
        }
        # define the set of methods in another model called WebApiModel|ApiModel
        $webApiModel = new ApiModel($this->request,$this->response);
        if (method_exists($webApiModel, $method)) {
            $webApiModel->$method($args, $apiType);
            return;
        }else{
            # method no dey exist for this place ooo
            return $this->response->setStatusCode(405)->setJSON(['status'=>false,'message'=>'denied']);
        }
        
    }

    /**
     * This is to ensure the URI is format based on the user_type requesting
     * the endpoint without breaking the code
     * 
     * @param string &$method   This is to pass it by reference just like a pointer
     * @param  array  &$args    This is to pass it by reference just like a pointer
     * @return string
     */
    private function formatURIType(string &$method, array &$args){
        $apiType = 'owners';
        $method = $args[0];
        unset($args[0]);

        #reset the array index
        $args = array_values($args);
        return $apiType;
    }
    
    public function accessFiles(string $directory, string $filename){
        if (!is_dir(WRITEPATH.'uploads/'.$directory)) {
            displayJson(false,"Oops, {$directory} does not exists");return;
        }
        $filename = trim(urldecode($filename));
        $target = WRITEPATH.'uploads/'.urldecode($directory).'/'.$filename;
        $link = "uploads/equipments/{$filename}";
        if(!is_link($link)){
            symlink($target, $link);
        }
    }

}
