<?php
namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\enum\IsDeletedEnum;

/**
 * Trait IsDeletedTrait
 * @package concepture\yii2logic\models\traits
 */
trait IsDeletedTrait
{
    /**
     * Возвращает метку удаления
     *
     * @return string|null
     */
    public function isDeletedLabel()
    {
        return IsDeletedEnum::label($this->is_deleted);
    }
}

