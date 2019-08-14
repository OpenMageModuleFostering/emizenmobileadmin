<?php
class EmizenTech_MobileAdmin_Model_Api extends Mage_Api_Model_Resource_Abstract
{  
    /* @method: $soap->call($session_id,'mobileadmin_api.create',$data);
    * @param: $data
    */      
	public function create($data)
    {
    	$collections = Mage::getModel("mobileadmin/emizenmob")
                                ->getCollection()
                                ->addFieldToFilter('username',Array('eq'=>$data['user']))
                                ->addFieldToFilter('apikey',Array('eq'=>$data['key']))
                                ->addFieldToFilter('device_token',Array('eq'=>$data['devicetoken']));
            $count       = count($collections);
            //return $count;
            if($count == 0)
            { 
                Mage::getModel("mobileadmin/emizenmob") // load model to save user detail in database
                ->setUsername($data['user'])
                ->setFirstname($data['firstname'])
                ->setLastname($data['lastname'])
                ->setEmail($data['email'])
                ->setApikey($data['key'])
                ->setDeviceToken($data['devicetoken'])
                ->setDeviceType($data['device_type'])
                ->setNotificationFlag($data['notification_flag'])
                ->save();
            }
            //return $count;
            if($count == 1)
            { 
                foreach($collections as $user)
                {
                    $user_id = $user->getUserId();
                    $flag    = $user->getNotificationFlag();
                }
                if($flag != $data['notification_flag'] || $data['is_logout'] != 1)
                {
                    try
                    {
                        $prefix = Mage::getConfig()->getTablePrefix();
                        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                        $connection->beginTransaction();
                        $fields = array();
                        $fields['notification_flag'] = $data['notification_flag'];
                        $fields['is_logout'] = $data['is_logout'];
                        $where = $connection->quoteInto('user_id =?', $user_id);
                        $connection->update($prefix.'emizenmob', $fields, $where);
                        $connection->commit();
                    }
                    catch (Exception $e)
                    {
                        return $e->getMessage();
                    }
                }
            }

            $successArr[] = array('success_msg' => 'Login sucessfully','session_id' => $data['session_id'],'firstname' => $data['firstname'],'lastname' => $data['lastname'],'email' => $data['email']) ; // return logged in status

            foreach(Mage::app()->getWebsites() as $website)
            {
                foreach ($website->getGroups() as $group)
                {
                    $stores = $group->getStores();
                    foreach ($stores as $store)
                    {
                        $storeArr[] = array('id' =>$store->getId(),
                            'name' => $store->getName()                                
                        );
                    }
                }
            }
            $isPos =  0;
            $result = array('success' => $successArr,'stores' => $storeArr,'is_pos' => $isPos);
            return $result;
    }
}