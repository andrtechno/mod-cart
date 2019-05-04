<?php

//namespace panix\mod\cart\migrations;
/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m170908_134034_cart
 */
use yii\db\Migration;
use panix\mod\cart\models\Order;

/**
 * Class m170908_134034_cart
 */
class m170908_134034_cart extends Migration
{

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // create table order
        $this->createTable(Order::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'secret_key' => $this->string(10)->notNull(),
            'delivery_id' => $this->integer()->notNull()->unsigned(),
            'payment_id' => $this->integer()->notNull()->unsigned(),
            'status_id' => $this->integer()->notNull()->unsigned(),
            //'delivery_price' => 'float(10,2) DEFAULT NULL',
            //'total_price' => 'float(10,2) DEFAULT NULL',
            'delivery_price' => $this->money(10,2),
            'total_price' => $this->money(10,2),

            'user_name' => $this->string(100),
            'user_email' => $this->string(100),
            'user_address' => $this->string(255),
            'user_phone' => $this->string(30),
            'user_comment' => $this->text(),
            'admin_comment' => $this->text()->comment('Admin Comment'),
            'invoice' => $this->string(100),
            'user_agent' => $this->string(255),
            'ip_create' => $this->string(50),
            'discount' => $this->string(10),
            'created_at' => $this->integer(11)->null(),
            'updated_at' => $this->integer(11)->null(),
            'paid' => $this->boolean()->defaultValue(0),
            'buyOneClick' => $this->boolean()->defaultValue(0),
        ], $tableOptions);

        // create table order status
        $this->createTable('{{%order__status}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(100),
            'color' => $this->string(7),
            'ordern' => $this->integer(),
        ], $tableOptions);


        // create table order products
        $this->createTable('{{%order__product}}', [
            'id' => $this->primaryKey()->unsigned(),
            'order_id' => $this->integer()->notNull()->unsigned(),
            'product_id' => $this->integer()->notNull()->unsigned(),
            'currency_id' => $this->integer()->unsigned(),
            'supplier_id' => $this->integer()->unsigned(),
            'configurable_id' => $this->integer()->unsigned(),
            'name' => $this->string(255),
            'configurable_name' => $this->text(),
            'configurable_data' => $this->text(),
            'variants' => $this->text(),
            'quantity' => $this->smallInteger(8),
            'sku' => $this->string(100),
            'price' => 'float(10,2) DEFAULT NULL',
        ], $tableOptions);



        // create table order history
        $this->createTable('{{%order__history}}', [
            'id' => $this->primaryKey()->unsigned(),
            'order_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'username' => $this->string(255),
            'handler' => $this->string(255),
            'data_before' => $this->text(),
            'data_after' => $this->text(),
            'date_create' => $this->datetime(),
        ], $tableOptions);

        // create table order history product
        $this->createTable('{{%order__history_product}}', [
            'id' => $this->primaryKey()->unsigned(),
            'order_id' => $this->integer()->notNull()->unsigned(),
            'product_id' => $this->integer()->notNull()->unsigned(),
            'date_create' => $this->datetime(),
        ], $tableOptions);


        // create table order history product
        $this->createTable('{{%order__payment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'currency_id' => $this->integer()->unsigned(),
            'switch' => $this->boolean()->defaultValue(1),
            'payment_system' => $this->string(100),
            'ordern' => $this->integer(),
        ], $tableOptions);

        $this->createTable('{{%order__payment_translate}}', [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'name' => $this->string(255),
            'description' => $this->text(),
        ], $tableOptions);


        $this->createTable('{{%order__delivery}}', [
            'id' => $this->primaryKey()->unsigned(),
            'price' => 'float(10,2) DEFAULT NULL',
            'free_from' => 'float(10,2) DEFAULT NULL',
            'switch' => $this->boolean()->defaultValue(1),
            'ordern' => $this->integer(),
        ], $tableOptions);

        $this->createTable('{{%order__delivery_translate}}', [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'name' => $this->string(255),
            'description' => $this->text(),
        ], $tableOptions);

        $this->createTable('{{%order__delivery_payment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'delivery_id' => $this->integer()->unsigned(),
            'payment_id' => $this->integer()->unsigned(),
        ], $tableOptions);


        $this->addIndexes();


        $this->batchInsert('{{%order__status}}', ['name', 'color', 'ordern'], [
            ['Новый', '#c0c0c0', 1],
            ['Отправлен', '#cссссс', 2],
        ]);


        $this->batchInsert('{{%order__payment}}', ['currency_id', 'ordern'], [
            [1, 1],
            [1, 2],
        ]);


        $columns = ['object_id', 'language_id', 'name', 'description'];
        $this->batchInsert('{{%order__payment_translate}}', $columns, [
            [1, Yii::$app->language, 'Наличными', ''],
            [2, Yii::$app->language, 'Приват24', ''],
        ]);


        $this->batchInsert('{{%order__delivery}}', ['ordern'], [
            [1],
            [2],
        ]);


        $columns = ['object_id', 'language_id', 'name', 'description'];
        $this->batchInsert('{{%order__delivery_translate}}', $columns, [
            [1, Yii::$app->language, 'Самовывоз', ''],
            [2, Yii::$app->language, 'Новая почта', ''],
        ]);


        if ($this->db->driverName != "sqlite") {
            $this->addForeignKey('{{%fk_order_status}}', Order::tableName(), 'status_id', '{{%order__status}}', 'id', "NO ACTION", "NO ACTION");
            $this->addForeignKey('{{%fk_order_payment}}', Order::tableName(), 'payment_id', '{{%order__payment}}', 'id', "NO ACTION", "NO ACTION");
            $this->addForeignKey('{{%fk_order_delivery}}', Order::tableName(), 'delivery_id', '{{%order__delivery}}', 'id', "NO ACTION", "NO ACTION");
            $this->addForeignKey('{{%fk_product_order}}', '{{%order__product}}', 'order_id', Order::tableName(), 'id', "NO ACTION", "NO ACTION");
        }
    }

    public function down()
    {
        if ($this->db->driverName != "sqlite") {
            $this->dropForeignKey('{{%fk_order__status}}', Order::tableName());
            $this->dropForeignKey('{{%fk_order__payment}}', Order::tableName());
            $this->dropForeignKey('{{%fk_order__delivery}}', Order::tableName());
            $this->dropForeignKey('{{%fk_product__order}}', '{{%order__product}}');
        }
        $this->dropTable(Order::tableName());
        $this->dropTable('{{%order__status}}');
        $this->dropTable('{{%order__product}}');
        $this->dropTable('{{%order__history}}');
        $this->dropTable('{{%order__history_product}}');
        $this->dropTable('{{%order__payment}}');
        $this->dropTable('{{%order__payment_translate}}');
        $this->dropTable('{{%order__delivery}}');
        $this->dropTable('{{%order__delivery_translate}}');
        $this->dropTable('{{%order__delivery_payment}}');

    }

    private function addIndexes()
    {
        // order indexes
        $this->createIndex('user_id', Order::tableName(), 'user_id');
        $this->createIndex('secret_key', Order::tableName(), 'secret_key');
        $this->createIndex('delivery_id', Order::tableName(), 'delivery_id');
        $this->createIndex('payment_id', Order::tableName(), 'payment_id');
        $this->createIndex('status_id', Order::tableName(), 'status_id');

        // order status indexes
        $this->createIndex('ordern', '{{%order__status}}', 'ordern');



        // order products indexes
        $this->createIndex('order_id', '{{%order__product}}', 'order_id');
        $this->createIndex('product_id', '{{%order__product}}', 'product_id');
        $this->createIndex('currency_id', '{{%order__product}}', 'currency_id');
        $this->createIndex('supplier_id', '{{%order__product}}', 'supplier_id');
        $this->createIndex('configurable_id', '{{%order__product}}', 'configurable_id');

        // order history indexes
        $this->createIndex('order_id', '{{%order__history}}', 'order_id');
        $this->createIndex('user_id', '{{%order__history}}', 'user_id');
        $this->createIndex('date_create', '{{%order__history}}', 'date_create');


        // order history product indexes
        $this->createIndex('order_id', '{{%order__history_product}}', 'order_id');
        $this->createIndex('product_id', '{{%order__history_product}}', 'product_id');

        // order_payment_method indexes
        $this->createIndex('ordern', '{{%order__payment}}', 'ordern');
        $this->createIndex('switch', '{{%order__payment}}', 'switch');

        $this->createIndex('object_id', '{{%order__payment_translate}}', 'object_id');
        $this->createIndex('language_id', '{{%order__payment_translate}}', 'language_id');

        $this->createIndex('object_id', '{{%order__delivery_translate}}', 'object_id');
        $this->createIndex('language_id', '{{%order__delivery_translate}}', 'language_id');

        $this->createIndex('delivery_id', '{{%order__delivery_payment}}', 'delivery_id');
        $this->createIndex('payment_id', '{{%order__delivery_payment}}', 'payment_id');
    }

}
