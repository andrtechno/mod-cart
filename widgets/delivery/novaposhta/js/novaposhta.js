
$(document).on('change', '#novaposhtamodel-city, #novaposhtamodel-type, #novaposhtamodel-area', function(e) {
    var delivery_id = $('input[name="Order[delivery_id]"]:checked').val();
//console.log($('input[name="Order[delivery_id]"]:checked').val());
    $.ajax({
        //url: common.url('cart/delivery/process?id='+delivery_id),
        url: 'cart/delivery/process?id='+delivery_id,
        type: 'POST',
        data: $('#cartForm').serialize(),
        dataType: 'html',
        beforeSend: function(){
            $('#order-delivery_id').addClass('loading');
        },
        complete: function(){
            $('#order-delivery_id').removeClass('loading');
        },
        error: function(){
            $('#order-delivery_id').removeClass('loading');
        },
        success: function (data) {
            $('.delivery-form-'+delivery_id).html(data);
            $('#order-delivery_id').removeClass('loading');
        }
    });
});
$(document).on('change', '#novaposhtamodel-type', function(e) {
    $('#delivery-1').html($('option:selected',this).text());
});
$(document).on('change', '#novaposhtamodel-city', function(e) {
    $('#delivery-2').html($('option:selected',this).text());
});
$(document).on('change', '#novaposhtamodel-warehouse', function(e) {
    $('#delivery-3').html($('option:selected',this).text());
});

if($('#novaposhtamodel-area option:selected').val()){
    $('#delivery-1').html($('#novaposhtamodel-area option:selected').text());
}

if($('#novaposhtamodel-city option:selected').val()){
    $('#delivery-2').html($('#novaposhtamodel-city option:selected').text());
}

if($('#novaposhtamodel-warehouse option:selected').val()){
    $('#delivery-3').html($('#novaposhtamodel-warehouse option:selected').text());
}

var deliveryCheck = $('#order-delivery_id input[type=\"radio\"]:checked');
$('#delivery').html($("label[for='"+deliveryCheck.attr('id')+"']").text());
