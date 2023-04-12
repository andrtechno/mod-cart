<?php

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m170915_134424_promocode
 */

use panix\engine\db\Migration;
use panix\mod\cart\models\PromoCode;

/**
 * Class m170915_134424_promocode
 */
class m170915_134424_promocode extends Migration
{

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


        $this->createTable(PromoCode::$categoryTable, [
            'id' => $this->primaryKey()->unsigned(),
            'promocode_id' => $this->integer()->unsigned(),
            'category_id' => $this->integer()->unsigned(),
        ], $tableOptions);


        $this->createTable(PromoCode::$brandTable, [
            'id' => $this->primaryKey()->unsigned(),
            'promocode_id' => $this->integer()->unsigned(),
            'brand_id' => $this->integer()->unsigned(),
        ], $tableOptions);

        $this->createIndex('code', PromoCode::tableName(), 'code');
        $this->createIndex('promocode_id', PromoCode::$brandTable, 'promocode_id');
        $this->createIndex('brand_id', PromoCode::$brandTable, 'brand_id');

        $this->createIndex('promocode_id', PromoCode::$categoryTable, 'promocode_id');
        $this->createIndex('category_id', PromoCode::$categoryTable, 'category_id');

    }

    public function down()
    {
        if ($this->db->driverName != "sqlite") {
            //$this->dropForeignKey('{{%fk_order__promocode}}', PromoCode::tableName());
        }
        $this->dropTable(PromoCode::tableName());
        $this->dropTable(PromoCode::$categoryTable);
        $this->dropTable(PromoCode::$brandTable);
    }

}
