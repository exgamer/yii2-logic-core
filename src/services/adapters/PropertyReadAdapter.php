<?php

namespace concepture\yii2logic\services\adapters;

use concepture\yii2logic\data\ActiveDataProvider;
use concepture\yii2logic\services\traits\HasDbConnectionTrait;
use concepture\yii2logic\services\traits\ReadTrait;
use concepture\yii2logic\services\traits\SqlReadTrait;
use Exception;
use Yii;
use yii\base\Component;
use yii\db\ActiveQuery;

/**
 * Адаптер для работы с проперти только чтение
 *
 * Class PropertyAdapter
 * @package concepture\yii2logic\services\adapters
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PropertyReadAdapter extends Component
{
    use ReadTrait;
    use SqlReadTrait;
    use HasDbConnectionTrait;

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

    /**
     *
     * @param array $queryParams
     * @param array $config
     * @param null $searchModel
     * @param null $formName
     * @param null $condition
     * @throws Exception
     */
    public function getDataProvider($queryParams = [], $config = [], $searchModel = null, $formName = null, $condition = null)
    {
        if ($searchModel === null) {
            $searchModel = Yii::createObject($this->propertyModelClass);
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
        $this->extendDataProviderModels($dataProvider);

        return $dataProvider;
    }

    /**
     * @deprecated метод не поддерживается
     *
     * @param $id
     * @param array $with
     * @param bool $asArray
     * @return mixed
     * @throws Exception
     *
     */
    public function findById($id , $with = [], $asArray = false)
    {
        throw new Exception("unsupported method, use getOneByCondition");
    }

    /**
     * Получить класс связанной модели
     *
     * @throws Exception
     */
    public function getRelatedModelClass()
    {
        return $this->propertyModelClass;
    }

    /**
     * @deprecated метод не поддерживается
     *
     * @return string
     * @throws Exception
     *
     * Получить класс связанной формы
     *
     */
    public function getRelatedFormClass()
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * Получить класс связанной search модели
     *
     * @throws Exception
     */
    public function getRelatedSearchModelClass()
    {
        throw new Exception("unsupported method");
    }


    /**
     * @deprecated метод не поддерживается
     *
     * Получить новый обьект формы
     *
     * @throws Exception
     */
    public function getRelatedForm()
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * Получить новый обьект серч формы
     *
     * @throws Exception
     */
    public function getRelatedSearchModel()
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * @param $model
     * @throws Exception
     */
    protected function getEntityService($model)
    {
        throw new Exception("unsupported method");
    }

    /**
     * @deprecated метод не поддерживается
     *
     * @param $tableName
     * @throws Exception
     */
    protected function getServiceByEntityTable($tableName)
    {
        throw new Exception("unsupported method");
    }
}
