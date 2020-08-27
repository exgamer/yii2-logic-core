<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\services\events\modify\BeforeChangeStatusEvent;
use concepture\yii2logic\services\events\modify\AfterChangeStatusEvent;
use concepture\yii2logic\services\events\modify\AfterModifyEvent;

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
        $result = $model->save(false);
        $this->afterStatusChange($model, $status);
        return $result;
    }

    protected function beforeStatusChange(ActiveRecord $model, $status)
    {
        $this->trigger(static::EVENT_BEFORE_CHANGE_STATUS, new BeforeChangeStatusEvent(['model' => $model, 'status' => $status]));
    }

    protected function afterStatusChange(ActiveRecord $model, $status)
    {
        $this->trigger(static::EVENT_AFTER_CHANGE_STATUS, new AfterChangeStatusEvent(['model' => $model, 'status' => $status]));
        $this->trigger(static::EVENT_AFTER_MODIFY, new AfterModifyEvent(['model' => $model]));
    }
}