<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\models\ActiveRecord;

/**
 * Методы сервиса дял сущностей которые имеют аттрибут status
 *
 * Trait StatusTrait
 * @package concepture\yii2logic\services\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait StatusTrait
{
    public function statusChange(ActiveRecord $model, $status)
    {
        $this->beforeStatusChange($model, $status);
        $model->status = $status;
        $model->save(false);
        $this->invalidateQueryCache();
    }

    protected function beforeStatusChange(ActiveRecord $model, $status){}
}

