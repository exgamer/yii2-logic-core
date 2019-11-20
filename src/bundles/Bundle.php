<?php

namespace concepture\yii2logic\bundles;

use Yii;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * Базовый пакет ресурсов виджета
 *
 * @todo в кор, перебить все виджеты в админке
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class Bundle extends AssetBundle
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $class = static::class;
        $reflector = new \ReflectionClass($class);
        $namespace = "{$reflector->getNamespaceName()}\\resources";
        $namespace = str_replace('\\', '/', $namespace);
        $resourcesFolder = Yii::getAlias("@{$namespace}");
        if(
            ! is_dir($resourcesFolder)
            || ! file_exists($resourcesFolder)
        ) {
            return;
        }

        $this->sourcePath = $resourcesFolder;
    }

    public $jsOptions = [
        'position' => View::POS_END
    ];

    public $publishOptions = [
        'forceCopy'=> YII_DEBUG  ? true : false,
    ];
}