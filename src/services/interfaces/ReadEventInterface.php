<?php

namespace concepture\yii2logic\services\interfaces;

/**
 * Interface ReadEventInterface
 * @package concepture\yii2logic\services\interfaces
 */
interface ReadEventInterface
{
    // событие для глобальнйо модификации запроса дял получения каталога
    const EVENT_GLOBAL_CATALOG_QUERY_EXTEND = 'globalCatalogQueryExtend';
}