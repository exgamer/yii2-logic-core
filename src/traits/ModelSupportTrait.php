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
        foreach ($this->attributes as $attribute => $value) {
            if ($this->isAttributeRequired($attribute)) {
                $required[] = $attribute;
            }
        }

        return $required;
    }

    /**
     * Заполнены ли обязательные аттрибуты
     * @return boolean
     */
    public function isAllRequiredEmpty()
    {
        $required = $this->getRequiredAttributes();
        $empty = true;
        foreach ($required as $attribute) {
            if (! in_array($this->{$attribute}, [null, ''])) {
                $empty = false;
            }
        }

        return $empty;
    }
}