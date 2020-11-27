<?php

namespace concepture\yii2logic\models;

use concepture\yii2logic\models\interfaces\HasJsonFieldInterface;
use concepture\yii2logic\models\traits\ToJsonAttributesTrait;

/**
 * Class PropertyWithJsonActiveRecord
 * @package concepture\yii2logic\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
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
