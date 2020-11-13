<?php

namespace concepture\yii2logic\services\events\read;

use yii\base\Event;

/**
 * Событие для глобальной модификации запроса получения каталога
 *
 * Class CatalogQueryExtendEvent
 * @package concepture\yii2logic\services\events\modify
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class QueryExtendEvent extends Event
{
    public $query;
}