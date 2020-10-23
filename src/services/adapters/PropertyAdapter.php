<?php

namespace concepture\yii2logic\services\adapters;

use concepture\yii2logic\services\traits\ReadTrait;
use yii\base\Component;
use yii\db\ActiveQuery;

/**
 * Адаптер для работы с проперти
 *
 * Class PropertyAdapter
 * @package concepture\yii2logic\services\adapters
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PropertyAdapter extends Component
{
    use ReadTrait;

    public $propertyModelClass;

    /**
     * Возвращает QueryBuilder
     *
     * @return ActiveQuery
     */
    public function getQuery()
    {
        $propertyModelClass = $this->propertyModelClass;
        $query = $propertyModelClass::find();
        $this->extendQuery($query);

        return $query;
    }
}
