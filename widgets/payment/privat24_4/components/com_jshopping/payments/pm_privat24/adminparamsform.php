<div class="col100">
<fieldset class="adminform">
<table class="admintable" width = "100%" >
 <tr>
   <td style="width:250px;" class="key">
     <?php echo _JSHOP_TESTMODE;?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.booleanlist', 'pm_params[testmode]', 'class = "inputbox" size = "1"', $params['testmode']);
     ?>
   </td>
 </tr>
 <tr>
   <td  class="key">
     <?php echo _JSHOP_PRIVAT24_MERCHANT_ID;?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[merchant_id]" size="30" value = "<?php echo $params['merchant_id']?>" />
   </td>
 </tr>
 <tr>
   <td  class="key">
     <?php echo _JSHOP_PRIVAT24_MERCHANT_PASS;?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[merchant_pass]" size="60" value = "<?php echo $params['merchant_pass']?>" />
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_END;?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_end_status'] );
     echo " ".JHTML::tooltip(_JSHOP_PRIVAT24_TRANSACTION_END_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_PENDING;?>
   </td>
   <td>
     <?php 
     echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_pending_status']);
     echo " ".JHTML::tooltip(_JSHOP_PRIVAT24_TRANSACTION_PENDING_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_FAILED;?>
   </td>
   <td>
     <?php 
     echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_failed_status']);
     echo " ".JHTML::tooltip(_JSHOP_PRIVAT24_TRANSACTION_FAILED_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_PRIVAT24_CHECK_DATA_RETURN;?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.booleanlist', 'pm_params[checkdatareturn]', 'class = "inputbox" size = "1"', $params['checkdatareturn']);     
     ?>
   </td>
 </tr>
</table>
</fieldset>
</div>
<div class="clr"></div>