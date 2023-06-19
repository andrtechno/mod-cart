{use class="Yii"}
{use class="yii\helpers\Url"}
{use class="panix\engine\Html"}
{use class="panix\engine\CMS"}

<!--HEADER-->
<table width="100%" border="0" style="margin-bottom:30px">
    <tr>
        <td width="60%">
            <h1>№{CMS::idToNumber($model->id)}</h1>
        </td>
        <td width="40%" style="text-align: right">{$model->getAttributeLabel('created_at')}:
            <strong>{Yii::$app->formatter->asDatetime($model->created_at)}</strong>
        </td>
    </tr>
</table>
<!--/HEADER-->

<table border="0" cellspacing="0" cellpadding="0" style="width:100%;" class="table2">
    <tr>
        <td width="50%" valign="top">
            <table border="0" cellspacing="0" cellpadding="5" style="width:100%;" class="table2">
                {if $model->user_name}
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px solid #ccc;">
                            {$model->getAttributeLabel('user_name')}:
                            <strong>{$model->user_name}</strong>
                            {if $model->user_lastname}
                                <strong>{$model->user_lastname}</strong>
                            {/if}
                        </td>
                    </tr>
                {/if}
                {if $model->user_phone}
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px solid #ccc;">
                            {$model->getAttributeLabel('user_phone')}:
                            <strong>{$model->user_phone}</strong>
                        </td>
                    </tr>
                {/if}
                {if $model->user_email}
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px solid #ccc;">
                            {$model->getAttributeLabel('user_email')}:
                            <strong>{$model->user_email}</strong>
                        </td>
                    </tr>
                {/if}
            </table>
        </td>
        <td width="50%" valign="top">
            <table border="0" cellspacing="0" cellpadding="5" style="width:100%;">
                {if $model->getDeliveryEach()}
                    {foreach from=$model->getDeliveryEach() item=value}
                        <tr>
                            <td align="left" class="text-left" style="border-bottom: 1px solid #ccc;">
                                {$value['key']}:
                                <strong>{$value['value']}</strong>
                            </td>
                        </tr>
                        <p>: <strong></strong></p>
                    {/foreach}
                {/if}

                {if $model->deliveryMethod}
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px solid #ccc;">
                            {$model->getAttributeLabel('delivery_id')}:
                            <strong>{Yii::$app->formatter->asHtml($model->deliveryMethod->name)}</strong>
                    </tr>
                {/if}
                {if $model->paymentMethod}
                    <tr>
                        <td align="left" class="text-left" style="border-bottom: 1px solid #ccc;">
                            {$model->getAttributeLabel('payment_id')}:
                            <strong>{Yii::$app->formatter->asHtml($model->paymentMethod->name)}</strong>
                        </td>
                    </tr>
                {/if}
            </table>
        </td>
    </tr>
</table>
<br/><br/>
{if $model->products}
    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered">
        <thead>
        <tr>
            <th width="35%" colspan="2" class="text-center">{Yii::t('cart/default', 'TABLE_PRODUCT')}</th>
            <th width="10%" class="text-center">{Yii::t('cart/default', 'QUANTITY')}</th>
            <th width="15%" class="text-center">{Yii::t('cart/default', 'PRICE_PER_UNIT')}</th>
            <th width="20%" class="text-center">{Yii::t('cart/default', 'TOTAL_COST')}</th>
        </tr>
        </thead>
        <tbody>

        {$totalCountQuantity=0}
        {$totalCountPrice=0}
        {$totalCountPriceAll=0}

        {foreach from=$model->products item=product}


            {$totalCountQuantity = $product->quantity + $totalCountQuantity}
            {$totalCountPrice = $product->price + $totalCountPrice}
            {$totalCountPriceAll = {$product->price * $product->quantity + $totalCountPriceAll}}
            {$price=$product->price}
            {if $product->originalProduct}
                {$image=$product->originalProduct->getMainImage('small')->url}
            {else}
                {$image='/uploads/no-image.png'}
            {/if}
            <tr>
                <td width="10%"
                    align="center">{Html::img(Url::to($image, true), ['width' => 50, 'height' => 50])}</td>
                <td width="40%">
                    {$product->name}
                    <br/>

                    {if $product->sku}
                        {$product->getAttributeLabel('sku')}:
                        <strong>{$product->sku}</strong>
                    {/if}
                    {$attributes=$product->getAttributesProduct()}
                    {if $attributes}
                        {foreach from=$attributes item=item}
                            {$item['title']}:
                            <strong>{$item['value']}</strong>
                            ;
                        {/foreach}
                    {/if}

                    <br/>
                    <strong>{Yii::$app->currency->number_format($price)}</strong>
                    {Yii::$app->currency->active['symbol']}
                    / {Yii::t('shop/Product', 'UNITS_CUT', ['n' => 1])}
                </td>
                <td align="center">
                    <strong>{$product->quantity}</strong> {Yii::t('shop/Product', 'UNITS_CUT', ['n' => $product->unit])}
                </td>
                <td align="center">{Yii::$app->currency->number_format($price)}
                    {Yii::$app->currency->active['symbol']}</td>
                <td align="center">{Yii::$app->currency->number_format($price * $product->quantity)}
                    {Yii::$app->currency->active['symbol']}</td>
            </tr>
        {/foreach}

        </tbody>
        <tfoot>
        <tr>
            <th colspan="2" class="text-right">Всего</th>
            <th class="text-center">{$totalCountQuantity}</th>
            <th class="text-center">{Yii::$app->currency->number_format(Yii::$app->currency->convert($totalCountPrice))}
                {Yii::$app->currency->active['symbol']}</th>
            <th class="text-center">{Yii::$app->currency->number_format(Yii::$app->currency->convert($totalCountPriceAll))}
                {Yii::$app->currency->active['symbol']}</th>
        </tr>
        </tfoot>
    </table>
    <br/>
    <hr/>
    <div class="text-right">

        {if $model->delivery_price > 0}
            <p>{Yii::t('cart/default', 'COST_DELIVERY')}:
                <strong>{Yii::$app->currency->number_format($model->delivery_price)} {Yii::$app->currency->active['symbol']}</strong>
            </p>
        {/if}

        {if $model->discount}
            <p>{Yii::t('cart/Order', 'DISCOUNT')}:
                {if ('%' === substr($model->discount,-1,1))}
                    <strong>{$model->discount}</strong>
                {else}
                    <strong>{Yii::$app->currency->number_format($model->discount)}</strong> {Yii::$app->currency->active['symbol']}
                {/if}</p>
        {/if}


        {Yii::t('cart/default', 'TOTAL_PAY')}:
        <h3>{Yii::$app->currency->number_format($model->full_price)}
            {Yii::$app->currency->active['symbol']}</h3>
    </div>
{/if}


