<?php
namespace concepture\yii2logic\models\traits\v2\property;

use Yii;

/**
 * Trait HasDomainPropertyTrait
 * @package common\models\traits
 */
trait HasDomainPropertyTrait
{
    use HasPropertyTrait;

    /**
     * Возвращает название поля по которому будет разделение свойств
     *
     * @return string
     */
    public static function uniqueField()
    {
        return "domain_id";
    }

    /**
     * Возвращает значение поля по которому будет разделение свойств
     *
     * @return mixed
     */
    public static function uniqueFieldValue()
    {
        return Yii::$app->domainService->getCurrentDomainId();
    }
}