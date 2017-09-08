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
            'secret_key' => $this->string(10),
            'delivery_id' => $this->integer(),
            'payment_id' => $this->integer(),
            'status_id' => $this->integer(),
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
            'order_id' => $this->integer(),
            'product_id' => $this->integer(),
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
            'order_id' => $this->integer(),
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
            'order_id' => $this->integer(),
            'product_id' => $this->integer(),
            'date_create' => $this->datetime(),
                ], $tableOptions);
        

        
        
        
        
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

        
        if ($this->db->driverName != "sqlite") {
            $this->addForeignKey('{{%fk_order_status}}', '{{%order}}', 'status_id', '{{%order_status}}', 'id', "NO ACTION", "NO ACTION");
            $this->addForeignKey('{{%fk_order_product}}', '{{%order_product}}', 'order_id', '{{%order}}', 'id', "NO ACTION", "NO ACTION");
            
        }
        
    }

    public function down() {
        if ($this->db->driverName != "sqlite") {
            $this->dropForeignKey('{{%fk_order_status}}', '{{%order}}');
            $this->dropForeignKey('{{%fk_order_product}}', '{{%order_product}}');
        }
        $this->dropTable('{{%order}}');
        $this->dropTable('{{%order_status}}');
        $this->dropTable('{{%order_product}}');
        $this->dropTable('{{%order_history}}');
        $this->dropTable('{{%order_history_product}}');
    }

}
