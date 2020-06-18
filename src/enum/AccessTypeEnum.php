<?php

namespace concepture\yii2logic\enum;

use Yii;

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

    public static function labels()
    {
        return [
            self::READ => Yii::t('core', "Чтение"),
            self::WRITE => Yii::t('core', "Запись"),
        ];
    }
}
