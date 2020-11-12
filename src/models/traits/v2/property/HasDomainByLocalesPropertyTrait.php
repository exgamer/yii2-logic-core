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
    public function groupUniqueFields()
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
    public function updatedFieldsByGroup()
    {
        return [

        ];
    }

    /**
     * Обновление общих полей для пропертей по одному и тому же домену
     *
     * @param $property
     */
    public function afterPropertySave($property)
    {
        $groupFields = $this->groupUniqueFields();
        if (! $groupFields) {
            return;
        }

        $updatedFields = $this->updatedFieldsByGroup();
        if (! $updatedFields) {
            return;
        }

        $condition = [];
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
