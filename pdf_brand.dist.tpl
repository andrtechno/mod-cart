{use class="Yii"}
{use class="yii\helpers\Url"}
{use class="panix\engine\Html"}
{use class="panix\mod\shop\models\Product"}
{use class="panix\mod\shop\models\Attribute"}

{if Yii::$app->request->get('image')}
    {$small=false}
    {$rowsCount=5}
    {$nums=2}
    {$footnum=3}
{else}
    {$rowsCount=4}
    {$nums=1}
    {$footnum=2}
    {$small=true}
{/if}


{foreach from=$model item=order}
    {if (isset($order->products))}
        {foreach from=$order->products item=item}
            {$original=$item->originalProduct}

            {if ($original)}
                {if ($original->brand)}
                    {$title = ($original->brand) ? $original->brand->name : null}
                    {if ($original->mainImage)}
                        {$image = $original->getMainImage('50x50')->url}
                    {else}
                        {$image = '/uploads/no-image.png'}
                    {/if}
                    {$newprice = ($original->hasDiscount) ? $original->discountPrice : $item->price}
                    {$total_price = ($newprice * $item->quantity)}
                    {$array[$title][] = [
                    'item'=>$item,
                    'order_date' => $order->created_at,
                    'order_url' => Url::to($order->getUpdateUrl(), true),
                    'image' => Url::to($image, true),
                    'username' => $order->user_name,
                    'price' => $newprice,
                    'model' => $original,
                    'url' => Url::to($original->getUrl()),
                    'title' => $title,
                    'price_total' => $total_price
                    ]}
                {/if}
            {/if}
        {/foreach}
    {/if}
{/foreach}


{$total_count = 0}
{$total_price = 0}
{foreach from=$array key=key item=items}
    {$brand = explode('|', $key)}
    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered table-striped">
        <tbody>
        <tr>
            <th colspan="{$rowsCount}" align="center" class="text-center" style="background-color:#9b9b9b;color:#fff">
                <strong>{$key}</strong>
            </th>
        </tr>
        <tr>
            <th width="5%" align="center" class="text-center">№</th>
            <th width="50%" {if (!$small)} colspan="{$nums}" {/if} align="center"
                class="text-center">{Yii::t('cart/default', 'TABLE_PRODUCT')}</th>
            <th width="10%" align="center" class="text-center">{Yii::t('cart/default', 'QUANTITY')}</th>
            <th width="25%" align="center" class="text-center">Сумма</th>
        </tr>
        {usort($items, [$this->context, "titleSort"])}
        {$brand_count = 0}
        {$brand_price = 0}
        {$num = 0}
        {$i = 1}
        {foreach from=$items item=row}
            {assign var="brand_count" value=$brand_count+$row['item']->quantity}
            {assign var="brand_price" value=$brand_price+$row['price_total']}
            {assign var="num" value=$num+$row['item']->quantity}
            <tr>
                <td align="center">{$i}</td>
                {if (!$small)}
                    <td width="10%" align="center">
                        {Html::img($row['image'], ['width' => 50, 'height' => 50])}
                    </td>
                {/if}
                <td>
                    {$row['item']->name}<br/>
                    <strong>{Yii::$app->currency->number_format($row['price'])}</strong> {Yii::$app->currency->active['symbol']}
                    / {$row['model']->units[$row['model']->unit]}
                    <br/>
                    {if ($row['model']->sku)}
                        {$row['item']->getAttributeLabel('sku')}:
                        <strong>{$row['model']->sku}</strong>
                    {/if}
                    {$query = Attribute::find()->where(['IN', 'name', array_keys($row['model']->eavAttributes)])->displayOnPdf()->sort()}
                    {$result = $query->all()}
                    {$attributes = $row['model']->eavAttributes}
                    {foreach from=$result item=q}
                        {$q->title}:
                        <strong>{$q->renderValue($attributes[$q->name])}</strong>
                        ;
                    {/foreach}

                </td>
                <td align="center">
                    <strong>{$row['item']->quantity}</strong> {$row['model']->units[$row['model']->unit]}
                </td>
                <td align="center">
                    <strong>{Yii::$app->currency->number_format($row['price_total'])}</strong> {Yii::$app->currency->active['symbol']}
                </td>
            </tr>
            {assign var="i" value=$i++}
        {/foreach}
        <tr>
            <td align="center" colspan="{$footnum}"></td>
            <td align="center">
                {Yii::t('cart/default', 'QUANTITY')}: <strong>{$num}</strong>
            </td>

            <td align="center">
                И того:
                <strong>{Yii::$app->currency->number_format($brand_price)}</strong> {Yii::$app->currency->active['symbol']}
            </td>
        </tr>
        </tbody>
    </table>
    <!--  <pagebreak /> добавляем разрыв страницы -->
    {assign var="total_count" value=$total_count+$brand_count}
    {assign var="total_price" value=$total_price+$brand_price}

{/foreach}

<br/>

<h3 style="text-align: center">
    <small>{Yii::t('shop/default', 'PRODUCTS_COUNTER', $total_count)} на сумму:
    </small> {Yii::$app->currency->number_format($total_price)}
    <small>{Yii::$app->currency->active['symbol']}</small>
</h3>