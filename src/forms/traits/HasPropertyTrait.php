<?php
namespace concepture\yii2logic\forms\traits;

use Yii;

/**
 * Trait HasPropertyTrait
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait HasPropertyTrait
{
    /**
     * Загрузка свойств из модели в форму
     *
     * @param $model
     * @return bool
     */
    public function loadProperties($model)
    {
        $propertyModelClass = $model::getPropertyModelClass();
        $propertyModel = new $propertyModelClass();
        $excludedFields = $model->excludedPropertyFields();
        $uniqueField = $model->uniqueField();
        if (($key = array_search($uniqueField, $excludedFields)) !== false) {
            unset($excludedFields[$key]);
        }

        $attributes = array_keys($propertyModel->attributes);
        foreach ($attributes as $attribute){
            if (in_array($attribute, $excludedFields)){
                continue;
            }

            $this->{$attribute} = $model->{$attribute};
        }

        return true;
    }
}