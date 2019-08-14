<?php
class EmizenTech_MobileAdmin_CmsController extends Mage_Core_Controller_Front_Action{

	public function cmsPagesListAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check if extension is enabled or not ?
        {
            $post_data = Mage::app()->getRequest()->getParams();
            // $sessionId = $post_data['session'];
            // if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check if customer is not logged in then return Access denied
            // {
            //     echo $this->__("The Login has expired. Please try log in again.");
            //     return false; // return logged out
            // }
            
            $cmsPageLoad = Mage::getModel('cms/page')->getCollection();

            $cmsArrayVal = array();
            //$cmsPagesList = array();
            foreach ($cmsPageLoad as $value)
            {
            	$cmsArrayVal['page_id'] = $value->getPageId();
            	$cmsArrayVal['url_key'] = $value->getIdentifier();
            	$cmsArrayVal['title'] = $value->getTitle();
            	$cmsArrayVal['is_active'] = $value->getIsActive();
            	$cmsArrayVal['status'] = ($value->getIsActive() == 1 ? 'Enabled' : 'Disabled');
            	//$cmsArrayVal['sort_order'] = $value->getSortOrder();
            	//$cmsArrayVal['content'] = $value->getContent();
            	$cmsPagesList[] = $cmsArrayVal;
            }

            echo "<pre>"; print_r($cmsPagesList); die;
                
        }
        else
        {
            $result['error'] = $this->__('Please activate the Mobile Emizentech Extension on the Magento Store.');
        }
        $isEnable = Mage::helper('core')->jsonEncode($cmsArrayVal);
        return Mage::app()->getResponse()->setBody($isEnable);
	}

	public function getCmsPageContentByIdAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check if extension is enabled or not ?
        {
            $post_data = Mage::app()->getRequest()->getParams();
            // $sessionId = $post_data['session'];
            // if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check if customer is not logged in then return Access denied
            // {
            //     echo $this->__("The Login has expired. Please try log in again.");
            //     return false; // return logged out
            // }
            
            $page_id = $post_data['page_id'];

            $cmsPageLoadById = Mage::getModel('cms/page')->load($page_id);

            $pageDetailArray = array();

            $pageDetailArray['page_id'] = $cmsPageLoadById->getPageId();
            $pageDetailArray['title'] = $cmsPageLoadById->getTitle();
            $pageDetailArray['url_key'] = $cmsPageLoadById->getIdentifier();
            $pageDetailArray['is_active'] = $cmsPageLoadById->getIsActive();
            $pageDetailArray['content_heading'] = $cmsPageLoadById->getContentHeading();

            $storeDetail = Mage::getModel('core/store')->load(1);

            // echo "<pre>"; print_r($storeDetail->getData()); die;
            echo "<pre>"; print_r($cmsPageLoadById->getData()); die;
                
        }
        else
        {
            $result['error'] = $this->__('Please activate the Mobile Emizentech Extension on the Magento Store.');
        }
        $isEnable = Mage::helper('core')->jsonEncode($cmsArrayVal);
        return Mage::app()->getResponse()->setBody($isEnable);
	}

	public function checkAction()
	{
		$collectionProductType = Mage::getModel('catalog/product_type')->getOptionArray();

		$valArr = array();
		foreach ($collectionProductType as $key => $value)
		{
			$valArr['value'] = $key;
			$valArr['label'] = $value;
			$options['productTypeList'][] = $valArr;
		}

		echo "<pre>"; print_r($options);
	}

}
?>