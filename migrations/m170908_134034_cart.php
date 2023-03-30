<?php

/**
 * Generation migrate by PIXELION CMS
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 *
 * Class m170908_134034_cart
 */

use panix\engine\db\Migration;
use panix\mod\cart\models\Order;
use panix\mod\cart\models\OrderStatus;
use panix\mod\cart\models\OrderProduct;
use panix\mod\cart\models\OrderHistory;
use panix\mod\cart\models\Delivery;
use panix\mod\cart\models\Payment;
use panix\mod\cart\models\translate\DeliveryTranslate;
use panix\mod\cart\models\translate\PaymentTranslate;
use panix\mod\cart\models\DeliveryPayment;

/**
 * Class m170908_134034_cart
 */
class m170908_134034_cart extends Migration
{
    public $settingsForm = 'panix\mod\cart\models\forms\SettingsForm';

    public function up()
    {
        // create table order
        $this->createTable(Order::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'secret_key' => $this->string(10)->notNull(),
            'delivery_id' => $this->integer()->unsigned()->notNull(),
            'payment_id' => $this->integer()->unsigned()->notNull(),
            'status_id' => $this->integer()->unsigned()->notNull(),
            'promocode_id' => $this->integer()->unsigned()->null(),
            'delivery_price' => $this->money(10, 2),
            'total_price' => $this->money(10, 2),
            'total_price_purchase' => $this->money(10, 2),
            'diff_price' => $this->money(10, 2),
            'user_lastname' => $this->string(100),
            'user_name' => $this->string(100),
            'user_email' => $this->string(100),
            //'user_address' => $this->string(255),
            'user_phone' => $this->phone(),
            'user_comment' => $this->text(),
            'admin_comment' => $this->text()->comment('Admin Comment'),
            'invoice' => $this->string(100)->comment('Счет'),
            'user_agent' => $this->string(255),
            'ip_create' => $this->string(50),
            'discount' => $this->string(10),
            'created_at' => $this->integer(11)->null(),
            'updated_at' => $this->integer(11)->null(),
            'paid' => $this->boolean()->defaultValue(false),
            'call_confirm' => $this->boolean()->defaultValue(false),
            'ttn' => $this->string(100)->null(),
            'points' => $this->integer()->defaultValue(0),
            'points_expire' => $this->integer()->null(),
            'apply_user_points' => $this->boolean()->defaultValue(false),
            'buyOneClick' => $this->boolean()->defaultValue(false),
        ], $this->tableOptions);

