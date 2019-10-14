<?php

namespace panix\mod\cart\widgets\promocode;


use Yii;
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
        $json = [];
        $json['success'] = false;

        $code = Yii::$app->request->post('code');
        $accept = Yii::$app->request->post('accept');
        if (Yii::$app->request->isAjax && $code) {

            if ($accept) {
                $query = PromoCode::find();
                $query->where([
                    'code' => $code
                ]);
                $resultQuery = $query->one();

                if ($resultQuery) {
                    $json['success'] = true;
                    $json['message'] = 'Ваш промо-код применен, Ваша скидка ' . $resultQuery->discount . ' ';
                    $json['id'] = $resultQuery->id;
                } else {
                    $json['message'] = 'Промо-код не найдет!';
                }

            } else {
                $query = PromoCode::find();
                $query->where([
                    'code' => $code
                ]);
                $resultQuery = $query->one();

                if ($resultQuery) {
                    $json['success'] = true;
                    $json['message'] = 'Ваш промо-код применен, Ваша скидка ' . $resultQuery->discount . ' ';
                    $json['id'] = $resultQuery->id;
                } else {
                    $json['message'] = 'Промо-код не найдет!';
                }
            }
        } else {
            throw new \yii\web\ForbiddenHttpException('denied');
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }
}