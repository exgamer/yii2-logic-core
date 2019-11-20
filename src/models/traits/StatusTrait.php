<?php
namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\enum\StatusEnum;

/**
 * Треит содержит методы для использования моделей
 * которые используют атрибут status
 *
 * Trait StatusTrait
 * @package concepture\yii2logic\models\traits
 */
trait StatusTrait
{
    /**
     * Возвращает метку статуса
     *
     * @return string|null
     */
    public function statusLabel()
    {
        return StatusEnum::label($this->status);
    }
}

