<?php 

/**
 * this will get different entity details that can be use inside api
 */
namespace App\Models;

class EntityDetails
{
	function __construct()
    {
        helper('string');
    }

    public function getEquipmentsDetails(int $id)
    {
        # Get something
        $entity = loadClass('equipments');
        $entity->ID = $id;
        $data = $entity->load();
        if(!$data) return false;
        $data= $entity->toArray();
        $customer = getCustomer();
        $result = array();

        $equip_images = loadClass('equip_images');
        $temp = $data;
        $owners = $entity->owners->hirers;
        if (!$owners) {
            $owners = null;
        }
        $tempImage = $equip_images->getWhereNonObject(array('equipments_id'=>$data['ID']),$count,0,null,true);
        if (!$tempImage) {
            $tempImage = [];
        }

        $temp['equip_images']=$tempImage;
        if(isset($customer->is_owner)){
            $temp['equip_request'] = $entity->equip_request ?? null;
        }
        $temp['owners'] = $owners ? $owners->toArray() : null;
        $temp['reviews'] = $entity->reviewList;
        $temp['average_rating'] = $entity->averageRating;
        $result[] = $temp;
        return $result;   
    }

    public function getEquip_requestDetails($id)
    {
        # Get something
        $entity = loadClass('equip_request');
        $entity->ID=$id;
        $data = $entity->load();
        if(!$data) return false;
        $data= $entity->toArray();
        $result = array();

        $hirers = loadClass('hirers');
        $equipments = loadClass('equipments');
        $equip_payment = loadClass('equip_payment');

        $temp = $data;
        $tempHirer = $hirers->getWhereNonObject(array('id'=>$data['hirers_id']),$count,0,1,false);
        $tempEquip = $equipments->getWhereNonObject(array('equipments.id'=>$data['equipments_id']),$count,0,1,true);
        $tempPay = $equip_payment->loadEquipOrderTransaction($data['equip_order_id']);
        if (!$tempHirer) {
            $tempHirer = null;
        }else{
            $tempHirer = $tempHirer[0];
        }
        if (!$tempEquip) {
            $tempEquip = null;
        }
        if(empty($tempPay)){
            $tempPay = null; 
        }
        $temp['equipments']=$tempEquip;
        $temp['hirers']=$tempHirer;
        $temp['equip_payment']=$tempPay;
        $result[]=$temp;
        return $result;
        
    }

    public function getExtend_equip_requestDetails($id)
    {
        # Get something
        $entity = loadClass('extend_equip_request');
        $entity->ID = $id;
        $data = $entity->load();
        if(!$data) return false;
        $data = $entity->toArray();
        $result = array();
        $hirers = loadClass('hirers');
        $equip_payment = loadClass('equip_payment');
        $equip_order = loadClass('equip_order');

        $temp = $data;
        $tempHirer = $hirers->getWhereNonObject(array('id'=>$data['hirers_id']),$count,0,1,false);
        $tempOrder = $equip_order->getWhereNonObject(array('id'=>$data['equip_order_id']),$count,0,1,false);
        $tempPay = $equip_payment->loadEquipOrderTransaction($data['equip_order_id']);
        if (!$tempHirer) {
            $tempHirer = null;
        }else{
            $tempHirer = $tempHirer[0];
        }
        if (!$tempOrder) {
            $tempOrder = null;
        }else{
            $tempOrder = $tempOrder[0];
        }
        if(empty($tempPay)){
            $tempPay = null; 
        }
        $temp['equip_order']=$tempOrder;
        $temp['hirers']=$tempHirer;
        $temp['equip_payment']=$tempPay;
        $result = $temp;
        return $result;
        
    }

    public function getEquip_orderDetails($id){
        # Get something
        $entity = loadClass('equip_order');
        $entity = $entity->getWhereNonObject(['ID'=>$id],$count,0,1,false);
        if(!$entity) return false;
        $data = $entity[0];
        $result = array();

        $temp = $data;
        $equip_request = loadClass('equip_request');
        $customer = getCustomer();
        $tempEquipRequest = $equip_request->getWhereNonObject(array('equip_order_id'=>$data['ID']),$count,0,null,false);
        $temp['equip_request']=$equip_request->APIList($tempEquipRequest,$customer,[],false) ?? null;
        $result = $temp;
        return $result;
    }


}




?>