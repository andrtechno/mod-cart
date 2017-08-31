
$(document).ready(function(){
    $('#paymentmethod-payment_system').on('change',function(){
        $('#payment_configuration').load('/admin/cart/payment/render-configuration-form?system='+$(this).val()+'&payment_method_id='+$(this).attr('rel'));
    });
    $('#paymentmethod-payment_system').change();
});