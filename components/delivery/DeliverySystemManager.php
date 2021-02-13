<?php

namespace panix\mod\cart\components\delivery;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

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
        $pattern = Yii::getAlias('@cart/widgets/delivery') . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'config.json';

        foreach (glob($pattern, GLOB_BRACE) as $file) {
            $config = Json::decode(file_get_contents($file));
            $this->_systems[$config['id']] = $config;
        }
        return $this->_systems;
    }

    /**
     * Read and return system config.json
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
        $className = $systemInfo['class'];
        return new $className;
        //$systemArray = $this->getDefaultModelClasses();

       // return new $systemArray[$className];
    }

   /* protected function getDefaultModelClasses() {
        return [
            'NovaPoshtaDeliverySystem' => 'panix\mod\cart\widgets\delivery\novaposhta\NovaPoshtaDeliverySystem',
        ];
    }*/

}
