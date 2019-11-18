<?php

namespace concepture\yii2logic\traits;

use Yii;

/**
 * Трейт для виджетов компонента
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait WidgetTrait
{
    /**
     * Регистрация ресурсов виджета
     */
    protected function registerBundle()
    {
        $class = static::class;
        if(Yii::$container->has($class)){
            return;
        }

        Yii::$container->set($class);
        $reflector = new \ReflectionClass($class);
        $bundle = "{$reflector->getNamespaceName()}\Bundle";
        if (! class_exists($bundle)) {
            return;
        }

        $bundle::register($this->getView());
    }
}