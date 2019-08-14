<?php
class EmizenTech_MobileAdmin_ReportController extends Mage_Core_Controller_Front_Action{

    /*
    *@ get admin report sales order using start and end date and more specific variables
    *Parameter: startDate, endDate, sessionId, period(ex. day,month), status(ex. canceled,completed etc.)
    */
	public function AdminReportOrderAction()
	{
        if(Mage::helper('mobileadmin')->isEnable()) // check if extension is enabled or not ?
        {
            $post_data = Mage::app()->getRequest()->getParams();
            $sessionId = $post_data['session'];
            if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check if customer is not logged in then return Access denied
            {
                echo $this->__("The Login has expired. Please try log in again.");
                return false; // return logged out
            }
    		
    		$start_date = $post_data['date_start'];
    		$end_date = $post_data['date_end'];
    		$storeId = $post_data['store_id'];
    		$dateStart   = date('Y-m-d', strtotime($start_date));
            $dateEnd     = date('Y-m-d',strtotime($end_date)); 
            $period = $post_data['period'];

            $store = $post_data['store_id'];

            $orderStatuses = array($post_data['status']);

            $dat = array(
                        'orders_count'          => 'sum(orders_count)',
                        'total_qty_ordered'     => 'sum(total_qty_ordered)',
                        'total_income_amount'   => 'sum(total_income_amount)', // Queries in mysql
                        'total_invoiced_amount' => 'sum(total_invoiced_amount)',
                        'total_refunded_amount' => 'sum(total_refunded_amount)',
                        'total_tax_amount'      => 'sum(total_tax_amount)',
                        'total_shipping_amount' => 'sum(total_shipping_amount)',
                        'total_discount_amount' => 'sum(total_discount_amount)',
                        'total_canceled_amount' => 'sum(total_canceled_amount)'
                   );

            if(is_array($orderStatuses))
            {
                if(count($orderStatuses) == 1 && strpos($orderStatuses[0],',')!== false)
                {
                    $post_data['status'] = explode(',',$orderStatuses[0]);
                }
            }

            $Rcolection = Mage::getResourceModel('sales/report_order_collection') // report sales order model
                            ->setPeriod($period)
                            ->setDateRange($dateStart,$dateEnd)
                            ->addStoreFilter($store)         //filtering report order sing these parameters
                            ->addOrderStatusFilter($post_data['status'])
                            ->setAggregatedColumns($dat);
            
            $result = array();
            foreach($Rcolection as $_data)
            {
                $result['report_sales_order'][] = $_data->getData();
            }

            $totalOrder = 0;
            $totalOrderQty = 0;
            $totalOrderQtyInvoice = 0;
            $totalIncomeAmount = 0;
            $totalRevenueAmount = 0;
            $totalProfitAmount = 0;
            $totalInvoicedAmount = 0;
            $totalCanceledAmount = 0;
            $totalPaidAmount = 0;
            $totalRefundedAmount = 0;
            $totalTaxAmount = 0;
            $totalTaxAmountActual = 0;
            $totalShippingAmount = 0;
            $totalShippingAmountActual = 0;
            $totalDiscountAmount = 0;
            $totalDiscountAmountActual = 0;

            foreach ($result['report_sales_order'] as $key => $value) // caculated price
            {
                $totalOrder = $totalOrder + $value['orders_count'];
                $totalOrderQty = $totalOrderQty + $value['total_qty_ordered'];
                $totalOrderQtyInvoice = $totalOrderQtyInvoice + $value['total_qty_invoiced'];
                $totalIncomeAmount = $totalIncomeAmount + $value['total_income_amount'];
                $totalRevenueAmount = $totalRevenueAmount + $value['total_revenue_amount'];
                $totalProfitAmount = $totalProfitAmount + $value['total_profit_amount'];
                $totalInvoicedAmount = $totalInvoicedAmount + $value['total_invoiced_amount'];
                $totalCanceledAmount = $totalCanceledAmount + $value['total_canceled_amount'];
                $totalPaidAmount = $totalPaidAmount + $value['total_paid_amount'];
                $totalRefundedAmount = $totalRefundedAmount + $value['total_refunded_amount'];
                $totalTaxAmount = $totalTaxAmount + $value['total_tax_amount'];
                $totalTaxAmountActual = $totalTaxAmountActual + $value['total_tax_amount_actual'];
                $totalShippingAmount = $totalShippingAmount + $value['total_shipping_amount'];
                $totalShippingAmountActual = $totalShippingAmountActual + $value['total_shipping_amount_actual'];
                $totalDiscountAmount = $totalDiscountAmount + $value['total_discount_amount'];
                $totalDiscountAmountActual = $totalDiscountAmountActual + $value['total_discount_amount_actual'];
            }

            $result['Total']['totalOrder'] = $totalOrder;
            $result['Total']['totalOrderQty'] = $totalOrderQty;
            $result['Total']['totalOrderQtyInvoice'] = $totalOrderQtyInvoice;
            $result['Total']['totalIncomeAmount'] = $totalIncomeAmount;
            $result['Total']['totalRevenueAmount'] = $totalRevenueAmount;
            $result['Total']['totalProfitAmount'] = $totalProfitAmount;
            $result['Total']['totalInvoicedAmount'] = $totalInvoicedAmount;
            $result['Total']['totalCanceledAmount'] = $totalCanceledAmount;
            $result['Total']['totalPaidAmount'] = $totalPaidAmount;
            $result['Total']['totalRefundedAmount'] = $totalRefundedAmount;
            $result['Total']['totalTaxAmount'] = $totalTaxAmount;
            $result['Total']['totalTaxAmountActual'] = $totalTaxAmountActual;
            $result['Total']['totalShippingAmount'] = $totalShippingAmount;
            $result['Total']['totalShippingAmountActual'] = $totalShippingAmountActual;
            $result['Total']['totalDiscountAmount'] = $totalDiscountAmount;
            $result['Total']['totalDiscountAmountActual'] = $totalDiscountAmountActual;     
            //echo json_encode($result);
            //echo "<pre>"; print_r($result);
        }
        else
        {
            $result['error'] = $this->__('Please activate the Mobile Emizentech Extension on the Magento Store.');
        }
        $isEnable = Mage::helper('core')->jsonEncode($result);
        return Mage::app()->getResponse()->setBody($isEnable);
	}

