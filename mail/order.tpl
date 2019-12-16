{use class="Yii"}
{use class="yii\helpers\Url"}
{use class="panix\engine\Html"}
{use class="panix\mod\shop\models\Product"}


{if $order.user_name}
    <p><strong>{$order->getAttributeLabel('user_name')}:</strong> {$order->user_name}</p>
{/if}
{if $order.user_phone}
    <p><strong>{$order->getAttributeLabel('user_phone')}:</strong> {Html::tel($order->user_phone)}</p>
{/if}
{if $order.user_email}
    <p><strong>{$order->getAttributeLabel('user_email')}:</strong> {$order->user_email}</p>
{/if}
{if $order.deliveryMethod.name}
    <p><strong>{$order->getAttributeLabel('delivery_id')}:</strong> {$order.deliveryMethod.name}</p>
{/if}
{if $order.paymentMethod.name}
    <p><strong>{$order->getAttributeLabel('payment_id')}:</strong> {$order.paymentMethod.name}</p>
{/if}
{if $order.user_address}
    <p><strong>{$order->getAttributeLabel('user_address')}:</strong> {$order.user_address}</p>
{/if}
{if $order.user_comment}
    <p><strong>{$order->getAttributeLabel('user_comment')}:</strong> {$order.user_comment}</p>
{/if}

<table border="0" width="100%" cellspacing="1" cellpadding="5" style="border-spacing: 0;border-collapse: collapse;">
    <tr>
        <th colspan="2"
            style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'MAIL_TABLE_TH_PRODUCT')}</th>
        <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'QUANTITY')}</th>
        <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'PRICE_PER_UNIT')}</th>
        <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'TOTAL_PRICE')}</th>
    </tr>
    {foreach from=$order.products item=product}
        <tr>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">
                {Html::a(Html::img(Url::to($product->originalProduct->getMainImage('x100')->url,true), [
                'alt' => $product->name,
                'title' => $product->name
                ]),$product->originalProduct->getUrl(),['target'=>'_blank'])}
            </td>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Html::a($product->originalProduct->name, Url::to($product->originalProduct->getUrl(), true), ['target' => '_blank'])}</td>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;"
                align="center">{$product->quantity}</td>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">
                <strong>{$app->currency->number_format($app->currency->convert($product->price))}</strong>
                <sup>{$app->currency->active['symbol']}</sup></td>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">
                <strong>{$app->currency->number_format($app->currency->convert($product->price * $product->quantity))}</strong>
                <sup>{$app->currency->active['symbol']}</sup></td>
        </tr>
    {/foreach}
</table>

<p><strong>{Yii::t('cart/default', 'DETAIL_ORDER_VIEW')}:</strong><br/>
    {Html::a(Url::to($order->getUrl(),true),Url::to($order->getUrl(),true),['target'=>'_blank'])}</p>
<br/><br/><br/>
{Yii::t('cart/default', 'DELIVERY_PRICE')}:
<h2 style="display:inline">{$app->currency->number_format($order->delivery_price)} <sup>{$app->currency->active['symbol']}</sup></h2>

{Yii::t('cart/default', 'TOTAL_PAY')}:
<h1 style="display:inline">{$app->currency->number_format($order->total_price + $order->delivery_price)} <sup>{$app->currency->active['symbol']}</sup></h1>
