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
        $query = $class::find();
        $this->extendQuery($query);

        return $query;
    }

    /**
     * Метод для расширения find()
     *
     * @param ActiveQuery $query
     */
    private function extendQuery(ActiveQuery $query)
    {
        $extendFindCondition = $this->extendFindCondition();
        if (empty($extendFindCondition) || ! is_array($extendFindCondition)){
            return;
        }

        foreach ($extendFindCondition as $condition){
            $query->andWhere($condition);
        }
    }

    /**
     * Возвращает массив для автоматической подстановки в запрос
     * !! ВНимание эти данные будут поставлены в find по умолчанию все всех случаях
     *
     * [
     *       ['domain_id' => [3, null]],
     *       [locale' => 1],
     *      'is_deleted = 0',
     *      ['is_deleted = :is_deleted', [':is_deleted' => 0]],
     * ]
     *
     * @return array
     */
    protected function extendFindCondition()
    {
        return [];
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

