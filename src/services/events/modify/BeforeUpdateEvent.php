<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Class BeforeUpdateEvent
 * @package concepture\yii2logic\services\events\modify
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class BeforeUpdateEvent extends Event
{
    public $form;
    public $model;
}