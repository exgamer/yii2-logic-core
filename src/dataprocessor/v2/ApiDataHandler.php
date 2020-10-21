<?php

namespace concepture\yii2logic\dataprocessor\v2;

use yii\base\Exception;

/**
 * Вспомогательный класс для обработки данных полученных через апи
 *
 * @author CitizenZet
 */
abstract class ApiDataHandler extends DataHandler
{
    public $method = 'GET';
    public $queryConfig = [];

    /**
     * @return string
     * @throws Exception
     */
    public function getQuery()
    {
        throw new Exception("set url");
    }

    /**
     * @return \concepture\yii2logic\services\Service|void
     */
    public function getService()
    {
        throw new Exception("not using");
    }
}