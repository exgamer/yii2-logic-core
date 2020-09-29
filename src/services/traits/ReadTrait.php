<?php
namespace concepture\yii2logic\services\traits;

use Yii;
use concepture\yii2logic\data\ActiveDataProvider;
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
     * @param null $model
     * @return ActiveQuery
     */
    public function getQuery($model = null)
    {
        if (! $model) {
            $class = $this->getRelatedModel();
            $query = $class::find();
        }else {
            $query = $model::find();
        }

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
     *
     * @param array $queryParams
     *
     * Конфиг DataProvider
     * @param array $config
     * @param ActiveRecord $searchModel
     * @param string $formName
     *
     * function(ActiveQuery $query) {
     *       $query->andWhere("object_type = :object_type", [':object_type' => 2]);
     * }
     *
     * @param callable|array $condition
     *
     * @return ActiveDataProvider
     */
    public function getDataProvider($queryParams = [], $config = [], $searchModel = null, $formName = null, $condition = null)
    {
        if ($searchModel === null) {
            $searchModel = $this->getRelatedSearchModel();
        }

        $query = $this->getQuery($searchModel);
        if (is_callable($condition)){
            call_user_func($condition, $query);
        }
        if (is_array($condition)){
            foreach ($condition as $name => $value){
                $query->andWhere([$name => $value]);
            }
        }

        if (! isset($config['query'])) {
            $config['query'] = $query;
        }

        $dataProvider = new ActiveDataProvider($config);
        if (! empty($queryParams)) {
            $searchModel->load($queryParams, $formName);
        }
        if (!$searchModel->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->andWhere('0=1');

            return $dataProvider;
        }

        $searchModel->extendQuery($query);
        $searchModel->extendDataProvider($dataProvider);
        $this->extendDataProviderModels($dataProvider);

        return $dataProvider;
    }

    /**
     * Получение объекта по идентификатору
     *
     * @param integer $id
     * @param array $with
     * @param bool $asArray - если true запрос будет выполнен как простой sql и вернет обыяный массив данных
     *
     * @return ActiveRecord
     */
    public function findById($id , $with = [], $asArray = false)
    {
        return $this->getOneByCondition(function (ActiveQuery $query) use ($id, $with){
            if (! empty($with)){
                $query->with($with);
            }
            $query->andWhere(["{$this->getTableName()}.id" => $id]);
        }, $asArray);
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
     * @param bool $asArray - если true запрос будет выполнен как простой sql и вернет обыяный массив данных
     * @return mixed
     */
    public function getOneByCondition($condition = null, $asArray = false)
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

        if ($asArray){
            $query->asArray();
        }

        $model = $query->one();
        $this->extendModel($model);

        return $model;
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
     * @param bool $asArray - если true запрос будет выполнен как простой sql и вернет обыяный массив данных
     * @return mixed
     */
    public function getAllByCondition($condition = null, $asArray = false)
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

        if ($asArray){
            $query->asArray();
        }

        $models = $query->all();
        $this->extendModels($models);

        return $models;
    }

    /**
     * Дополнение данными моделей дата провайдера
     *
     * @param \yii\data\ActiveDataProvider $dataProvider
     */
    public function extendDataProviderModels(\yii\data\ActiveDataProvider $dataProvider)
    {
        $models = $dataProvider->getModels();
        $this->extendModels($models);
        $dataProvider->setModels($models);
    }

    /**
     * Дополненеи данными списка моделей
     *
     * @param $models
     */
    public function extendModels(&$models){}

    /**
     * Дополнение данными модели
     *
     * @param $model
     */
    public function extendModel(&$model){}

    /**
     * Возвращает количество записей
     *
     * @param null $condition
     * @return int|string
     */
    public function getCount($condition = null)
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

        return $query->count();
    }
}

