{use class="Yii"}
{use class="yii\helpers\Url"}
{use class="panix\engine\Html"}
{use class="panix\mod\shop\models\Attribute"}

{if Yii::$app->request->get('image')}
    {$small=false}
    {$rowsCount=6}
    {$nums=2}
    {$footnum=3}
{else}
    {$rowsCount=5}
    {$nums=1}
    {$footnum=2}
    {$small=true}
{/if}

{foreach from=$model item=order}
    {if (isset($order->products))}
        {foreach from=$order->products item=item}
            {$original=$item->originalProduct}

            {if ($original)}
                {$supplier = $original->supplier}
                {if ($supplier)}
                    {$supplierData = [
                    'id' => ($supplier) ? $supplier->id : null,
                    'name' => ($supplier) ? $supplier->name : null,
                    'address' => ($supplier) ? $supplier->address : null,
                    'phone' => ($supplier) ? $supplier->phone : null
                    ]}
                    {if ($original->mainImage)}
                        {$image = $original->getMainImage('50x50')->url}
                    {else}
                        {$image = '/uploads/no-image.png'}
                    {/if}
                    <!--$newprice = ($original->hasDiscount) ? $original->discountPrice : $item->price;-->

                    {$newprice = $item->price}

                    {if (Yii::$app->request->get('price') == 1)}
                        {if ($item->price_purchase)}
                            {$newprice = $item->price_purchase}
                        {else}
                            {$newprice = 0}
                        {/if}
                    {/if}
                    {$box = $original->eav_par_v_asiku}
                    {$price = 0}
                    {if (isset($box))}
                        {$price = $newprice / $box->value}
                    {/if}

                    {$total_price = ($newprice * $item->quantity)}

                    {$array[$supplierData['id']][] = [
                    'item' => $item,
                    'supplier' => $supplierData,
                    'order_date' => $order->created_at,
                    'order_url' => Url::to($order->getUpdateUrl(), true),
                    'image' => Url::to($image, true),
                    'username' => $order->user_name,
                    'price' => $price,
                    'model' => $original,
                    'in_box'=>$box,
                    'url' => Url::to($original->getUrl()),
                    'price_total' => $total_price
                    ]}
                {/if}
            {/if}
        {/foreach}
    {/if}
{/foreach}

{$total_count = 0}
{$total_price = 0}
{$contact = Yii::$app->settings->get('contacts')}
{$phones = []}
{foreach from=$contact->phone item=phone}
    {$phones[] = $phone['number']}
{/foreach}

{foreach from=$array key=key item=items}
    {$brand = explode('|', $key)}
    <table border="1" cellspacing="0" cellpadding="2" style="width:100%;" class="table table-bordered table-striped">
        <tbody>
        <tr>
            <th colspan="{$rowsCount}" align="center" class="text-center">
                <strong>{Yii::$app->settings->get('app', 'sitename')} ({implode(', ', $phones)})</strong>
            </th>
        </tr>
        <tr>
            <th colspan="{$rowsCount}" align="center" class="text-center"
                style="background-color:#9b9b9b;color:#fff">
                <strong>{$items[0]['supplier']['name']} - {$items[0]['supplier']['address']}
                    ({$items[0]['supplier']['phone']})</strong>
            </th>
        </tr>
        <tr>
            <th width="5%" align="center" class="text-center">№</th>
            <th width="50%" {if (!$small)} colspan="{$nums}" {/if} align="center"
                class="text-center">{Yii::t('cart/default', 'TABLE_PRODUCT')}</th>
            <th width="10%" align="center" class="text-center">{Yii::t('cart/default', 'QUANTITY')}</th>
            <th width="10%" align="center" class="text-center">{Yii::t('cart/default', 'Пар в ящику')}</th>
            <th width="25%" align="center" class="text-center">Сума</th>
        </tr>
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
                    {$row['in_box']->value}
                </td>
                <td align="center">
                    {if ($row['price_total']) }
                        <strong>{Yii::$app->currency->number_format($row['price_total'])}</strong>
                        {Yii::$app->currency->active['symbol']}
                    {else}
                        ---
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/foreach}