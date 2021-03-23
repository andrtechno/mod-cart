<?php

namespace panix\mod\cart\widgets\buyOneClick;

use panix\engine\data\Widget;
use panix\mod\shop\models\Product;
use Yii;

/**
 * Виджет купить в один клик.
 *
 * Пример кода для контроллера:
 * <code>
 * public function actions() {
 *      return array(
 *          'buyOneClick' => 'panix\mod\widgets\buyOneClick\BuyOneClickAction'
 *      );
 * }
 * </code>
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 */
class BuyOneClickWidget extends Widget
{

    /** @var Product */
    public $model;

    public function init()
    {

        $this->view->registerJs("
            $(document).on('beforeSubmit', '#buyOneClick-form', function () {
                var form = $(this);
                $.ajax({
                    url:form.attr('action'),
                    type:form.attr('method'),
                    data:form.serialize(),
                    success:function(response){
                        if(response.success){
                            $.fancybox.close();
                            common.notify(response.message,'success');
                            window.dataLayer = window.dataLayer || [];
                            dataLayer.push({event: 'buy_one_click'});
                            
                            var transaction= {
                                event: 'transaction',
                                transactionId: response.data.order_id,
                                transactionAffiliation: '" . Yii::$app->settings->get('app', 'sitename') . "',
                                transactionTotal: response.data.total,
                                transactionProducts:[]
                            };
                            var transactionProducts = [];
                            $.each(response.data.products,function( index,item ) {
                                transactionProducts[index]={sku: item.id,name: item.name,price: item.price,quantity: item.quantity};
                            });
                            transaction.transactionProducts = transactionProducts;
    
                            dataLayer.push(transaction);

                        }
                       
                    }
                })
                return false; // Cancel form submitting.
            });");

        //$this->registerClientScript();
        parent::init();
    }

    public function run()
    {

        return $this->render($this->skin, ['model' => $this->model]);
    }


}
