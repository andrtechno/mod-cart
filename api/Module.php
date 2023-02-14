<?php

namespace panix\mod\cart\api;

use Yii;
use yii\base\BootstrapInterface;
use yii\rest\UrlRule;
use yii\helpers\ArrayHelper;

/**
 * Class Module
 * @package panix\mod\cart\api
 */
class Module extends \yii\base\Module
{

    public function init()
    {
        parent::init();
        $this->setAliases(['@' . $this->id => $this->basePath.'/../']);
        $this->registerTranslations($this->id);
    }

    private function getTranslationsFileMap($id, $path)
    {

        $lang = Yii::$app->language;
        $result = [];
        $basePath = realpath(Yii::getAlias("{$path}/{$lang}"));

        if (is_dir($basePath)) {
            $fileList = \yii\helpers\FileHelper::findFiles($basePath, [
                'only' => ['*.php'],
                'recursive' => false
            ]);
            foreach ($fileList as $path) {
                $result[$id . '/' . basename($path, '.php')] = basename($path);
                // $result[basename($path, '.php')] = basename($path);
            }
        }
        return $result;
    }

    private function registerTranslations($id)
    {
        $path = '@' . $id . '/messages';
        $translations[$id . '/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => $path,
            'fileMap' => $this->getTranslationsFileMap($id, $path)
        ];
        $this->i18n->translations = ArrayHelper::merge($translations, $this->i18n->translations);
    }

}
