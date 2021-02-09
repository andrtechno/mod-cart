{use class="Yii"}
{use class="yii\helpers\Url"}
{use class="panix\engine\Html"}
{use class="panix\mod\shop\models\Product"}

{if $is_admin}
    {if $model.buyOneClick}
        <h4>{Yii::t('cart/admin', 'MSG_BUYONECLICK')}</h4>
    {/if}
    {if $model.call_confirm}
        <h4>{Yii::t('cart/Order', 'CALL_CONFIRM')}</h4>
    {/if}
{/if}

{if $model.user_name}
    <p><strong>{$model->getAttributeLabel('user_name')}:</strong> {$model->user_name}</p>
{/if}
{if $model.user_lastname}
    <p><strong>{$model->getAttributeLabel('user_lastname')}:</strong> {$model->user_lastname}</p>
{/if}
{if $model.user_phone}
    <p><strong>{$model->getAttributeLabel('user_phone')}:</strong> {Html::tel($model->user_phone)}</p>
{/if}
{if $model.user_email}
    <p><strong>{$model->getAttributeLabel('user_email')}:</strong> {$model->user_email}</p>
{/if}
{if $model.deliveryMethod}
    <p><strong>{$model->getAttributeLabel('delivery_id')}:</strong> {$model.deliveryMethod.name}</p>
{/if}
{if $model.paymentMethod}
    <p><strong>{$model->getAttributeLabel('payment_id')}:</strong> {$model.paymentMethod.name}</p>
{/if}

{if $model.delivery_address}
    <p><strong>{$model->getAttributeLabel('delivery_address')}
            :</strong> {if $model.delivery_city}{$model.delivery_city},{/if} {$model.delivery_address}</p>
{/if}
{if $model.user_comment}
    <p><strong>{$model->getAttributeLabel('user_comment')}:</strong> {$model.user_comment}</p>
{/if}

<table border="0" width="100%" cellspacing="1" cellpadding="5" style="border-spacing: 0;border-collapse: collapse;">
    <tr>
        <th colspan="2"
            style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'MAIL_TABLE_TH_PRODUCT')}</th>
        <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'QUANTITY')}</th>
        <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'PRICE_PER_UNIT')}</th>
        <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{Yii::t('cart/default', 'TOTAL_PRICE')}</th>
    </tr>
    {foreach from=$model.products item=product}

        <tr>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;width: 5%" align="center">
                {Html::a(Html::img(Url::to($product->getProductImage('x100'),true), [
                'alt' => $product->name,
                'title' => $product->name
                ]),{Url::to($product->getProductUrl(),true)},['target'=>'_blank'])}
            </td>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;">{$product->getProductName(true,['target' => '_blank'])}
                {if $product.variantsConfigure}
                    {foreach from=$product.variantsConfigure key=key item=configure}
                        <div>{$configure->name}: <strong>{$configure->value}</strong></div>
                    {/foreach}
                {/if}
            </td>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;"
                align="center">{$product->quantity}</td>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;width: 15%" align="center">
                <strong>{$app->currency->number_format($app->currency->convert($product->price))}</strong>
                <sup>{$app->currency->active['symbol']}</sup></td>
            <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;width: 15%" align="center">
                <strong>{$app->currency->number_format($app->currency->convert($product->price * $product->quantity))}</strong>
                <sup>{$app->currency->active['symbol']}</sup>
            </td>
        </tr>
    {/foreach}
</table>

<p><strong>{Yii::t('cart/default', 'DETAIL_ORDER_VIEW')}:</strong><br/>
    {Html::a(Url::to($model->getUrl(),true),Url::to($model->getUrl(),true),['target'=>'_blank'])}</p>
<br/><br/><br/>
{if $model.delivery_price}
    {Yii::t('cart/default', 'DELIVERY_PRICE')}:
    <h2 style="display:inline">{$app->currency->number_format($model->delivery_price)}
        <sup>{$app->currency->active['symbol']}</sup>
    </h2>
{/if}

{Yii::t('cart/default', 'TOTAL_PAY')}:
<h1 style="display:inline">{$app->currency->number_format($model->total_price + $model->delivery_price)}
    <small>{$app->currency->active['symbol']}</small>
</h1>
