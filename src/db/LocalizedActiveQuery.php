<?php

namespace concepture\yii2logic\db;

use yii\db\ActiveQuery as Base;
use yii\db\Connection;

/**
 * ActiveQuery для сущностей с локализациями
 *
 * Class LocalizedActiveQuery
 * @package concepture\yii2logic\db
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocalizedActiveQuery extends Base
{
    /**
     * Переопределено для сброса статических переменных модели
     *
     * @param Connection $db
     * @return array|\yii\db\ActiveRecord[]
     */
    public function all($db = null)
    {
        $result = parent::all($db);
        $this->resetModel();

        return $result;
    }

    /**
     * Переопределено для сброса статических переменных модели
     *
     * @param Connection $db
     * @return array|\yii\db\ActiveRecord|null
     */
    public function one($db = null)
    {
        $row = parent::one($db);
        $this->resetModel();
        
        return $row;
    }

    /**
     * Сброс статических свойств модели
     */
    protected function resetModel()
    {
        $modelClass = $this->modelClass;
        $modelClass::$search_by_locale_callable = null;
        $modelClass::$by_locale_hard_search = true;
        $modelClass::$current_locale = null;
    }
}
