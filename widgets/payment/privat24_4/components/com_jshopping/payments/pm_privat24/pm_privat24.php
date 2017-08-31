<?php
defined('_JEXEC') or die('Restricted access');

class pm_privat24 extends PaymentRoot{

    // Get language
    function loadLanguageFile($langtag = ""){
        $lang = JFactory::getLanguage();

        if ($langtag==""){
            $langtag = $lang->getTag();
        }                              
        if(file_exists(dirname(__FILE__)."/lang/".$langtag.".php")) {
            require_once (dirname(__FILE__)."/lang/".$langtag.".php");
	} else {
            require_once (dirname(__FILE__)."/lang/en-GB.php");
	}
	
    }
    
    function showPaymentForm($params, $pmconfigs){
        include(dirname(__FILE__)."/paymentform.php");
    }

    //function call in admin
    function showAdminFormParams($params){
	  $array_params = array('testmode', 'merchant_id', 'merchant_pass', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status');
	  foreach ($array_params as $key){
	  	if (!isset($params[$key])) $params[$key] = '';
	  } 
	  $orders = JModelLegacy::getInstance('orders', 'JshoppingModel'); //admin model
	  pm_privat24::loadLanguageFile();
      include(dirname(__FILE__)."/adminparamsform.php");
	}

    // Check Transaction
    function checkTransaction($pmconfigs, $order, $act){

	$jshopConfig = &JSFactory::getConfig();

	if ($pmconfigs['testmode']){
            $mystate = 'test';
        } else{
            $mystate = 'ok';
        }

	$payment = $_POST['payment'];
	$signature = $_POST['signature'];
	$merchant_pass = $pmconfigs['merchant_pass'];
	$merchant_id = $pmconfigs['merchant_id'];
	$order->order_total = $this->fixOrderTotal($order);

	$payments = explode ( "&", $payment);
	$aorder = explode ("=", $payments[5]);
	$acurrency = explode ("=", $payments[1]);
	$atotal = explode ("=", $payments[0]);
	$amerchant = explode ("=", $payments[6]);
	$astate = explode ("=", $payments[7]);

	$currency_code_iso = $acurrency[1] == 'RUR' ? 'RUB' : $acurrency[1];
	
	// Check order ID
	if ($order->order_id != $aorder[1]){
            return array(0, 'Error order_id. Order ID '.$order->order_id);
        } 

	// Check total amount
        if ($order->order_total != $atotal[1]){
            return array(0, 'Error amount. Order ID '.$order->order_id);
        }

	// Check currency
        if (($order->currency_code_iso == 'RUR' ? 'RUB' : $order->currency_code_iso) != $currency_code_iso){
            return array(0, 'Error currency. Order ID '.$order->order_id);            
        }
	
	// Check merchant
        if ($merchant_id != $amerchant[1]){
            return array(0, 'Error merchant ID. Order ID '.$order->order_id);
        }

	// Check signature
	$sign = sha1(md5($payment.$merchant_pass));
        if ($signature!=$sign){
            return array(0, 'Error signature. Your signature is '.$sign.' but you receive '.$signature);
        }
        
	// Check payment state
	if ($astate[1] == $mystate) {
	    return array(1, '');
	} elseif ($astate[1] == 'fail') {
            return array(3, 'Status Failed. Order ID '.$order->order_id);
        } else {
            return array(0, "Order number ".$order->order_id."\nPrivat24 error\nPrivat24 state - ".$astate[1]);
        }

	}

    function showEndForm($pmconfigs, $order){
        
        $jshopConfig = &JSFactory::getConfig();        
	$item_name = sprintf(_JSHOP_PAYMENT_NUMBER, $order->order_number);
                $email = $pmconfigs['email_received'];
        
	$notify_url = JURI::root() . "index.php?option=com_jshopping&amp;controller=checkout&amp;task=step7&amp;act=notify&amp;js_paymentclass=pm_privat24&amp;no_lang=1";
        $return_url = JURI::root(). "index.php?option=com_jshopping&amp;controller=checkout&amp;task=step7&amp;act=return&amp;js_paymentclass=pm_privat24";
        $cancel_return = JURI::root() . "index.php?option=com_jshopping&amp;controller=checkout&amp;task=step7&amp;act=cancel&amp;js_paymentclass=pm_privat24";
        $server_url = '';
	$order->order_total = $this->fixOrderTotal($order);

        ?>
	<html>
	<head>
	    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	</head>
	<body>
          <form id="paymentform" action="https://api.privatbank.ua/p24api/ishop" name="paymentform" method="post">
	    <input type="text" name="amt" value="<?php print $order->order_total?>"/>
	    <input type="text" name="ccy" value="<?php print $order->currency_code_iso?>" />
	    <input type="hidden" name="merchant" value="<?php print $pmconfigs['merchant_id']?>" />
	    <input type="hidden" name="order" value="<?php print $order->order_id?>" />
	    <input type="hidden" name="details" value="Счёт <?php print $order->order_number?> от <?php print $order->order_date?>" />
	    <input type="hidden" name="ext_details" value="<?php print $item_name?>" />
	    <input type="hidden" name="pay_way" value="privat24" />
	    <input type="hidden" name="return_url" value="<?php print $return_url?>" />
	    <input type="hidden" name="server_url" value="<?php print $server_url?>" />
          </form>
        <?php print _JSHOP_REDIRECT_TO_PAYMENT_PAGE ?>
          <br />
          <script type="text/javascript">document.getElementById('paymentform').submit();</script>
	</body>
	</html>
        <?php
        die();
	}
    
    function getUrlParams($pmconfigs){
        $params = array();
        $payment = JRequest::getString("payment");
	$payments = explode ( "&", $payment);
	$aorder = explode ("=", $payments[5]);
	$params['order_id'] = $aorder[1];
        $params['hash'] = "";
        $params['checkHash'] = 0;
	$params['checkReturnParams'] = $pmconfigs['checkdatareturn'];
    return $params;
    }

    // Fix total amout
    function fixOrderTotal($order){
        $total = $order->order_total;
        $total = number_format($total, 2, '.', '');
    return $total;
    }

}

?>