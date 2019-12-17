{use class="Yii"}
{use class="yii\helpers\Url"}
{use class="panix\engine\Html"}
{use class="panix\engine\CMS"}

<p>Здраствуйте, {$order->user_name}</p>
<p>Ваш заказ <strong>{CMS::idToNumber($order->id)}</strong> изменен статус на <strong>{$order->status->name}</strong></p>

<p>
    <strong>{Yii::t('cart/default', 'DETAIL_ORDER_VIEW')}:</strong><br/>
    {Html::a(Url::to($order->getUrl(),true),Url::to($order->getUrl(),true),['target'=>'_blank'])}
</p>