    /*
    *@ get admin report sales invoice using start and end date and more specific variables
    *Parameter: startDate, endDate, sessionId, period(ex. day,month), status(ex. canceled,completed etc.)
    */
    public function AdminReportInvoiceAction()
    {
        if(Mage::helper('mobileadmin')->isEnable()) // check if extension is enabled or not ?
        {
            $post_data = Mage::app()->getRequest()->getParams();
            $sessionId = $post_data['session'];
            if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check if customer is not logged in then return Access denied
            {
                echo $this->__("The Login has expired. Please try log in again.");
                return false; // return logged out
            }
            
            $start_date = $post_data['date_start'];
            $end_date = $post_data['date_end'];
            $storeId = $post_data['store_id'];
            $dateStart   = date('Y-m-d', strtotime($start_date));
            $dateEnd     = date('Y-m-d',strtotime($end_date)); 
            $period = $post_data['period'];

            $store = $post_data['store_id'];

            $orderStatuses = array($post_data['status']);


            $dat = array( 
                        'orders_count'          => 'sum(orders_count)',   // sql queries for calculated price
                        'orders_invoiced'     => 'sum(orders_invoiced)',
                        'total_income_amount'   => 'sum(total_income_amount)',
                        'invoiced' => 'sum(invoiced)',
                        'invoiced_captured' => 'sum(invoiced_captured)',
                        'invoiced_not_captured'      => 'sum(invoiced_not_captured)'
                   );

            if(is_array($orderStatuses))
            {
                if(count($orderStatuses) == 1 && strpos($orderStatuses[0],',')!== false)
                {
                    $post_data['status'] = explode(',',$orderStatuses[0]);
                }
            }
            //echo "<pre>"; print_r($post_data['status']);
            $Rcolection = Mage::getResourceModel('sales/report_invoiced_collection_order') // report sales invoice model
                            ->setPeriod($period)
                            ->setDateRange($dateStart,$dateEnd)
                            ->addStoreFilter($store)
                            ->addOrderStatusFilter($post_data['status'])
                            ->setAggregatedColumns($dat);
            
            $result = array();
            foreach($Rcolection as $_data)
            {
                $result['report_invoice_order'][] = $_data->getData();
            }
            
            $orders_count = 0;
            $orders_invoiced = 0;
            $total_income_amount = 0;
            $invoiced = 0;
            $invoiced_captured = 0;
            $invoiced_not_captured = 0;

            foreach ($result['report_invoice_order'] as $key => $value)
            {
                $orders_count = $orders_count + $value['orders_count'];
                $orders_invoiced = $orders_invoiced + $value['orders_invoiced'];
                $total_income_amount = $total_income_amount + $value['total_income_amount'];
                $invoiced = $invoiced + $value['invoiced'];
                $invoiced_captured = $invoiced_captured + $value['invoiced_captured'];
                $invoiced_not_captured = $invoiced_not_captured + $value['invoiced_not_captured'];
            }

            $result['Total']['orders_count'] = $orders_count;
            $result['Total']['orders_invoiced'] = $orders_invoiced;
            $result['Total']['total_income_amount'] = $total_income_amount;
            $result['Total']['invoiced'] = $invoiced;
            $result['Total']['invoiced_captured'] = $invoiced_captured;
            $result['Total']['invoiced_not_captured'] = $invoiced_not_captured;
            
            //echo json_encode($result);
            //echo "<pre>"; print_r($result);
        }
        else
        {
            $result['error'] = $this->__('Please activate the Mobile Emizentech Extension on the Magento Store.');
        }
        $isEnable = Mage::helper('core')->jsonEncode($result);
        return Mage::app()->getResponse()->setBody($isEnable);
    }

