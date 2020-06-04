<?php

namespace concepture\yii2logic\db;

use Yii;

class HasPropertyActiveQuery extends ActiveQuery
{
    /**
     * переустановка условия для выборки проперти с учетом уникального поля
     * для моделей где подключен HasPropertyTrait
     *
     * @param $value
     */
    public function applyPropertyUniqueValue($value)
    {
        $modelClass = $this->modelClass;
        $modelClass::setPropertyJoinQuery($this, $value);
    }
}
