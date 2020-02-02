<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Class AfterDeleteEvent
 * @package concepture\yii2logic\services\events\modify
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class AfterDeleteEvent extends Event
{
    public $model;
}