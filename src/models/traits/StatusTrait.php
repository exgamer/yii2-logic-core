<?php
namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\enum\StatusEnum;

/**
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

