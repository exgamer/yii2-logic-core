<?php
namespace concepture\yii2logic\models\traits\v2\property;

use Yii;

/**
 * Trait HasLocalePropertyTrait
 * @package common\models\traits
 */
trait HasLocalePropertyTrait
{
    use HasPropertyTrait;

    /**
     * Возвращает название поля по которому будет разделение свойств
     *
     * @return string
     */
    public static function uniqueField()
    {
        return "locale_id";
    }

    /**
     * Возвращает значение поля по которому будет разделение свойств
     *
     * @return mixed
     */
    public static function uniqueFieldValue()
    {
        return Yii::$app->localeService->getCurrentLocaleId();
    }
}