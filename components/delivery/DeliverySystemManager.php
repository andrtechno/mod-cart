<?php

namespace panix\mod\cart\components\delivery;

use yii\base\Component;

class DeliverySystemManager extends Component {

    /**
     * @var array
     */
    private $_systems = [];

    /**
     * Find all payment systems installed
     * @return array
     */
    public function getSystems() {
        $pattern = \Yii::getAlias('@cart/widgets/delivery') . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'config.xml';

        foreach (glob($pattern, GLOB_BRACE) as $file) {
            $config = simplexml_load_file($file);
            $this->_systems[(string) $config->id] = $config;
        }
        return $this->_systems;
    }

    /**
     * Read and return system config.xml
     * @param $name
     */
    public function getSystemInfo($name) {
        return $this->systems[$name];
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSystemClass($id) {
        $systemInfo = $this->getSystemInfo($id);
        $className = (string) $systemInfo->class;

        $systemArray = $this->getDefaultModelClasses();

        return new $systemArray[$className];
    }

    protected function getDefaultModelClasses() {
        return [
            'NovaPoshtaDeliverySystem' => 'panix\mod\cart\widgets\delivery\novaposhta\NovaPoshtaDeliverySystem',
        ];
    }

}
