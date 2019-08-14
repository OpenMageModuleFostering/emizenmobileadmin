<?php
class EmizenTech_MobileAdmin_Model_Mysql4_Emizenmob extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("mobileadmin/emizenmob", "user_id");
    }
}