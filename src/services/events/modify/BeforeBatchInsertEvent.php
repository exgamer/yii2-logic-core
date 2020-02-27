<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Class BeforeBatchInsertEvent
 * @package concepture\yii2logic\services\events\modify
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class BeforeBatchInsertEvent extends Event
{
    public $fields;
    public $rows;
}