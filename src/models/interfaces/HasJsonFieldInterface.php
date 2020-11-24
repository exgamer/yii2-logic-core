<?php

namespace concepture\yii2logic\models\interfaces;

/**
 * Interface HasJsonFieldInterface
 * @package concepture\yii2logic\models\interfaces
 */
interface HasJsonFieldInterface
{
    public function jsonFieldName();
    public function toJsonAttributes();
}