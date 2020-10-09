<?php

namespace concepture\yii2logic\helpers;

use Yii;

/**
 * Class ApplicationHelper
 * @package concepture\yii2logic\helpers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ApplicationHelper
{
    /**
     * Установить компоненты приложения
     *
     * @param array $components
     */
    public static function setComponents($components)
    {
        $exists = array_intersect_key( Yii::$app->getComponents() , $components);
        if (! empty($exists)) {
            foreach ($exists as $key => $value) {
                unset($components[$key]);
            }
        }

        Yii::$app->setComponents($components);
    }
}

