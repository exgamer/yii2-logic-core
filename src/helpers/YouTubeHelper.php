<?php

namespace concepture\yii2logic\helpers;

use Exception;
use Yii;

/**
 * Class YouTubeHelper
 * @package concepture\yii2logic\helpers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class YouTubeHelper
{
    /**
     * Возвращает id канала из ссылки на канал
     *
     * @param $url
     * @return mixed
     * @throws Exception
     */
    public static function parseChannelIdFromUrl($url)
    {
        $parsed = parse_url(rtrim($url, '/'));
        if (isset($parsed['path']) && preg_match('/^\/channel\/(([^\/])+?)$/', $parsed['path'], $matches)) {
            return $matches[1];
        }

        throw new Exception("{$url} is not a valid YouTube channel URL");
    }
}