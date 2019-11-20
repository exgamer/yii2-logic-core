<?php

namespace concepture\yii2logic\modules;

use Yii;
use yii\base\Module as YiiModule;
use yii\helpers\ArrayHelper;

/**
 * Базовый модуль
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class Module extends YiiModule
{
    /**
     * @var bool признак глобального подключения компонентов модуля
     */
    protected $global = false;

    /**
     * @var string название файла конфигурации
     */
    protected $configFile = 'main';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $reflector = new \ReflectionClass(static::class);
        $name = $reflector->getShortName();
        $filename = $reflector->getFileName();
        $config = str_replace("/{$name}.php", '', $filename) . "/config/{$this->configFile}.php";
        $localConfig = str_replace("/{$name}.php", '', $filename) . "/config/{$this->configFile}-local.php";
        if(! file_exists($config)) {
            return;
        }
        # проверка на локальный конфиг
        if(! file_exists($localConfig)) {
            $result = require $config;
        } else {
            $result = ArrayHelper::merge(
                require $config,
                require $localConfig
            );
        }

        $object = $this->global ? Yii::$app : $this;
        \Yii::configure($object, $result);
    }
}