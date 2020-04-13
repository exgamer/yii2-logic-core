<?php

namespace concepture\yii2logic\models;

use concepture\yii2logic\converters\LocaleConverter;

/**
 * Базовая модель для сущности с локализацией
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class LocalizedActiveRecord extends ActiveRecord
{
    use \concepture\yii2logic\models\traits\HasLocalizationTrait;

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->saveLocalizations();

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritDoc
     */
    public function beforeDelete()
    {
        $this->deleteLocalizations();

        return parent::beforeDelete();
    }

    /**
     * @return mixed
     */
    public static function getLocaleConverterClass()
    {
        return LocaleConverter::class;
    }
}