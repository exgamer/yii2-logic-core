<?php
namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\helpers\StringHelper;
use yii\helpers\Json;

/**
 * Треит для работы с тадицами где данные хранятся в json поле
 *
 * Trait ToJsonAttributesTrait
 * @package concepture\yii2logic\models\traits
 */
trait ToJsonAttributesTrait
{
    /**
     * Возвращает название поля в котором хранится json
     * 
     * @return string
     */
    public function jsonFieldName()
    {
        return 'json';
    }

    /**
     * Возвращает массив атрибутов которые хранятся в json
     * 
     * @return array
     */
    public function toJsonAttributes()
    {
        return [];
    }

    /**
     * переопределно для того чтобы виртуальные on атрибуты воспринимальись как обычные
     *
     * @return array
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), $this->toJsonAttributes());
    }

    public function beforeSave($insert)
    {
        // сначала parent::beforeSave($insert) для того чтобы отработали поведения
        parent::beforeSave($insert);
        //а после готовые данные засовываем в json поле
        $jsonFieldName = $this->jsonFieldName();
        $toJsonAttrs = $this->toJsonAttributes();
        $json = [];
        foreach ($toJsonAttrs as $attr) {
            $json[$attr] = $this->{$attr};
        }

        $this->{$jsonFieldName} = $json;
        $dbType = $this->getAttrDbType($jsonFieldName);
        if (! in_array($dbType, ['json', 'jsonb'])) {
            $this->{$jsonFieldName} = Json::encode($this->{$jsonFieldName});
        }

        return true;
    }

    protected function cretateVirtualAttributesByJsonField()
    {
        //сначала разбираем json поле и распихиваем данные по виртуальным атрибутам
        $jsonFieldName = $this->jsonFieldName();
        $dbType = $this->getAttrDbType($jsonFieldName);
        if (! in_array($dbType, ['json', 'jsonb'])) {
            $this->{$jsonFieldName} = Json::decode($this->{$jsonFieldName}, true) ?? [];
        }

        if (!$this->{$jsonFieldName}) {
            $this->{$jsonFieldName} = [];
        }

        foreach ($this->{$jsonFieldName} as $attribute => $value) {
            $this->{$attribute} = $value;
        }
    }

    public function afterFind()
    {
        $this->cretateVirtualAttributesByJsonField();

        //потом обработка
        parent::afterFind(); // TODO: Change the autogenerated stub
    }


    /**
     *  Перед сохранением удаляем из атрибутов свойства которых нет в бд
     *
     * @param bool $runValidation
     * @param null $attributes
     * @return bool
     * @throws \Throwable
     */
    public function insert($runValidation = true, $attributes = null)
    {
        if (! $attributes) {
            $attributes = $this->attributes();
            foreach ($attributes as $key => $attribute) {
                if (in_array($attribute, $this->toJsonAttributes())) {
                    unset($attributes[$key]);
                }
            }
        }

        return parent::insert($runValidation, $attributes);
    }

    /**
     *  Перед сохранением удаляем из атрибутов свойства которых нет в бд
     *
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws \Throwable
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        if (! $attributeNames) {
            $attributeNames = $this->attributes();
            foreach ($attributeNames as $key => $attribute) {
                if (in_array($attribute, $this->toJsonAttributes())) {
                    unset($attributeNames[$key]);
                }
            }
        }

        return parent::update($runValidation, $attributeNames);
    }
}

