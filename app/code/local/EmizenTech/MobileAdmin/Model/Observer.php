<?php
class EmizenTech_MobileAdmin_Model_Observer
{
	public function salesOrderSaveAfter(Varien_Event_Observer $observer)
	{
		//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
		//$user = $observer->getEvent()->getUser();
		//$user->doSomething();
	}

	public function customerRegisterNotification(Varien_Event_Observer $observer)
	{
		//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
		//$user = $observer->getEvent()->getUser();
		//$user->doSomething();
	}

	public function customerRegisterNotificationCheckout(Varien_Event_Observer $observer)
	{
		//Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
		//$user = $observer->getEvent()->getUser();
		//$user->doSomething();
	}	
}
