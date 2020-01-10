<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\forms\Model;
use concepture\yii2logic\models\ActiveRecord;
use Yii;
use yii\db\ActiveQuery;

/**
 * Треит содержит методы для копирования
 *
 * Trait CopyTrait
 * @package concepture\yii2logic\services\traits
 */
trait CopyTrait
{
    /**
     * Создание копии сущности
     *
     * @param Model $form
     * @param ActiveRecord $model
     * @return ActiveRecord
     */
    public function copy(Model $form, ActiveRecord $model)
    {
        $this->beforeCopy($form, $model);
        $model = $this->create($form);
        if (! $model){
            return false;
        }

        $this->afterCopy($model);

        return $model;
    }

    /**
     * Дополнительные действия с моделью перед созданием копии
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     */
    protected function beforeCopy(Model $form, ActiveRecord $model){}

    /**
     * Дополнительные действия с моделью после создания копии
     * @param ActiveRecord $model
     */
    protected function afterCopy(ActiveRecord $model){}
}

