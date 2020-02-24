<?php

namespace concepture\yii2logic\dataprocessor;

use concepture\yii2logic\services\Service;

/**
 * Interface DataHandlerInterface
 * @package concepture\yii2logic\services\interfaces
 */
interface DataHandlerInterface
{
    /**
     * @return Service
     */
    public function getService();


}