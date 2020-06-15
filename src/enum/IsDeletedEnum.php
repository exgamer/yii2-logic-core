<?php

namespace concepture\yii2logic\enum;

use Yii;

/**
 * Класс содержащий константы для сущностей которые не удаляются физически
 * а ставится метка об удалении
 *
 * Class IsDeletedEnum
 * @package concepture\yii2logic\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IsDeletedEnum extends Enum
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    public static function labels()
    {
        return [
            self::NOT_DELETED => Yii::t('core', "Нет"),
            self::DELETED => Yii::t('core', "Да"),
        ];
    }

    public static function canDelete($is_deleted)
    {
        if (in_array($is_deleted, [static::NOT_DELETED])) {
            return true;
        }

        return false;
    }
}
