<?php
namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\enum\IsDeletedEnum;

/**
 * Треит содержит методы для использования моделей
 * которые используют атрибут is_deleted
 *
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

