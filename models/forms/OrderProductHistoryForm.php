<?php

class OrderProductHistoryForm extends CFormModel {

    /**
     * Дата с @from_date по @to_date
     * @from_date string date Y-m-d
     * @to_date string date Y-m-d
     */
    public $from_date;
    public $to_date;

    public function rules() {
        return array(
            //     array('to_date, from_date', 'required'),
            array('to_date, from_date', 'date', 'format' => 'yyyy-M-d'),
        );
    }

    public function attributeLabels() {
        return array(
            'from_date' => Yii::t('app/default', 'с:'),
            'to_date' => Yii::t('app/default', 'до:'),
        );
    }

}
