{use class="Yii"}
{use class="yii\helpers\Url"}
{use class="panix\engine\Html"}


<h1 style="text-align: center">Вы успешно зарегистрированы {$user->first_name}!</h1>
<div style="padding: 15px">
{if $user.id}
    <p><strong>{$user->getAttributeLabel('id')}:</strong> {$user->id}</p>
{/if}
<p>Ваш логин: <strong>{$user->username}</strong></p>
<p>Ваш пароль: <strong>{$password}</strong></p>


<div style="text-align: center">{Html::a(Yii::t('user/default','LOGIN'),Url::to(['/user/default/login'],true),['target'=>'_blank','style'=>'border:2px solid #EB0002;background-color:#fff;color:#333;padding: 9px 1rem;font-size: 16px;border-radius: 5px;font-weight: 600;'])}</div>


</div>