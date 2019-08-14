<?php
class EmizenTech_MobileAdmin_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
    * @ Get formetted price
    * @param: $price
    */
	public function getPrice($price)
    {
        $price = strip_tags(Mage::helper('core')->currency($this->getPriceFormat($price)));
        return $price;
    } 

    /*
    * @ Get price format
    * @param: $price
    */
    public function getPriceFormat($price)
    {
        $price = sprintf("%01.2f", $price);
        return $price;
    }

    /*
    * @ Get Actual Date
    * @param: $updateddate
    */
    public function getActualDate($updated_date)
    {
        $date          = Mage::app()->getLocale()->date(strtotime($updated_date));
        $timestamp     = $date->get(Zend_Date::TIMESTAMP) - $date->get(Zend_Date::TIMEZONE_SECS);
        $updated_date  = date("Y-m-d H:i:s", $timestamp);
        return $updated_date;
    }

    /*
    * @ Using this function, check if extension is enabled or not !
    * @param: NULL
    */
    public function isEnable()
    {
        return Mage::getStoreConfig('emizen_mob/emizen_general/enabled');
    }

    /*
    * @Description: if any customer register and products add and new order come, will process of three type methods and then notification will come on your ios and android device.
    * @ Notification of both device type like iphone & android 
    * @param: $notification_type and $entityId
    */
    public function pushNotification($notification_type)
    {
        //$google_api_key = 'AIzaSyAZPkT165oPcjfhUmgJnt5Lcs2OInBFJmE';
        $passphrase  = '123456789';

        $collections = Mage::getModel("mobileadmin/emizenmob")->getCollection()
                        ->addFieldToFilter('notification_flag',1)
                        ->addFieldToFilter('is_logout',0);

        //return $collections->getFirstItem();

        $collection_items = $collections->getFirstItem();

        if($notification_type=='customer')
        {
            $messageText     = Mage::getStoreConfig('emizen_mob/emizen_general/emizen_register');
            if($messageText == null)
            {
                $messageText     = Mage::helper('mobileadmin')->__('A New customer has been registered on the Store.');
            }
        }
        else
        {
            $messageText     = Mage::getStoreConfig('emizen_mob/emizen_general/emizen_noti');
            if($messageText == null)
            {
                $messageText     = Mage::helper('mobileadmin')->__('A New order has been received on the Store.');
            }
        }

        foreach($collections->getData() as $collection)
        {
            $deviceType = $collection['device_type'];
            if($deviceType == 'ios')
            {
                $message = new Zend_Mobile_Push_Message_Apns();
                $message->setAlert($messageText);
                $message->setBadge(1);
                $message->setSound('default');
                $message->setId(time());
                $message->setToken($collection['device_token']);
             
                $apns = new Zend_Mobile_Push_Apns();
                //$apns->setCertificate('/path/to/provisioning-certificate.pem');
                $apns->setCertificate(Mage::getBaseDir('lib'). DS. "EmizenMobileAdmin".DS."PushNotificationAppCertificateKey.pem");
                // if you have a passphrase on your certificate:
                $apns->setCertificatePassphrase($passphrase);
             
                try {
                    $apns->connect(Zend_Mobile_Push_Apns::SERVER_SANDBOX_URI);
                } catch (Zend_Mobile_Push_Exception_ServerUnavailable $e) {
                    // you can either attempt to reconnect here or try again later
                    Mage::log("1_". $e->getMessage() , null , 'push_notification1345.log');
                } catch (Zend_Mobile_Push_Exception $e) {
                    Mage::log("2_". $e->getMessage() , null , 'push_notification1345.log');
                }
             
                try {
                    $apns->send($message);
                } catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
                    // you would likely want to remove the token from being sent to again
                    Mage::log("3_". $e->getMessage() , null , 'push_notification1345.log');
                } catch (Zend_Mobile_Push_Exception $e) {
                    // all other exceptions only require action to be sent
                    Mage::log("4_". $e->getMessage() , null , 'push_notification1345.log');
                }
                $apns->close();
            }  
        } 
  
    }
}
?>