        // create table order status
        $this->createTable(OrderStatus::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(100),
            'color' => $this->string(7),
            'use_in_stats' => $this->boolean()->defaultValue(false),
            'ordern' => $this->integer(),
        ], $this->tableOptions);


        // create table order products
        $this->createTable(OrderProduct::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'order_id' => $this->integer()->notNull()->unsigned(),
            'product_id' => $this->integer()->notNull()->unsigned(),
            'currency_id' => $this->integer()->unsigned(),
            'user_id' => $this->integer()->null()->unsigned()->comment('for reviews product is buy'),
            'supplier_id' => $this->integer()->unsigned(),
            'manufacturer_id' => $this->integer()->unsigned(),
            'configurable_id' => $this->integer()->unsigned(),
            'weight_class_id' => $this->integer()->null(),
            'length_class_id' => $this->integer()->null(),
            'currency_rate' => $this->money(10, 2)->comment('По курсу'),
            'weight' => $this->decimal(15, 4),
            'length' => $this->decimal(15, 4),
            'width' => $this->decimal(15, 4),
            'height' => $this->decimal(15, 4),
            'name' => $this->string(255),
            'discount' => $this->string(25)->null(),
            'configurable_name' => $this->text()->null(),
            'configurable_data' => $this->text()->null(),
            'attributes_data' => $this->text()->null(),
            'variants' => $this->text()->null(),
            'quantity' => $this->smallInteger(8),
            'sku' => $this->string(100),
            'price' => $this->money(10, 2),
            'price_purchase' => $this->money(10, 2)->comment('Цена закупки'),
        ], $this->tableOptions);


        // create table order history
        $this->createTable(OrderHistory::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'order_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'username' => $this->string(255),
            'handler' => $this->string(255),
            'data_before' => $this->text(),
            'data_after' => $this->text(),
            'date_create' => $this->datetime(),
        ], $this->tableOptions);


        // create table order history product
        $this->createTable(Payment::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'currency_id' => $this->integer()->unsigned(),
            'switch' => $this->boolean()->defaultValue(true),
            'payment_system' => $this->string(100),
            'ordern' => $this->integer()->unsigned(),
        ], $this->tableOptions);

        $this->createTable(PaymentTranslate::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'name' => $this->string(255),
            'description' => $this->text(),
        ], $this->tableOptions);


        $this->createTable(Delivery::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'price' => $this->money(10, 2)->null(),
            'free_from' => $this->money(10, 2)->null(),
            'system' => $this->string(100),
            'switch' => $this->boolean()->defaultValue(true),
            'ordern' => $this->integer()->unsigned(),
        ], $this->tableOptions);

        $this->createTable(DeliveryTranslate::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'object_id' => $this->integer()->unsigned(),
            'language_id' => $this->tinyInteger()->unsigned(),
            'name' => $this->string(255),
            'description' => $this->text(),
        ], $this->tableOptions);

        $this->createTable(DeliveryPayment::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'delivery_id' => $this->integer()->unsigned(),
            'payment_id' => $this->integer()->unsigned(),
        ], $this->tableOptions);


        $this->addIndexes();


        $this->batchInsert(OrderStatus::tableName(), ['name', 'color', 'ordern', 'use_in_stats'], [
            ['Новый', '#67bf3b', 1, 0],
            ['Удален', '#db6058', 2, 0],
            ['Отправлен', '#b4b4c2', 3, 0],
            ['Выполнен', '#e0e089', 4, 1],
            ['Возврат', '#799ff3', 5, 0]
        ]);


        $this->batchInsert(Payment::tableName(), ['currency_id', 'ordern'], [
            [1, 1],
            [1, 2],
        ]);


        $this->batchInsert(Delivery::tableName(), ['ordern'], [
            [1],
            [2],
        ]);


        $columns = ['object_id', 'language_id', 'name', 'description'];

        foreach (Yii::$app->languageManager->getLanguages(false) as $lang) {
            $this->batchInsert(PaymentTranslate::tableName(), $columns, [
                [1, $lang['id'], 'Наличными', ''],
                [2, $lang['id'], 'Приват24', ''],
            ]);
            $this->batchInsert(DeliveryTranslate::tableName(), $columns, [
                [1, $lang['id'], 'Самовывоз', ''],
                [2, $lang['id'], 'Новая почта', ''],
            ]);
        }

        if ($this->db->driverName != "sqlite") {

            $this->addForeignKey('{{%fk_order_status}}', Order::tableName(), 'status_id', OrderStatus::tableName(), 'id', "NO ACTION", "NO ACTION");
            $this->addForeignKey('{{%fk_order_payment}}', Order::tableName(), 'payment_id', Payment::tableName(), 'id', "NO ACTION", "NO ACTION");
            $this->addForeignKey('{{%fk_order_delivery}}', Order::tableName(), 'delivery_id', Delivery::tableName(), 'id', "NO ACTION", "NO ACTION");
            $this->addForeignKey('{{%fk_product_order}}', OrderProduct::tableName(), 'order_id', Order::tableName(), 'id', "CASCADE", "CASCADE");
        }

        $this->loadSettings();
    }

    public function down()
    {
        if ($this->db->driverName != "sqlite") {
            //$this->dropForeignKey('{{%fk_order__status}}', Order::tableName());
            //$this->dropForeignKey('{{%fk_order__payment}}', Order::tableName());
            //$this->dropForeignKey('{{%fk_order__delivery}}', Order::tableName());
            //$this->dropForeignKey('{{%fk_product__order}}', OrderProduct::tableName());
        }
        $this->dropTable(Order::tableName());
        $this->dropTable(OrderStatus::tableName());
        $this->dropTable(OrderProduct::tableName());
        $this->dropTable(OrderHistory::tableName());
        $this->dropTable(Payment::tableName());
        $this->dropTable(PaymentTranslate::tableName());
        $this->dropTable(Delivery::tableName());
        $this->dropTable(DeliveryTranslate::tableName());
        $this->dropTable(DeliveryPayment::tableName());

    }

    private function addIndexes()
    {
        // order indexes
        $this->createIndex('user_id', Order::tableName(), 'user_id');
        $this->createIndex('secret_key', Order::tableName(), 'secret_key');
        $this->createIndex('delivery_id', Order::tableName(), 'delivery_id');
        $this->createIndex('payment_id', Order::tableName(), 'payment_id');
        $this->createIndex('promocode_id', Order::tableName(), 'promocode_id');
        $this->createIndex('status_id', Order::tableName(), 'status_id');
        $this->createIndex('created_at', Order::tableName(), 'created_at');
        $this->createIndex('updated_at', Order::tableName(), 'updated_at');
        $this->createIndex('diff_price', Order::tableName(), 'diff_price');

        // order status indexes
        $this->createIndex('ordern', OrderStatus::tableName(), 'ordern');
        $this->createIndex('use_in_stats', OrderStatus::tableName(), 'use_in_stats');


        // order products indexes
        $this->createIndex('order_id', OrderProduct::tableName(), 'order_id');
        $this->createIndex('product_id', OrderProduct::tableName(), 'product_id');
        $this->createIndex('currency_id', OrderProduct::tableName(), 'currency_id');
        $this->createIndex('supplier_id', OrderProduct::tableName(), 'supplier_id');
        $this->createIndex('configurable_id', OrderProduct::tableName(), 'configurable_id');
        $this->createIndex('manufacturer_id', OrderProduct::tableName(), 'manufacturer_id');
        $this->createIndex('weight_class_id', OrderProduct::tableName(), 'weight_class_id');
        $this->createIndex('length_class_id', OrderProduct::tableName(), 'length_class_id');

        // order history indexes
        $this->createIndex('order_id', OrderHistory::tableName(), 'order_id');
        $this->createIndex('user_id', OrderHistory::tableName(), 'user_id');
        $this->createIndex('date_create', OrderHistory::tableName(), 'date_create');

        // order_payment_method indexes
        $this->createIndex('ordern', Payment::tableName(), 'ordern');
        $this->createIndex('switch', Payment::tableName(), 'switch');

        $this->createIndex('object_id', PaymentTranslate::tableName(), 'object_id');
        $this->createIndex('language_id', PaymentTranslate::tableName(), 'language_id');

        $this->createIndex('object_id', DeliveryTranslate::tableName(), 'object_id');
        $this->createIndex('language_id', DeliveryTranslate::tableName(), 'language_id');

        $this->createIndex('delivery_id', DeliveryPayment::tableName(), 'delivery_id');
        $this->createIndex('payment_id', DeliveryPayment::tableName(), 'payment_id');
    }

}
