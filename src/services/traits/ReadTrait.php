<?php
namespace concepture\yii2logic\services\traits;

use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Треит сервиса содержащий методы для чтения данных
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
}

