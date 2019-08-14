<?php
class EmizenTech_MobileAdmin_Model_Observer
{
	private static $_handleCustomerFirstOrderCounter = 1;
    private static $_handleCustomerFirstRegisterNotificationCounter = 1;

    // when order palaced then will be notificate
	public function salesOrderSaveAfter(Varien_Event_Observer $observer)
	{
		if(Mage::getStoreConfig('emizen_mob/emizen_general/enabled'))
		{
            if(self::$_handleCustomerFirstOrderCounter > 1)
            {
                return $this;
            }
            self::$_handleCustomerFirstOrderCounter++;
            $result = Mage::helper('mobileadmin')->pushNotification('order');

            $quoteId = $observer->getEvent()->getOrder()->getData('quote_id');
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            $method = $quote->getCheckoutMethod(true);

            if ($method=='register')
            {
                Mage::dispatchEvent('customer_register_checkout',
                    array(
                        'customer' => $observer->getEvent()->getOrder()->getCustomer()
                    )
                );
            }
        }
	}

    // when customer registerd then will be notificate
	public function customerRegisterNotification(Varien_Event_Observer $observer)
	{
		//return true;
		if(Mage::getStoreConfig('emizen_mob/emizen_general/enabled'))
		{
            $customer               =   $observer->getEvent()->getCustomer();
            if($customer)
            {
                $customer_id        =   $customer->getId();
            }    
            if($customer_id)
            {
                $result = Mage::helper('mobileadmin')->pushNotification('customer');
                //echo "<pre>"; print_r($result); die;
            }
        }
	}

    // when customer registerd using checkout process then will be notificate
	public function customerRegisterNotificationCheckout(Varien_Event_Observer $observer)
	{
		$customer = $observer->getEvent()->getCustomer();
        if ($customer)
        {
            $customer_id        =   $customer->getId();
            $result = Mage::helper('mobileadmin')->pushNotification('customer');
        } 
	}	
}
