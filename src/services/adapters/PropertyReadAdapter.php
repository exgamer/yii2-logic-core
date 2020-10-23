<?php

namespace concepture\yii2logic\services\adapters;

use concepture\yii2logic\services\traits\HasDbConnectionTrait;
use concepture\yii2logic\services\traits\ReadTrait;
use concepture\yii2logic\services\traits\SqlReadTrait;
use Exception;
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
     * @deprecated метод пока не поддерживается
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
        throw new Exception("unsupported method");
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
