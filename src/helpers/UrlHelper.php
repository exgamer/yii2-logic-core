<?php

namespace concepture\yii2logic\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

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

    /**
     * @param $model
     * @param $urlParamAttrs
     * @param string $actionId
     * @param string $controllerId
     * @param string $moduleId
     * @return string
     */
    public static function getLocation($model, $urlParamAttrs, &$controllerId = null, $actionId = 'view', $moduleId = null)
    {
        $queryParams = [];
        foreach ($urlParamAttrs as $key => $attribute){
            if ( filter_var($key, FILTER_VALIDATE_INT) === true ) {
                $key = $attribute;
            }

            $queryParams[$key] = $model->{$attribute};
        }

        $className = ClassHelper::getShortClassName($model);
        if (! $controllerId) {
            $controllerId = Inflector::camel2id($className);
        }

        $url = $controllerId . "/" . $actionId;
        if ($moduleId){
            $url = $moduleId . "/" . $url;
        }

        $urlParams = ArrayHelper::merge([$url], $queryParams);
        $frontendUrlManager = static::getFrontendUrlManager();

        return $frontendUrlManager->createUrl($urlParams);
    }
}

