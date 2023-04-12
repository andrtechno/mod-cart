<?php

namespace panix\mod\cart\models;

use Yii;
use panix\engine\db\ActiveRecord;

/**
 * Class PromoCode
 *
 * @property array $categories Category ids
 * @property array $brands Brands ids
 *
 * @property integer $id
 * @property string $discount
 * @property string $code
 *
 * @package panix\mod\cart\models
 *
 */
class PromoCode extends ActiveRecord
{

    const MODULE_ID = 'cart';
    public static $categoryTable = '{{%order__promocode_categories}}';
    public static $brandTable = '{{%order__promocode_brand}}';
    /**
     * @var array ids of categories to apply promo-code
     */
    protected $_categories;

    /**
     * @var array ids of brands to apply promo-code
     */
    protected $_brands;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order__promocode}}';
    }

    public function attributeLabels()
    {
        return \yii\helpers\ArrayHelper::merge([
            'brands' => self::t('BRANDS'),
            'categories' => self::t('CATEGORIES'),
        ], parent::attributeLabels());
    }

    public static function find()
    {
        return new query\PromoCodeQuery(get_called_class());
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [['code', 'discount', 'max_use'], 'required'],
            [['max_use', 'used'], 'number'],
            ['code', 'string', 'max' => 50],
            ['discount', 'string', 'max' => 10],
            [['brands', 'categories'], 'validateArray'],
            //[['code'], 'string'],
        ];
    }

    public function validateArray($attribute)
    {
        if (!is_array($this->{$attribute})) {
            $this->addError($attribute, 'The attribute must be array.');
        }
    }

    /**
     * @param array $data
     */
    public function setCategories($data)
    {
        $this->_categories = $data;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        if (is_array($this->_categories))
            return $this->_categories;

        $table = self::$categoryTable;
        $this->_categories = Yii::$app->db->createCommand("SELECT category_id FROM {$table} WHERE promocode_id=:id")
            ->bindValue(':id', $this->id)
            ->queryColumn();

        return $this->_categories;
    }


    /**
     * @param array $data
     */
    public function setBrands($data)
    {
        $this->_brands = $data;
    }


    /**
     * @return array
     */
    public function getBrands()
    {
        if (is_array($this->_brands))
            return $this->_brands;

        $table = self::$brandTable;
        $this->_brands = Yii::$app->db->createCommand("SELECT brand_id FROM {$table} WHERE promocode_id=:id")
            ->bindValue(':id', $this->id)
            ->queryColumn();


        return $this->_brands;
    }

    /**
     * Clear discount brand and category
     */
    public function clearRelations()
    {
        Yii::$app->db->createCommand()
            ->delete(self::$brandTable, 'promocode_id=:id', [':id' => $this->id])
            ->execute();
        Yii::$app->db->createCommand()
            ->delete(self::$categoryTable, 'promocode_id=:id', [':id' => $this->id])
            ->execute();

    }

    public function afterDelete()
    {
        $this->clearRelations();
        parent::afterDelete();
    }

    /**
     * After save event
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->clearRelations();

        // Process brands
        if (!empty($this->_brands)) {
            foreach ($this->_brands as $id) {
                Yii::$app->db->createCommand()->insert(self::$brandTable, [
                    'promocode_id' => $this->id,
                    'brand_id' => $id,
                ])->execute();
            }
        }

        // Process categories
        if (!empty($this->_categories)) {
            foreach (array_unique($this->_categories) as $id) {

                Yii::$app->db->createCommand()->insert(self::$categoryTable, [
                    'promocode_id' => $this->id,
                    'category_id' => $id,
                ])->execute();
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
