<?php

namespace panix\mod\cart\widgets\delivery\pickup;


use panix\engine\CMS;
use Yii;
use yii\base\Model;
use yii\validators\RequiredValidator;

class PickupConfigurationModel extends Model
{

    public $address;

    public function rules()
    {
        return [
            [['address'], 'required'],
            [['address'], 'string'],
            [['address'], 'trim'],

            //['address', 'validateLang', 'skipOnEmpty' => true],
        ];
    }

    public function validateLang($attribute)
    {
        $requiredValidator = new RequiredValidator();
        // $attributes = Json::decode($this->$attribute);
        $attributes = $this->$attribute;
        foreach ($attributes as $index => $row) {
            $error = null;
            $value = isset($row) ? $row : null;

            $requiredValidator->validate($value, $error);
            if (!empty($error)) {
                $key = $attribute . '[' . $index . ']';

                $this->addError($key, $error);
            }
        }
    }


    public function attributeLabels()
    {
        return [
            'address' => Yii::t('cart/payments', 'API key'),
        ];
    }

}
