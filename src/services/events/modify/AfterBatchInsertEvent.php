<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Class AfterBatchInsertEvent
 * @package concepture\yii2logic\services\events\modify
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class AfterBatchInsertEvent extends Event
{
    public $fields;
    public $rows;
}