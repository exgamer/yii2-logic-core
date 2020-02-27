<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Class BeforeCreateEvent
 * @package concepture\yii2logic\services\events\modify
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class BeforeCreateEvent extends Event
{
    public $form;
}