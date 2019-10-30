<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\models\ActiveRecord;

/**
 * Trait StatusTrait
 * @package concepture\yii2logic\services\traits
 */
trait StatusTrait
{
    public function statusChange(ActiveRecord $model, $status)
    {
        $this->beforeStatusChange($model, $status);
        $model->status = $status;
        $model->save(false);
    }

    protected function beforeStatusChange(ActiveRecord $model, $status){}
}

