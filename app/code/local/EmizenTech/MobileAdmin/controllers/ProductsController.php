<?php
class EmizenTech_MobileAdmin_ProductsController extends Mage_Core_Controller_Front_Action{

	/*
	@ This function perform to dynamic data before product add
	*/
	public function beforeAddProductAction()
	{
		$options = array();
		$post_data = Mage::app()->getRequest()->getParams();

		$collectionAttributeSet = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(4);

        $collectionProductType = Mage::getModel('catalog/product_type')->getOptionArray();

        $options['attributeSetList'] = $collectionAttributeSet->toOptionArray();

        $visibility = Mage::getModel('catalog/product_visibility')->getOptionArray();

        // Countries of manufature list
        $options['countriesList'] = Mage::getModel('directory/country')->getResourceCollection()->toOptionArray(false);

        // Define a products type which type product you want to create.
        $valArr = array();
		foreach ($collectionProductType as $key => $value)
		{
			$valArr['value'] = $key;
			$valArr['label'] = $value;
			$options['productTypeList'][] = $valArr;
		}

		// All visibility data to show the add product field
		$valArr1 = array();
		foreach ($visibility as $key1 => $value1)
		{
			$valArr1['value'] = $key1;
			$valArr1['label'] = $value1;
			$options['visibility_list'][] = $valArr1;
		}

		// Load all website which you made in the magento admin
		$website = array();
		foreach (Mage::app()->getWebsites() as $_website)
    	{
    		$website['website_name'] = $_website->getName();
    		$website['website_id'] = $_website->getId();
    		$options['websiteList'][] = $website;
    	}

    	// Tax classes list for product
    	$TaxArr = array();
    	$collection = Mage::getModel('tax/class')->getCollection()->setClassTypeFilter('PRODUCT');
    	
    	$TaxArr['tax_class_name'] = "none";
		$TaxArr['tax_class_id'] = 0;
		$options['taxClassesList'][] = $TaxArr;
    	foreach ($collection as $value)
    	{
    		$TaxArr['tax_class_name'] = $value->getClassName();
    		$TaxArr['tax_class_id'] = $value->getClassId();
    		$options['taxClassesList'][] = $TaxArr;
    	}


    	// custom categories
    	if(isset($post_data['parent_id']) || isset($post_data['catLabel']) || isset($post_data['store']))
    	{
    		$parentId = $post_data['parent_id'];
    		$catLabel = $post_data['catLabel'];
    		$storeId = $post_data['store'];
    	}
    	$options['categoriesList'] = $this->customCategoriesAction($parentId, $catLabel, $storeId);

        $isEnable = Mage::helper('core')->jsonEncode($options);
        return Mage::app()->getResponse()->setBody($isEnable);
	}

	public function createSimpleProductAction()
    {
    	$post_data = Mage::app()->getRequest()->getParams();
		$product = $this->_createProduct($post_data);
		return $product;
    }

