<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Class AfterCreateEvent
 * @package concepture\yii2logic\services\events\modify
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class AfterCreateEvent extends Event
{
    public $form;
}