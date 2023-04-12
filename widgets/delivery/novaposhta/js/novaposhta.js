
$(document).on('change', '#dynamicmodel-city, #dynamicmodel-type, #dynamicmodel-area', function(e) {
    var delivery_id = $('input[name="Order[delivery_id]"]:checked').val();
//console.log($('input[name="Order[delivery_id]"]:checked').val());
    $.ajax({
        url: common.url('/cart/delivery/process?id='+delivery_id),
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
$(document).on('change', '#dynamicmodel-type', function(e) {
    $('#delivery-1').html($('option:selected',this).text());
});
$(document).on('change', '#dynamicmodel-city', function(e) {
    $('#delivery-2').html($('option:selected',this).text());
});
$(document).on('change', '#dynamicmodel-warehouse', function(e) {
    $('#delivery-3').html($('option:selected',this).text());
});

if($('#dynamicmodel-area option:selected').val()){
    $('#delivery-1').html($('#dynamicmodel-area option:selected').text());
}

if($('#dynamicmodel-city option:selected').val()){
    $('#delivery-2').html($('#dynamicmodel-city option:selected').text());
}

if($('#dynamicmodel-warehouse option:selected').val()){
    $('#delivery-3').html($('#dynamicmodel-warehouse option:selected').text());
}

var deliveryCheck = $('#order-delivery_id input[type=\"radio\"]:checked');
$('#delivery').html($("label[for='"+deliveryCheck.attr('id')+"']").text());