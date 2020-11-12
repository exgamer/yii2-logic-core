<?php
namespace concepture\yii2logic\models\traits\v2\property;

use Yii;

/**
 * Trait HasDomainByLocalesPropertyTrait
 * @package concepture\yii2logic\models\traits\v2\property
 */
trait HasDomainByLocalesPropertyTrait
{
    use HasPropertyTrait;

    /**
     * Возвращает название поля по которому будет разделение свойств
     *
     * @return array
     */
    public static function uniqueField()
    {
        return [
            "domain_id",
            "locale_id",
        ];
    }

    /**
     * Возвращает значение поля по которому будет разделение свойств
     *
     * @return mixed
     */
    public static function uniqueFieldValue()
    {
        return [
            "domain_id" => Yii::$app->domainService->getCurrentDomainId(),
            "locale_id" => Yii::$app->domainService->getCurrentDomainLocaleId(),
        ];
    }

    /**
     * Поля по которым будут обновляться поля для всех property
     *
     * @return array
     */
    public function propertyGroupUniqueFields()
    {
        return [
            "domain_id"
        ];
    }

    /**
     * Поля которые будут обновлены для всех property по groupUniqueFields
     *
     * @return array
     */
    public function updatedFieldsByPropertyGroup()
    {
        return [

        ];
    }

    /**
     * Загружает в переданную модель поля которые являются общими дял проперти в пределах домена
     *
     * @param $model
     * @throws \Exception
     */
    public function loadUpdatedFieldsToModel($model)
    {
        $propertyClass = static::getPropertyModelClass();
        $property = Yii::createObject($propertyClass);
        foreach ($property->attributes() as $attribute) {
            if (in_array($attribute, static::excludedPropertyFields())) {
                continue;
            }

            if (! in_array($attribute, $this->updatedFieldsByPropertyGroup())) {
                continue;
            }

            $model->{$attribute} = $this->{$attribute};
        }
    }

    /**
     * Обновление общих полей для пропертей по одному и тому же домену
     *
     * @param $property
     */
    public function afterPropertySave($property)
    {
        $groupFields = $this->propertyGroupUniqueFields();
        if (! $groupFields) {
            return;
        }

        $updatedFields = $this->updatedFieldsByPropertyGroup();
        if (! $updatedFields) {
            return;
        }

        $condition = [
            'entity_id' => $property->entity_id
        ];
        foreach ($groupFields as $field) {
            $condition[$field] = $property->{$field};
        }

        $params = [];
        foreach ($updatedFields as $field) {
            $params[$field] = $property->{$field};
        }

        $property::updateAll($params, $condition);
    }
}
