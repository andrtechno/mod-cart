{use class="Yii"}
{use class="yii\helpers\Html"}
{use class="yii\helpers\Url"}
{use class="panix\mod\shop\models\Product"}


{if $order.user_name}
<p><b>{$order->getAttributeLabel('user_name')}:</b> {$order->user_name}</p>
{/if}
{if $order.user_phone}
<p><b>{$order->getAttributeLabel('user_phone')}:</b> {$order->user_phone}</p>
{/if}
{if $order.user_email}
<p><b>{$order->getAttributeLabel('user_email')}:</b> {$order->user_email}</p>
{/if}
{if $order.deliveryMethod.name}
<p><b>{$order->getAttributeLabel('delivery_id')}:</b> {$order.deliveryMethod.name}</p>
{/if}
{if $order.paymentMethod.name}
<p><b>{$order->getAttributeLabel('payment_id')}:</b> {$order.paymentMethod.name}</p>
{/if}
{if $order.user_address}
<p><b>{$order->getAttributeLabel('user_address')}:</b> {$order.user_address}</p>
{/if}
{if $order.user_comment}
<p><b>{$order->getAttributeLabel('user_comment')}:</b> {$order.user_comment}</p>
{/if}

<table border="0" width="600px" cellspacing="1" cellpadding="5" style="border-spacing: 0;border-collapse: collapse;">
    <tr>
        <th colspan="2" style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'MAIL_TABLE_TH_PRODUCT')}</th>
        <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'MAIL_TABLE_TH_QUANTITY')}</th>
        <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'MAIL_TABLE_TH_PRICE_FOR')}</th>
        <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'MAIL_TABLE_TH_TOTALPRICE')}</th>
    </tr>
{foreach from=$order.products item=product}
    <tr>
        <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">
       {Html::img(Url::to($product->originalProduct->getMainImageUrl('100x'), true), ['alt' => $product->originalProduct->name,'title' => $product->originalProduct->name])}
        </td>
        <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Html::a($product->originalProduct->name, Url::toRoute($product->originalProduct->getUrl(), true), ['target' => '_blank'])}</td>
        <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">{$product->quantity}</td>
        <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">{$app->currency->convert($product->originalProduct->price)} {$app->currency->active->symbol}</td>
        <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">{$app->currency->convert($product->originalProduct->price * $product->quantity)} {$app->currency->active->symbol}</td>
    </tr>
{/foreach}
</table>

<p><b>Детали заказа вы можете просмотреть на странице:</b><br/> {Html::a(Url::to($order->getUrl(),true),Url::to($order->getUrl(),true),['target'=>'_blank'])}</p>
<br/><br/><br/>
{Yii::t('cart/default', 'TOTAL_PAY')}:
<h1 style="display:inline">{Product::formatPrice($order->total_price + $order->delivery_price)}</h1>
{$app->currency->active->symbol}