	protected function _createProduct($data, $doSave=true) 
	{
		$$result_su = array();
		if(count($data) > 0)
		{
			$product_attribute_type = $data['attribute_type'];
			$product_type = $data['type'];

			// General Tab
			$product_name = $data['name'];
			$product_description = $data['description'];
			$product_short_description = $data['short_description'];
			$product_sku = $data['sku'];
			$product_weight = $data['weight'];
			$product_new_from_date = $data['new_from_date'];
			$product_new_to_Date = $data['new_to_date'];
			$product_status = $data['status'];
			$product_url_key = $data['url_key'];
			$product_visibility = $data['visibility'];
			$product_manufacture = $data['manufacture'];

			// Prices Tab
			$product_price = $data['price'];
			$product_special_price = $data['special_price'];
			$product_special_price_from = $data['special_price_from'];
			$product_special_price_to = $data['special_price_to'];
			$product_tax_class = $data['tax_class'];

			// Meta Information Tab
			$product_meta_title = $data['meta_title'];
			$product_meta_keywords = $data['meta_keywords'];
			$product_meta_description = $data['meta_description'];

			// Images Tab
			//$product_images = $data['product_images'];

			// Inventory
			$product_qty = $data['qty'];
			//$product_stock_availability = $data['stock_availability'];
			$product_manage_stock = $data['manage_stock'];
			$product_is_in_stock = $data['is_in_stock'];

			// Websites
			$product_websites = $data['websites'];
			//$product_websites_commas_value = implode(',', $product_websites);

			// Categories
			$product_categories = $data['categories'];
			//$product_categories_commas_value = implode(',', $product_categories);

		}
		else
		{
			Mage::log("No data specified", null, "ios_admin.log");
			$result_su['error'] = array('No data specified.');
		}

		// required for some versions
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		
	    $product = Mage::getModel('catalog/product');

	    $product
			->setTypeId($product_type)     // e.g. Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
			->setAttributeSetId($product_attribute_type) // default attribute set
			->setSku($product_sku) // generate some random SKU 
			->setWebsiteIDs($product_websites)
		;			

		// make the product visible
		$product
			->setCategoryIds($product_categories)
			->setStatus($product_status)
			->setUrlKey($product_url_key)
			->setVisibility($product_visibility) // visible in catalog and search
			->setCountryOfManufacture($product_manufacture)
		;

		// configure stock
		if(isset($product_manage_stock))
		{
			$check_manage_stock = $product_manage_stock;
		}
		else
		{
			$check_manage_stock = 0;
		}
		$product->setStockData(array(
			'use_config_manage_stock' => 1, // use global config ?
			'manage_stock'            => $check_manage_stock, // shoudl we manage stock or not?
			'is_in_stock'             => $product_is_in_stock, 
			'qty'                     => $product_qty,
        ));		
		
		// optimize performance, tell Magento to not update indexes
		$product
		    ->setIsMassupdate(true)
			->setExcludeUrlRewrite(true)
		;
		
		// Set Meta Information
		$product
			->setMetaTitle($product_meta_title)
		    ->setMetaKeyword($product_meta_keywords)
		    ->setMetaDescription($product_meta_description)
		;

		// finally set custom data
		$product
			->setName($product_name) // add string attribute
			->setDescription($product_description) // add text attribute
			->setShortDescription($product_short_description) // add text attribute

			// set up prices
			->setPrice($product_price)
			->setSpecialPrice($product_special_price)
			->setSpecialFromDate($product_special_price_from) //special price from (MM-DD-YYYY)
    		->setSpecialToDate($product_special_price_to) //special price to (MM-DD-YYYY)
			->setTaxClassId($product_tax_class)    // Taxable Goods by default
			->setWeight($product_weight)
			->setNewsFromDate($product_new_from_date)
			->setNewsToDate($product_new_to_Date)
		;
			
		// add dropdown attributes like brand, color or size
		if(isset($data['color']))
		{
			$optionId = $this->_getOptionIDByCode('color', 'Black'); 
			$product->setColor($optionId);	
		}
		
		if(isset($data['size']))
		{
			$optionId = $this->_getOptionIDByCode('size', 'M'); 
			$product->setSize($optionId);
		}

		// add product images
		if(count($_FILES) > 0)
		{
			$countFiles = count($_FILES);

			$check_dir = Mage::getBaseDir('media') . DS . 'mobileadmin'. DS . 'product';
			if(!is_dir($check_dir))
			{
				$io = new Varien_Io_File();
				$io->checkAndCreateFolder($check_dir);
			}

			for ($i = 0; $i < $countFiles; $i++)
			{ 
				$imageFileName = $_FILES['product_images_'.$i]['name'];
				$dir = Mage::getBaseDir('media') . DS . 'mobileadmin/product/';
				$path = $dir.$imageFileName;
				if (move_uploaded_file($_FILES["product_images_".$i]["tmp_name"], $path))
				{
					Mage::log("Product Images Uploaded.", null, "ios_admin.log");
				}
				else
				{
					Mage::log("Can not upload image.", null, "ios_admin.log");
					$result_su['error'] = array('Can not upload image.');
				}

				try
				{
					$product->addImageToMediaGallery($path, array('image','small_image','thumbnail'), true, false);
				}
				catch (Exception $e)
				{
					Mage::log($e->getMessage(), null, "ios_admin.log");
					return $e->getMessage();
				}

			}

		}
		Mage::log($data, null, "cart_android.log");
		// die('die');
		if ($doSave)
			$product->save();

		$result_su['success'] = array('Product saved successfully.');
		$isEnable = Mage::helper('core')->jsonEncode($result_su);
        return Mage::app()->getResponse()->setBody($isEnable);
	}

	protected function _getOptionIDByCode($attrCode, $optionLabel) 
	{
		$attrModel   = Mage::getModel('eav/entity_attribute');

		$attrID      = $attrModel->getIdByCode('catalog_product', $attrCode);
		$attribute   = $attrModel->load($attrID);

		$options     = Mage::getModel('eav/entity_attribute_source_table')
			->setAttribute($attribute)
			->getAllOptions(false);

		foreach ($options as $option)
		{
			if ($option['label'] == $optionLabel)
			{
				return $option['value'];
			}
		}

		return false;
	}

	public function customCategoriesAction($parentId = null, $catLevel = null, $store = null)
    {
		if (is_null($parentId) && !is_null($store))
		{
        	$parent = Mage::app()->getStore($store)->getRootCategoryId();
        }
        elseif(is_null($parentId))
        {
        	$parent = 1;
        }

        if(is_null($catLevel))
        {
        	$catLevel = 5;
        }
        
	    $tree = Mage::getResourceModel('catalog/category_tree');

	    $nodes = $tree->loadNode($parent)
				        ->loadChildren($catLevel)
				        ->getChildren();

	    $tree->addCollectionData(null, false, $parent);
	    $json = array('success' => true);
	    $result = array();
	    foreach ($nodes as $node)
	    {
	        $result[] = array(
				'category_id'   => $node->getData('entity_id'),
				'parent_id'     => $parent,
				'name'          => $node->getName(),
				'product_count'	=> Mage::getModel('catalog/category')->load($node->getData('entity_id'))->getProductCount(),
				'categories'    => $this->getNodeChildrenData($node)
			);
	    }

	    return $result;

	    //echo "<pre>"; print_r($json['categories']); die;
	    //return json_encode($json);
    }

    protected function getNodeChildrenData(Varien_Data_Tree_Node $node)
	{
	    foreach ($node->getChildren() as $childNode)
	    {
	        $result[] = array(
				'category_id'   => $childNode->getData('entity_id'),
				'parent_id'     => $node->getData('entity_id'),
				'name'          => $childNode->getData('name'),
				'product_count'	=> Mage::getModel('catalog/category')->load($childNode->getData('entity_id'))->getProductCount(),
				'categories'    => $this->getNodeChildrenData($childNode)
				);
		 }
	    return $result;
	}
}
?>