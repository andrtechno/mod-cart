<?php

namespace panix\mod\cart\models;

use Yii;
use panix\engine\db\ActiveRecord;

/**
 * Class PromoCode
 *
 * @property array $categories Category ids
 * @property array $manufacturers Manufacturer ids
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
    public static $manufacturerTable = '{{%order__promocode_manufacturer}}';
    /**
     * @var array ids of categories to apply promo-code
     */
    protected $_categories;

    /**
     * @var array ids of manufacturers to apply promo-code
     */
    protected $_manufacturers;

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
            'manufacturers' => self::t('MANUFACTURERS'),
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
            [['manufacturers', 'categories'], 'validateArray'],
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
    public function setManufacturers($data)
    {
        $this->_manufacturers = $data;
    }


    /**
     * @return array
     */
    public function getManufacturers()
    {
        if (is_array($this->_manufacturers))
            return $this->_manufacturers;

        $table = self::$manufacturerTable;
        $this->_manufacturers = Yii::$app->db->createCommand("SELECT manufacturer_id FROM {$table} WHERE promocode_id=:id")
            ->bindValue(':id', $this->id)
            ->queryColumn();


        return $this->_manufacturers;
    }

    /**
     * Clear discount manufacturer and category
     */
    public function clearRelations()
    {
        Yii::$app->db->createCommand()
            ->delete(self::$manufacturerTable, 'promocode_id=:id', [':id' => $this->id])
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

        // Process manufacturers
        if (!empty($this->_manufacturers)) {
            foreach ($this->_manufacturers as $id) {
                Yii::$app->db->createCommand()->insert(self::$manufacturerTable, [
                    'promocode_id' => $this->id,
                    'manufacturer_id' => $id,
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
