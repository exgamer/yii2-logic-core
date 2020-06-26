<?php

namespace concepture\yii2logic\enum;

use Yii;

/**
 * Class EmailStatusEnum
 * @package concepture\yii2logic\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class EmailStatusEnum extends Enum
{
    const SENT = 250;

    public static function labels()
    {
        return [
            self::SENT => Yii::t('common', "Отправлено"),
        ];
    }
}
