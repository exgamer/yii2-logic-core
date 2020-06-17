<?php

namespace concepture\yii2logic\enum;

/**
 * Class AccessTypeEnum
 * @package concepture\yii2logic\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class AccessTypeEnum extends Enum
{
    /**
     * Чтение
     */
    const READ = "r";
    /**
     * запись
     */
    const WRITE = "w";
    /**
     * чтение и запись
     */
    const READ_WRITE = "rw";
}
