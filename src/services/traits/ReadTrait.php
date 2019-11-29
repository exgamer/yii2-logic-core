<?php
namespace concepture\yii2logic\services\traits;

use yii\data\ActiveDataProvider;
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
     * !! ВНимание эти данные будут поставлены в find по умолчанию все всех случаях
     *
     * @param ActiveQuery $query
     */
    protected function extendQuery(ActiveQuery $query){}

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
        $query = $searchClass::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $searchModel->load($queryParams);
        if (!$searchModel->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');

            return $dataProvider;
        }

        $searchModel->extendQuery($query);
        $searchModel->extendDataProvider($dataProvider);

        return $dataProvider;
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

