<?php

namespace panix\mod\cart\widgets\promocode;


use panix\engine\Html;
use panix\mod\shop\models\Category;
use panix\mod\shop\models\Brand;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\base\Action;
use panix\mod\cart\models\PromoCode;

class PromoCodeAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $json = [];
        $json['success'] = false;
        $json['message']='';
        $code = Yii::$app->request->post('code');
        $accept = Yii::$app->request->post('accept');
        if (Yii::$app->request->isAjax && $code) {


            $query = PromoCode::find()->where([
                'code' => $code
            ])->one();

            if ($query) {

if($accept){
    $json['redirect'] = Url::to('');
}


                $json['success'] = true;
                $json['message'] .= Html::icon('check').' Ваш промо-код применен, Ваша скидка ' . $query->discount . ' на ';
                $json['id'] = $query->id;

                if ($query->categories) {
                    $json['categories'] = [];
                    $categoriesList = Category::find()->where(['id' => $query->categories])->all();
                    foreach ($categoriesList as $category) {
                        $json['categories'][$category->id] = $category->name;
                        $json['message'] .= Html::tag('span',$category->name,['class'=>'badge badge-secondary']).' ';
                    }
                }


                if ($query->brands) {
                    $brandsList = Brand::find()->where(['id' => $query->brands])->all();
                    $json['brands'] = [];
                    foreach ($brandsList as $brand) {
                        $json['brands'][$brand->id] = $brand->name;
                        $json['message'] .= Html::tag('span',$brand->name,['class'=>'badge badge-secondary']).' ';
                    }
                }


            } else {
                $json['message'] = Html::icon('warning').' Промо-код не найдет!';
            }

        } else {
            throw new \yii\web\ForbiddenHttpException('denied');
        }

        return $json;
    }
}