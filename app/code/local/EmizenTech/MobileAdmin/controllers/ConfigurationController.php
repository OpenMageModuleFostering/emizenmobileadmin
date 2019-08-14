<?php
class EmizenTech_MobileAdmin_ConfigurationController extends Mage_Core_Controller_Front_Action{

	/***** Start General Section *****/

	public function countriesOptionsAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

			try
			{

				$storeId = $post_data['store'];

				$storeName = Mage::getModel('core/store')->load($storeId)->getName();

				//Mage::log($sessionId,null,"cart_android.log");
				//Mage::log($storeId,null,"cart_android.log");

				// Default countries Process
				$countryAcion['countries'] = Mage::getModel('directory/country')->getResourceCollection()->load()->toOptionArray(false);

				$countryAcion['selectedCountryValue'] = Mage::getStoreConfig('general/country/default', $storeId);

				//Mage::log($countryAcion['selectedCountryValue'],null,"cart_android.log");

				$countryAcion['selectedCountryLabel'] = Mage::getModel("directory/country")->load($countryAcion['selectedCountryValue'])->getName();
				//Mage::log($countryAcion['selectedCountryLabel'],null,"cart_android.log");
				$countryValue = $post_data['countryVal'];
				if(!empty($countryValue))
				{
					if($storeId == 0)
					{
						Mage::getConfig()->saveConfig('general/country/default', $countryValue);
						Mage::getConfig()->saveConfig('general/country/default', $countryValue, 'stores', $storeId);	
					}
					else
					{
						//Mage::getConfig()->saveConfig('general/country/default', $countryValue);
						Mage::getConfig()->saveConfig('general/country/default', $countryValue, 'stores', $storeId);
					}
					
					$countryAcion['successMessage'] = "Default Country Has Been Saved On ".$storeName." Store.";

					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();	
				}


				// Allow Countries Process
				$allowCountriesSelectedValue = Mage::getStoreConfig('general/country/allow', $storeId);
				$explodedVal = explode(",", $allowCountriesSelectedValue);
				foreach ($explodedVal as $value)
				{
					$allowValue['val'][] = $value;
				}
				//Mage::log($storeId,null,"cart_android.log");
				//Mage::log($allowValue['val'],null,"cart_android.log");
				foreach ($countryAcion['countries'] as $value)
				{
					//Mage::log($value['value'],null,"cart_android.log");
					if(in_array($value['value'], $allowValue['val']))
					{
						$counVal['value'] = $value['value'];
						$counVal['label'] = $value['label'];
						$counVal['status'] = 1;
						$countryAcion['allowCountriesStatus'][] = $counVal;
					}
					else
					{
						$counVal['value'] = $value['value'];
						$counVal['label'] = $value['label'];
						$counVal['status'] = 0;
						$countryAcion['allowCountriesStatus'][] = $counVal;
					}
				}
				//Mage::log($countryAcion['allowCountriesStatus'],null,"cart_android.log");
				$allowCountriesValue = $post_data['allowCountryVal'];
				if(!empty($allowCountriesValue))
				{
					$csv = implode(",", $allowCountriesValue);
					// Mage::log($storeId,null,"cart_android.log");
					// Mage::log($csv,null,"cart_android.log");
					$config = Mage::getModel('core/config');
					//Mage::getConfig()->saveConfig('general/country/allow', $csv, 'default', $storeId);
					if($storeId == 0)
					{
						Mage::getConfig()->saveConfig('general/country/allow', $csv, 'default', $storeId);
					}
					else
					{
						Mage::getConfig()->saveConfig('general/country/allow', $csv, 'stores', $storeId);
					}
					$countryAcion['successMessage'] = "Allow Countries Has Been Saved On ".$storeName." Store.";
					//Mage::log($countryAcion['successMessage'],null,"cart_android.log");
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}

	

				//Postal Code is Optional for the following countries Pocess
				$postalCodeSelectedValue = Mage::getStoreConfig('general/country/optional_zip_countries', $storeId);
				$postalExplodedVal = explode(",", $postalCodeSelectedValue);
				foreach ($postalExplodedVal as $value)
				{
					$postalVal['val'][] = $value;
				}

				foreach ($countryAcion['countries'] as $value)
				{
					if(in_array($value['value'], $postalVal['val']))
					{
						$postalCounVal['value'] = $value['value'];
						$postalCounVal['label'] = $value['label'];
						$postalCounVal['status'] = 1;
						$countryAcion['postalCountriesStatus'][] = $postalCounVal;
					}
					else
					{
						$postalCounVal['value'] = $value['value'];
						$postalCounVal['label'] = $value['label'];
						$postalCounVal['status'] = 0;
						$countryAcion['postalCountriesStatus'][] = $postalCounVal;
					}
				}

				$postalCountriesValue = $post_data['postalCountryVal'];
				if(!empty($postalCountriesValue))
				{
					$csv = implode(",", $postalCountriesValue);
					//Mage::log($csv,null,"cart_android.log");
					$config = Mage::getModel('core/config');
					Mage::getConfig()->saveConfig('general/country/optional_zip_countries', $csv, 'default', $storeId);
					$countryAcion['successMessage'] = "Postal Code Has Been Saved For The Following Countries On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}



				//European Union Countries
				$unionCountriesValue = Mage::getStoreConfig('general/country/eu_countries', $storeId);
				$unionExplodedVal = explode(",", $unionCountriesValue);
				foreach ($unionExplodedVal as $value)
				{
					$unionCountriesVal['val'][] = $value;
				}

				foreach ($countryAcion['countries'] as $value)
				{
					if(in_array($value['value'], $unionCountriesVal['val']))
					{
						$unionCounVal['value'] = $value['value'];
						$unionCounVal['label'] = $value['label'];
						$unionCounVal['status'] = 1;
						$countryAcion['unionCountriesStatus'][] = $unionCounVal;
					}
					else
					{
						$unionCounVal['value'] = $value['value'];
						$unionCounVal['label'] = $value['label'];
						$unionCounVal['status'] = 0;
						$countryAcion['unionCountriesStatus'][] = $unionCounVal;
					}
				}

				$unionCountriesValue = $post_data['unionCountryVal'];
				if(!empty($unionCountriesValue))
				{
					$csv = implode(",", $unionCountriesValue);
					//Mage::log($csv,null,"cart_android.log");
					//$config = Mage::getModel('core/config');
					Mage::getConfig()->saveConfig('general/country/eu_countries', $csv, 'default', $storeId);
					$countryAcion['successMessage'] = "European Union Countries Has Been Saved On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}

				//echo "<pre>"; print_r($countryAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($countryAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
	        }
	        catch(Exception $e)
	        {
	            $errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }
	    }
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}

	}

	public function statesOptionsAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
			{
				$storeId = $post_data['store'];

				$storeName = Mage::getModel('core/store')->load($storeId)->getName();

				// State is required for
				$statesCountries = Mage::getModel('directory/country')->getResourceCollection()->load()->toOptionArray(false);

				$statesValue = Mage::getStoreConfig('general/region/state_required','default');
				$statesExplodedVal = explode(",", $statesValue);

				foreach ($statesExplodedVal as $value)
				{
					$statesVal['val'][] = $value;
				}
				// Mage::app()->getCacheInstance()->flush();
				// Mage::app()->cleanCache();

				//echo "<pre>"; print_r($statesValue); die;
				foreach ($statesCountries as $value)
				{
					if(in_array($value['value'], $statesVal['val']))
					{
						$statesCounVal['value'] = $value['value'];
						$statesCounVal['label'] = $value['label'];
						$statesCounVal['status'] = 1;
						$statesAcion['statesCountriesStatus'][] = $statesCounVal;
					}
					else
					{
						$statesCounVal['value'] = $value['value'];
						$statesCounVal['label'] = $value['label'];
						$statesCounVal['status'] = 0;
						$statesAcion['statesCountriesStatus'][] = $statesCounVal;
					}
				}

				$statesCountriesValue = $post_data['statesCountryVal'];
				if(!empty($statesCountriesValue))
				{
					$csv = implode(",", $statesCountriesValue);
					//Mage::log($csv,null,"cart_android.log");
					//Mage::getConfig()->saveConfig('general/region/state_required', $csv, 'default', $storeId);
					Mage::getConfig()->saveConfig('general/region/state_required', $csv);
					$statesAcion['successMessage'] = "State Has Been Saved For The Following Countries On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}


				// Display not required State
				$statesValueDisplay = Mage::getStoreConfig('general/region/display_all');

				$statesAcion['statesDsiplayVal'] = $statesValueDisplay;

				$statesReqValue = $post_data['statesReq'];
				if($statesReqValue == 0 || $statesReqValue == 1)
				{
					//Mage::log($statesReqValue,null,"cart_android.log");
					Mage::getConfig()->saveConfig('general/region/display_all', $statesReqValue, 'default');
					$statesAcion['successMessage'] = "Display Not Required State Saved On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}

				//echo "<pre>"; print_r($statesExplodedVal); die;
				$jsonData = Mage::helper('core')->jsonEncode($statesAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 

			}
			catch(Exception $e)
	        {
	            $errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}		
	}


	public function localeOptionsAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();


				// Timezone
				$timezones = Mage::getModel('core/locale')->getOptionTimezones();
				//$localeAction['timezones'] = $timezones;
				$timezoneValues = Mage::getStoreConfig('general/locale/timezone');
				//$localeAction['timezoneSelected'] = $timezoneValues;
				//echo "<pre>"; print_r($timezones); die;
				foreach ($timezones as $value)
				{
					if($value['value'] == $timezoneValues)
					{
						$timezoneArr['label'] = $value['label'];
						$timezoneArr['value'] = $value['value'];
						$timezoneArr['status'] = 'selected';
						$timezoneAnotherArr[] = $timezoneArr;
					}
					else
					{
						$timezoneArr['label'] = $value['label'];
						$timezoneArr['value'] = $value['value'];
						$timezoneArr['status'] = 'not selected';
						$timezoneAnotherArr[] = $timezoneArr;
					}
				}
				$localeAction['timezoneListSelected'] = $timezoneAnotherArr;
				// /echo $timezoneValues; die;


				$timezonesVal = $post_data['timezoneVal'];
				if(!empty($timezonesVal))
				{
					Mage::getConfig()->saveConfig('general/locale/timezone', $timezonesVal, 'default');
					$localeAction['successMessage'] = "Timezone Saved On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}


				
				// Locale
				//echo "<pre>"; print_r(Mage::app()->getLocale()->getOptionWeekdays()); die;
				// $selectedLocale = Mage::getStoreConfig('general/locale/code');
				// echo $selectedLocale;
				// $allLanguages = Mage::app()->getLocale()->getOptionLocales();
				//echo "<pre>"; print_r($allLanguages);

				//Mage::getConfig()->saveConfig('general/locale/code', 'en_US', 'default');
				//Mage::app()->getLocale()->setLocaleCode('en_IE');
				// Mage::getSingleton('core/translate')->setLocale('en_IE')->init('frontend', true);

				// Mage::getConfig()->reinit();
				// Mage::app()->reinitStores();

				//die;
				// $localVal = $post_data['localVal'];
				// if(!empty($localVal))
				// {
				// 	Mage::getConfig()->saveConfig('general/locale/code', $localVal, 'default');
				// 	$statesAcion['successMessage'] = "Default Local Saved On ".$storeName." Store.";
				// 	Mage::getConfig()->reinit();
				// 	Mage::app()->reinitStores();
				// }
				// echo "<pre>"; print_r($allLanguages); die;



				// First day of week
				$weekDaySelected = Mage::getStoreConfig('general/locale/firstday', $storeId);
				//$weekDaySelected['slel'][] = Mage::getStoreConfig('general/locale/firstday');

				$weekDaysValues = Mage::app()->getLocale()->getOptionWeekdays();

				foreach ($weekDaysValues as $value)
				{
					if($value['value'] == $weekDaySelected)
					//if(in_array($value['value'], $weekDaySelected['slel']))
					{
						$makeArr['label'] = $value['label'];
						$makeArr['value'] = $value['value'];
						$makeArr['status'] = 'selected';
						$makeAnotherArr[] = $makeArr;
					}
					else
					{
						$makeArr['label'] = $value['label'];
						$makeArr['value'] = $value['value'];
						$makeArr['status'] = 'not selected';
						$makeAnotherArr[] = $makeArr;
					}
				}
				$localeAction['weekDaysArr'] = $makeAnotherArr;

				$weekDayVal = $post_data['weekDays'];
				if(!empty($weekDayVal))
				{
					if($storeId == 0)
					{
						Mage::getConfig()->saveConfig('general/locale/firstday', $weekDayVal, 'default', $storeId);
					}
					else
					{
						Mage::getConfig()->saveConfig('general/locale/firstday', $weekDayVal, 'stores', $storeId);
					}
					$localeAction['successMessage'] = "First Day Of Week Saved On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}
				


				// Weekend Days
				$weekendDaySelected = Mage::getStoreConfig('general/locale/weekend', $storeId);
				//echo $weekendDaySelected;
				$weekendExplode = explode(',', $weekendDaySelected);
				foreach ($weekendExplode as $value)
				{
					$weekendExplodeValue['val'][] = $value;
				}

				$weekendDaysValues = Mage::app()->getLocale()->getOptionWeekdays();

				foreach ($weekendDaysValues as $value)
				{
					if(in_array($value['value'], $weekendExplodeValue['val']))
					{
						$weekendArr['label'] = $value['label'];
						$weekendArr['value'] = $value['value'];
						$weekendArr['status'] = 'selected';
						$weekendAnotherArr[] = $weekendArr;
					}
					else
					{
						$weekendArr['label'] = $value['label'];
						$weekendArr['value'] = $value['value'];
						$weekendArr['status'] = 'not selected';
						$weekendAnotherArr[] = $weekendArr;
					}
				}
				$localeAction['weekendDaysMulti'] = $weekendAnotherArr;

				$weekendDayVal = $post_data['weekendDays'];
				if(!empty($weekendDayVal))
				{
					$makeStr = implode(',', $weekendDayVal);
					if($storeId == 0)
					{
						Mage::getConfig()->saveConfig('general/locale/weekend', $makeStr, 'default', $storeId);
					}
					else
					{
						Mage::getConfig()->saveConfig('general/locale/weekend', $makeStr, 'stores', $storeId);
					}
					$localeAction['successMessage'] = "Weekend Days Saved On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}

				//echo "<pre>"; print_r($weekendAnotherArr); die;
				$jsonData = Mage::helper('core')->jsonEncode($localeAction);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
	        }
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }

	    }
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}

	/***** End General Section *****/



	/***** Start web section ******/

	public function webSectionAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();

				//Mage::log($post_data, null, "cart_android.log");
				// echo Mage::getStoreConfig('web/default/no_route', 0);
				// Mage::getConfig()->saveConfig('web/default/no_route', 'admin/index/noRoute2', 'default', 0);
				// Mage::getConfig()->reinit();
				// Mage::app()->reinitStores();
				// die;

				// Add store code to URL's
				$storeCodeUrlAction['selectedStoreCode'] = Mage::getStoreConfig('web/url/use_store');

				//$storeCodeValue = is;
				if(isset($post_data['selectedStoreVal']))
				{
					Mage::getConfig()->saveConfig('web/url/use_store', $post_data['selectedStoreVal'], 'default', 0);
					$storeCodeUrlAction['successMessage'] = "Store Code Url Saved On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}



				// Auto-redirect to Base URL
				$storeCodeUrlAction['selectedAutoRedirect'] = Mage::getStoreConfig('web/url/redirect_to_base');

				// $autoRedirectValue = $post_data['selectedRedirect'];
				if(isset($post_data['selectedRedirect']))
				{
					Mage::getConfig()->saveConfig('web/url/redirect_to_base', $post_data['selectedRedirect'], 'default', 0);
					// Mage::app()->getCacheInstance()->flush();
					// Mage::app()->cleanCache();
					$storeCodeUrlAction['successMessage'] = "Auto-redirect to Base URL Saved On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}



				// Search Engines Optimization
				$storeCodeUrlAction['webServerRewriteSlectedValue'] = Mage::getStoreConfig('web/seo/use_rewrites', $storeId);

				//$webRewritesValue = $post_data['webRewriteVal'];
				if(isset($post_data['webRewriteVal']))
				{
					//Mage::log('Welcome', null, "cart_android.log");
					if($storeId == 0)
					{
						// Mage::log($webRewritesValue, null, "cart_android.log");
						// Mage::log($storeId, null, "cart_android.log");
						Mage::getConfig()->saveConfig('web/seo/use_rewrites', $post_data['webRewriteVal'], 'default', $storeId);
					}
					else
					{
						// Mage::log($webRewritesValue, null, "cart_android.log");
						// Mage::log($storeId, null, "cart_android.log");
						Mage::getConfig()->saveConfig('web/seo/use_rewrites', $post_data['webRewriteVal'], 'stores', $storeId);
					}
					
					$storeCodeUrlAction['successMessage'] = "Search Engine Optimization Saved On ".$storeName." Store.";
					Mage::getConfig()->reinit();
					Mage::app()->reinitStores();
				}



				//////// Unsecure

					//Base URL
					$storeCodeUrlAction['baseUrlSelectedValue'] = Mage::getStoreConfig('web/unsecure/base_url', $storeId);

					//$baseUrlValue = $post_data['baseUrlVal'];
					if(isset($post_data['baseUrlVal']))
					{
						//Mage::log('Welcome', null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_url', $post_data['baseUrlVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_url', $post_data['baseUrlVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Unsecure Base Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Base Link URL
					$storeCodeUrlAction['baseLinkUrlSelectedValue'] = Mage::getStoreConfig('web/unsecure/base_link_url', $storeId);

					// $baseLinkUrlValue = $post_data['baseLinkUrlVal'];
					if(isset($post_data['baseLinkUrlVal']))
					{
						//Mage::log('Welcome', null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_link_url', $post_data['baseLinkUrlVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_link_url', $post_data['baseLinkUrlVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Unsecure Base Link Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Base Skin URL
					$storeCodeUrlAction['baseSkinUrlSelectedValue'] = Mage::getStoreConfig('web/unsecure/base_skin_url', $storeId);

					// $baseSkinUrlValue = $post_data['baseSkinUrlVal'];
					if(isset($post_data['baseSkinUrlVal']))
					{
						//Mage::log('Welcome', null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_skin_url', $post_data['baseSkinUrlVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_skin_url', $post_data['baseSkinUrlVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Unsecure Base Skin Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Base Media URL
					$storeCodeUrlAction['baseMediaUrlSelectedValue'] = Mage::getStoreConfig('web/unsecure/base_media_url', $storeId);

					//$baseMediaUrlValue = $post_data['baseMediaUrlVal'];
					if(isset($post_data['baseMediaUrlVal']))
					{
						//Mage::log('Welcome', null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_media_url', $post_data['baseMediaUrlVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_media_url', $post_data['baseMediaUrlVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Unsecure Base Media Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Base JavaScript URL
					$storeCodeUrlAction['baseJavaUrlSelectedValue'] = Mage::getStoreConfig('web/unsecure/base_js_url', $storeId);
					//Mage::log($storeCodeUrlAction['baseJavaUrlSelectedValue'], null, "cart_android.log");
					//$baseJavaUrlValue = $post_data['baseJavaUrlVal'];
					if(isset($post_data['baseJavaUrlVal']))
					{
						//Mage::log($baseJavaUrlValue, null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							$emizenSwitch = new Mage_Core_Model_Config();
							/*
							*turns notice on
							*/
							$emizenSwitch->saveConfig('web/unsecure/base_js_url', $post_data['baseJavaUrlVal'], 'default', 0);
							//Mage::getConfig()->saveConfig('web/unsecure/base_js_url', $post_data['baseJavaUrlVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_js_url', $post_data['baseJavaUrlVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Unsecure Base JavaScript Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//////// Secure

					//Base URL
					$storeCodeUrlAction['baseUrlSecureSelectedValue'] = Mage::getStoreConfig('web/secure/base_url', $storeId);

					//$baseUrlSecureValue = $post_data['baseUrlSecureVal'];
					if(isset($post_data['baseUrlSecureVal']))
					{
						//Mage::log('Welcome', null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/secure/base_url', $post_data['baseUrlSecureVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/secure/base_url', $post_data['baseUrlSecureVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Secure Base Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Base Link URL
					$storeCodeUrlAction['baseLinkUrlSecureSelectedValue'] = Mage::getStoreConfig('web/secure/base_link_url', $storeId);

					// $baseLinkUrlSecureValue = $post_data['baseLinkUrlSecureVal'];
					if(isset($post_data['baseLinkUrlSecureVal']))
					{
						//Mage::log('Welcome', null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/secure/base_link_url', $post_data['baseLinkUrlSecureVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/secure/base_link_url', $post_data['baseLinkUrlSecureVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Secure Base Link Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Base Skin URL
					$storeCodeUrlAction['baseSkinUrlSecureSelectedValue'] = Mage::getStoreConfig('web/secure/base_skin_url', $storeId);

					//$baseSkinUrlSecureValue = $post_data['baseSkinUrlSecureVal'];
					if(isset($post_data['baseSkinUrlSecureVal']))
					{
						//Mage::log('Welcome', null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/secure/base_skin_url', $post_data['baseSkinUrlSecureVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/secure/base_skin_url', $post_data['baseSkinUrlSecureVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Secure Base Skin Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Base Media URL
					$storeCodeUrlAction['baseMediaUrlSecureSelectedValue'] = Mage::getStoreConfig('web/secure/base_media_url', $storeId);

					//$baseMediaUrlSecureValue = $post_data['baseMediaUrlSecureVal'];
					if(isset($post_data['baseMediaUrlSecureVal']))
					{
						//Mage::log('Welcome', null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/secure/base_media_url', $post_data['baseMediaUrlSecureVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/secure/base_media_url', $post_data['baseMediaUrlSecureVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Secure Base Media Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Base JavaScript URL
					$storeCodeUrlAction['baseJavaUrlSecureSelectedValue'] = Mage::getStoreConfig('web/secure/base_js_url', $storeId);

					//$baseJavaUrlSecureValue = $post_data['baseJavaUrlSecureVal'];
					if(isset($post_data['baseJavaUrlSecureVal']))
					{
						//Mage::log('Welcome', null, "cart_android.log");
						if($storeId == 0)
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_js_url', $post_data['baseJavaUrlSecureVal'], 'default', $storeId);
						}
						else
						{
							// Mage::log($webRewritesValue, null, "cart_android.log");
							// Mage::log($storeId, null, "cart_android.log");
							Mage::getConfig()->saveConfig('web/unsecure/base_js_url', $post_data['baseJavaUrlSecureVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Secure Base JavaScript Url Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Use Secure URLs in Frontend
					$storeCodeUrlAction['secureUrlFrontendSelectedValue'] = Mage::getStoreConfig('web/secure/use_in_frontend', $storeId);
					//$secureUrlFrontendValue = $post_data['secureUrlFrontendVal'];
					if(isset($post_data['secureUrlFrontendVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/secure/use_in_frontend', $post_data['secureUrlFrontendVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/secure/use_in_frontend', $post_data['secureUrlFrontendVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Secure URLs Saved in Frontend On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Use Secure URLs in Admin
					$storeCodeUrlAction['secureUrlAdminSelectedValue'] = Mage::getStoreConfig('web/secure/use_in_adminhtml');
					//$secureUrlAdminValue = $post_data['secureUrlAdminVal'];
					if(isset($post_data['secureUrlAdminVal']))
					{
						Mage::getConfig()->saveConfig('web/secure/use_in_adminhtml', $post_data['secureUrlAdminVal'], 'default', 0);
						
						$storeCodeUrlAction['successMessage'] = "Secure URLs Saved in Admin On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Offloader header
					$storeCodeUrlAction['offloaderHeaderSelectedValue'] = Mage::getStoreConfig('web/secure/offloader_header');
					//$offloaderHeaderValue = $post_data['offloaderHeaderVal'];
					if(isset($post_data['offloaderHeaderVal']))
					{
						Mage::getConfig()->saveConfig('web/secure/offloader_header', $post_data['offloaderHeaderVal'], 'default', 0);
						
						$storeCodeUrlAction['successMessage'] = "Offloader Header Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



				//Default Pages

					//Default Web URL
					$storeCodeUrlAction['defaultWebUrlSelectedValue'] = Mage::getStoreConfig('web/default/front', $storeId);
					//$defaultWebUrlValue = $post_data['defaultWebUrlVal'];
					if(isset($post_data['defaultWebUrlVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/default/front', $post_data['defaultWebUrlVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/default/front', $post_data['defaultWebUrlVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Default Web URL Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//CMS Home Page
					$AllCmsPages = Mage::getModel('cms/page')->getCollection()->toOptionArray();

					$selected = Mage::getStoreConfig('web/default/cms_home_page', $storeId);

					foreach ($AllCmsPages as $value)
					{
						if($value['value'] == $selected)
						{
							$cms_home['value'] = $value['value'];
							$cms_home['label'] = $value['label'];
							$cms_home['select'] = 1;
							$storeCodeUrlAction['cmsPagesAllValue'][] = $cms_home;
						}
						else
						{
							$cms_home['value'] = $value['value'];
							$cms_home['label'] = $value['label'];
							$cms_home['select'] = 0;
							$storeCodeUrlAction['cmsPagesAllValue'][] = $cms_home;
						}
					}

					//$cmsHomeValue = $post_data['cmsHomeVal'];
					if(isset($post_data['cmsHomeVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/default/cms_home_page', $post_data['cmsHomeVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/default/cms_home_page', $post_data['cmsHomeVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Default Home Page Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

					
					//Default No-route URL
					$storeCodeUrlAction['noRouteSelectedValue'] = Mage::getStoreConfig('web/default/no_route', 1);
					//Mage::log($storeId, null, "cart_android.log");
					//$noRouteValue = $post_data['noRouteVal'];
					if(isset($post_data['noRouteVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/default/no_route', $post_data['noRouteVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/default/no_route', $post_data['noRouteVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Default No Route URL Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//CMS No Route Page
					$noRoutePageselected = Mage::getStoreConfig('web/default/cms_no_route', $storeId);

					foreach ($AllCmsPages as $value)
					{
						if($value['value'] == $noRoutePageselected)
						{
							$noRoute['value'] = $value['value'];
							$noRoute['label'] = $value['label'];
							$noRoute['select'] = 1;
							$storeCodeUrlAction['noRouteAllValue'][] = $noRoute;
						}
						else
						{
							$noRoute['value'] = $value['value'];
							$noRoute['label'] = $value['label'];
							$noRoute['select'] = 0;
							$storeCodeUrlAction['noRouteAllValue'][] = $noRoute;
						}
					}

					//$CmsNoRouteValue = $post_data['CmsNoRouteVal'];
					if(isset($post_data['CmsNoRouteVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/default/cms_no_route', $post_data['CmsNoRouteVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/default/cms_no_route', $post_data['CmsNoRouteVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Cmd No-Route Page Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




					//CMS No Cookies Page
					$noCookiesPageselected = Mage::getStoreConfig('web/default/cms_no_cookies', $storeId);

					foreach ($AllCmsPages as $value)
					{
						if($value['value'] == $noCookiesPageselected)
						{
							$noRoute['value'] = $value['value'];
							$noRoute['label'] = $value['label'];
							$noRoute['select'] = 1;
							$storeCodeUrlAction['noCookiesAllValue'][] = $noRoute;
						}
						else
						{
							$noRoute['value'] = $value['value'];
							$noRoute['label'] = $value['label'];
							$noRoute['select'] = 0;
							$storeCodeUrlAction['noCookiesAllValue'][] = $noRoute;
						}
					}

					//$noCookiesValue = $post_data['noCookiesVal'];
					if(isset($post_data['noCookiesVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/default/cms_no_cookies', $post_data['noCookiesVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/default/cms_no_cookies', $post_data['noCookiesVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Cmd No-Cookies Value Page Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Show Breadcrumbs for CMS Pages
					$storeCodeUrlAction['breadCrumbsValue'] = Mage::getStoreConfig('web/default/show_cms_breadcrumbs', $storeId);
					//$breadcrumbsValue = $post_data['breadCrumbsVal'];
					if(isset($post_data['breadCrumbsVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/default/show_cms_breadcrumbs', $post_data['breadCrumbsVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/default/show_cms_breadcrumbs', $post_data['breadCrumbsVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Saved Breadcrumbs for CMS Pages On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



				//Polls

					//Disallow Voting in a Poll Multiple Times from Same IP-address
					$storeCodeUrlAction['pollsValue'] = Mage::getStoreConfig('web/polls/poll_check_by_ip', $storeId);
					//$pollValue = $post_data['pollVal'];
					if(isset($post_data['pollVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/polls/poll_check_by_ip', $post_data['pollVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/polls/poll_check_by_ip', $post_data['pollVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Saved Voting Poll On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



				//Session Cookie Management

					//Cookie Lifetime
					$storeCodeUrlAction['cookieLifetimeValue'] = Mage::getStoreConfig('web/cookie/cookie_lifetime', $storeId);
					//$cookieLifetimeValue = $post_data['cookieLifetimeVal'];
					if(isset($post_data['cookieLifetimeVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/cookie/cookie_lifetime', $post_data['cookieLifetimeVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/cookie/cookie_lifetime', $post_data['cookieLifetimeVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Cookie Lifetime Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Cookie Path
					$storeCodeUrlAction['cookiePaathValue'] = Mage::getStoreConfig('web/cookie/cookie_path', $storeId);
					// $cookiePaathValue = $post_data['cookiePaathVal'];
					if(isset($post_data['cookiePaathVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/cookie/cookie_path', $post_data['cookiePaathVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/cookie/cookie_path', $post_data['cookiePaathVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Cookie Path Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Cookie Domain
					$storeCodeUrlAction['cookieDomainValue'] = Mage::getStoreConfig('web/cookie/cookie_domain', $storeId);
					//$cookieDomainValue = $post_data['cookieDomainVal'];
					if(isset($post_data['cookieDomainVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/cookie/cookie_domain', $post_data['cookieDomainVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/cookie/cookie_domain', $post_data['cookieDomainVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Cookie Domain Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Use HTTP Only
					$storeCodeUrlAction['httpOnlyValue'] = Mage::getStoreConfig('web/cookie/cookie_httponly', $storeId);
					//$httpOnlyValue = $post_data['httpOnlyVal'];
					if(isset($post_data['httpOnlyVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/cookie/cookie_httponly', $post_data['httpOnlyVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/cookie/cookie_httponly', $post_data['httpOnlyVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Use HTTP Only Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Cookie Restriction Mode
					$storeCodeUrlAction['cookieRestrictionModeValue'] = Mage::getStoreConfig('web/cookie/cookie_restriction');
					//$cookieRestrictionModeValue = $post_data['cookieRestrictionModeVal'];
					if(isset($post_data['cookieRestrictionModeVal']))
					{
						Mage::getConfig()->saveConfig('web/cookie/cookie_restriction', $post_data['cookieRestrictionModeVal'], 'default', 0);
						
						$storeCodeUrlAction['successMessage'] = "Cookie Restriction Mode Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//Session Validation Settings

					//Validate REMOTE_ADDR
					$storeCodeUrlAction['validateRemoteValue'] = Mage::getStoreConfig('web/session/use_remote_addr');
					//$validateRemoteValue = $post_data['validateRemoteVal'];
					if(isset($post_data['validateRemoteVal']))
					{
						Mage::getConfig()->saveConfig('web/session/use_remote_addr', $post_data['validateRemoteVal'], 'default', 0);
						
						$storeCodeUrlAction['successMessage'] = "Validate REMOTE_ADDR Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Validate HTTP_VIA
					$storeCodeUrlAction['validateHttpValue'] = Mage::getStoreConfig('web/session/use_http_via');
					//$validateHttpValue = $post_data['validateHttpVal'];
					if(isset($post_data['validateHttpVal']))
					{
						Mage::getConfig()->saveConfig('web/session/use_http_via', $post_data['validateHttpVal'], 'default', 0);
						
						$storeCodeUrlAction['successMessage'] = "Validate HTTP_VIA Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Validate HTTP_X_FORWARDED_FOR
					$storeCodeUrlAction['validateHttpXXValue'] = Mage::getStoreConfig('web/session/use_http_x_forwarded_for');
					//$validateHttpXXValue = $post_data['validateHttpXXVal'];
					if(isset($post_data['validateHttpXXVal']))
					{
						Mage::getConfig()->saveConfig('web/session/use_http_x_forwarded_for', $post_data['validateHttpXXVal'], 'default', 0);
						
						$storeCodeUrlAction['successMessage'] = "Validate HTTP_X_FORWARDED_FOR Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Validate HTTP_USER_AGENT
					$storeCodeUrlAction['validateHttpUserValue'] = Mage::getStoreConfig('web/session/use_http_user_agent');
					//$validateHttpUserValue = $post_data['validateHttpUserVal'];
					if(isset($post_data['validateHttpUserVal']))
					{
						Mage::getConfig()->saveConfig('web/session/use_http_user_agent', $post_data['validateHttpUserVal'], 'default', 0);
						
						$storeCodeUrlAction['successMessage'] = "Validate HTTP_USER_AGENT Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Use SID on Frontend
					$storeCodeUrlAction['useSIDValue'] = Mage::getStoreConfig('web/session/use_frontend_sid');
					//$useSIDValue = $post_data['useSIDVal'];
					if(isset($post_data['useSIDVal']))
					{
						Mage::getConfig()->saveConfig('web/session/use_frontend_sid', $post_data['useSIDVal'], 'default', 0);
						
						$storeCodeUrlAction['successMessage'] = "Use SID on Frontend Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



				//Browser Capabilities Detection

					//Redirect to CMS-page if Cookies are Disabled
					$storeCodeUrlAction['redirectToCmsValue'] = Mage::getStoreConfig('web/browser_capabilities/cookies', $storeId);
					//$redirectToCmsValue = $post_data['redirectToCmsVal'];
					if(isset($post_data['redirectToCmsVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/browser_capabilities/cookies', $post_data['redirectToCmsVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/browser_capabilities/cookies', $post_data['redirectToCmsVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Redirect to CMS-page if Cookies are Disabled Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Show Notice if JavaScript is Disabled
					$storeCodeUrlAction['showNoticeValue'] = Mage::getStoreConfig('web/browser_capabilities/javascript', $storeId);
					//$showNoticeValue = $post_data['showNoticeVal'];
					if(isset($post_data['showNoticeVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('web/browser_capabilities/javascript', $post_data['showNoticeVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('web/browser_capabilities/javascript', $post_data['showNoticeVal'], 'stores', $storeId);
						}
						
						$storeCodeUrlAction['successMessage'] = "Show Notice if JavaScript is Disabled Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



				// echo "<pre>"; print_r($storeCodeUrlAction); die;
				$jsonData = Mage::helper('core')->jsonEncode($storeCodeUrlAction);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
	        }
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }
	    }
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}

	}
	/***** End web section ******/



	/***** Start Currency Setup section ******/

	public function currencySetupAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();

				// echo Mage::getStoreConfig('currency/options/base');
				// Mage::getConfig()->saveConfig('currency/options/base', 'INR', 'default', 0);
				// Mage::getConfig()->reinit();
				// Mage::app()->reinitStores();
				// die;

				//Currency Options

					//Base Currency
					$currenciesList = Mage::app()->getLocale()->getOptionCurrencies();
					$selectedCurrency = Mage::getStoreConfig('currency/options/base', 0);
					
					foreach ($currenciesList as $value)
					{
						if($value['value'] == $selectedCurrency)
						{
							$baseCurr['value'] = $value['value'];
							$baseCurr['label'] = $value['label'];
							$baseCurr['status'] = 1;
							$currencySetupAction['baseCurrencyStatus'][] = $baseCurr;
						}
						else
						{
							$baseCurr['value'] = $value['value'];
							$baseCurr['label'] = $value['label'];
							$baseCurr['status'] = 0;
							$currencySetupAction['baseCurrencyStatus'][] = $baseCurr;
						}
					}

					//$baseCurrValue = $post_data['baseCurrVal'];
					if(isset($post_data['baseCurrVal']))
					{
						//Mage::log($post_data['baseCurrVal'], null, "cart_android.log");
						Mage::getConfig()->saveConfig('currency/options/base', $post_data['baseCurrVal'], 'default', 0);
						
						//$storeCodeUrlAction['successMessage'] = "Base Currency Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Default Display Currency
					$selectedDefaultCurrency = Mage::getStoreConfig('currency/options/default', $storeId);
					
					foreach ($currenciesList as $value)
					{
						if($value['value'] == $selectedDefaultCurrency)
						{
							$defaultCurr['value'] = $value['value'];
							$defaultCurr['label'] = $value['label'];
							$defaultCurr['status'] = 1;
							$currencySetupAction['defaultDisplayCurrencyStatus'][] = $defaultCurr;
						}
						else
						{
							$defaultCurr['value'] = $value['value'];
							$defaultCurr['label'] = $value['label'];
							$defaultCurr['status'] = 0;
							$currencySetupAction['defaultDisplayCurrencyStatus'][] = $defaultCurr;
						}
					}

					//$defaultDisCurrValue = $post_data['defaultDisplayCurrVal'];
					if(isset($post_data['defaultDisplayCurrVal']))
					{
						//Mage::log("nav", null, "cart_android.log");
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('currency/options/default', $post_data['defaultDisplayCurrVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('currency/options/default', $post_data['defaultDisplayCurrVal'], 'stores', $storeId);
						}
						
						$currencySetupAction['successMessage'] = "Default Display Currency Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}
					//echo "<pre>"; print_r($currencySetupAction); die;


					//Allowed Currencies
					$allowedCurrencies = Mage::getStoreConfig('currency/options/allow', $storeId);
					$explodedCurr = explode(',', $allowedCurrencies);
					foreach ($explodedCurr as $value)
					{
						$selectedCurrAllowed['allowedCurr'][] = $value;
					}
					//echo "<pre>"; print_r($selectedCurrAllowed['allowedCurr']); die;
					foreach ($currenciesList as $value)
					{
						if(in_array($value['value'], $selectedCurrAllowed['allowedCurr']))
						{
							$allowedCurr['value'] = $value['value'];
							$allowedCurr['label'] = $value['label'];
							$allowedCurr['status'] = 1;
							$currencySetupAction['allowedCurrenciesList'][] = $allowedCurr;
						}
						else
						{
							$allowedCurr['value'] = $value['value'];
							$allowedCurr['label'] = $value['label'];
							$allowedCurr['status'] = 0;
							$currencySetupAction['allowedCurrenciesList'][] = $allowedCurr;
						}
					}
					//echo "<pre>"; print_r($currencySetupAction1); die;
					//$allowedCurrValue = $post_data['allowedCurrVal'];
					if(isset($post_data['allowedCurrVal']))
					{
						//Mage::log($post_data['allowedCurrVal'], null, "cart_android.log");
						$implodeVal = implode(',', $post_data['allowedCurrVal']);
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('currency/options/allow', $implodeVal, 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('currency/options/allow', $implodeVal, 'stores', $storeId);
						}
						
						$currencySetupAction['successMessage'] = "Allowed Currencies Value Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//Webservicex

					//Connection Timeout in Seconds
					$currencySetupAction['connectionTimedOutSelectedValue'] = Mage::getStoreConfig('currency/webservicex/timeout');

					if(isset($post_data['connectionTimedOutVal']))
					{
						//Mage::log($post_data['baseCurrVal'], null, "cart_android.log");
						Mage::getConfig()->saveConfig('currency/webservicex/timeout', $post_data['connectionTimedOutVal'], 'default', 0);
						
						$storeCodeUrlAction['successMessage'] = "Connection Timeout Value Saved in Seconds On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				//echo "<pre>"; print_r($currencySetupAction); die;
				$jsonData = Mage::helper('core')->jsonEncode($currencySetupAction);
		      	return Mage::app()->getResponse()->setBody($jsonData); 

			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }
	    }
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}
	/***** End Currency Setup section ******/



	/***** Start Store Email Addresses section ******/

	public function storeEmailAddressAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();

				//General Contact

					//Sender Name
					$storeEmailAddresses['generalSenderNameSelectedValue'] = Mage::getStoreConfig('trans_email/ident_general/name', $storeId);
					
					if(isset($post_data['generalSenderNameVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_general/name', $post_data['generalSenderNameVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_general/name', $post_data['generalSenderNameVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Name Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

					//Sender Email
					$storeEmailAddresses['generalSenderEmailSelectedValue'] = Mage::getStoreConfig('trans_email/ident_general/email', $storeId);
					
					if(isset($post_data['generalSenderEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_general/email', $post_data['generalSenderEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_general/email', $post_data['generalSenderEmailVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Email Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//Sales Representative

					//Sender Name
					$storeEmailAddresses['salesSenderNameSelectedValue'] = Mage::getStoreConfig('trans_email/ident_sales/name', $storeId);
					
					if(isset($post_data['salesSenderNameVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_sales/name', $post_data['salesSenderNameVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_sales/name', $post_data['salesSenderNameVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Name Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

					//Sender Email
					$storeEmailAddresses['salesSenderEmailSelectedValue'] = Mage::getStoreConfig('trans_email/ident_sales/email', $storeId);
					
					if(isset($post_data['salesSenderEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_sales/email', $post_data['salesSenderEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_sales/email', $post_data['salesSenderEmailVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Email Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//Customer Support

					//Sender Name
					$storeEmailAddresses['customerSenderNameSelectedValue'] = Mage::getStoreConfig('trans_email/ident_support/name', $storeId);
					
					if(isset($post_data['customerSenderNameVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_support/name', $post_data['customerSenderNameVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_support/name', $post_data['customerSenderNameVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Name Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

					//Sender Email
					$storeEmailAddresses['customerSenderEmailSelectedValue'] = Mage::getStoreConfig('trans_email/ident_support/email', $storeId);
					
					if(isset($post_data['customerSenderEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_support/email', $post_data['customerSenderEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_support/email', $post_data['customerSenderEmailVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Email Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//Custom Email 1

					//Sender Name
					$storeEmailAddresses['custom1NameSelectedValue'] = Mage::getStoreConfig('trans_email/ident_custom1/name', $storeId);
					
					if(isset($post_data['custom1SenderNameVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_custom1/name', $post_data['custom1SenderNameVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_custom1/name', $post_data['custom1SenderNameVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Name Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

					//Sender Email
					$storeEmailAddresses['custom1EmailSelectedValue'] = Mage::getStoreConfig('trans_email/ident_custom1/email', $storeId);
					
					if(isset($post_data['custom1EmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_custom1/email', $post_data['custom1EmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_custom1/email', $post_data['custom1EmailVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Email Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//Custom Email 2

					//Sender Name
					$storeEmailAddresses['custom2NameSelectedValue'] = Mage::getStoreConfig('trans_email/ident_custom2/name', $storeId);
					
					if(isset($post_data['custom2SenderNameVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_custom2/name', $post_data['custom2SenderNameVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_custom2/name', $post_data['custom2SenderNameVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Name Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

					//Sender Email
					$storeEmailAddresses['custom2EmailSelectedValue'] = Mage::getStoreConfig('trans_email/ident_custom2/email', $storeId);
					
					if(isset($post_data['custom2EmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('trans_email/ident_custom2/email', $post_data['custom2EmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('trans_email/ident_custom2/email', $post_data['custom2EmailVal'], 'stores', $storeId);
						}
						
						$storeEmailAddresses['successMessage'] = "Sender Email Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				//echo "<pre>"; print_r($storeEmailAddresses); die;
				$jsonData = Mage::helper('core')->jsonEncode($storeEmailAddresses);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}		
	}
	/***** End Store Email Addresses section ******/


	/***** Start Contacts section ******/

	protected $_options = null;
    public function emailSendersListArray()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $config = Mage::getSingleton('adminhtml/config')->getSection('trans_email')->groups->children();
            foreach ($config as $node) {
                $nodeName   = $node->getName();
                $label      = (string) $node->label;
                $sortOrder  = (int) $node->sort_order;
                $this->_options[$sortOrder] = array(
                    'value' => preg_replace('#^ident_(.*)$#', '$1', $nodeName),
                    'label' => Mage::helper('adminhtml')->__($label)
                );
            }
            ksort($this->_options);
        }

        return $this->_options;
    }

	public function contactsAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();

				//Contact Us

					//Enable Contact Us
					$storeContacts['enableContactUsSelectedValue'] = Mage::getStoreConfig('contacts/contacts/enabled', $storeId);
					
					if(isset($post_data['enableContactUsVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('contacts/contacts/enabled', $post_data['enableContactUsVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('contacts/contacts/enabled', $post_data['enableContactUsVal'], 'stores', $storeId);
						}
						
						$storeContacts['successMessage'] = "Conatct Us Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



				//Email Options

					//Send Emails To
					$storeContacts['sendEmailsToSelectedValue'] = Mage::getStoreConfig('contacts/email/recipient_email', $storeId);
					
					if(isset($post_data['sendEmailsToVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('contacts/email/recipient_email', $post_data['sendEmailsToVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('contacts/email/recipient_email', $post_data['sendEmailsToVal'], 'stores', $storeId);
						}
						
						$storeContacts['successMessage'] = "Send Emails To Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Email Sender
					$emailSenderSelectedValue = Mage::getStoreConfig('contacts/email/sender_email_identity', $storeId);
					foreach ($this->emailSendersListArray() as $value)
					{
		                if($value['value'] == $emailSenderSelectedValue)
		                {
		                	$emailSenderArr['label'] = $value['label'];
			                $emailSenderArr['value'] = $value['value'];
			                $emailSenderArr['status'] = 1;
			                $storeContacts['emailSenderStatusList'][] = $emailSenderArr;
		                }
		                else
		                {
		                	$emailSenderArr['label'] = $value['label'];
			                $emailSenderArr['value'] = $value['value'];
			                $emailSenderArr['status'] = 0;
			                $storeContacts['emailSenderStatusList'][] = $emailSenderArr;
		                }
		            }

		            if(isset($post_data['senderEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('contacts/email/sender_email_identity', $post_data['senderEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('contacts/email/sender_email_identity', $post_data['senderEmailVal'], 'stores', $storeId);
						}
						
						$storeContacts['successMessage'] = "Email Sender updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Email Template
					$emailTemplateSelectedValue = Mage::getStoreConfig('contacts/email/email_template', $storeId);

					$emailTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$emailTemCollection = $emailTemObj->toOptionArray();
					if($emailTemCollection[0]['value'] == "")
					{
						$emailTemCollection[0]['value'] = "contacts_email_email_template";
					}
					//echo "<pre>"; print_r($emailTemCollection); die;
					foreach ($emailTemCollection as $value)
					{
						if($value['value'] == $emailTemplateSelectedValue)
						{

							$emailTemArr['label'] = $value['label'];
							$emailTemArr['value'] = $value['value'];
							$emailTemArr['status'] = 1;
							$storeContacts['emailTempColl'][] = $emailTemArr;
						}
						else
						{
							$emailTemArr['label'] = $value['label'];
							$emailTemArr['value'] = $value['value'];
							$emailTemArr['status'] = 0;
							$storeContacts['emailTempColl'][] = $emailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['emailTemplateVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('contacts/email/email_template', $post_data['emailTemplateVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('contacts/email/email_template', $post_data['emailTemplateVal'], 'stores', $storeId);
						}
						
						$storeContacts['successMessage'] = "Conatct Email Template updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				//echo "<pre>"; print_r($storeContacts); die;
				$jsonData = Mage::helper('core')->jsonEncode($storeContacts);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}	
	/***** End Contacts section ******/


	/***** Start Content Management section ******/

	public function contentManagementAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();

				//WYSIWYG Options

					//Enable WYSIWYG Editor
					$enableEditorSelectedValue = Mage::getStoreConfig('cms/wysiwyg/enabled', $storeId);

					$ediorObj = new Mage_Adminhtml_Model_System_Config_Source_Cms_Wysiwyg_Enabled();
					$enabledEditorArray = $ediorObj->toOptionArray();
					
					//echo "<pre>"; print_r($enabledEditorArray); die;
					foreach ($enabledEditorArray as $value)
					{
						if($value['value'] == $enableEditorSelectedValue)
						{

							$editorArr['label'] = $value['label'];
							$editorArr['value'] = $value['value'];
							$editorArr['status'] = 1;
							$contentManagementAcion['enabledEditorList'][] = $editorArr;
						}
						else
						{
							$editorArr['label'] = $value['label'];
							$editorArr['value'] = $value['value'];
							$editorArr['status'] = 0;
							$contentManagementAcion['enabledEditorList'][] = $editorArr;
						}
					}

					if(isset($post_data['editorVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('cms/wysiwyg/enabled', $post_data['editorVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('cms/wysiwyg/enabled', $post_data['editorVal'], 'stores', $storeId);
						}
						
						$contentManagementAcion['successMessage'] = "WYSIWYG Editor Option updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Use Static URLs for Media Content in WYSIWYG for Catalog
					$contentManagementAcion['contentStaticSelectedValue'] = Mage::getStoreConfig('cms/wysiwyg/use_static_urls_in_catalog');
					if(isset($post_data['contentStaticUrlVal']))
					{
						Mage::getConfig()->saveConfig('cms/wysiwyg/use_static_urls_in_catalog', $post_data['contentStaticUrlVal'], 'default', 0);

						$contentManagementAcion['successMessage'] = "Static URLs for Media Content in WYSIWYG for Catalog updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


				//echo "<pre>"; print_r($contentManagementAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($contentManagementAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}	

	/***** End Content Management section ******/


	/***** Start Sales section ******/

	public function salesAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();

				//General

					//Hide Customer IP
					$salesAcion['hideCustomerIpSelectedValue'] = Mage::getStoreConfig('sales/general/hide_customer_ip', $storeId);
					if(isset($post_data['hideCustomerIpVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales/general/hide_customer_ip', $post_data['hideCustomerIpVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales/general/hide_customer_ip', $post_data['hideCustomerIpVal'], 'stores', $storeId);
						}
						
						$salesAcion['successMessage'] = "Hide Customer IP updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


				//Checkout Totals Sort Order

					//Subtotal
					$salesAcion['subtotalSelectedValue'] = Mage::getStoreConfig('sales/totals_sort/subtotal');
					if(isset($post_data['subtotalVal']))
					{
						Mage::getConfig()->saveConfig('sales/totals_sort/subtotal', $post_data['subtotalVal'], 'default', 0);

						$salesAcion['successMessage'] = "Subtotal Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Discount
					$salesAcion['discountSelectedValue'] = Mage::getStoreConfig('sales/totals_sort/discount');
					if(isset($post_data['discountVal']))
					{
						Mage::getConfig()->saveConfig('sales/totals_sort/discount', $post_data['discountVal'], 'default', 0);

						$salesAcion['successMessage'] = "Discount Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Shipping
					$salesAcion['shippingSelectedValue'] = Mage::getStoreConfig('sales/totals_sort/shipping');
					if(isset($post_data['shippingVal']))
					{
						Mage::getConfig()->saveConfig('sales/totals_sort/shipping', $post_data['shippingVal'], 'default', 0);

						$salesAcion['successMessage'] = "Shipping Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Fixed Product Tax
					$salesAcion['fixProductTaxSelectedValue'] = Mage::getStoreConfig('sales/totals_sort/weee');
					if(isset($post_data['fixProductTaxVal']))
					{
						Mage::getConfig()->saveConfig('sales/totals_sort/weee', $post_data['fixProductTaxVal'], 'default', 0);

						$salesAcion['successMessage'] = "Fixed Product Tax Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Tax
					$salesAcion['taxSelectedValue'] = Mage::getStoreConfig('sales/totals_sort/tax');
					if(isset($post_data['taxVal']))
					{
						Mage::getConfig()->saveConfig('sales/totals_sort/tax', $post_data['taxVal'], 'default', 0);

						$salesAcion['successMessage'] = "Tax Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Grand Total
					$salesAcion['grandTotalSelectedValue'] = Mage::getStoreConfig('sales/totals_sort/grand_total');
					if(isset($post_data['grandTotalVal']))
					{
						Mage::getConfig()->saveConfig('sales/totals_sort/grand_total', $post_data['grandTotalVal'], 'default', 0);

						$salesAcion['successMessage'] = "Grand Total Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



				//Reorder

					//Allow Reorder
					$salesAcion['allowReorderSelectedValue'] = Mage::getStoreConfig('sales/reorder/allow', $storeId);
					if(isset($post_data['allowReorderVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales/reorder/allow', $post_data['allowReorderVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales/reorder/allow', $post_data['allowReorderVal'], 'stores', $storeId);
						}
						
						$salesAcion['successMessage'] = "Allow Reorder Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



				//Minimum Order Amount

					//Enable
					$salesAcion['minOrderEnableSelectedValue'] = Mage::getStoreConfig('sales/minimum_order/active');
					if(isset($post_data['minOrderEnableVal']))
					{
						Mage::getConfig()->saveConfig('sales/minimum_order/active', $post_data['minOrderEnableVal'], 'default', 0);

						$salesAcion['successMessage'] = "Minimum Order Amount extension updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Minimum Amount
					$salesAcion['minAmountSelectedValue'] = Mage::getStoreConfig('sales/minimum_order/amount');
					if(isset($post_data['minAmountVal']))
					{
						Mage::getConfig()->saveConfig('sales/minimum_order/amount', $post_data['minAmountVal'], 'default', 0);

						$salesAcion['successMessage'] = "Minimum Order Amount updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Description Message
					$salesAcion['descriptionMessSelectedValue'] = Mage::getStoreConfig('sales/minimum_order/description', $storeId);
					if(isset($post_data['descriptionMessVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales/minimum_order/description', $post_data['descriptionMessVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales/minimum_order/description', $post_data['descriptionMessVal'], 'stores', $storeId);
						}
						
						$salesAcion['successMessage'] = "Description Message updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Error to Show in Shopping Cart
					$salesAcion['errorMessSelectedValue'] = Mage::getStoreConfig('sales/minimum_order/error_message', $storeId);
					if(isset($post_data['errorMessVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales/minimum_order/error_message', $post_data['errorMessVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales/minimum_order/error_message', $post_data['errorMessVal'], 'stores', $storeId);
						}
						
						$salesAcion['successMessage'] = "Error to Show in Shopping Cart updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Validate Each Address Separately in Multi-address Checkout
					$salesAcion['validateAddressSelectedValue'] = Mage::getStoreConfig('sales/minimum_order/multi_address');
					if(isset($post_data['validateAddressVal']))
					{
						Mage::getConfig()->saveConfig('sales/minimum_order/multi_address', $post_data['validateAddressVal'], 'default', 0);

						$salesAcion['successMessage'] = "Validate Each Address Separately in Multi-address Checkout updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Multi-address Description Message
					$salesAcion['multiAddressSelectedValue'] = Mage::getStoreConfig('sales/minimum_order/multi_address_description', $storeId);
					if(isset($post_data['multiAddressVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales/minimum_order/multi_address_description', $post_data['multiAddressVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales/minimum_order/multi_address_description', $post_data['multiAddressVal'], 'stores', $storeId);
						}
						
						$salesAcion['successMessage'] = "Multi-address Description Message updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Multi-address Error to Show in Shopping Cart
					$salesAcion['multiAddressErrorSelectedValue'] = Mage::getStoreConfig('sales/minimum_order/multi_address_error_message', $storeId);
					if(isset($post_data['multiAddressErrorVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales/minimum_order/multi_address_error_message', $post_data['multiAddressErrorVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales/minimum_order/multi_address_error_message', $post_data['multiAddressErrorVal'], 'stores', $storeId);
						}
						
						$salesAcion['successMessage'] = "Multi-address Error to Show in Shopping Cart updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//Dashboard

					//Use Aggregated Data (beta)
					$salesAcion['useAggregatedSelectedValue'] = Mage::getStoreConfig('sales/dashboard/use_aggregated_data');
					if(isset($post_data['useAggregatedVal']))
					{
						Mage::getConfig()->saveConfig('sales/dashboard/use_aggregated_data', $post_data['useAggregatedVal'], 'default', 0);

						$salesAcion['successMessage'] = "Use Aggregated Data updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//Gift Options

					//Allow Gift Messages on Order Level
					$salesAcion['allowGiftSelectedValue'] = Mage::getStoreConfig('sales/gift_options/allow_order');
					if(isset($post_data['allowGiftVal']))
					{
						Mage::getConfig()->saveConfig('sales/gift_options/allow_order', $post_data['allowGiftVal'], 'default', 0);

						$salesAcion['successMessage'] = "Allow Gift Messages on Order Level updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Allow Gift Messages for Order Items
					$salesAcion['allowGiftOrderSelectedValue'] = Mage::getStoreConfig('sales/gift_options/allow_items');
					if(isset($post_data['allowGiftOrderVal']))
					{
						Mage::getConfig()->saveConfig('sales/gift_options/allow_items', $post_data['allowGiftOrderVal'], 'default', 0);

						$salesAcion['successMessage'] = "Allow Gift Messages for Order Items updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




				//Minimum Advertised Price

					//Enable MAP
					$salesAcion['enableMapSelectedValue'] = Mage::getStoreConfig('sales/msrp/enabled');
					if(isset($post_data['enableMapVal']))
					{
						Mage::getConfig()->saveConfig('sales/msrp/enabled', $post_data['enableMapVal'], 'default', 0);

						$salesAcion['successMessage'] = "Enable MAP Extension Updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Apply MAP (Default Value)
					$salesAcion['applyMapSelectedValue'] = Mage::getStoreConfig('sales/msrp/apply_for_all');
					if(isset($post_data['applyMapVal']))
					{
						Mage::getConfig()->saveConfig('sales/msrp/apply_for_all', $post_data['applyMapVal'], 'default', 0);

						$salesAcion['successMessage'] = "Apply MAP Value Updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Display Actual Price
					$salesAcion['displayActualSelectedValue'] = Mage::getStoreConfig('sales/msrp/display_price_type');
					if(isset($post_data['displayActualVal']))
					{
						Mage::getConfig()->saveConfig('sales/msrp/display_price_type', $post_data['displayActualVal'], 'default', 0);

						$salesAcion['successMessage'] = "Display Actual Price Updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Default Popup Text Message
					$salesAcion['defaultPopUpSelectedValue'] = Mage::getStoreConfig('sales/msrp/explanation_message', $storeId);
					if(isset($post_data['defaultPopUpVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales/msrp/explanation_message', $post_data['defaultPopUpVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales/msrp/explanation_message', $post_data['defaultPopUpVal'], 'stores', $storeId);
						}
						
						$salesAcion['successMessage'] = "Default Popup Text Message updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Default "What's This" Text Message
					$salesAcion['defaultWhatsTextSelectedValue'] = Mage::getStoreConfig('sales/msrp/explanation_message_whats_this', $storeId);
					if(isset($post_data['defaultWhatsTextVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales/msrp/explanation_message_whats_this', $post_data['defaultWhatsTextVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales/msrp/explanation_message_whats_this', $post_data['defaultWhatsTextVal'], 'stores', $storeId);
						}
						
						$salesAcion['successMessage'] = "Default Text Message updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				//echo "<pre>"; print_r($salesAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($salesAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}
	/***** End Sales section ******/



	/***** Start Sales Email section ******/

	public function salesEmailsOrderAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();


				//Order

					//Enabled
					$salesEmailsAcion['salesEmailEnableSelectedValue'] = Mage::getStoreConfig('sales_email/order/enabled', $storeId);
					if(isset($post_data['salesEmailEnableVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order/enabled', $post_data['salesEmailEnableVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order/enabled', $post_data['salesEmailEnableVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Sales Email Extension updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//New Order Confirmation Email Sender
					$newOrderConfirmationSelectedValue = Mage::getStoreConfig('sales_email/order/identity', $storeId);

					foreach ($this->emailSendersListArray() as $value)
					{
						if($value['value'] == $newOrderConfirmationSelectedValue)
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 1;
							$salesEmailsAcion['newOrderConfirmationList'][] = $emailComArr;
						}
						else
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 0;
							$salesEmailsAcion['newOrderConfirmationList'][] = $emailComArr;
						}
						
					}

					if(isset($post_data['newOrderConfirmationVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order/identity', $post_data['newOrderConfirmationVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order/identity', $post_data['newOrderConfirmationVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "New Order Confirmation Email Sender updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




					//New Order Confirmation Template
					$newOrderEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/order/template', $storeId);

					$newOrderEmailTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderEmailTemCollection = $newOrderEmailTemObj->toOptionArray();
					if($newOrderEmailTemCollection[0]['value'] == "")
					{
						$newOrderEmailTemCollection[0]['value'] = "sales_email_order_template";
					}
					//echo "<pre>"; print_r($newOrderEmailTemCollection); die;
					foreach ($newOrderEmailTemCollection as $value)
					{
						if($value['value'] == $newOrderEmailTemplateSelectedValue)
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderEmailTempColl'][] = $newOrderEmailTemArr;
						}
						else
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderEmailTempColl'][] = $newOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderEmailTemplateVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order/template', $post_data['newOrderEmailTemplateVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order/template', $post_data['newOrderEmailTemplateVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "New Order Confirmation Template updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//New Order Confirmation Template for Guest
					$forGuestEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/order/guest_template', $storeId);

					$newOrderEmailGuestTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderEmailGuestTemCollection = $newOrderEmailGuestTemObj->toOptionArray();
					if($newOrderEmailGuestTemCollection[0]['value'] == "")
					{
						$newOrderEmailGuestTemCollection[0]['value'] = "sales_email_order_guest_template";
					}

					//echo "<pre>"; print_r($emailTemCollection); die;
					foreach ($newOrderEmailGuestTemCollection as $value)
					{
						if($value['value'] == $forGuestEmailTemplateSelectedValue)
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
						else
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderForGuestVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order/guest_template', $post_data['newOrderForGuestVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order/guest_template', $post_data['newOrderForGuestVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "New Order Confirmation Template for Guest updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Send Order Email Copy To
					$salesEmailsAcion['emailCopyToSelectedValue'] = Mage::getStoreConfig('sales_email/order/copy_to', $storeId);
					if(isset($post_data['emailCopyToVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order/copy_to', $post_data['emailCopyToVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order/copy_to', $post_data['emailCopyToVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Order Email Copy To Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Send Order Email Copy Method
					$salesEmailsAcion['orderEmailCopySelectedValue'] = Mage::getStoreConfig('sales_email/order/copy_method', $storeId);
					if(isset($post_data['orderEmailCopyVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order/copy_method', $post_data['orderEmailCopyVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order/copy_method', $post_data['orderEmailCopyVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Order Email Copy Method Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				// echo "<pre>"; print_r($salesEmailsAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($salesEmailsAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}


	public function salesEmailsOrderCommentsAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();


				//Order Comments

					//Enabled
					$salesEmailsAcion['salesEmailCommentEnableSelectedValue'] = Mage::getStoreConfig('sales_email/order_comment/enabled', $storeId);
					if(isset($post_data['salesEmailCommentEnableVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/enabled', $post_data['salesEmailCommentEnableVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/enabled', $post_data['salesEmailCommentEnableVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Sales Email for Order Comment Extension updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Order Comment Email Sender
					$newOrderConfirmationSelectedValue = Mage::getStoreConfig('sales_email/order_comment/identity', $storeId);

					foreach ($this->emailSendersListArray() as $value)
					{
						if($value['value'] == $newOrderConfirmationSelectedValue)
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 1;
							$salesEmailsAcion['newOrderConfirmationCommentList'][] = $emailComArr;
						}
						else
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 0;
							$salesEmailsAcion['newOrderConfirmationCommentList'][] = $emailComArr;
						}
						
					}

					if(isset($post_data['newOrderConfirmationCommentVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/identity', $post_data['newOrderConfirmationCommentVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/identity', $post_data['newOrderConfirmationCommentVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Order Comment Email Sender updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




					//Order Comment Email Template
					$newOrderEmailTemplateCommentSelectedValue = Mage::getStoreConfig('sales_email/order_comment/template', $storeId);

					$newOrderEmailCommentTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderEmailTemCommentCollection = $newOrderEmailCommentTemObj->toOptionArray();
					if($newOrderEmailTemCommentCollection[0]['value'] == "")
					{
						$newOrderEmailTemCommentCollection[0]['value'] = "sales_email_order_comment_template";
					}
					//echo "<pre>"; print_r($newOrderEmailTemCommentCollection); die;
					foreach ($newOrderEmailTemCommentCollection as $value)
					{
						if($value['value'] == $newOrderEmailTemplateCommentSelectedValue)
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderEmailTempCommentColl'][] = $newOrderEmailTemArr;
						}
						else
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderEmailTempCommentColl'][] = $newOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderEmailCommentTemplateVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/template', $post_data['newOrderEmailCommentTemplateVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/template', $post_data['newOrderEmailCommentTemplateVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Order Comment Email Template updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Order Comment Email Template for Guest
					$forGuestEmailTemplateCommentSelectedValue = Mage::getStoreConfig('sales_email/order_comment/guest_template', $storeId);

					$newOrderEmailGuestCommentTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderEmailGuestTemCommentCollection = $newOrderEmailGuestCommentTemObj->toOptionArray();
					if($newOrderEmailGuestTemCommentCollection[0]['value'] == "")
					{
						$newOrderEmailGuestTemCommentCollection[0]['value'] = "sales_email_order_comment_guest_template";
					}

					//echo "<pre>"; print_r($emailTemCollection); die;
					foreach ($newOrderEmailGuestTemCommentCollection as $value)
					{
						if($value['value'] == $forGuestEmailTemplateCommentSelectedValue)
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderForGuestCommentColl'][] = $forGuestOrderEmailTemArr;
						}
						else
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderForGuestCommentColl'][] = $forGuestOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderForGuestCommentVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/guest_template', $post_data['newOrderForGuestCommentVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/guest_template', $post_data['newOrderForGuestCommentVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Order Comment Email Template for Guest updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Send Order Comment Email Copy To
					$salesEmailsAcion['emailCopyCommentToSelectedValue'] = Mage::getStoreConfig('sales_email/order_comment/copy_to', $storeId);
					if(isset($post_data['emailCopyToCommentVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/copy_to', $post_data['emailCopyToCommentVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/copy_to', $post_data['emailCopyToCommentVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Order Comment Email Copy To updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Send Order Comments Email Copy Method
					$salesEmailsAcion['orderEmailCopyCommentSelectedValue'] = Mage::getStoreConfig('sales_email/order_comment/copy_method', $storeId);
					if(isset($post_data['orderEmailCopyCommentVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/copy_method', $post_data['orderEmailCopyCommentVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/order_comment/copy_method', $post_data['orderEmailCopyCommentVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Order Comments Email Copy Method Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				//echo "<pre>"; print_r($salesEmailsAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($salesEmailsAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}


	public function salesEmailsOrderInvoiceAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();


				//Invoice

					//Enabled
					$salesEmailsAcion['salesEmailInvoiceEnableSelectedValue'] = Mage::getStoreConfig('sales_email/invoice/enabled', $storeId);
					if(isset($post_data['salesEmailInvoiceEnableVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/enabled', $post_data['salesEmailInvoiceEnableVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/enabled', $post_data['salesEmailInvoiceEnableVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Sales Email Extension for Invoice updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Invoice Email Sender
					$invoiceEmailSelectedValue = Mage::getStoreConfig('sales_email/invoice/identity', $storeId);

					foreach ($this->emailSendersListArray() as $value)
					{
						if($value['value'] == $invoiceEmailSelectedValue)
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 1;
							$salesEmailsAcion['invoiceEmailList'][] = $emailComArr;
						}
						else
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 0;
							$salesEmailsAcion['invoiceEmailList'][] = $emailComArr;
						}
						
					}

					if(isset($post_data['invoiceEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/identity', $post_data['invoiceEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/identity', $post_data['invoiceEmailVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Invoice Email updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




					//Invoice Email Template
					$newOrderInvoiceEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/invoice/template', $storeId);

					$newOrderInvoiceEmailTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderInvoiceEmailTemCollection = $newOrderInvoiceEmailTemObj->toOptionArray();
					if($newOrderInvoiceEmailTemCollection[0]['value'] == "")
					{
						$newOrderInvoiceEmailTemCollection[0]['value'] = "sales_email_invoice_template";
					}
					//echo "<pre>"; print_r($newOrderInvoiceEmailTemCollection); die;
					foreach ($newOrderInvoiceEmailTemCollection as $value)
					{
						if($value['value'] == $newOrderInvoiceEmailTemplateSelectedValue)
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderInvoiceEmailTempColl'][] = $newOrderEmailTemArr;
						}
						else
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderInvoiceEmailTempColl'][] = $newOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderInvoiceEmailTemplateVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/template', $post_data['newOrderInvoiceEmailTemplateVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/template', $post_data['newOrderInvoiceEmailTemplateVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Invoice Email Template updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Invoice Email Template for Guest
					$forGuestInvoiceEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/invoice/guest_template', $storeId);

					$newOrderInvoiceEmailGuestTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderInvoiceEmailGuestTemCollection = $newOrderInvoiceEmailGuestTemObj->toOptionArray();
					if($newOrderInvoiceEmailGuestTemCollection[0]['value'] == "")
					{
						$newOrderInvoiceEmailGuestTemCollection[0]['value'] = "sales_email_invoice_guest_template";
					}

					//echo "<pre>"; print_r($emailTemCollection); die;
					foreach ($newOrderInvoiceEmailGuestTemCollection as $value)
					{
						if($value['value'] == $forGuestInvoiceEmailTemplateSelectedValue)
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderInvoiceForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
						else
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderInvoiceForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderInvoiceForGuestVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/guest_template', $post_data['newOrderInvoiceForGuestVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/guest_template', $post_data['newOrderInvoiceForGuestVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Invoice Email Template for Guest updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Send Invoice Email Copy To
					$salesEmailsAcion['emailInvoiceCopyToSelectedValue'] = Mage::getStoreConfig('sales_email/invoice/copy_to', $storeId);
					if(isset($post_data['emailInvoiceCopyToVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/copy_to', $post_data['emailInvoiceCopyToVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/copy_to', $post_data['emailInvoiceCopyToVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Invoice Email Copy To Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Send Invoice Email Copy Method
					$salesEmailsAcion['orderInvoiceEmailCopySelectedValue'] = Mage::getStoreConfig('sales_email/invoice/copy_method', $storeId);
					if(isset($post_data['orderInvoiceEmailCopyVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/copy_method', $post_data['orderInvoiceEmailCopyVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice/copy_method', $post_data['orderInvoiceEmailCopyVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Invoice Email Copy Method Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				// echo "<pre>"; print_r($salesEmailsAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($salesEmailsAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}


	public function salesEmailsOrderInvoiceCommentAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();


				//Invoice Comments

					//Enabled
					$salesEmailsAcion['salesEmailInvoiceCommentEnableSelectedValue'] = Mage::getStoreConfig('sales_email/invoice_comment/enabled', $storeId);
					if(isset($post_data['salesEmailInvoiceCommentEnableVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/enabled', $post_data['salesEmailInvoiceCommentEnableVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/enabled', $post_data['salesEmailInvoiceCommentEnableVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Sales Email Extension for Invoice Comment updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Invoice Comment Email Sender
					$invoiceCommentEmailSelectedValue = Mage::getStoreConfig('sales_email/invoice_comment/identity', $storeId);

					foreach ($this->emailSendersListArray() as $value)
					{
						if($value['value'] == $invoiceCommentEmailSelectedValue)
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 1;
							$salesEmailsAcion['invoiceCommentEmailList'][] = $emailComArr;
						}
						else
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 0;
							$salesEmailsAcion['invoiceCommentEmailList'][] = $emailComArr;
						}
						
					}

					if(isset($post_data['invoiceCommentEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/identity', $post_data['invoiceCommentEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/identity', $post_data['invoiceCommentEmailVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Invoice Comment Email Sender updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




					//Invoice Comment Email Template
					$newOrderInvoiceCommentEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/invoice_comment/template', $storeId);

					$newOrderInvoiceCommentEmailTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderInvoiceCommentEmailTemCollection = $newOrderInvoiceCommentEmailTemObj->toOptionArray();
					if($newOrderInvoiceCommentEmailTemCollection[0]['value'] == "")
					{
						$newOrderInvoiceCommentEmailTemCollection[0]['value'] = "sales_email_invoice_comment_template";
					}
					//echo "<pre>"; print_r($newOrderInvoiceCommentEmailTemCollection); die;
					foreach ($newOrderInvoiceCommentEmailTemCollection as $value)
					{
						if($value['value'] == $newOrderInvoiceCommentEmailTemplateSelectedValue)
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderInvoiceCommentEmailTempColl'][] = $newOrderEmailTemArr;
						}
						else
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderInvoiceCommentEmailTempColl'][] = $newOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderInvoiceCommentEmailTemplateVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/template', $post_data['newOrderInvoiceCommentEmailTemplateVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/template', $post_data['newOrderInvoiceCommentEmailTemplateVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Invoice Comment Email Template updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Invoice Comment Email Template for Guest
					$forGuestInvoiceCommentsEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/invoice_comment/guest_template', $storeId);

					$newOrderInvoiceCommentEmailGuestTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderInvoiceCommentsEmailGuestTemCollection = $newOrderInvoiceCommentEmailGuestTemObj->toOptionArray();
					if($newOrderInvoiceCommentsEmailGuestTemCollection[0]['value'] == "")
					{
						$newOrderInvoiceCommentsEmailGuestTemCollection[0]['value'] = "sales_email_invoice_comment_guest_template";
					}

					//echo "<pre>"; print_r($emailTemCollection); die;
					foreach ($newOrderInvoiceCommentsEmailGuestTemCollection as $value)
					{
						if($value['value'] == $forGuestInvoiceCommentsEmailTemplateSelectedValue)
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderInvoiceCommentsForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
						else
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderInvoiceCommentsForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderInvoiceCommentsForGuestVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/guest_template', $post_data['newOrderInvoiceCommentsForGuestVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/guest_template', $post_data['newOrderInvoiceCommentsForGuestVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Invoice Comment Email Template for Guest updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Send Invoice Comment Email Copy To
					$salesEmailsAcion['emailInvoiceCommentsCopyToSelectedValue'] = Mage::getStoreConfig('sales_email/invoice_comment/copy_to', $storeId);
					if(isset($post_data['emailInvoiceCommentCopyToVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/copy_to', $post_data['emailInvoiceCommentCopyToVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/copy_to', $post_data['emailInvoiceCommentCopyToVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Invoice Comment Email Copy To Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Send Invoice Comments Email Copy Method
					$salesEmailsAcion['orderInvoiceCommentEmailCopySelectedValue'] = Mage::getStoreConfig('sales_email/invoice_comment/copy_method', $storeId);
					if(isset($post_data['orderInvoiceCommentEmailCopyVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/copy_method', $post_data['orderInvoiceCommentEmailCopyVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/invoice_comment/copy_method', $post_data['orderInvoiceCommentEmailCopyVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Invoice Comments Email Copy Method Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				// echo "<pre>"; print_r($salesEmailsAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($salesEmailsAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}


	public function salesEmailsOrderShipmentAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();


				//Shipment

					//Enabled
					$salesEmailsAcion['salesEmailShipmentEnableSelectedValue'] = Mage::getStoreConfig('sales_email/shipment/enabled', $storeId);
					if(isset($post_data['salesEmailShipmentEnableVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/enabled', $post_data['salesEmailShipmentEnableVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/enabled', $post_data['salesEmailShipmentEnableVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Sales Email Extension for Shipment updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Shipment Email Sender
					$shipmentEmailSelectedValue = Mage::getStoreConfig('sales_email/shipment/identity', $storeId);

					foreach ($this->emailSendersListArray() as $value)
					{
						if($value['value'] == $shipmentEmailSelectedValue)
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 1;
							$salesEmailsAcion['shipmentEmailList'][] = $emailComArr;
						}
						else
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 0;
							$salesEmailsAcion['shipmentEmailList'][] = $emailComArr;
						}
						
					}

					if(isset($post_data['shipmentEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/identity', $post_data['shipmentEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/identity', $post_data['shipmentEmailVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Shipment Email Sender updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




					//Shipment Email Template
					$newOrderShipmentEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/shipment/template', $storeId);

					$newOrderShipmentEmailTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderShipmentEmailTemCollection = $newOrderShipmentEmailTemObj->toOptionArray();
					if($newOrderShipmentEmailTemCollection[0]['value'] == "")
					{
						$newOrderShipmentEmailTemCollection[0]['value'] = "sales_email_shipment_template";
					}
					//echo "<pre>"; print_r($newOrderShipmentEmailTemCollection); die;
					foreach ($newOrderShipmentEmailTemCollection as $value)
					{
						if($value['value'] == $newOrderShipmentEmailTemplateSelectedValue)
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderShipmentEmailTempColl'][] = $newOrderEmailTemArr;
						}
						else
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderShipmentEmailTempColl'][] = $newOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderShipmentEmailTemplateVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/template', $post_data['newOrderShipmentEmailTemplateVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/template', $post_data['newOrderShipmentEmailTemplateVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Shipment Email Template updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Shipment Email Template for Guest
					$forGuestShipmentEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/shipment/guest_template', $storeId);

					$newOrderShipmentEmailGuestTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderShipmentEmailGuestTemCollection = $newOrderShipmentEmailGuestTemObj->toOptionArray();
					if($newOrderShipmentEmailGuestTemCollection[0]['value'] == "")
					{
						$newOrderShipmentEmailGuestTemCollection[0]['value'] = "sales_email_shipment_guest_template";
					}

					//echo "<pre>"; print_r($emailTemCollection); die;
					foreach ($newOrderShipmentEmailGuestTemCollection as $value)
					{
						if($value['value'] == $forGuestShipmentEmailTemplateSelectedValue)
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderShipmentForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
						else
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderShipmentForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderShipmentForGuestVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/guest_template', $post_data['newOrderShipmentForGuestVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/guest_template', $post_data['newOrderShipmentForGuestVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Shipment Email Template for Guest updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Send Shipment Email Copy To
					$salesEmailsAcion['emailShipmentCopyToSelectedValue'] = Mage::getStoreConfig('sales_email/shipment/copy_to', $storeId);
					if(isset($post_data['emailShipmentCopyToVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/copy_to', $post_data['emailShipmentCopyToVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/copy_to', $post_data['emailShipmentCopyToVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Shipment Email Copy To Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Send Shipment Email Copy Method
					$salesEmailsAcion['orderShipmentEmailCopySelectedValue'] = Mage::getStoreConfig('sales_email/shipment/copy_method', $storeId);
					if(isset($post_data['orderShipmentEmailCopyVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/copy_method', $post_data['orderShipmentEmailCopyVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment/copy_method', $post_data['orderShipmentEmailCopyVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Shipment Email Copy Method Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				// echo "<pre>"; print_r($salesEmailsAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($salesEmailsAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}


	public function salesEmailsOrderShipmentCommentsAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();


				//Shipment Comments

					//Enabled
					$salesEmailsAcion['salesEmailShipmentCommentsEnableSelectedValue'] = Mage::getStoreConfig('sales_email/shipment_comment/enabled', $storeId);
					if(isset($post_data['salesEmailShipmentCommentsEnableVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/enabled', $post_data['salesEmailShipmentCommentsEnableVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/enabled', $post_data['salesEmailShipmentCommentsEnableVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Sales Email Extension for Shipment Comments updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Shipment Comment Email Sender
					$shipmentCommentsEmailSelectedValue = Mage::getStoreConfig('sales_email/shipment_comment/identity', $storeId);

					foreach ($this->emailSendersListArray() as $value)
					{
						if($value['value'] == $shipmentCommentsEmailSelectedValue)
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 1;
							$salesEmailsAcion['shipmentCommentsEmailList'][] = $emailComArr;
						}
						else
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 0;
							$salesEmailsAcion['shipmentCommentsEmailList'][] = $emailComArr;
						}
						
					}

					if(isset($post_data['shipmentCommentsEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/identity', $post_data['shipmentCommentsEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/identity', $post_data['shipmentCommentsEmailVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Shipment Comment Email Sender updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




					//Shipment Comment Email Template
					$newOrderShipmentCommentsEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/shipment_comment/template', $storeId);

					$newOrderShipmentCommentsEmailTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderShipmentCommentsEmailTemCollection = $newOrderShipmentCommentsEmailTemObj->toOptionArray();
					if($newOrderShipmentCommentsEmailTemCollection[0]['value'] == "")
					{
						$newOrderShipmentCommentsEmailTemCollection[0]['value'] = "sales_email_shipment_comment_template";
					}
					//echo "<pre>"; print_r($newOrderShipmentCommentsEmailTemCollection); die;
					foreach ($newOrderShipmentCommentsEmailTemCollection as $value)
					{
						if($value['value'] == $newOrderShipmentCommentsEmailTemplateSelectedValue)
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderShipmentCommentsEmailTempColl'][] = $newOrderEmailTemArr;
						}
						else
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderShipmentCommentsEmailTempColl'][] = $newOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderShipmentCommentsEmailTemplateVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/template', $post_data['newOrderShipmentCommentsEmailTemplateVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/template', $post_data['newOrderShipmentCommentsEmailTemplateVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Shipment Comment Email Template updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Shipment Comment Email Template for Guest
					$forGuestShipmentCommentsEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/shipment_comment/guest_template', $storeId);

					$newOrderShipmentCommentsEmailGuestTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderShipmentCommentsEmailGuestTemCollection = $newOrderShipmentCommentsEmailGuestTemObj->toOptionArray();
					if($newOrderShipmentCommentsEmailGuestTemCollection[0]['value'] == "")
					{
						$newOrderShipmentCommentsEmailGuestTemCollection[0]['value'] = "sales_email_shipment_comment_guest_template";
					}

					//echo "<pre>"; print_r($newOrderShipmentCommentsEmailGuestTemCollection); die;
					foreach ($newOrderShipmentCommentsEmailGuestTemCollection as $value)
					{
						if($value['value'] == $forGuestShipmentCommentsEmailTemplateSelectedValue)
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderShipmentCommentsForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
						else
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderShipmentCommentsForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderShipmentCommentsForGuestVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/guest_template', $post_data['newOrderShipmentCommentsForGuestVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/guest_template', $post_data['newOrderShipmentCommentsForGuestVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Shipment Comment Email Template for Guest updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Send Shipment Comment Email Copy To
					$salesEmailsAcion['emailShipmentCommentsCopyToSelectedValue'] = Mage::getStoreConfig('sales_email/shipment_comment/copy_to', $storeId);
					if(isset($post_data['emailShipmentCommentsCopyToVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/copy_to', $post_data['emailShipmentCommentsCopyToVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/copy_to', $post_data['emailShipmentCommentsCopyToVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Shipment Comment Email Copy To Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Send Shipment Comments Email Copy Method
					$salesEmailsAcion['orderShipmentCommentsEmailCopySelectedValue'] = Mage::getStoreConfig('sales_email/shipment_comment/copy_method', $storeId);
					if(isset($post_data['orderShipmentCommentsEmailCopyVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/copy_method', $post_data['orderShipmentCommentsEmailCopyVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/shipment_comment/copy_method', $post_data['orderShipmentCommentsEmailCopyVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Shipment Email Copy Method Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				// echo "<pre>"; print_r($salesEmailsAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($salesEmailsAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}


	public function salesEmailsOrderCreditMemoAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();


				//Credit Memo

					//Enabled
					$salesEmailsAcion['salesEmailCreditMemoEnableSelectedValue'] = Mage::getStoreConfig('sales_email/creditmemo/enabled', $storeId);
					if(isset($post_data['salesEmailCreditMemoEnableVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/enabled', $post_data['salesEmailCreditMemoEnableVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/enabled', $post_data['salesEmailCreditMemoEnableVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Sales Email Extension for Credit Memo updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Credit Memo Email Sender
					$creditMemoEmailSelectedValue = Mage::getStoreConfig('sales_email/creditmemo/identity', $storeId);

					foreach ($this->emailSendersListArray() as $value)
					{
						if($value['value'] == $creditMemoEmailSelectedValue)
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 1;
							$salesEmailsAcion['creditMemoEmailList'][] = $emailComArr;
						}
						else
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 0;
							$salesEmailsAcion['creditMemoEmailList'][] = $emailComArr;
						}
						
					}

					if(isset($post_data['creditMemoEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/identity', $post_data['creditMemoEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/identity', $post_data['creditMemoEmailVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Credit Memo Email Sender updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




					//Credit Memo Email Template
					$newOrderCreditMemoEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/creditmemo/template', $storeId);

					$newOrderCreditMemoEmailTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderCreditMemoEmailTemCollection = $newOrderCreditMemoEmailTemObj->toOptionArray();
					if($newOrderCreditMemoEmailTemCollection[0]['value'] == "")
					{
						$newOrderCreditMemoEmailTemCollection[0]['value'] = "sales_email_creditmemo_template";
					}
					//echo "<pre>"; print_r($newOrderCreditMemoEmailTemCollection); die;
					foreach ($newOrderCreditMemoEmailTemCollection as $value)
					{
						if($value['value'] == $newOrderCreditMemoEmailTemplateSelectedValue)
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderCreditMemoEmailTempColl'][] = $newOrderEmailTemArr;
						}
						else
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderCreditMemoEmailTempColl'][] = $newOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderCreditMemoEmailTemplateVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/template', $post_data['newOrderCreditMemoEmailTemplateVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/template', $post_data['newOrderCreditMemoEmailTemplateVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Credit Memo Email Template updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Credit Memo Email Template for Guest
					$forGuestCreditMemoEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/creditmemo/guest_template', $storeId);

					$newOrderCreditMemoEmailGuestTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderCreditMemoEmailGuestTemCollection = $newOrderCreditMemoEmailGuestTemObj->toOptionArray();
					if($newOrderCreditMemoEmailGuestTemCollection[0]['value'] == "")
					{
						$newOrderCreditMemoEmailGuestTemCollection[0]['value'] = "sales_email_creditmemo_guest_template";
					}

					//echo "<pre>"; print_r($newOrderShipmentCommentsEmailGuestTemCollection); die;
					foreach ($newOrderCreditMemoEmailGuestTemCollection as $value)
					{
						if($value['value'] == $forGuestCreditMemoEmailTemplateSelectedValue)
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderCreditMemoForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
						else
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderCreditMemoForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderCreditMemoForGuestVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/guest_template', $post_data['newOrderCreditMemoForGuestVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/guest_template', $post_data['newOrderCreditMemoForGuestVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Credit Memo Email Template for Guest updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Send Credit Memo Email Copy To
					$salesEmailsAcion['emailCreditMemoCopyToSelectedValue'] = Mage::getStoreConfig('sales_email/creditmemo/copy_to', $storeId);
					if(isset($post_data['emailCreditMemoCopyToVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/copy_to', $post_data['emailCreditMemoCopyToVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/copy_to', $post_data['emailCreditMemoCopyToVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Credit Memo Email Copy To Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Send Credit Memo Email Copy Method
					$salesEmailsAcion['orderCreditMemoEmailCopySelectedValue'] = Mage::getStoreConfig('sales_email/creditmemo/copy_method', $storeId);
					if(isset($post_data['orderCreditMemoEmailCopyVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/copy_method', $post_data['orderCreditMemoEmailCopyVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo/copy_method', $post_data['orderCreditMemoEmailCopyVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Credit Memo Email Copy Method Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				// echo "<pre>"; print_r($salesEmailsAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($salesEmailsAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}


	public function salesEmailsOrderCreditMemoCommentsAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        $sessionId = $post_data['session'];
	        if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        {
	            echo $this->__("The Login has expired. Please try log in again.");
	            return false;
	        }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();


				//Credit Memo Comments

					//Enabled
					$salesEmailsAcion['salesEmailCreditMemoCommentsEnableSelectedValue'] = Mage::getStoreConfig('sales_email/creditmemo_comment/enabled', $storeId);
					if(isset($post_data['salesEmailCreditMemoCommentsEnableVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/enabled', $post_data['salesEmailCreditMemoCommentsEnableVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/enabled', $post_data['salesEmailCreditMemoCommentsEnableVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Sales Email Extension for Credit Memo Comments updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Credit Memo Comment Email Sender
					$creditMemoCommentEmailSelectedValue = Mage::getStoreConfig('sales_email/creditmemo_comment/identity', $storeId);

					foreach ($this->emailSendersListArray() as $value)
					{
						if($value['value'] == $creditMemoCommentEmailSelectedValue)
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 1;
							$salesEmailsAcion['creditMemoCommentEmailList'][] = $emailComArr;
						}
						else
						{
							$emailComArr['label'] = $value['label'];
							$emailComArr['value'] = $value['value'];
							$emailComArr['status'] = 0;
							$salesEmailsAcion['creditMemoCommentEmailList'][] = $emailComArr;
						}
						
					}

					if(isset($post_data['creditMemoCommentEmailVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/identity', $post_data['creditMemoCommentEmailVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/identity', $post_data['creditMemoCommentEmailVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Credit Memo Comment Email Sender updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}




					//Credit Memo Comment Email Template
					$newOrderCreditMemoCommentEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/creditmemo_comment/template', $storeId);

					$newOrderCreditMemoCommentEmailTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderCreditMemoCommentEmailTemCollection = $newOrderCreditMemoCommentEmailTemObj->toOptionArray();
					if($newOrderCreditMemoCommentEmailTemCollection[0]['value'] == "")
					{
						$newOrderCreditMemoCommentEmailTemCollection[0]['value'] = "sales_email_creditmemo_comment_template";
					}
					//echo "<pre>"; print_r($newOrderCreditMemoCommentEmailTemCollection); die;
					foreach ($newOrderCreditMemoCommentEmailTemCollection as $value)
					{
						if($value['value'] == $newOrderCreditMemoCommentEmailTemplateSelectedValue)
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderCreditMemoCommentsEmailTempColl'][] = $newOrderEmailTemArr;
						}
						else
						{
							$newOrderEmailTemArr['label'] = $value['label'];
							$newOrderEmailTemArr['value'] = $value['value'];
							$newOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderCreditMemoCommentsEmailTempColl'][] = $newOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderCreditMemoCommentsEmailTemplateVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/template', $post_data['newOrderCreditMemoCommentsEmailTemplateVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/template', $post_data['newOrderCreditMemoCommentsEmailTemplateVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Credit Memo Comment Email Template updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Credit Memo Comment Email Template for Guest
					$forGuestCreditMemoCommentsEmailTemplateSelectedValue = Mage::getStoreConfig('sales_email/creditmemo_comment/guest_template', $storeId);

					$newOrderCreditMemoCommentsEmailGuestTemObj = new Mage_Adminhtml_Model_System_Config_Source_Email_Template();
					$newOrderCreditMemoCommentsEmailGuestTemCollection = $newOrderCreditMemoCommentsEmailGuestTemObj->toOptionArray();
					if($newOrderCreditMemoCommentsEmailGuestTemCollection[0]['value'] == "")
					{
						$newOrderCreditMemoCommentsEmailGuestTemCollection[0]['value'] = "sales_email_creditmemo_comment_guest_template";
					}

					//echo "<pre>"; print_r($newOrderCreditMemoCommentsEmailGuestTemCollection); die;
					foreach ($newOrderCreditMemoCommentsEmailGuestTemCollection as $value)
					{
						if($value['value'] == $forGuestCreditMemoCommentsEmailTemplateSelectedValue)
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 1;
							$salesEmailsAcion['newOrderCreditMemoCommentsForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
						else
						{
							$forGuestOrderEmailTemArr['label'] = $value['label'];
							$forGuestOrderEmailTemArr['value'] = $value['value'];
							$forGuestOrderEmailTemArr['status'] = 0;
							$salesEmailsAcion['newOrderCreditMemoCommentsForGuestColl'][] = $forGuestOrderEmailTemArr;
						}
					}
					//echo $post_data['emailTemplateVal']."***";
					if(isset($post_data['newOrderCreditMemoCommentsForGuestVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/guest_template', $post_data['newOrderCreditMemoCommentsForGuestVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/guest_template', $post_data['newOrderCreditMemoCommentsForGuestVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Credit Memo Comment Email Template for Guest updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}



					//Send Credit Memo Comment Email Copy To
					$salesEmailsAcion['emailCreditMemoCommentsCopyToSelectedValue'] = Mage::getStoreConfig('sales_email/creditmemo_comment/copy_to', $storeId);
					if(isset($post_data['emailCreditMemoCommentsCopyToVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/copy_to', $post_data['emailCreditMemoCommentsCopyToVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/copy_to', $post_data['emailCreditMemoCommentsCopyToVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Credit Memo Comment Email Copy To Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


					//Send Credit Memo Comments Email Copy Method
					$salesEmailsAcion['orderCreditMemoCommentsEmailCopySelectedValue'] = Mage::getStoreConfig('sales_email/creditmemo_comment/copy_method', $storeId);
					if(isset($post_data['orderCreditMemoCommentsEmailCopyVal']))
					{
						if($storeId == 0)
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/copy_method', $post_data['orderCreditMemoCommentsEmailCopyVal'], 'default', $storeId);
						}
						else
						{
							Mage::getConfig()->saveConfig('sales_email/creditmemo_comment/copy_method', $post_data['orderCreditMemoCommentsEmailCopyVal'], 'stores', $storeId);
						}
						
						$salesEmailsAcion['successMessage'] = "Send Credit Memo Comments Email Copy Method Value updated On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}

				// echo "<pre>"; print_r($salesEmailsAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($salesEmailsAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}

	/***** End Sales Email section ******/



	/***** Start Sales TAX section ******/

	// System > Configuration > SALES > Tax > Calculation Settings > Tax Calculation Method Based On
	/*public function taxCalculationMethodBasedOn()
    {
        $list = array(
            array(
                'value' => Mage_Tax_Model_Calculation::CALC_UNIT_BASE,
                'label' => Mage::helper('tax')->__('Unit Price')
            ),
            array(
                'value' => Mage_Tax_Model_Calculation::CALC_ROW_BASE,
                'label' => Mage::helper('tax')->__('Row Total')
            ),
            array(
                'value' => Mage_Tax_Model_Calculation::CALC_TOTAL_BASE,
                'label' => Mage::helper('tax')->__('Total')
            ),
        );
        return $list;
    }*/

	public function salesTaxAction()
	{
		if(Mage::helper('mobileadmin')->isEnable()) // check extension if enabled or not
      	{
	        $post_data = Mage::app()->getRequest()->getParams();
	        // $sessionId = $post_data['session'];
	        // if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check session if not, will return false
	        // {
	        //     echo $this->__("The Login has expired. Please try log in again.");
	        //     return false;
	        // }

	        try
	        {
	        	$storeId = $post_data['store'];
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();

				//Tax Classes

					//Tax Class for Shipping
					$taxArr = Mage::getModel('tax/class_source_product')->toOptionArray();
					$taxClassForShippingSelectedValue = Mage::getStoreConfig('tax/classes/shipping_tax_class');
					foreach ($taxArr as $value)
					{
						if($value['value'] == $taxClassForShippingSelectedValue)
						{
							$taxNewArr['value']   =   $value['value'];
							$taxNewArr['label']   =   $value['label'];
							$taxNewArr['status']  =   1;
							$saleTaxAcion['taxClassForShippingList'][] = $taxNewArr;
						}
						else
						{
							$taxNewArr['value']   =   $value['value'];
							$taxNewArr['label']   =   $value['label'];
							$taxNewArr['status']  =   0;
							$saleTaxAcion['taxClassForShippingList'][] = $taxNewArr;
						}
					}

					if(isset($post_data['taxClassForShippingVal']))
					{
						Mage::getConfig()->saveConfig('tax/classes/shipping_tax_class', $post_data['taxClassForShippingVal'], 'default', 0);

						$saleTaxAcion['successMessage'] = "Tax Class for Shipping Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}
					


				//Calculation Settings

					//Tax Calculation Method Based On
					$taxCalculationMethodSelectedValue = Mage::getStoreConfig('tax/calculation/algorithm');
					$taxCalculationMethod = new Mage_Tax_Model_System_Config_Source_Algorithm();
					foreach ($taxCalculationMethod->toOptionArray() as $value)
					{
						if($value['value'] == $taxCalculationMethodSelectedValue)
						{
							$taxCalculationArr['value'] = $value['value'];
							$taxCalculationArr['label'] = $value['label'];
							$taxCalculationArr['status'] = 1;
							$saleTaxAcion['status'] = 1;
						}
						else
						{
							$taxCalculationArr['value'] = $value['value'];
							$taxCalculationArr['label'] = $value['label'];
							$taxCalculationArr['status'] = 0;
						}
					}

					if(isset($post_data['taxCalculationMethodVal']))
					{
						Mage::getConfig()->saveConfig('tax/calculation/algorithm', $post_data['taxCalculationMethodVal'], 'default', 0);

						$saleTaxAcion['successMessage'] = "Tax Calculation Method Based On Saved On ".$storeName." Store.";
						Mage::getConfig()->reinit();
						Mage::app()->reinitStores();
					}


				echo "<pre>"; print_r($saleTaxAcion); die;
				$jsonData = Mage::helper('core')->jsonEncode($saleTaxAcion);
		      	return Mage::app()->getResponse()->setBody($jsonData); 
			}
	        catch(Exception $e)
	        {
	        	$errorResult['error'] = $e->getMessage();

	            $jsonData = Mage::helper('core')->jsonEncode($errorResult);
		      	return Mage::app()->getResponse()->setBody($jsonData);
	        }	
		}
        else
		{
			$isEnable    = Mage::helper('core')->jsonEncode(array('enable' => false));
			return Mage::app()->getResponse()->setBody($isEnable); // set body with json format
		}
	}

	/***** End Sales TAX section ******/

}
?>