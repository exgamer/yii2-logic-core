<?php
namespace concepture\yii2logic\services\traits;

use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Trait ReadTrait
 * @package concepture\yii2logic\services\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait ReadTrait
{
    /**
     * Возвращает QueryBuilder
     *
     * @return ActiveQuery
     */
    public function getQuery()
    {
        $class = $this->getRelatedModelClass();

        return $class::find();
    }

    /**
     * Возвращает DataProvider
     *
     * @param array $queryParams
     * @return mixed
     */
    public function getDataProvider($queryParams = [])
    {
        $searchClass = $this->getRelatedSearchModelClass();
        $searchModel = new $searchClass();

        return $searchModel->search($queryParams);
    }

    /**
     * Получение объекта по идентификатору
     *
     * @param integer $id
     * @param array $with
     *
     * @return ActiveRecord
     */
    public function findById($id , $with = [])
    {
        $q = $this->getQuery();
        if (! empty($with)){
            $q->with($with);
        }
        $q->where(["{$this->getTableName()}.id" => $id]);

        return $q->one();
    }

    /**
     * Возвращает одну запись
     *
     * Пример расширения запроса через $callback
     *
     * function(ActiveQuery $query) {
     *       $query->andWhere("object_type = :object_type", [':object_type' => 2]);
     * }
     *
     * @param array|callable $condition
     * @return mixed
     */
    public function getOneByCondition($condition = null)
    {
        $query = $this->getQuery();
        if (is_callable($condition)){
            call_user_func($condition, $query);
        }
        if (is_array($condition)){
            foreach ($condition as $name => $value){
                $query->andWhere([$name => $value]);
            }
        }

        return $query->one();
    }

    /**
     * Возвращает массив записей
     *
     * Пример расширения запроса через $callback
     *
     * function(ActiveQuery $query) {
     *       $query->andWhere("object_type = :object_type", [':object_type' => 2]);
     * }
     * @param array|callable $condition
     * @return mixed
     */
    public function getAllByCondition($condition = null)
    {
        $query = $this->getQuery();
        if (is_callable($condition)){
            call_user_func($condition, $query);
        }
        if (is_array($condition)){
            foreach ($condition as $name => $value){
                $query->andWhere([$name => $value]);
            }
        }

        return $query->all();
    }

    /**
     * Возвращает данные для исползования с виджетом \yii\jui\AutoComplete
     *
     * @param null $term
     * @return array []
     */
    public function getAutocompleteList($term = null)
    {
        if(!$term){
            return [];
        }

        $searchClass = $this->getRelatedSearchModelClass();
        $searchKey = $searchClass::getListSearchKeyAttribute();
        $searchAttr = $searchClass::getListSearchAttribute();
        $data = $this->getQuery()
            ->select(["{$searchAttr} as value", "{$searchAttr} as  label","{$searchKey} as id"])
            ->where(['like', $searchAttr, $term])
            ->asArray()
            ->all();

        return $data;
    }

    /**
     * Возвращает массив даных для выпадающих списков
     * Для использования у search модели должны быть определены методы
     * getListSearchAttribute и getListSearchKeyAttribute
     *
     * @param array $queryParams
     * @return array
     */
    public function getDropDownList($queryParams = [])
    {
        $searchClass = $this->getRelatedSearchModelClass();
        $searchModel = new $searchClass();
        $searchAttribute = $searchClass::getListSearchAttribute();
        $dataProvider = $searchModel->search($queryParams);

        return ArrayHelper::map($dataProvider->getModels(), $searchClass::getListSearchKeyAttribute(), $searchAttribute);
    }

    /**
     * Возвращает массив записей таблицы для выпадающих списков
     *
     * @param string $from
     * @param $to
     * @param array $where
     * @return mixed
     */
    public function getAllList($from = 'id', $to, $where = [])
    {
        $models = $this->getQuery()->where($where)->all();

        return ArrayHelper::map($models, $from , $to);
    }
}

