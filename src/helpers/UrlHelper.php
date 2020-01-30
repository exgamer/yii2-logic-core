<?php

namespace concepture\yii2logic\helpers;

use Yii;

/**
 * Class UrlHelper
 * @package concepture\yii2logic\helpers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UrlHelper
{
    /**
     * Возвращает urlManager фронта
     *
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public static function getFrontendUrlManager()
    {
        $config = require(Yii::getAlias('@frontend/config/main.php'));
        $urlManagerConfig = $config['components']['urlManager'];
        $urlManagerConfig['baseUrl'] = "";

        return Yii::createObject($urlManagerConfig);
    }
}

