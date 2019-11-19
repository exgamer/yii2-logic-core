<?php

namespace concepture\yii2logic\helpers;

use yii\helpers\Url as Base;

/**
 * Вспомогательный класс для работы с ссылками по сертификату https
 * 
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CertificateUrl extends Base
{
    /**
     * Переопределено для https
     * 
     * @param string|array $route
     * @param boolean $scheme
     *
     * @return string
     */
    public static function toRoute($route, $scheme = 'https')
    {
        if(YII_DEBUG) {
            $scheme = false;
        }
        
        return parent::toRoute($route, $scheme);
    }
    
    /**
     * Переопределено для https
     * 
     * @param string|array $url
     * @param boolean $scheme
     *
     * @return string
     */
    public static function to($url = '', $scheme = 'https') 
    {
        if(YII_DEBUG) {
            $scheme = false;
        }
        
        return parent::to($url, $scheme);
    }
    
    /**
     * Переопределено для https
     * 
     * @param array $params
     * @param boolean $scheme
     *
     * @return string
     */
    public static function current(array $params = array(), $scheme = 'https') 
    {
        if(YII_DEBUG) {
            $scheme = false;
        }
        
        return parent::current($params, $scheme);
    }
}

