function buyOneClickSend(){
    var form = $("#buyOneClick-form");
    $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data:form.serialize(),
        dataType:'html',
        success:function(data){
            $('#buyOneClick-dialog').html(data);
            $('.ui-widget-button').attr('disabled',false);
          //  common.removeLoader();

        },
        beforeSend:function(){
            common.addLoader();
            $('.ui-widget-button').attr('disabled',true);
        },
        error: function(data) {
            common.notify('Ошибка.','error'); 
        },
    });
}

$(function(){
   $('.buyOneClick-button').click(function(){
      if($(this).attr('disabled')){
          return false;
      } 
   });
});