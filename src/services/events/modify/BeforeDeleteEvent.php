<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Class BeforeDeleteEvent
 * @package concepture\yii2logic\services\events\modify
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class BeforeDeleteEvent extends Event
{
    public $model;
}