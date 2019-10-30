<?php
namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\enum\StatusEnum;

/**
 * Trait StatusTrait
 * @package concepture\yii2logic\models\traits
 */
trait StatusTrait
{
    public function statusLabel()
    {
        return StatusEnum::label($this->status);
    }
}

