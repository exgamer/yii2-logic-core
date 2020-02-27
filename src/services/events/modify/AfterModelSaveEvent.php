<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Class AfterModelSaveEvent
 * @package concepture\yii2logic\services\events\modify
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class AfterModelSaveEvent extends Event
{
    public $form;
    public $model;
    public $is_new_record;
}