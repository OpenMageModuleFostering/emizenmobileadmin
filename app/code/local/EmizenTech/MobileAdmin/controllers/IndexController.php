<?php
class EmizenTech_MobileAdmin_IndexController extends Mage_Core_Controller_Front_Action{

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/index    // pass parameter like this after second index from this URL ?userapi=test&keyapi=test etc.
    * required parameter: @ userapi,keyapi,magento_url
    */
    public function IndexAction()
    {
      $modules = Mage::getConfig()->getNode('modules')->children();
      $modulesArray = (array)$modules;
      $check_arr = (array)$modulesArray['EmizenTech_MobileAdmin'];

      if(!isset($modulesArray['EmizenTech_MobileAdmin']) && $check_arr['active'])
      {
        $error = array('error' => 'Please install and activate EmizenTech_MobileAdminn extension on your store');
        $jsonData = Mage::helper('core')->jsonEncode($error);
        return Mage::app()->getResponse()->setBody($jsonData);
      }
      
      
	   if(Mage::getStoreConfig('emizen_mob/emizen_general/enabled')) // check if extension is enabled on your magento store.
     {
        $isSecure = Mage::app()->getFrontController()->getRequest()->isSecure(); 
        $validate_url = false;
        if($isSecure)
        {
            if(Mage::getStoreConfig('web/secure/base_url') == Mage::getStoreConfig('web/secure/base_link_url')) // check secure URL
            {
                $validate_url = true;
            }
        }
        else
        {
            if(Mage::getStoreConfig('web/unsecure/base_url') == Mage::getStoreConfig('web/unsecure/base_link_url')) // check unsecure URL
            {
                $validate_url = true;
            }
        }
        if($validate_url) // if validate is true
        {
          $details     = Mage::app()->getRequest()->getParams();
          $user        = $details['userapi']; 
          $api_key     = $details['keyapi']; 
          $deviceToken = $details['token'];
          $flag        = $details['notification_flag'];
          $device_type = $details['device_type'];

          $get_length = strlen($details['magento_url']);

          if(substr($details['magento_url'], $get_length-1) != '/')
          {
             $details['magento_url'] =   $details['magento_url']."/";
          }

          $url         = $details['magento_url'].'api/soap/?wsdl';

          //Mage::log("userapi:".$details['userapi'] . " ~ keyapi:".$details['keyapi'] ." ~ magento_url:".$details['magento_url'] ." ~ token:".$details['token'] ." ~ notification_flag:".$details['notification_flag'] ." ~ url:".$url, null , 'myadmin.log');

          try
          {
            $soap       = new SoapClient($url); // load shop library
            $session_id = $soap->login($user, $api_key);

          }
          catch(SoapFault $fault)
          {
            $result['error'] = $fault->getMessage();
            $jsonData = Mage::helper('core')->jsonEncode($result); // encode array to json
            return Mage::app()->getResponse()->setBody($jsonData);
          }
          //echo $session_id; die;
          if($session_id)
          {
            $webservice_user = Mage::getModel('api/user')->getCollection()->addFieldToFilter('username',Array('eq'=>$user))->getFirstItem();
            $data[]   = array(
                          'user' => $user,
                          'key' => $api_key,
                          'devicetoken'=>$deviceToken,
                          'session_id' => $session_id,
                          'notification_flag'=> $flag,
                          'device_type'=> $device_type,
                          'is_logout'=> '0',
                          'firstname'=>$webservice_user->getFirstname(),
                          'lastname'=>$webservice_user->getLastname(),
                          'email'=>$webservice_user->getEmail()
                          ); // get data in array
            $result   = $soap->call($session_id,'mobileadmin_api.create',$data); // create api with entry in database
            $jsonData = Mage::helper('core')->jsonEncode($result);
            //echo "<pre>"; print_r($result); die;
            return Mage::app()->getResponse()->setBody($jsonData);
          }
        }
        else
        {
            $result['error'] = $this->__('Please check web base URL and magento base url on the store.'); // error if condishion is not true
        } 
      }
      else
      { 
          $result['error'] = $this->__('Please activate the Mobile Emizentech Extension on the Magento Store.');
      }
      $jsonData = Mage::helper('core')->jsonEncode($result);
      return Mage::app()->getResponse()->setBody($jsonData);
    }

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/checkfront    // pass parameter like this after checkfront from this URL ?userapi=test&keyapi=test etc.
    * required parameter: @ magento_url
    */
    public function checkFrontAction()
    {
      $post_data   = Mage::app()->getRequest()->getParams();
      $url         = $post_data['magento_url']; // get magento url from the query string
      $url_info    = parse_url($url); // parse url using this function

      $modules = Mage::getConfig()->getNode('modules')->children();
      $modulesArray = (array)$modules;
      $check_arr = (array)$modulesArray['EmizenTech_MobileAdmin'];

      if(!isset($modulesArray['EmizenTech_MobileAdmin']) && $check_arr['active'])
      {
        $error = array('error' => 'Please install and activate EmizenTech_MobileAdminn extension in your store');
        $jsonData = Mage::helper('core')->jsonEncode($error);
        return Mage::app()->getResponse()->setBody($jsonData);
      }

      if(Mage::getStoreConfig('emizen_mob/emizen_general/enabled')) // if extension is enabled return true
      {
        $isSecure = Mage::app()->getFrontController()->getRequest()->isSecure(); 
        $validate_url = false;
        if($isSecure)
        {
          if(Mage::getStoreConfig('web/secure/base_url') == Mage::getStoreConfig('web/secure/base_link_url'))
          {
              $validate_url = true;
          }

          if($url_info['scheme'] == 'http') // check http and https in the URL
          {
            $result['error'] = $this->__('It seems you use secure url for your store. So please use "https". '); // check add index.php or not
            $jsonData = Mage::helper('core')->jsonEncode($result);
            return Mage::app()->getResponse()->setBody($jsonData);
          }

        }
        else
        {
          if(Mage::getStoreConfig('web/unsecure/base_url') == Mage::getStoreConfig('web/unsecure/base_link_url'))
          {
            $validate_url = true;
          }
        }
        if($validate_url)
        {
          $is_index = Mage::getStoreConfig('web/seo/use_rewrites');
          if(!$is_index && basename($url) != 'index.php')
          {
            $result['error'] = $this->__('Please add "index.php" after url.'); // return error
            $jsonData = Mage::helper('core')->jsonEncode($result);
            return Mage::app()->getResponse()->setBody($jsonData);
          }
          $result['success'] = $this->__('Now connection is fine, you can run app.'); // if will be validated URL it will return this message.
        }
        else
        {
          $result['error'] = $this->__('There seems some difference between the Based URL & Magento Based URL(on the store).');
        }
      }
      else
      {
        $result['error'] = $this->__('Please activate the Mobile Emizentech Extension on the Magento Store.');
      }
      $jsonData = Mage::helper('core')->jsonEncode($result); // convert data array to json
      return Mage::app()->getResponse()->setBody($jsonData);
    }

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/adminlogout    // pass parameter like this after adminlogout from this URL ?userapi=test&token=test etc.
    * required parameter: @ userapi,token
    */
    public function AdminLogoutAction()
    {
      $post_data   = Mage::app()->getRequest()->getParams();
      $user        = $post_data['userapi']; 
      $deviceToken = $post_data['token'];
      $collections = Mage::getModel("mobileadmin/emizenmob")->getCollection()->addFieldToFilter('device_token',Array('eq'=>$deviceToken));
      $count       = count($collections); // get number of record

      foreach($collections as $user) // get device token using this loop
      {
        $device_token = $user->getDeviceToken();    
        try
        {
          $prefix = Mage::getConfig()->getTablePrefix();
          $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
          $connection->beginTransaction();
          $fields = array();
          $fields['is_logout'] = 1; // if admin will be logged out then update this field in the database
          $where = $connection->quoteInto('device_token =?', $device_token);
          $connection->update($prefix.'emizenmob', $fields, $where); // update field if admin logout
          $connection->commit();
        }
        catch(Exception $e)
        {
          return $e->getMessage(); // generate exception if will not perform try method data.
        }
        $successArr[] = array('success_msg' => 'You are now successfully logged out.'); // if admin is logged out will return this message
        $result       = Mage::helper('core')->jsonEncode($successArr);
        return Mage::app()->getResponse()->setBody($result);
      }
    }   

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/getlogoandcurrency  // pass parameter like this after getlogoandcurrency from this URL ?userapi=test&keyapi=test etc.
    * required parameter: @ storeid(optional)
    */
    public function getLogoAndCurrencyAction()
    {
        $post_data = Mage::app()->getRequest()->getParams(); // get post data in array format.
        $storeId   = $post_data['storeid'];
        $block     = new Mage_Page_Block_Html_Header(); // load block to get header content of your current package theme logo
        $logo      = $block->getLogoSrc(); // get current store logo URL

        $currency_code = Mage::getModel('core/store')->load($storeId)->getCurrentCurrencyCode(); // get current currency symbol on current store

        $isPos     =  0;
        $resultArr = array('logo' => $logo,'currency_symbol' => Mage::app()->getLocale()->currency($currency_code)->getSymbol(),'is_pos' => $isPos);
        //echo "<pre>"; print_r($resultArr); die;
        $result    = Mage::helper('core')->jsonEncode($resultArr); // convert array data to json
        return Mage::app()->getResponse()->setBody($result); // set json result in body
    }

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/admindashboard  // pass parameter like this after admindashboard from this URL ?session=sdfsdf
    * required parameter: @ session
    */
    public function AdminDashboardAction()
    {
      $result = array(); // make attay to use one by one key push in array
      if(Mage::helper('mobileadmin')->isEnable()) // check if extension is enabled or not ?
      {
        $post_data = Mage::app()->getRequest()->getParams();
        $sessionId = $post_data['session'];
        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) { // check if customer is not logged in then return Access denied
            echo $this->__("The Login has expired. Please try log in again.");
            return false; // return logged out
        }

        // It will return admin stores from using this code
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $result['store'][] = array('id' =>$store->getId(),
                        'name' => $store->getName()                                
                    );
                }
            }
        }

        // It will return total lifetime sale from using this code
        $orderColl = Mage::getResourceModel('reports/order_collection')->calculateSales()->load()->getFirstItem()->getLifetime();
        $total_sale_with_formetted_price = Mage::helper('core')->currency($orderColl, true, false);
        $result['total_sale'] = $total_sale_with_formetted_price;

        // It will return total average order from using this code
        $collection = Mage::getResourceModel('reports/order_collection')->calculateSales(1);
        $collection->load();
        $sales = $collection->getFirstItem();
        $total_avg_order_formetted_price = Mage::helper('core')->currency($sales->getAverage(), true, false);
        $result['total_avg_orders'] = $total_avg_order_formetted_price;
       
        // It will return last 5 orders from using this code
        $collection_customer = Mage::getResourceModel('reports/order_collection')
                          ->addItemCountExpr()
                          ->joinCustomerName('customer')
                          ->orderByCreatedAt();
        $collection_customer->addAttributeToFilter('store_id', 1);
        $collection_customer->addRevenueToSelect();
        $collection_customer->setPageSize(5);
        
        //echo "<pre>"; print_r($collection_customer->getData()); die;
        foreach ($collection_customer->getData() as $value)
        {
          $result['last_orders']['Customer'][] = $value['customer_firstname']." ".$value['customer_lastname'];
          $result['last_orders']['Items'][] = $value['total_item_count'];
          $result['last_orders']['Grand Total'][] = $value['grand_total'];
        }
        //echo "<pre>"; print_r($result); die;
      }
      else
      {
        $result['error'] = $this->__('Please activate the Mobile Emizentech Extension on the Magento Store.');
      }

      $isEnable = Mage::helper('core')->jsonEncode($result);
      return Mage::app()->getResponse()->setBody($isEnable);
    }

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/adminorders  // pass parameter like this after adminorders from this URL ?session=sdfsdf
    * required parameter: @ session , limit , See $post_data variable
    */
    public function AdminOrdersAction()
    {
      if(Mage::helper('mobileadmin')->isEnable()) // check if extesion enabled or not ?
      {
        $post_data = Mage::app()->getRequest()->getParams(); // get parameter from post method
        $sessionId = $post_data['session'];
        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) { // check session if expired return access denied
            echo $this->__("The Login has expired. Please try log in again.");
            return false;
        }

        $limit      = $post_data['limit'];
        $page       = $post_data['page_num'];
        $storeId    = $post_data['storeid'];   // Make varibles to get post data one by one
        $offset     = $post_data['offset'];
        $is_refresh = $post_data['is_refresh'];

        $orderCollection = Mage::getResourceModel('sales/order_grid_collection')->addFieldToFilter('store_id',Array('eq'=>$storeId))->setOrder('entity_id', 'desc'); // get order collection filter by storeId and desc order by entityId

        $before_coll = count(Mage::getResourceModel('sales/order_grid_collection')->addFieldToFilter('store_id',Array('eq'=>$storeId))->setOrder('entity_id', 'desc')); //echo $before_coll; die;

        if($offset != null)
        {
          $orderCollection->addAttributeToFilter('entity_id', array('lt' => $offset)); // lt means less then
        }

        if($is_refresh == 1) // check last updated order when you pass parameter to $is_refresh = 1
        {
          $last_fetch_order  = $post_data['last_fetch_order'];
          $min_fetch_order   = $post_data['min_fetch_order'];
          $last_updated      = Mage::helper('mobileadmin')->getActualDate($post_data['last_updated']);

          $orderCollection->getSelect()->where("(entity_id BETWEEN '".$min_fetch_order."'AND '".$last_fetch_order ."' AND updated_at > '".$last_updated."') OR entity_id >'".$last_fetch_order."'"); // collection filter by updated date
        }
        //$orderCollection->getSelect()->limit($limit); // define limit
        //echo "<pre>"; print_r(get_class_methods($orderCollection)); die;

        if(isset($page) && $limit)
        {
          //echo $page."**".$limit; die;
          $orderCollection->setPage($page,$limit);
          $orderCollection->setPageSize($limit);
        }

        $totl_rocrd = round($before_coll/$limit); //echo $totl_rocrd; die;

        $i = ($page - 1)*$limit;
        
        foreach($orderCollection as $order){
          if($i < $before_coll)
          {
            $orderListData[] = array(
                'entity_id'     => $order->getEntityId(),
                'increment_id'  => $order->getIncrementId(),
                'store_id'      => $order->getStoreId(),
                'customer_name' => $order->getBillingName(),
                'status'        => $order->getStatus(),
                'order_date'    => date('Y-m-d H:i:s', strtotime($order->getCreatedAt())),
                'grand_total'   => Mage::helper('mobileadmin')->getPrice($order->getGrandTotal()),
                'toal_qty'      => Mage::getModel('sales/order')->load($order->getEntityId())->getTotalQtyOrdered()
            );
          $i++;
          }  
        }

        $updated_time       = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())); // get updated time
        $orderListResultArr = array('orderlistdata' => $orderListData,'updated_time' =>$updated_time, 'total_record'=>$totl_rocrd);
        //echo "<pre>"; print_r($orderListResultArr); die;
        $orderListResult    = Mage::helper('core')->jsonEncode($orderListResultArr); // comvert data array to json
        return Mage::app()->getResponse()->setBody($orderListResult);
      }
      else
      {
        $result['error'] = $this->__('Please activate the Mobile Emizentech Extension on the Magento Store.');
      }
    }

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/adminorderdetail  // pass parameter like this after adminorderdetail from this URL ?session=sdfsdf
    * required parameter: @ session , entity_id , See $post_data variable
    */
    public function AdminOrderDetailAction()
    {
      if(Mage::helper('mobileadmin')->isEnable()){ // check extension if enabled or not
        $post_data = Mage::app()->getRequest()->getParams();
        $sessionId = $post_data['session'];
        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) { // check session if not, will return false
            echo $this->__("The Login has expired. Please try log in again.");
            return false;
        }

        $order_id = $post_data['entity_id']; // get entity id form post method
        $order    = Mage::getModel('sales/order')->load($order_id); // load sales order model by order id

        $order_detail = array(
            'entity_id'    => $order->getEntityId(),
            'increment_id' => $order->getIncrementId(),
            'status'       => $order->getStatus(),
            'order_date'   => date('Y-m-d H:i:s', strtotime($order->getCreatedAt())),
            'total_qty'    => $order->getTotalQtyOrdered(),
            'grand_total'  => Mage::helper('mobileadmin')->getPrice($order->getGrandTotal()),
            'sub_total'    => Mage::helper('mobileadmin')->getPrice($order->getSubtotal()),
            'discount'     => Mage::helper('mobileadmin')->getPrice($order->getDiscountAmount()),
            'tax'          => Mage::helper('mobileadmin')->getPrice($order->getTax())
        );

        $customer_id   = $order->getCustomerId(); // get order customer id
        $customer_name = $order->getCustomerFirstname()." ".$order->getCustomerLastname(); // get order first and last name
        if($customer_id == null)
        {
          $customer_name = $order->getCustomerName();
        }

        $customer_detail = array(
            'customer_id'    => $customer_id,
            'customer_name'  => $customer_name,
            'customer_email' => $order->getCustomerEmail()
        );

        $billing_address = $order->getBillingAddress(); // get order billing address
        $billing_address_data = array(
            'name'      => $billing_address->getFirstname().' '.$billing_address->getLastname(),
            'street'    => $billing_address->getData('street'),
            'city'      => $billing_address->getCity(),
            'region'    => $billing_address->getRegion(),
            'postcode'  => $billing_address->getPostcode(),
            'country'   => Mage::getModel('directory/country')->loadByCode($billing_address->getCountryId())->getName(),
            'telephone' => $billing_address->getTelephone()
        );
        $shipping_address = $order->getShippingAddress(); // get order shipping address
        if($shipping_address)
        {
          $shipping_address_data = array(
              'name'      => $shipping_address->getFirstname().' '.$shipping_address->getLastname(),
              'street'    => $shipping_address->getData('street'),
              'city'      => $shipping_address->getCity(),
              'region'    => $shipping_address->getRegion(),
              'postcode'  => $shipping_address->getPostcode(),
              'country'   => Mage::getModel('directory/country')->loadByCode($shipping_address->getCountryId())->getName(),
              'telephone' => $shipping_address->getTelephone()
          );
        }

        $payment_info = array(
            'payment_method' => $order->getPayment()->getMethodInstance()->getTitle() // Include payment method to array
        );

        $shipping_info = array(
            'shipping_method' => $order->getShippingDescription(), // Shipping methods also add
            'shipping_charge' => Mage::helper('mobileadmin')->getPrice($order->getShippingAmount())
        );

        $products_detail = $this->_orderedAdminProductDetails($order_id); // get products detail by order id

        $full_order_detail = array(
            'basic_order_detail' => $order_detail,
            'customer_detail'    => $customer_detail,
            'billing_address'    => $billing_address_data, // Order and product detail in array
            'shipping_address'   => $shipping_address_data,
            'payment_info'       => $payment_info,
            'shipping_info'      => $shipping_info,
            'product_detail'     => $products_detail               
        );
        $orderDetailResultArr = array('orderlistdata' => $full_order_detail); // make array of order list
        //echo "<pre>"; print_r($orderDetailResultArr); die;
        $orderDetailResult    = Mage::helper('core')->jsonEncode($orderDetailResultArr);
        return Mage::app()->getResponse()->setBody($orderDetailResult);
      }else{
          $isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
          return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
      }
    }

    /*
    * Protected function use in another function according to order id
    * It will return order detail by order id
    */
    protected function _orderedAdminProductDetails($order_id)
    {
      $order = Mage::getModel('sales/order')->load($order_id); // load order according to order id
      foreach ($order->getItemsCollection() as $item)
      {
        $options = $item->getProductOptions();
        if($item->getProductType() == "downloadable")
        {
          $obj = new Mage_Downloadable_Block_Adminhtml_Sales_Items_Column_Downloadable_Name();
          foreach($options['links'] as $links)
          {

              $this->_purchased = Mage::getModel('downloadable/link_purchased')
              ->load($order_id, 'order_id');
              $purchasedItem = Mage::getModel('downloadable/link_purchased_item')->getCollection() // downloadable products collection
              ->addFieldToFilter('order_item_id', $item->getId());
              $this->_purchased->setPurchasedItems($purchasedItem);

              foreach ($this->_purchased->getPurchasedItems() as $_link)
              {
                $links_value[] = $_link->getLinkTitle().'('. $_link->getNumberOfDownloadsUsed() . ' / ' . ($_link->getNumberOfDownloadsBought() ? $_link->getNumberOfDownloadsBought() : Mage::helper('downloadable')->__('U')) .')'; 
              }

              $info = array(array(
                  'label' => $obj->getLinksTitle(),
                  'value' => implode(',',$links_value)
              ));
          }
        }
        else
        {
          $result = array();
          if ($options = $item->getProductOptions()) {
              if (isset($options['options'])) {
                  $result = array_merge($result, $options['options']);
              }
              if (isset($options['additional_options'])) {
                  $result = array_merge($result, $options['additional_options']);
              }
              if (!empty($options['attributes_info'])) {
                  $result = array_merge($options['attributes_info'], $result);
              }
          }

          $info = array();
          if($result)
          {
              foreach ($result as $_option){ // label and value
                  $info[] = array(
                      'label' => $_option['label'],
                      'value' => $_option['value']
                  );
              }
          }
        }
        $skus = '';
        $product = Mage::getModel('catalog/product')->load($item->getProductId()); // load product collection

        if($item->getParentItem()) continue;

        if($_options = $this->_getAdminItemOptions($item))
        {
          $skus = $_options;
        }
        $products_detail[] = array(
            'product_id'  => $item->getProductId(),
            'name'        => $item->getName(),
            'sku'         => $item->getSku(),
            'unit_price'  => Mage::helper('mobileadmin')->getPrice($item->getOriginalPrice()),
            'ordered_qty' => round($item->getQtyOrdered(), 2),
            'row_total'   => Mage::helper('mobileadmin')->getPrice($item->getRowTotal()),
            'options'     => $skus ? $skus : '',
            'image'       => ($product->getImage())?Mage::helper('catalog/image')->init($product, 'image',$product->getImage())->resize(300,330)->keepAspectRatio(true)->constrainOnly(true)->__toString():'N/A',
            'attribute_info' => $info ? $info : ''
        );
      }
      return $products_detail; 
    }

    /* Private function using another function 
    * It will return product SKU's according to item ID
    */
    private function _getAdminItemOptions($item)
    {
      $id = array('id' => $item->getItemId()); // get item id by products items
      $order_items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('parent_item_id',Array('eq'=>$id)); // sales order
      foreach($order_items as $order_item)
      {
        $product_data = Mage::getModel('catalog/product')->load($order_item->getProductId());
        $skus[] = $product_data->getSku();
      }
      return $skus; //return SKU's
    }

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/adminproductlist  // pass parameter like this after adminproductlist from this URL ?session=sdfsdf
    * required parameter: @ session , limit , See $post_data variable
    */
    public function AdminProductListAction()
    {
      if(Mage::helper('mobileadmin')->isEnable())
      {
        $post_data = Mage::app()->getRequest()->getParams(); // get data from post method
        $sessionId = $post_data['session'];
        if(!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // if session expired return access denied
        {
          echo $this->__("The Login has expired. Please try log in again.");
          return false;
        }
        $storeId   = $post_data['storeid'];
        $limit     = $post_data['limit'];   // Pass parameter according to post method pass
        $page       = $post_data['page_num'];
        $offset    = $post_data['offset'];
        $new_products    = $post_data['last_fetch_product'];
        $is_refresh = $post_data['is_refresh'];

        $products  = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId)->setOrder('entity_id', 'desc');

        $before_coll = count(Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId)->setOrder('entity_id', 'desc')); //echo $before_coll; die;

        if($offset != null)
        {
          $products->addAttributeToFilter('entity_id', array('lt' => $offset)); // lt means less then
        }

        if($is_refresh == 1) // When you pull dowm your ios app it will be refresh according to last updated time
        {
          $last_fetch_product     =   $post_data['last_fetch_product'];
          $min_fetch_product      =   $post_data['min_fetch_product'];
          $last_updated           =   $post_data['last_updated'];
          $products->getSelect()->where("(entity_id BETWEEN '".$min_fetch_product."'AND '".$last_fetch_product ."' AND updated_at > '".$last_updated."') OR entity_id >'".$last_fetch_product."'");
        }

        //$products->getSelect()->limit($limit); // define limit how many show items in your page

        if(isset($page) && $limit)
        {
          //echo $page."**".$limit; die;
          $products->setPage($page,$limit);
          $products->setPageSize($limit);
        }

        $totl_rocrd = round($before_coll/$limit); //echo $totl_rocrd; die;

        $i = ($page - 1)*$limit;
        foreach($products as $product) // make array of products detail 
        {
          if($i < $before_coll)
          {
            $product_data = Mage::getModel('catalog/product')->load($product->getId()); // load product according to product id
            $status       = $product_data->getStatus(); // get product status
            $qty          = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_data)->getQty(); // get Quantity
            if($status == 1)
            {
              $status = 'Enabled';}else{$status = 'Disabled';
            }
            if($qty == 0 || $product_data->getIsInStock() == 0)
            {
              $qty = 'Out of Stock';
            }
            $product_list[] = array(
                'id'     => $product->getId(), // products list array
                'sku'    => $product_data->getSku(),
                'name'   => $product_data->getName(),
                'status' => $status,
                'qty'    => $qty,
                'price'  => Mage::helper('mobileadmin')->getPrice($product_data->getPrice()),
                'image'  => ($product_data->getImage())?Mage::helper('catalog/image')->init($product, 'image',$product_data->getImage())->resize(300,330)->keepAspectRatio(true)->constrainOnly(true)->__toString():'N/A',
                'type'   => $product->getTypeId()
            );
          $i++;  
          }  
        }
        $updated_time       = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())); // last item updated time
        $productResultArr  = array('productlistdata' => $product_list,'updated_time' =>$updated_time,'total_record'=>$totl_rocrd);
        //echo "<pre>"; print_r($productResultArr); die;
        $productListResult = Mage::helper('core')->jsonEncode($productResultArr);
        return Mage::app()->getResponse()->setBody($productListResult);
      }
      else
      {
        $isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
        return Mage::app()->getResponse()->setBody($isEnable);
      }
    }

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/adminproductdetail  // pass parameter like this after adminproductdetail from this URL ?session=sdfsdf
    * required parameter: @ session , productid , See $post_data variable
    */
    public function AdminProductDetailAction()
    {
        if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
        {
          $post_data = Mage::app()->getRequest()->getParams(); // parameter of array which is sending by URL
          $sessionId = $post_data['session']; // session id
          if(!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // if exists session id return true otherwise return false
          {
            echo $this->__("The Login has expired. Please try log in again.");
            return false;
          }
          try
          {
            $storeId      = $post_data['storeid']; // store id
            $productId    = $post_data['productid']; // product id
            $product_data = Mage::getModel('catalog/product')->load($productId); // load product by product id
            $status       = $product_data->getStatus(); // get product status
            $qty          = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_data)->getQty(); // get Quantity

            if($status == 1) // if status 1 return Enabled Otherwise return Disabled
            {
              $status = 'Enabled';
            }
            else
            {
              $status = 'Disabled';
            }

            if($product_data->getTypeId() == 'grouped') // if product type is grouped, load product type of grouped
            {
                $associated_products = $product_data->getTypeInstance(true)->getAssociatedProducts($product_data); 
            }
            elseif($product_data->getTypeId() == 'configurable') // if product type is configurable, load product type of configurable
            {

              $associated_products = $product_data->getTypeInstance()->getUsedProducts(); 

            }elseif($product_data->getTypeId() == 'bundle') // if product type is bundle, load product type of bundle
            {
              $associated_products = $product_data->getTypeInstance(true)->getSelectionsCollection($product_data->getTypeInstance(true)->getOptionsIds($product_data), $product_data);  
            }
            foreach($associated_products as $associated_product) // make products detail array
            {
              $status = $associated_product->getStatus(); // product status
              $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($associated_product)->getQty(); // Quantity

              if($status == 1) // if status 1 return Enabled otherwise disabled
              {
                $status = 'Enabled';
              }
              else
              {
                $status = 'Disabled';
              }

              if($qty == 0 || $associated_product->getIsInStock() == 0) // if qty 0 return out of stock products
              {
                $qty = 'Out of Stock';
              }

              $associated_products_details[] = array( // associated products array of product
                  'id'  => $associated_product->getId(),
                  'sku' => $associated_product->getSku()
              );

              $associated_products_list[] = array( // associated products array with other key of product
                  'id'     => $associated_product->getId(),
                  'sku'    => $associated_product->getSku(),
                  'name'   => $associated_product->getName(),
                  'status' => $status,
                  'qty'    => $qty,
                  'price'  => Mage::helper('mobileadmin')->getPrice($associated_product->getPrice())
              );
            }
            $product_details[] = array( // products detail array
                'id'     => $product_data->getId(),
                'sku'    => $product_data->getSku(),
                'name'   => $product_data->getName(),
                'status' => $status,
                'qty'    => $qty,
                'price'  => Mage::helper('mobileadmin')->getPrice($product_data->getPrice()),
                'desc'   => $product_data->getDescription(),
                'type'   => $product_data->getTypeId(),
                'image'  => Mage::getModel('catalog/product_media_config')->getMediaUrl($product_data->getImage()),
                'special_price'   => Mage::helper('mobileadmin')->getPrice($product_data->getSpecialPrice()),
                'image'  => ($product_data->getImage())?Mage::helper('catalog/image')->init($product_data, 'image',$product_data->getImage())->resize(300,330)->keepAspectRatio(true)->constrainOnly(true)->__toString():'N/A',
                'associated_skus' => $associated_products_details
            );

            $productResultArr    = array('productdata' => $product_details , 'associated_products_list' =>$associated_products_list);
            //echo "<pre>"; print_r($productResultArr); die;
            $productDetailResult = Mage::helper('core')->jsonEncode($productResultArr);
            return Mage::app()->getResponse()->setBody($productDetailResult);
          }
          catch (Exception $e)
          {
            $product_details = array (
                'status'    =>  'error',
                'message'   =>  $e->getMessage()
            );
            return Mage::app()->getResponse()->setBody(Mage::helper('core')->jsonEncode($product_details));
          }
        }
        else
        {
          $isEnable = Mage::helper('core')->jsonEncode(array('enable' => false));
          return Mage::app()->getResponse()->setBody($isEnable); // set body of json products data
        }
      }


    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/admincustomerlist  // pass parameter like this after admincustomerlist from this URL ?session=sdfsdf
    * required parameter: @ session , limit See $post_data variable
    */
    public function AdminCustomerListAction()
    {
      if(Mage::helper('mobileadmin')->isEnable()) // check if extension enabled or not
      {
        $post_data = Mage::app()->getRequest()->getParams(); // get parameter from array
        $sessionId = $post_data['session']; // get session
        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // if exists session is return true
        {
          echo $this->__("The Login has expired. Please try log in again.");
          return false;
        }

        $limit          =   $post_data['limit']; // define limit
        $page           =   $post_data['page_num'];
        $offset         =   $post_data['offset']; // define offset
        $new_customers  =   $post_data['last_fetch_customer']; // define last fetch customer time
        $is_refresh     =   $post_data['is_refresh']; // pass is_refresh int 1 or 0
        $customers      =   Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->setOrder('entity_id', 'desc');

        $before_coll = count(Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->setOrder('entity_id', 'desc')); //echo $before_coll; die;

        if($offset != null)
        {
          $customers->addAttributeToFilter('entity_id', array('lt' => $offset)); // lt means less then
        }

        if($is_refresh == 1) // if 1 refresh last updated entry come or not
        {
          $last_fetch_customer  =   $post_data['last_fetch_customer'];
          $min_fetch_customer   =   $post_data['min_fetch_customer'];
          $last_updated         =   Mage::helper('mobileadmin')->getActualDate($post_data['last_updated']);
          $customers->getSelect()->where("(e.entity_id BETWEEN '".$min_fetch_customer."'AND '".$last_fetch_customer ."' AND updated_at > '".$last_updated."') OR e.entity_id >'".$last_fetch_customer."'");
        }

          //$customers->getSelect()->limit($limit); // define limit

        if(isset($page) && $limit)
        {
          //echo $page."**".$limit; die;
          $customers->setPage($page,$limit);
          $customers->setPageSize($limit);
        }

        $totl_rocrd = round($before_coll/$limit); //echo $totl_rocrd; die;

        $i = ($page - 1)*$limit;

        foreach($customers as $customer)
        {
          if($i < $before_coll)
          {
            $billing_address  = Mage::getModel('customer/address')->load($customer->getDefaultBilling()); // get billing address
            $shipping_address = Mage::getModel('customer/address')->load($customer->getDefaultShipping()); // get shipping address

            $customer_list[] = array(
                'entity_id'     => $customer->getEntityId(), // make array of customer list
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'email_id'      => $customer->getEmail(),
                'telephone'     => $billing_address->getData('telephone'),
                'billing_address_id'    => $billing_address->getId(),
                'shipping_address_id'   => $shipping_address->getId()
            );
          $i++;
          }
        }
        $updated_time       = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())); // updated time
        $customerListResultArr = array('customerlistdata' => $customer_list,'updated_time' =>$updated_time,'total_record'=>$totl_rocrd);
        $customerListResult    = Mage::helper('core')->jsonEncode($customerListResultArr);
        return Mage::app()->getResponse()->setBody($customerListResult); // return customer list in default json format
      }
      else
      {
          $isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
          return Mage::app()->getResponse()->setBody($isEnable);
      }
    }

    /* If you want to check function is working perfect or not please direct hit this URL on your browser: YOUR WEB BASE URL/emizenstore/mobileadmin/index/admincustomerdetail  // pass parameter like this after admincustomerdetail from this URL ?session=sdfsdf
    * required parameter: @ session , customer_id  limit See $post_data variable
    */
    public function AdminCustomerDetailAction()
    {
      if(Mage::helper('mobileadmin')->isEnable()) // if extension is enabled
      {
        $post_data = Mage::app()->getRequest()->getParams(); // get parameter
        $sessionId = $post_data['session']; // session id
        if(!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // if session not exists return access denied
        {
          echo $this->__("The Login has expired. Please try log in again.");
          return false;
        }
        $customer_id  = $post_data['customer_id']; // get customer id

        $customerData = Mage::getModel('customer/customer')->load($customer_id); // load customer according to customer id

        $basic_detail = array(
            'entity_id' => $customerData->getEntityId(), // customer detail in array
            'firstname'      => $customerData->getFirstname(),
            'lastname'      => $customerData->getLastname(),
            'email'     => $customerData->getEmail(),
        );

       foreach ($customerData->getAddresses() as $address)
       {
          $billing_type = 0;
          $shipping_type = 0; // make customer address array
          $billing_country_name  = null;

          if($address->getCountryId()) // get country id
          {
            $billing_country_name = Mage::getModel('directory/country')->loadByCode($address->getCountryId())->getName();
          }

          if ($address->getId()==$customerData->getDefaultBilling()) // get billing type
              $billing_type=1;

          if ($address->getId()==$customerData->getDefaultShipping()) // get shipping type
              $shipping_type=1;

          $billing_address_detail[] = array( // make array of billing address detail
              'firstname'     => $address->getFirstname(),
              'lastname'      => $address->getLastname(),
              'street'        => $address->getData('street'),
              'city'          => $address->getCity(),
              'region_id'     => $address->getRegionId() ? $address->getRegionId() : '',
              'region'        => $address->getRegion(),
              'postcode'      => $address->getPostcode(),
              'country'       => $billing_country_name,
              'country_id'    => $address->getCountryId(),
              'telephone'     => $address->getTelephone(),
              'address_id'    => $address->getId(),
              'billing_type'  => $billing_type,
              'shipping_type' => $shipping_type
          );
        }

        $customer_detail = array(
            'basic_details'    => $basic_detail, // customer address detail
            'address'  => $billing_address_detail,
        );

        $order_detail = $this->_getCustomerOrderList($customer_id); // get customer order list according to customer id

        $customerDetailResultArr = array('customerDetails' => $customer_detail,'customerOrderDetail' =>$order_detail); /// return customer detail

        $customerDetailResult    = Mage::helper('core')->jsonEncode($customerDetailResultArr);

        return Mage::app()->getResponse()->setBody($customerDetailResult);
      }else{
          $isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
          return Mage::app()->getResponse()->setBody($isEnable);
      }
    }

    /* make protected function to use further function directly
    * // It will return customer order list by customer id
    */
    protected function _getCustomerOrderList($customer_id)
    {
      $orderCollection = Mage::getResourceModel('sales/order_grid_collection')->addFieldToFilter('customer_id',Array('eq'=>$customer_id))->setOrder('entity_id', 'desc'); // order collection with filter spicific parameter

      $limit = 5; // define limit 5

      $orderCollection->getSelect()->limit($limit); // get latest 5 orders

      foreach($orderCollection as $order) // order detail array, which will return in array
      {
        $orderListData[] = array(
            'entity_id'     => $order->getEntityId(),
            'increment_id'  => $order->getIncrementId(),
            'store_id'      => $order->getStoreId(),
            'customer_name' => $order->getBillingName(),
            'status'        => $order->getStatus(),
            'order_date'    => date('Y-m-d H:i:s', strtotime($order->getCreatedAt())),
            'grand_total'   => Mage::helper('mobileadmin')->getPrice($order->getGrandTotal()),
            'toal_qty'      => Mage::getModel('sales/order')->load($order->getEntityId())->getTotalQtyOrdered()
        );
      }
      return $orderListData; // return customer order detail
    }

    /* Filter customer accoprding to firstname and lastname and email
    *  It will return filterable collection of customer
    */
    public function AdminFilterCustomerListAction()
    {
      if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      {
        $post_data = Mage::app()->getRequest()->getParams(); // get data from post method
        $sessionId = $post_data['session']; // session id
        if(!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // if logged in return true otherwise access denied
        {
            echo $this->__("The Login has expired. Please try log in again.");
            return false;
        }
        $search    = $post_data['search_content']; // pass search content which you want to search by firstname,lastname,email

        // get latest customer from customer model
        $customers = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*')->setOrder('entity_id', 'desc');

        if($search != null) // if search content is not null return filterable data
        {
          $customers->addAttributeToFilter(array(
                  array(
                      'attribute' => 'firstname', // firstname
                      'like' => '%'.$search.'%'
                  ),
                  array(
                      'attribute' => 'lastname', // lastname
                      'like' => '%'.$search.'%'
                  ),
                  array(
                      'attribute' => 'email', // email
                      'like' => '%'.$search.'%'
                  )
              ));
        }
 
        foreach($customers as $customer) // make customer filterable data in array
        {
          $billing_address  = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
          $shipping_address = Mage::getModel('customer/address')->load($customer->getDefaultShipping());

          $customer_list[] = array(
              'entity_id'     => $customer->getEntityId(),
              'firstname' => $customer->getFirstname(), // get every customer detail
              'lastname' => $customer->getLastname(),
              'email_id'      => $customer->getEmail(),
              'telephone'     => $billing_address->getData('telephone'),
              'billing_address_id'    => $billing_address->getId(),
              'shipping_address_id'   => $shipping_address->getId(),
          );
        }
        $customerListResultArr = array('customerlistdata' => $customer_list); // return customer array
        $customerListResult    = Mage::helper('core')->jsonEncode($customerListResultArr);
        return Mage::app()->getResponse()->setBody($customerListResult);
      }
      else
      {
        $isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
        return Mage::app()->getResponse()->setBody($isEnable);
      }
    }

    /* Shoing x and y value for admin graph in particular days
    * @ required parameter: session
    */
    public function AdminDashboardGraphAction()
    {

      if(Mage::helper('mobileadmin')->isEnable()) // check if extension enabled or not
      {
        $post_data = Mage::app()->getRequest()->getParams();
        $sessionId = $post_data['session'];
        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if exists session continue otherwise access denied
        {
          echo $this->__("The Login has expired. Please try log in again.");
          return false;
        }

        $storeId  = $post_data['storeid']; // get magento admin stores
        $type_id  = $post_data['days_for_dashboard']; // days for graph like 7 days , current month etc.

        $now      = Mage::getModel('core/date')->timestamp(time()); // current datetime
        $end_date = date('Y-m-d 23:59:59', $now);  // change current datetime format
        $start_date = '';
        $orderCollection  = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('store_id',Array('eq'=>$storeId))->addFieldToFilter('status',Array('eq'=>'complete'))->setOrder('entity_id', 'desc');            

        // select days range for different different days
        if($type_id == 7) // for 7 days
        {
          $start_date = date('Y-m-d 00:00:00', strtotime('-6 days'));
        }
        elseif($type_id == 30) // for 30 days
        {
          $start_date = date('Y-m-d 00:00:00', strtotime('-29 days'));
        }
        elseif($type_id == 90) // for 90 days
        {
          $start_date = date('Y-m-d 00:00:00', strtotime('-89 days'));
        }
        else if ($type_id == 24) // for 24 days
        {
          $end_date = date("Y-m-d H:m:s");
          $start_date = date("Y-m-d H:m:s", strtotime('-24 hours', time()));
          $timezoneLocal = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);

          list ($dateStart, $dateEnd) = Mage::getResourceModel('reports/order_collection')
          ->getDateRange('12h', '', '', true);

          $dateStart->setTimezone($timezoneLocal);
          $dateEnd->setTimezone($timezoneLocal);

          $dates = array();

          while($dateStart->compare($dateEnd) < 0)
          {
            $d = $dateStart->toString('yyyy-MM-dd HH:mm:ss');
            $dateStart->addHour(1);
            $dates[] = $d;
          }

          $start_date = $dates[0];
          $end_date   = $dates[count($dates)-1];

          $orderCollection->addAttributeToFilter('created_at', array('from'=>$start_date, 'to'=>$end_date));
          $total_count = count($orderCollection);
        } 
        //echo $type_id; die;
        if($type_id != 'year')
        {
          if ($type_id=='month')
          {
            $end_date = date("Y-m-d H:m:s");
            $start_date = date('Y-m-01 H:m:s');
          }

          if($type_id!=24)
          {
            $orderCollection->addAttributeToFilter('created_at', array('from'=>$start_date, 'to'=>$end_date));
            $total_count = count($orderCollection);
            $dates       = $this->getDatesFromRange($start_date, $end_date);
          }
          $count = 0;
          foreach($dates as $date)
          {
            //$orderCollectionByDate = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('store_id',Array('eq'=>$storeId))->addFieldToFilter('status',Array('eq'=>'complete'))->setOrder('entity_id', 'desc');
            $orderCollectionByDate = Mage::getModel('sales/order')->getCollection();

            if($type_id==24)
            {
              $dateStart   = $dates[$count];
              $dateEnd     = $dates[$count+1]; 
            }
            else
            {
              $dateStart   = date('Y-m-d 00:00:00',strtotime($date));
              $dateEnd     = date('Y-m-d 23:59:59',strtotime($date)); 
            }
            $orderByDate = $orderCollectionByDate->addAttributeToFilter('created_at', array('from'=>$dateStart, 'to'=>$dateEnd));
            //echo "<pre>"; print_r($orderByDate->getData()); 
             //$orderByDate->getSelect()->columns('SUM(grand_total) AS grand_total_sum');
            // $orderByDate->getSelect()->group(array('store_id'));
            // $orderdata= $orderByDate->getData();
            //echo count($orderByDate); die;
            if(count($orderByDate) == 0)
            {
              if ($type_id==24)
              {
                $orderTotalByDate[date("Y-m-d H:i",strtotime($date))] = 0;
              }
              else if ($type_id=='month')
              {
                $orderTotalByDate[date('d',strtotime($date))] = 0;
              }
              else
              {
                $orderTotalByDate[$date] = 0; 
              }
            }
            else
            {
              //echo $type_id; die;
              if($type_id==24)
              {
                //$ordersByDate[date("Y-m-d H:i",strtotime($date))][]   = $orderdata[0]['grand_total_sum'];
                //$orderTotalByDate[date("Y-m-d H:i",strtotime($date))] = array_sum($ordersByDate[date("Y-m-d H:i",strtotime($date))]);    
                $orderTotalByDate[date("Y-m-d H:i",strtotime($date))] = count($orderByDate);    

                $orderByDate->getSelect()->columns('SUM(grand_total) AS grand_total_sum');
                $orderByDate->getSelect()->columns('SUM(tax_amount) AS tax_total_sum');
                $orderByDate->getSelect()->columns('SUM(shipping_amount) AS shipping_total_sum');
                $orderByDate->getSelect()->columns('SUM(total_qty_ordered) AS qty_total_sum');

                $orderByDateData = $orderByDate->getData();

                $ordersByDate[$date][]   = $orderByDateData[0]['grand_total_sum'];
                $orderTotalByDateTotoal[] =  array_sum($ordersByDate[$date]);

                $ordersByDateTax[$date][]   = $orderByDateData[0]['tax_total_sum'];
                $orderTotalByDateTax[] =  array_sum($ordersByDateTax[$date]);

                $ordersByDateShipping[$date][]   = $orderByDateData[0]['shipping_total_sum'];
                $orderTotalByDateShipping[] =  array_sum($ordersByDateShipping[$date]);

                $ordersByDateQty[$date][]   = $orderByDateData[0]['qty_total_sum'];
                $orderTotalByDateQty[] =  array_sum($ordersByDateQty[$date]);

              }
              else if ($type_id=='month')
              {
                //$ordersByDate[date('d',strtotime($date))][]   = $orderdata[0]['grand_total_sum'];
                //$orderTotalByDate[date('d',strtotime($date))] = array_sum($ordersByDate[date('d',strtotime($date))]);    
                $orderTotalByDate[date('d',strtotime($date))] = count($orderByDate);    

                $orderByDate->getSelect()->columns('SUM(grand_total) AS grand_total_sum');
                $orderByDate->getSelect()->columns('SUM(tax_amount) AS tax_total_sum');
                $orderByDate->getSelect()->columns('SUM(shipping_amount) AS shipping_total_sum');
                $orderByDate->getSelect()->columns('SUM(total_qty_ordered) AS qty_total_sum');

                $orderByDateData = $orderByDate->getData();

                $ordersByDate[$date][]   = $orderByDateData[0]['grand_total_sum'];
                $orderTotalByDateTotoal[] =  array_sum($ordersByDate[$date]);

                $ordersByDateTax[$date][]   = $orderByDateData[0]['tax_total_sum'];
                $orderTotalByDateTax[] =  array_sum($ordersByDateTax[$date]);

                $ordersByDateShipping[$date][]   = $orderByDateData[0]['shipping_total_sum'];
                $orderTotalByDateShipping[] =  array_sum($ordersByDateShipping[$date]);

                $ordersByDateQty[$date][]   = $orderByDateData[0]['qty_total_sum'];
                $orderTotalByDateQty[] =  array_sum($ordersByDateQty[$date]);

              }
              else
              {
                //$ordersByDate[$date][]   = $orderdata[0]['grand_total_sum'];
                //$orderTotalByDate[$date] = array_sum($ordersByDate[$date]);    
                $orderTotalByDate[$date] = count($orderByDate);


                $orderByDate->getSelect()->columns('SUM(grand_total) AS grand_total_sum');
                $orderByDate->getSelect()->columns('SUM(tax_amount) AS tax_total_sum');
                $orderByDate->getSelect()->columns('SUM(shipping_amount) AS shipping_total_sum');
                $orderByDate->getSelect()->columns('SUM(total_qty_ordered) AS qty_total_sum');

                $orderByDateData = $orderByDate->getData();

                $ordersByDate[$date][]   = $orderByDateData[0]['grand_total_sum'];
                $orderTotalByDateTotoal[] =  array_sum($ordersByDate[$date]);

                $ordersByDateTax[$date][]   = $orderByDateData[0]['tax_total_sum'];
                $orderTotalByDateTax[] =  array_sum($ordersByDateTax[$date]);

                $ordersByDateShipping[$date][]   = $orderByDateData[0]['shipping_total_sum'];
                $orderTotalByDateShipping[] =  array_sum($ordersByDateShipping[$date]);

                $ordersByDateQty[$date][]   = $orderByDateData[0]['qty_total_sum'];
                $orderTotalByDateQty[] =  array_sum($ordersByDateQty[$date]);
                
                
              }
            }

            $count++;
          }
        }
        else
        {
          $end_date = date ('Y-m-d');
          $start_date = date ('Y-01-01');
          $orderCollection->addAttributeToFilter('created_at', array('from'=>$start_date, 'to'=>$end_date));
          $total_count = count($orderCollection);
          $months = $this->get_months($start_date, $end_date);
          $current_year = date("Y");
          foreach ($months as $month)
          {
            $first_day = $this->firstDay($month,$current_year);
            $ordersByDate = array();

            if ($month==date('F'))
              $last_day = date ('Y-m-d');
            else
              $last_day = $this->lastday($month,$current_year);

            $dates       = $this->getDatesFromRange($first_day, $last_day);

            foreach($dates as $date)
            {

              $orderCollectionByDate = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('store_id',Array('eq'=>$storeId));

              $dateStart   = date('Y-m-d 00:00:00',strtotime($date));
              $dateEnd     = date('Y-m-d 23:59:59',strtotime($date)); 
              $orderByDate = $orderCollectionByDate->addAttributeToFilter('created_at', array('from'=>$dateStart, 'to'=>$dateEnd));

            }

            //$orderTotalByDate[$month] = array_sum($ordersByDate);
            $orderTotalByDate[$month] = count($orderByDate);

            $orderByDate->getSelect()->columns('SUM(grand_total) AS grand_total_sum');
            $orderByDate->getSelect()->columns('SUM(tax_amount) AS tax_total_sum');
            $orderByDate->getSelect()->columns('SUM(shipping_amount) AS shipping_total_sum');
            $orderByDate->getSelect()->columns('SUM(total_qty_ordered) AS qty_total_sum');

            $orderByDateData = $orderByDate->getData();

            $ordersByDate[$date][]   = $orderByDateData[0]['grand_total_sum'];
            $orderTotalByDateTotoal[] =  array_sum($ordersByDate[$date]);

            $ordersByDateTax[$date][]   = $orderByDateData[0]['tax_total_sum'];
            $orderTotalByDateTax[] =  array_sum($ordersByDateTax[$date]);

            $ordersByDateShipping[$date][]   = $orderByDateData[0]['shipping_total_sum'];
            $orderTotalByDateShipping[] =  array_sum($ordersByDateShipping[$date]);

            $ordersByDateQty[$date][]   = $orderByDateData[0]['qty_total_sum'];
            $orderTotalByDateQty[] =  array_sum($ordersByDateQty[$date]);

          }
        }

        /*** These commented lines get us grandTotal,lifeTimeSales,averageOrder if you want these, uncomment please ***/
        //$orderGrandTotal      = strip_tags(Mage::helper('core')->currency(array_sum($orderTotalByDate)));
        //$lifeTimeSales        = strip_tags(Mage::helper('core')->currency(round(Mage::getResourceModel('reports/order_collection')->addFieldToFilter('store_id', $storeId)->calculateSales()->load()->getFirstItem()->getLifetime(),2)));
        //$averageOrder         = strip_tags(Mage::helper('core')->currency(round(Mage::getResourceModel('reports/order_collection')->addFieldToFilter('store_id', $storeId)->calculateSales()->load()->getFirstItem()->getAverage(),2)));
        //$orderTotalResultArr  = array('dashboard_result' =>array('ordertotalbydate' => $orderTotalByDate,'ordergrandtotal' => $orderGrandTotal,'totalordercount' => $total_count,'lifetimesales' => $lifeTimeSales,'averageorder' => $averageOrder));

       
        $tot = 0;
        $totTax = 0;
        $$totShipping = 0;
        $totQty = 0;
        for($i=0; $i<=count($orderTotalByDateTotoal); $i++)
        {
          $tot = $tot + $orderTotalByDateTotoal[$i];
        }

        for($j = 0; $j <= count($orderTotalByDateTax); $j++)
        {
          $totTax = $totTax + $orderTotalByDateTax[$j];
        }

        for($k = 0; $k <= count($orderTotalByDateShipping); $k++)
        {
          $totShipping = $totShipping + $orderTotalByDateShipping[$k];
        }

        for($l = 0; $l <= count($orderTotalByDateQty); $l++)
        {
          $totQty = $totQty + $orderTotalByDateQty[$l];
        }        

        //$orderTotalResultArr  = array('dashboard_result' =>array('ordertotalbydate' => $orderTotalByDate));
        $orderTotalResultArr  = array(
                                  'dashboard_result' => array(
                                                            'ordertotalbydate' => $orderTotalByDate,
                                                            'orderTotalAmount' => array(
                                                                                    'Revenue' => Mage::helper('core')->currency($tot, true, false),
                                                                                    'Tax' => Mage::helper('core')->currency($totTax, true, false),
                                                                                    'Shipping' => Mage::helper('core')->currency($totShipping, true, false),
                                                                                    'Quantity' => $totQty
                                                                                  )
                                                        ),
                                );
                                  
        //echo "<pre>"; print_r($orderTotalResultArr); die;
        $orderDashboardResult = Mage::helper('core')->jsonEncode($orderTotalResultArr);
        return Mage::app()->getResponse()->setBody($orderDashboardResult);
      }
      else
      {
        $isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
        return Mage::app()->getResponse()->setBody($isEnable);
      }
    }

    /*
    * @ get range between two dates
    * @param: $start_date,$end_date
    */
    public function getDatesFromRange($start_date, $end_date)
    {
      $date_from = strtotime(date('Y-m-d', strtotime($start_date)));
      $date_to   = strtotime(date('Y-m-d', strtotime($end_date))); 

      for($i=$date_from; $i<=$date_to; $i+=86400)
      {  
        $dates[] = date("Y-m-d", $i);  
      }  
      return $dates;
    }

    /*
    * @ get months
    * @param: $date1,$date2
    */
    public function get_months($date1, $date2)
    {
      $time1 = strtotime($date1); 
      $time2 = strtotime($date2); 
      $my = date('mY', $time2); 
      $months = array(); 
      $f = ''; 

      while($time1 < $time2)
      { 
        $time1 = strtotime((date('Y-m-d', $time1).' +15days')); 

        if(date('m', $time1) != $f)
        { 
          $f = date('m', $time1); 

          if(date('mY', $time1) != $my && ($time1 < $time2)) 
            $months[] = date('m', $time1); 
        } 
      } 

      $months[] = date('m', $time2); 
      return $months; 
    } 

    /*
    * @ get last day
    * @param: $month,$year
    */
    public function lastday($month = '', $year = '')
    {
      if(empty($month))
      {
        $month = date('m');
      }
      if(empty($year))
      {
        $year = date('Y');
      }
      $result = strtotime("{$year}-{$month}-01");
      $result = strtotime('-1 day', strtotime('+1 month', $result));
      return date('Y-m-d', $result);
    }

    /*
    * @ get first day
    * @param: $month,$year
    */
    public function firstDay($month = '', $year = '')
    {
      if(empty($month))
      {
        $month = date('m');
      }
      if(empty($year))
      {
        $year = date('Y');
      }
      $result = strtotime("{$year}-{$month}-01");
      return date('Y-m-d', $result);
    }

    /*public function reIndexAction()
    {
      $collection = Mage::getResourceModel('index/process_collection');
      $re = Mage::getModel('index/process');
      echo "<pre>"; print_r($re->getCollection()->getData()); die;


      $data = Mage::app()->getRequest()->getParams();

      $processId = $data['process'];
      //echo $processId['process']; die;
      $result = array();

      $process = $this->_initProcess($processId);

      echo $process->getIndexer()->getName();

      echo "<pre>"; print_r($process->getData()); die;
      if ($process)
      {
          try {
              Varien_Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');

              $process->reindexEverything();
              Varien_Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
              
              $result['status'] = "index was rebuilt.".$process->getIndexer()->getName();
          } catch (Mage_Core_Exception $e) {
              
              $result['error'] = $e->getMessage();
          } catch (Exception $e) {

              $result['error'] = "There was a problem with reindexing process.";
          }
      } else {

          $result['error'] = "Cannot initialize the indexer process.";
      }

      echo "<pre>"; print_r($result);
    }

    protected function _initProcess($processId)
    {
        //$processId = Mage::app()->getRequest()->getParams('process');
        if($processId)
        {  
          $process = Mage::getModel('index/process')->load($processId);
          if($process->getId() && $process->getIndexer()->isVisible())
          {
            return $process;
          }
        }
        return false;
    }*/

    /**
     * Reindex all using command
     */
    public function reindexAllAction()
    {
      if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      {
        $post_data = Mage::app()->getRequest()->getParams(); // get data from post method
        $sessionId = $post_data['session']; // session id
        if(!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // if logged in return true otherwise access denied
        {
            echo $this->__("The Login has expired. Please try log in again.");
            return false;
        }   
          try
          {
            $result = array();
            $mage_base = Mage::getBaseDir();
            //echo $mage_base; die;
            system("php ". $mage_base ."/shell/indexer.php reindexall > ".$mage_base."/var/log/reindexall.log &");
            $result['success'] = "reindexall via SHELL processing !";
          }
          catch(Exception $e)
          {
            $result['error'] = $e->getMessage();
          }
          $jsonData = Mage::helper('core')->jsonEncode($result);
          return Mage::app()->getResponse()->setBody($jsonData);
      }    
      else
      {
        $isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
        return Mage::app()->getResponse()->setBody($isEnable);
      }
    }

    /**
     * Flush cache storage
     */
    public function flushAllAction()
    {
      if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      {
        $post_data = Mage::app()->getRequest()->getParams(); // get data from post method
        $sessionId = $post_data['session']; // session id
        if(!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // if logged in return true otherwise access denied
        {
            echo $this->__("The Login has expired. Please try log in again.");
            return false;
        }
          try
          {
            $result = array();
            Mage::app()->getCacheInstance()->flush();
            $result['success'] = "The cache storage has been flushed.";
          }
          catch (Exception $e)
          {
            $result['error'] = $e->getMessage();  
          }
          $jsonData = Mage::helper('core')->jsonEncode($result);
          return Mage::app()->getResponse()->setBody($jsonData);
      }    
      else
      {
        $isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
        return Mage::app()->getResponse()->setBody($isEnable);
      }
    }

    /**
     * Flush all magento cache
     */
    public function flushSystemAction()
    {
      if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      {
        $post_data = Mage::app()->getRequest()->getParams(); // get data from post method
        $sessionId = $post_data['session']; // session id
        if(!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // if logged in return true otherwise access denied
        {
            echo $this->__("The Login has expired. Please try log in again.");
            return false;
        }
          try
          {
            $result = array();
            Mage::app()->cleanCache();
            $result['success'] = "The Magento cache storage has been flushed.";
          }
          catch(Exception $e)
          {
            $result['error'] = $e->getMessage();
          }
          $jsonData = Mage::helper('core')->jsonEncode($result);
          return Mage::app()->getResponse()->setBody($jsonData);
      }    
      else
      {
        $isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
        return Mage::app()->getResponse()->setBody($isEnable);
      }
    }
}