

<p><strong>Номер заказ:</strong> #{*order_id*}</p>
<p><strong>Способ доставки: </strong>{*order_delivery_name*} {*dt_name*} {*dt_hint_name*}</p>
<p><strong>Способ оплаты: </strong>{*order_payment_name*}</p>
<p><strong>Адрес доставки:</strong> {*city_name*}, {*address_name*}</p>
<p>&nbsp;&nbsp;</p>
<table border="0" width="100%" cellspacing="1" cellpadding="5" style="border-spacing: 0;border-collapse: collapse;">
<tr>
    <th style="{*cssStyle*}" colspan="2">{*th.product*}</th>
    {if*isWholesale*} 
        <th style="{*cssStyle*}">{*th.pcs*}</th>
    {endif}
    <th style="{*cssStyle*}">{*th.quantity*}</th>
    <th style="{*cssStyle*}">{*th.price*}</th>
    <th style="{*cssStyle*}">{*th.price_total*}</th>
</tr>
{%*products*}
    <tr>
        <td style="{*cssStyle*}" align="center"><a href="{*products:url*}"  target="_blank">{*products:image*}</a></td>
        <td style="{*cssStyle*}"><a href="{*products:url*}"  target="_blank">{*products:name*}</a></td>
        {if*isWholesale*} 
            <td style="{*cssStyle*}" align="center">{*products:pcs*}</td>
        {endif}
        <td style="{*cssStyle*}" align="center">{*products:quantity*}</td>
        <td style="{*cssStyle*}" align="center">{*products:price*} {*currency*}</td>
        <td style="{*cssStyle*}" align="center">{*products:price_total*} {*currency*}</td>
    </tr>
{%}
</table>
<p>&nbsp;</p>
<p>Общая стоимость: <strong>{*total_price*}</strong> {*currency*}</p>
<p>&nbsp;________________________________________________________________________________________</p>
<p>&nbsp;</p>
<p><strong>Контактные данные:</strong></p>
<p><strong>Имя:</strong> {*user_name*}</p>
<p><strong>Телефон:</strong> <a href="tel:{*user_phone*}">{*user_phone*}</a></p>
<p><strong>E-mail:</strong> {*user_email*}</p>
<p>&nbsp;</p>
<p>----------------------</p>
<p>IP-адрес: <strong>{*ip*}</strong></p>
<p>{*user_agent*}</p>






