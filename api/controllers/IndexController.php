<?php

namespace panix\mod\cart\api\controllers;

use panix\engine\CMS;
use panix\engine\api\Serializer;
use yii\filters\AccessControl;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Class IndexController
 * @package panix\mod\cart\api\controllers
 */
class IndexController extends ActiveController
{
    public $modelClass = 'panix\mod\cart\models\Order';
    public $serializer = [
        'class' => Serializer::class,
    ];
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formatParam' => 'format',
                'formats' => [
                    'json' => Response::FORMAT_JSON,
                ]
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
            ],
            'authenticator' => [
                'class' => QueryParamAuth::class,
                'tokenParam' => 'token',
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],
            ]
        ];
    }

    public function actionLogin2()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            return [
                'access_token' => $model->login(),
            ];
        } else {
            return $model->getFirstErrors();
        }
    }


    public function actionIndex()
    {

        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;

        $query = $modelClass::find();
        $query->where(['user_id'=>Yii::$app->user->id]);

        return Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $query,
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ]);
    }


}


