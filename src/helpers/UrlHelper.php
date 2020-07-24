<?php

namespace concepture\yii2logic\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;

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
     * Возвращает текущую схему урл
     * @return array|false|int|string|null
     */
    public static function getCurrentSchema()
    {
        $current = Url::current([], true);

        return parse_url($current, PHP_URL_SCHEME);
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
            if ( $key === 0 || filter_var($key, FILTER_VALIDATE_INT) === true ) {
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

    /**
     * добавляет слеш перед queryString
     *
     * @param string $url
     * @return mixed
     */
    public static function addSlash($url)
    {
        $parsed = parse_url($url);
        if (isset($parsed['path'])) {
            $path = trim($parsed['path'], '/');
            $path = "/" . $path . "/";
            $parsed['path'] = $path;
        }

        return static::buildUrl($parsed);
    }

    /**
     * Собирает url из массива который возвращает parse_url()
     *
     * @param array $parts
     * @return string
     */
    public static function buildUrl(array $parts)
    {
        $scheme   = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '';

        $host     = ($parts['host'] ?? '');
        $port     = isset($parts['port']) ? (':' . $parts['port']) : '';

        $user     = ($parts['user'] ?? '');

        $pass     = isset($parts['pass']) ? (':' . $parts['pass'])  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';

        $path     = ($parts['path'] ?? '');
        $query    = isset($parts['query']) ? ('?' . $parts['query']) : '';
        $fragment = isset($parts['fragment']) ? ('#' . $parts['fragment']) : '';

        return implode('', [$scheme, $user, $pass, $host, $port, $path, $query, $fragment]);
    }
}

