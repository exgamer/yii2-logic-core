<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\enum\CacheTagsEnum;
use Yii;
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
        $class = $this->getRelatedModel();
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

        $query = $this->getQuery();
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

        return $dataProvider;
    }

    /**
     * Получение объекта по идентификатору
     *
     * @param integer $id
     * @param array $with
     * @param bool $asSql - если true запрос будет выполнен как простой sql и вернет обыяный массив данных
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](https://secure.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     *
     * @return ActiveRecord
     */
    public function findById($id , $with = [], $asSql = false, $fetchMode = null)
    {
        return $this->getOneByCondition(function (ActiveQuery $query) use ($id, $with){
            if (! empty($with)){
                $query->with($with);
            }
            $query->andWhere(["{$this->getTableName()}.id" => $id]);
        }, $asSql, $fetchMode);
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
     * @param bool $asSql - если true запрос будет выполнен как простой sql и вернет обыяный массив данных
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](https://secure.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return mixed
     */
    public function getOneByCondition($condition = null, $asSql = false, $fetchMode = null)
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

        if (! $asSql){
            return $query->one();
        }

        $sql = $query->prepare($this->getDb()->queryBuilder)->createCommand()->rawSql;
        $command = $this->createCommand($sql);

        return $command->queryOne($fetchMode);
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
     * @param bool $asSql - если true запрос будет выполнен как простой sql и вернет обыяный массив данных
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](https://secure.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return mixed
     */
    public function getAllByCondition($condition = null, $asSql = false, $fetchMode = null)
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

        if (! $asSql){
            return $query->all();
        }

        $sql = $query->prepare($this->getDb()->queryBuilder)->createCommand()->rawSql;
        $command = $this->createCommand($sql);

        return $command->queryAll($fetchMode);
    }

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

