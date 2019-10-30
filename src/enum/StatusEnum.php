<?php

namespace concepture\yii2logic\enum;

use Yii;

class StatusEnum extends Enum
{
    const INACTIVE = 0;
    const ACTIVE = 1;

    public static function labels()
    {
        return [
            self::ACTIVE => Yii::t('core', "Активный"),
            self::INACTIVE => Yii::t('core', "Неактивный"),
        ];
    }
}