    /*
    *@ get admin report sales shipping using start and end date and more specific variables
    *Parameter: startDate, endDate, sessionId, period(ex. day,month), status(ex. canceled,completed etc.)
    */
    public function AdminReportShippingAction()
    {
        if(Mage::helper('mobileadmin')->isEnable()) // check if extension is enabled or not ?
        {
            $post_data = Mage::app()->getRequest()->getParams();
            $sessionId = $post_data['session'];
            if (!Mage::getSingleton('api/session')->isLoggedIn($sessionId)) // check if customer is not logged in then return Access denied
            {
                echo $this->__("The Login has expired. Please try log in again.");
                return false; // return logged out
            }
            
            $start_date = $post_data['date_start'];
            $end_date = $post_data['date_end'];
            $storeId = $post_data['store_id'];
            $dateStart   = date('Y-m-d', strtotime($start_date));
            $dateEnd     = date('Y-m-d',strtotime($end_date)); 
            $period = $post_data['period'];

            $store = $post_data['store_id'];

            $orderStatuses = array($post_data['status']);


            $dat = array(
                        'orders_count'          => 'sum(orders_count)', // calculating order and price using database queries
                        'total_shipping'     => 'sum(total_shipping)',
                        'total_shipping_actual'   => 'sum(total_shipping_actual)'
                   );

            if(is_array($orderStatuses))
            {
                if(count($orderStatuses) == 1 && strpos($orderStatuses[0],',')!== false)
                {
                    $post_data['status'] = explode(',',$orderStatuses[0]);
                }
            }
            //echo "<pre>"; print_r($post_data['status']);
            $Rcolection = Mage::getResourceModel('sales/report_shipping_collection_order') // report sales shipping model
                            ->setPeriod($period)
                            ->setDateRange($dateStart,$dateEnd)
                            ->addStoreFilter($store)
                            ->addOrderStatusFilter($post_data['status'])
                            ->setAggregatedColumns($dat);
            
            $result = array();

            $array_sum_array = array();

            $orders_count = 0;
            $total_shipping = 0;
            $total_shipping_actual = 0;

            foreach($Rcolection as $_key => $_data)
            {
                //$result['report_invoice_order'][] = $_data->getData();
                //echo "<pre>"; print_r($_data->getData());
                $result[$_data->getData('period')][] = $_data->getData();

                
                $array_sum_array[$_data->getData('period')]['orders_count'][] = $_data->getData('orders_count');
                $array_sum_array[$_data->getData('period')]['total_shipping'][] = $_data->getData('total_shipping');
                $array_sum_array[$_data->getData('period')]['total_shipping_actual'][] = $_data->getData('total_shipping_actual');

                // $orders_count += $_data->getData('orders_count');
                // $total_shipping = $total_shipping + $_data->getData('total_shipping');
                // $total_shipping_actual = $total_shipping_actual + $_data->getData('total_shipping_actual');

                $result[$_data->getData('period')]['Total']['orders_count'] = array_sum($array_sum_array[$_data->getData('period')]['orders_count']);
                $result[$_data->getData('period')]['Total']['total_shipping'] = array_sum($array_sum_array[$_data->getData('period')]['total_shipping']);
                $result[$_data->getData('period')]['Total']['total_shipping_actual'] = array_sum($array_sum_array[$_data->getData('period')]['total_shipping_actual']);

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
}
?>
