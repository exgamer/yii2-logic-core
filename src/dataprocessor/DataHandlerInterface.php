<?php

namespace concepture\yii2logic\dataprocessor;

use concepture\yii2logic\services\Service;
use yii\db\Query;
use yii\data\ActiveDataProvider;
/**
 * Interface DataHandlerInterface
 * @package concepture\yii2logic\services\interfaces
 */
interface DataHandlerInterface
{
    /**
     * @return Query
     */
    public static function getQuery();

    /**
     * @param $config
     * @return ActiveDataProvider
     */
    public static function getDataProvider($config);
}