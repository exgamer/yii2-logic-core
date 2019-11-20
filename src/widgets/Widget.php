<?php

namespace concepture\yii2logic\widgets;

use yii\base\Widget as BaseWidget;

/**
 * Базовый виджет
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class Widget extends BaseWidget
{
    use WidgetTrait;

    /**
     * @inheritDoc
     */
    public function beforeRun()
    {
        $this->registerBundle();

        return parent::beforeRun();
    }
}