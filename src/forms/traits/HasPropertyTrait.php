<?php
namespace concepture\yii2logic\forms\traits;

use Yii;

/**
 * Trait HasDomainPropertyTrait
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait HasPropertyTrait
{
    /**
     * Метод для загрузки и атрибутов и виртуальных полей
     *
     * @param $data
     * @param null $formName
     * @return bool
     */

    public function loadProperties($model, $formName = null)
    {
        $modelClass = static::getModelClass();
        $propertyModelClass = $modelClass::getPropertyModelClass();
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