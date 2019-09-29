<?php

namespace panix\mod\cart\migrations;

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m170915_134424_promocode
 */
use yii\db\Migration;
use panix\mod\cart\models\PromoCode;

/**
 * Class m170915_134424_promocode
 */
class m170915_134424_promocode extends Migration
{
    public static $categoryTable = '{{%order__promocode_category}}';
    public static $manufacturerTable = '{{%order__promocode_manufacturer}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // create table order
        $this->createTable(PromoCode::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'code' => $this->string(50),
            'discount' => $this->string(10),
            'used' => $this->smallInteger()->unsigned()->defaultValue(0),
            'max_use' => $this->smallInteger()->unsigned()->defaultValue(1),
            'created_at' => $this->integer(11)->null(),
            'updated_at' => $this->integer(11)->null(),
        ], $tableOptions);


        $this->createTable(self::$categoryTable, [
            'id' => $this->primaryKey()->unsigned(),
            'promocode_id' => $this->integer()->unsigned(),
            'category_id' => $this->integer()->unsigned(),
        ], $tableOptions);


        $this->createTable(self::$manufacturerTable, [
            'id' => $this->primaryKey()->unsigned(),
            'promocode_id' => $this->integer()->unsigned(),
            'manufacturer_id' => $this->integer()->unsigned(),
        ], $tableOptions);


        $this->createIndex('promocode_id', self::$manufacturerTable, 'promocode_id');
        $this->createIndex('manufacturer_id', self::$manufacturerTable, 'manufacturer_id');

        $this->createIndex('promocode_id', self::$categoryTable, 'promocode_id');
        $this->createIndex('category_id', self::$categoryTable, 'category_id');

    }

    public function down()
    {
        if ($this->db->driverName != "sqlite") {
            //$this->dropForeignKey('{{%fk_order__promocode}}', PromoCode::tableName());
        }
        $this->dropTable(PromoCode::tableName());
        $this->dropTable(self::$categoryTable);
        $this->dropTable(self::$manufacturerTable);
    }

}
