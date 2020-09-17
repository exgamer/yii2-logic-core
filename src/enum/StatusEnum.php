<?php

namespace concepture\yii2logic\enum;

use Yii;

/**
 * Класс перечисления который содержит константы для статусов
 *
 * Class StatusEnum
 * @package concepture\yii2logic\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StatusEnum extends Enum
{
    const INACTIVE = 0;
    const ACTIVE = 1;

    public static function labels()
    {
        return [
            self::ACTIVE => Yii::t('core', "Опубликован"),
            self::INACTIVE => Yii::t('core', "Черновик"),
        ];
    }

    /**
     * @return array
     */
    public static function colors()
    {
        return [
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        ];
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public static function color($value)
    {
        $items = self::colors();
        return $items[$value] ?? null;
    }

    public static function canActivate($status)
    {
        if (in_array($status, [static::INACTIVE])) {
            return true;
        }

        return false;
    }

    public static function canDeactivate($status)
    {
        if (in_array($status, [static::ACTIVE])) {
            return true;
        }

        return false;
    }
}
