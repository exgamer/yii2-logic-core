<?php

namespace concepture\yii2logic\models;

use concepture\yii2logic\models\interfaces\HasJsonFieldInterface;
use concepture\yii2logic\models\traits\ToJsonAttributesTrait;

/**
 * Доменные свойства постов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class PropertyWithJsonActiveRecord extends ActiveRecord implements HasJsonFieldInterface
{
    use ToJsonAttributesTrait;

    /**
     * Возвращает массив атрибутов которые хранятся в json
     *
     * @return array
     */
    public function toJsonAttributes()
    {
        return [

        ];
    }
}
