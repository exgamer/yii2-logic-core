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

    /**
     * Создание копии сущности для кросс доменных сущностей
     *
     * @param Model $form
     * @param ActiveRecord $model
     * @return ActiveRecord
     */
    public function copyBetweenDomains(Model $form, ActiveRecord $model)
    {
        $form->domain_id = Yii::$app->domainService->getCurrentDomainId();
        $this->beforeCopyBetweenDomains($form, $model);
        $model = $this->update($form, $model);
        if (! $model){
            return false;
        }

        $this->afterCopyBetweenDomains($model);

        return $model;
    }

    /**
     * Дополнительные действия с моделью перед созданием копии
     * @param Model $form класс для работы
     * @param ActiveRecord $model
     */
    protected function beforeCopyBetweenDomains(Model $form, ActiveRecord $model){}

    /**
     * Дополнительные действия с моделью после создания копии
     * @param ActiveRecord $model
     */
    protected function afterCopyBetweenDomains(ActiveRecord $model){}
}

