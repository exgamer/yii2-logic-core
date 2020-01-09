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
     * Метод переопределен для загрузки в форму виртуальных полей
     *
     * @param $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);
        if (! $result){
            return $result;
        }

        if (is_array($data)){
            return $result;
        }

        $modelClass = static::getModelClass();
        $propertyModelClass = $modelClass::getPropertyModelClass();
        $propertyModel = new $propertyModelClass();
        $excludedFields = $data->excludedPropertyFields();
        $uniqueField = $data->uniqueField();
        if (($key = array_search($uniqueField, $excludedFields)) !== false) {
            unset($excludedFields[$key]);
        }

        $attributes = array_keys($propertyModel->attributes);
        foreach ($attributes as $attribute){
            if (in_array($attribute, $excludedFields)){
                continue;
            }

            $this->{$attribute} = $data->{$attribute};
        }

        return true;
    }
}