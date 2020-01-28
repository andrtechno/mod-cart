<html>
    <body>
        <h1>Телефон: <a style="text-decoration:none;" href="tel:{*phone*}">{*phone*}</a></h1>
        <br/>
        <table border="0" width="100%" cellspacing="1" cellpadding="5" style="border-spacing: 0;border-collapse: collapse;">
            <tr>
                <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center" colspan="2">{*th_name*}</th>
                <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">{*th_quantity*}</th>
                <th style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center" width="20%">{*th_price*}</th>
            </tr>
            <tr>
                <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">
                    <a href="{*url*}"  target="_blank"><img src="{*image*}" alt="{*name*}" title="{*name*}"></a>
                </td>
                <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;">
                    <a href="{*url*}"  target="_blank">{*name*}</a>
                    {if*hasDiscount*}
                    <small><span style="text-decoration: line-through;color:#606060">{*originalPrice*}</span> <sup>{*currency*}</sup></small>
                    {endif}</td>
                <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">{*quantity*}</td>
                <td style="border-color:#D8D8D8; border-width:1px; border-style:solid;" align="center">{*price*}<sup>{*currency*}</sup></td>
            </tr>
        </table>
        <br/>
        <small>
            <p>Дата отправки: <b>{*now_date*}</b></p>
            <p>IP-address: <b>{*ip*}</b></p>
            <p>{*browser_string*}</p>
        </small>
    </body>
</html>

