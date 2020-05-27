<?php

namespace concepture\yii2logic\traits;

use yii\helpers\ArrayHelper;

/**
 * Trait ModelSupportTrait
 * @package concepture\yii2logic\traits
 */
trait ModelSupportTrait
{
    /**
     * Возвращает обязательные атрибуты
     *
     * @return array
     */
    public function getRequiredAttributes()
    {
        $required = [];
        foreach ($this->attributes as $attribute) {
            if ($this->isAttributeRequired($attribute)) {
                $required[] = $attribute;
            }
        }

        return $required;
    }
}