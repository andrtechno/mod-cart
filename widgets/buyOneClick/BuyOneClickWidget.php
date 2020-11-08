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
class BuyOneClickWidget extends Widget {

    /** @var Product */
    public $model;

    public function init() {

        $this->view->registerJs("
            $(document).on('beforeSubmit', '#buyOneClick-form', function () {
                console.log(this);
                var form = $(this);
                $.ajax({
                    url:form.attr('action'),
                    type:form.attr('method'),
                    data:form.serialize(),
                    success:function(data){
                        console.log(data);
                        if(data.success){
                            $.fancybox.close();
                            common.notify(data.message,'success');
                        }
                       
                    }
                })
                return false; // Cancel form submitting.
            });");

        //$this->registerClientScript();
        parent::init();
    }

    public function run() {

        return $this->render($this->skin,['model'=>$this->model]);
    }


}
