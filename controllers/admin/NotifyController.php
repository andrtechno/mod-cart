<?php

namespace panix\mod\cart\controllers\admin;

use Yii;
use panix\mod\cart\models\ProductNotifications;
use panix\mod\cart\models\search\ProductNotificationsSearch;
use panix\engine\controllers\AdminController;
class NotifyController extends AdminController
{

    public $buttons = false;
    public $icon = 'envelope';

    public function actions()
    {
        return [
            'delete' => [
                'class' => 'panix\engine\grid\actions\DeleteAction',
                'modelClass' => ProductNotifications::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $this->pageName = Yii::t('cart/admin', 'NOTIFIER');

        $this->breadcrumbs[] = [
            'label' => Yii::t('cart/default', 'MODULE_NAME'),
            'url' => ['/admin/cart'],
        ];
        $this->breadcrumbs[] = $this->pageName;
        $this->buttons = [
            [
                'label' => 'Отправить новые товары всем подписчикам',
                'url' => ['delivery'],
                'options' => ['class' => 'btn btn-success']
            ]
        ];
        $searchModel = new ProductNotificationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionDelivery()
    {
        $this->pageName = Yii::t('app', 'Сегодняшние товары');

        /* $this->breadcrumbs = array(
          Yii::t('cart/admin', 'ORDER', 0) => array('/admin/cart'),
          Yii::t('cart/admin', 'NOTIFIER') => array('/admin/cart/notify'),
          $this->pageName
          ); */

        $searchModel = new ProductNotificationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), array('today' => true));

        return $this->render('delivery', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]
        );
    }

    public function actionDeliverySend()
    {
        Yii::app()->request->enableCsrfValidation = false;
        $model = new ShopProduct('search');
        $data = $model->search(array('today' => true))->getData();
        $config = Yii::app()->settings->get('app');
        $host = $_SERVER['HTTP_HOST'];
        $thStyle = 'border-color:#D8D8D8; border-width:1px; border-style:solid;';
        $tdStyle = 'border-color:#D8D8D8; border-width:1px; border-style:solid;';
        $currency = Yii::app()->currency->active->symbol;


        $tables = '<table border="0" width="600px" cellspacing="1" cellpadding="5" style="border-spacing: 0;border-collapse: collapse;">'; //border-collapse:collapse;
        $tables .= '<tr>';
        $tables .= '<th style="' . $thStyle . '">Изображение</th><th style="' . $thStyle . '">Товар</th><th style="' . $thStyle . '">Производитель</th><th style="' . $thStyle . '">Цена за шт.</th>';
        $tables .= '</tr>';
        foreach ($data as $row) {
            $tables .= '<tr>
            <td style="' . $tdStyle . '" align="center"><a href="' . $row->absoluteUrl . '"  target="_blank"><img border="0" src="http://' . $host . '/' . $row->getImageUrl("200x200") . '" alt="' . $row->name . '" /></a></td>
            <td style="' . $tdStyle . '"><a href="' . $row->absoluteUrl . '"  target="_blank">' . $row->name . '</a></td>
            <td style="' . $tdStyle . '" align="center" class="footer">' . $row->manufacturer->name . '</td>
            <td style="' . $tdStyle . '" align="center">' . $row->price . ' ' . $currency . '</td>
            </tr>';
        }
        $tables .= '</table>';

        $theme = Yii::t('cart/admin', '{site_name} Новое поступление', array('{site_name}' => $config['site_name']));
        $body = '
<html>
<body>

Здравствуйте!<br />
<p>
    Магазин <b>"' . $config['site_name'] . '"</b> уведомляет Вас о том, что появилось новое поступление.
</p>
' . $tables . '
<p>Будем рады обслужить Вас и ответить на любые вопросы!</p>
</body>
</html>
';


        $mailer = Yii::app()->mail;
        $mailer->From = 'noreply@' . $host;
        $mailer->FromName = Yii::app()->settings->get('app', 'site_name');
        $mailer->Subject = $theme;
        $mailer->Body = $body;
        foreach (DeliveryModule::getAllDelivery() as $mail) {
            $mailer->AddAddress($mail);
        }
        $mailer->AddReplyTo('noreply@' . $host);
        $mailer->isHtml(true);
        if (!$mailer->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mailer->ErrorInfo;
        } else {
            echo 'Message has been sent';
            $this->setFlashMessage(Yii::t('app', 'Письма успешно отправлены'));
            $this->redirect(array('delivery'));
        }
        //$mailer->ClearAddresses();
        //   }
        // }
    }

    /**
     * Send emails
     */
    public function actionSend()
    {
        $lang = Yii::app()->language;
        $record = ProductNotifications::model()->findAllByAttributes(array('product_id' => $_GET['product_id']));
        $siteName = Yii::app()->settings->get('app', 'site_mame');
        $host = $_SERVER['HTTP_HOST'];

        foreach ($record as $row) {
            if (!$row->product)
                continue;

            $theme = Yii::t('cart/admin', '{site_name} уведомляет о наличии интересующего Вас продукта', array(
                '{site_name}' => $siteName
            ));
            $body = '
<html>
<body>
Здравствуйте!<br />
<p>
    Магазин <?=$siteName?> уведомляет Вас о том,
    что появился в наличии продукт <a href="' . $row->product->absoluteUrl . '">' . $row->product->name . '</a>.
</p>
<p>Будем рады обслужить Вас и ответить на любые вопросы!</p>
</body>
</html>    
';
            $mailer = Yii::app()->mail;
            $mailer->From = 'robot@' . $host;
            $mailer->FromName = Yii::app()->settings->get('app', 'site_name');
            $mailer->Subject = $theme;
            $mailer->Body = $body;
            $mailer->AddAddress($row->email);
            $mailer->AddReplyTo('robot@' . $host);
            $mailer->isHtml(true);
            $mailer->Send();
            $mailer->ClearAddresses();

            $row->delete();
        }

        $this->setFlashMessage(Yii::t('cart/admin', 'Сообщения успешно отправлены.'));
        $this->redirect('index');
    }

}
