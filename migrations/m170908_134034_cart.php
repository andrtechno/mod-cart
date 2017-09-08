<?php

use yii\db\Migration;

class m170908_134034_cart extends Migration {

    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // create table order
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'secret_key' => $this->string(10)->notNull(),
            'delivery_id' => $this->integer()->notNull(),
            'payment_id' => $this->integer()->notNull(),
            'status_id' => $this->integer()->notNull(),
            'delivery_price' => 'float(10,2) DEFAULT NULL',
            'total_price' => 'float(10,2) DEFAULT NULL',
            'user_name' => $this->string(100),
            'user_email' => $this->string(100),
            'user_address' => $this->string(255),
            'user_phone' => $this->string(30),
            'user_comment' => $this->text(),
            'admin_comment' => $this->text()->comment('Admin Comment'),
            'user_agent' => $this->string(255),
            'ip_create' => $this->string(50),
            'discount' => $this->string(10),
            'date_create' => $this->timestamp()->defaultValue(null),
            'date_update' => $this->timestamp(),
            'paid' => $this->boolean()->defaultValue(0),
            'buyOneClick' => $this->boolean()->defaultValue(0),
                ], $tableOptions);

        // create table order status
        $this->createTable('{{%order_status}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100),
            'color' => $this->string(7),
            'ordern' => $this->integer(),
                ], $tableOptions);





        // create table order products
        $this->createTable('{{%order_product}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'currency_id' => $this->integer(),
            'supplier_id' => $this->integer(),
            'configurable_id' => $this->integer(),
            'name' => $this->string(255),
            'configurable_name' => $this->text(),
            'configurable_data' => $this->text(),
            'variants' => $this->text(),
            'quantity' => $this->smallInteger(8),
            'sku' => $this->string(100),
            'price' => 'float(10,2) DEFAULT NULL',
                ], $tableOptions);







        // create table order history
        $this->createTable('{{%order_history}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'user_id' => $this->integer(),
            'username' => $this->string(255),
            'handler' => $this->string(255),
            'data_before' => $this->text(),
            'data_after' => $this->text(),
            'date_create' => $this->datetime(),
                ], $tableOptions);

        // create table order history product
        $this->createTable('{{%order_history_product}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'date_create' => $this->datetime(),
                ], $tableOptions);



        // create table order history product
        $this->createTable('{{%order_payment}}', [
            'id' => $this->primaryKey(),
            'currency_id' => $this->integer(),
            'switch' => $this->boolean()->defaultValue(1),
            'payment_system' => $this->string(100),
            'ordern' => $this->integer(),
                ], $tableOptions);
        
        $this->createTable('{{%order_payment_translate}}', [
            'id' => $this->primaryKey(),
            'object_id' => $this->integer(),
            'language_id' => $this->string(2),
            'name' => $this->string(255),
            'description' => $this->text(),
                ], $tableOptions);

        
        
        
        

        $this->createTable('{{%order_delivery}}', [
            'id' => $this->primaryKey(),
            'price' => 'float(10,2) DEFAULT NULL',
            'free_from' => 'float(10,2) DEFAULT NULL',
            'switch' => $this->boolean()->defaultValue(1),
            'ordern' => $this->integer(),
                ], $tableOptions);
        
        $this->createTable('{{%order_delivery_translate}}', [
            'id' => $this->primaryKey(),
            'object_id' => $this->integer(),
            'language_id' => $this->string(2),
            'name' => $this->string(255),
            'description' => $this->text(),
                ], $tableOptions);
        
        $this->createTable('{{%order_delivery_payment}}', [
            'id' => $this->primaryKey(),
            'delivery_id' => $this->integer(),
            'payment_id' => $this->integer(),
                ], $tableOptions);
        
        
        

        $this->addIndexes();

        $columns = ['name', 'color', 'ordern'];
        $this->batchInsert('{{%order_status}}', $columns, [
            ['Новый', '#c0c0c0', 1],
            ['Отправлен', '#cссссс', 2],
        ]);




        $this->batchInsert('{{%order_payment}}', ['currency_id', 'ordern'], [
            [1, 1],
            [1, 2],
        ]);


        $columns = ['object_id', 'language_id', 'name', 'description'];
        $this->batchInsert('{{%order_payment_translate}}', $columns, [
            [1, Yii::$app->language, 'Наличными', ''],
            [2, Yii::$app->language, 'Приват24', ''],
        ]);



        
        $this->batchInsert('{{%order_delivery}}', ['ordern'], [
            [1],
            [2],
        ]);


        $columns = ['object_id', 'language_id', 'name', 'description'];
        $this->batchInsert('{{%order_delivery_translate}}', $columns, [
            [1, Yii::$app->language, 'Самовывоз', ''],
            [2, Yii::$app->language, 'Новая почта', ''],
        ]);
        
        
        

        if ($this->db->driverName != "sqlite") {
            $this->addForeignKey('{{%fk_order_status}}', '{{%order}}', 'status_id', '{{%order_status}}', 'id', "NO ACTION", "NO ACTION");
            //$this->addForeignKey('{{%fk_order_payment}}', '{{%order}}', 'payment_id', '{{%order_payment}}', 'id', "NO ACTION", "NO ACTION");
           // $this->addForeignKey('{{%fk_order_delivery}}', '{{%order}}', 'delivery_id', '{{%order_delivery}}', 'id', "NO ACTION", "NO ACTION");
            
            $this->addForeignKey('{{%fk_product_order}}', '{{%order_product}}', 'order_id', '{{%order}}', 'id', "NO ACTION", "NO ACTION");
        }
    }

    public function down() {
        if ($this->db->driverName != "sqlite") {
            $this->dropForeignKey('{{%fk_order_status}}', '{{%order}}');
            //$this->dropForeignKey('{{%fk_order_payment}}', '{{%order}}');
           // $this->dropForeignKey('{{%fk_order_delivery}}', '{{%order_delivery}}');
            
            $this->dropForeignKey('{{%fk_product_order}}', '{{%order_product}}');
        }
        $this->dropTable('{{%order}}');
        $this->dropTable('{{%order_status}}');
        $this->dropTable('{{%order_product}}');
        $this->dropTable('{{%order_history}}');
        $this->dropTable('{{%order_history_product}}');
        
        $this->dropTable('{{%order_payment}}');
        $this->dropTable('{{%order_payment_translate}}');
        
        $this->dropTable('{{%order_delivery}}');
        $this->dropTable('{{%order_delivery_translate}}');
        $this->dropTable('{{%order_delivery_payment}}');
    }
    
    
    private function addIndexes(){
                // order indexes
        $this->createIndex('user_id', '{{%order}}', 'user_id', 0);
        $this->createIndex('secret_key', '{{%order}}', 'secret_key', 0);
        $this->createIndex('delivery_id', '{{%order}}', 'delivery_id', 0);
        $this->createIndex('payment_id', '{{%order}}', 'payment_id', 0);
        $this->createIndex('status_id', '{{%order}}', 'status_id', 0);

        // order status indexes
        $this->createIndex('ordern', '{{%order_status}}', 'ordern', 0);

        // order products indexes
        $this->createIndex('order_id', '{{%order_product}}', 'order_id', 0);
        $this->createIndex('product_id', '{{%order_product}}', 'product_id', 0);
        $this->createIndex('currency_id', '{{%order_product}}', 'currency_id', 0);
        $this->createIndex('supplier_id', '{{%order_product}}', 'supplier_id', 0);
        $this->createIndex('configurable_id', '{{%order_product}}', 'configurable_id', 0);

        // order history indexes
        $this->createIndex('order_id', '{{%order_history}}', 'order_id', 0);
        $this->createIndex('user_id', '{{%order_history}}', 'user_id', 0);
        $this->createIndex('date_create', '{{%order_history}}', 'date_create', 0);


        // order history product indexes
        $this->createIndex('order_id', '{{%order_history_product}}', 'order_id', 0);
        $this->createIndex('product_id', '{{%order_history_product}}', 'product_id', 0);
        
        // order_payment_method indexes
        $this->createIndex('ordern', '{{%order_payment}}', 'ordern', 0);
        $this->createIndex('switch', '{{%order_payment}}', 'switch', 0);

        $this->createIndex('object_id', '{{%order_payment_translate}}', 'object_id', 0);
        $this->createIndex('language_id', '{{%order_payment_translate}}', 'language_id', 0);
        
        $this->createIndex('object_id', '{{%order_delivery_translate}}', 'object_id', 0);
        $this->createIndex('language_id', '{{%order_delivery_translate}}', 'language_id', 0);
        
        $this->createIndex('delivery_id', '{{%order_delivery_payment}}', 'delivery_id', 0);
        $this->createIndex('payment_id', '{{%order_delivery_payment}}', 'payment_id', 0);
    }

}
