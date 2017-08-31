<?php

class AddcartWidget extends Widget {

    public $data;
    public $conf = false;
    public $spinner = false;

    public function init() {

        CartModule::registerAssets();
    }

    public function run() {
        $this->render($this->skin);
    }

}
