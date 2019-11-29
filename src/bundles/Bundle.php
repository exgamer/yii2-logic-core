<?php

namespace concepture\yii2logic\bundles;

use Yii;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * Базовый пакет ресурсов
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
        # отключаем автопоиск, если путь к файлам установлен
        if($this->sourcePath) {
            return ;
        }

        $class = static::class;
        $reflector = new \ReflectionClass($class);
        $namespace = "{$reflector->getNamespaceName()}\\resources";
        $namespace = str_replace('\\', '/', $namespace);
        $extend = $this->extendPath();
        if($extend) {
            $extend = "/" . trim("{$extend}", '/');
        }

        $resourcesFolder = Yii::getAlias("@{$namespace}{$extend}");
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

    /**
     * Дополнение путей для автопоиска ресурсов
     *
     * @return |null
     */
    protected function extendPath() {}
}