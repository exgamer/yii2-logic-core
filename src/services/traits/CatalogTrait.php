<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\enum\CacheTagsEnum;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use Yii;
use Exception;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Треит сервиса содержащий методы для реализации методов, которые помогают использовать сущность как справочник
 * Получать записи как массив ключ => значение
 * и ключи и значения из массива
 *
 * Trait CatalogTrait
 * @package concepture\yii2logic\services\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait CatalogTrait
{
    /**
     * Возвращает массив с массивом  записей индексированным по $searchClass::getListSearchKeyAttribute
     * Для использования у search модели должны быть определен метод
     *  getListSearchKeyAttribute
     *
     *
     * @param bool $resetModels - по умолчанию всегда будет делать запрсо на получение всех моделей
     * @return array
     * @throws Exception
     */
    public function modelsCatalog($resetModels = true)
    {
        static $_catalog = null;
        if (! empty($_catalog)){
            return $_catalog;
        }

        $searchClass = $this->getRelatedSearchModelClass();
        $searchKey = $searchClass::getListSearchKeyAttribute();
        $models = ArrayHelper::index( $this->getAllModelsForList(), $searchKey);
        if ($resetModels){
            return $models;
        }

        $_catalog = $models;

        return $_catalog;
    }

    /**
     * Возвращает модель из каталога по ключу
     *
     * @param $id
     * @return ActiveRecord|null
     * @throws Exception
     */
    public function getCatalogModel($id)
    {
        $catalog = $this->modelsCatalog();

        if (isset($catalog[$id])){

            return $catalog[$id];
        }

        return null;
    }

    /**
     * Возвращает массив с каталогом записей
     * Для использования у search модели должны быть определены методы
     * getListSearchAttribute и getListSearchKeyAttribute
     *
     *   таким способом можно вызывать методы модели
     *   например если в модели сделать метод  getLabel()
     *   если метод getListSearchAttribute вернет 'label'
     *   будет вызван метод getLabel() модели
     *
     *
     * @param bool $resetModels - по умолчанию всегда будет делать запрсо на получение modelsCatalog
     * @return array
     * @throws Exception
     */
    public function catalog($resetModels = true)
    {
        static $_catalog = null;
        if (! empty($_catalog)){
            return $_catalog;
        }

        $searchClass = $this->getRelatedSearchModelClass();
        $searchKey = $searchClass::getListSearchKeyAttribute();
        $searchAttr = $searchClass::getListSearchAttribute();
        if (! $searchKey || ! $searchAttr){
            throw new Exception("please realize getListSearchKeyAttribute() and getListSearchAttribute() in ".$searchClass);
        }
        $models = $this->modelsCatalog($resetModels);
        $_catalog = ArrayHelper::map($models, $searchKey , $searchAttr);

        return $_catalog;
    }

    /**
     * Возвращает воличество элементов каталога
     *
     * @return int
     * @throws Exception
     */
    public function catalogCount()
    {
        $catalog = $this->catalog();

        return count($catalog);
    }

    /**
     * Доп действия перед получением ключа по значению
     * @param $value
     * @param $catalog
     * @return mixed
     */
    protected function catalogKeyPreAction(&$value, &$catalog){}

    /**
     * Возвращает ключ из каталога по значению
     * Для использования у search модели должны быть определены методы
     * getListSearchAttribute и getListSearchKeyAttribute
     *
     * @param $value
     * @return mixed|null
     * @throws Exception
     */
    public function catalogKey($value)
    {
        $catalog = $this->catalog();
        $catalog = array_flip($catalog);
        $this->catalogKeyPreAction($value, $catalog);
        if (isset($catalog[$value])){
            return $catalog[$value];
        }

        return null;
    }

    /**
     * Возвращает значение из каталога по ключу
     * Для использования у search модели должны быть определены методы
     * getListSearchAttribute и getListSearchKeyAttribute
     *
     * @param $key
     * @return mixed|null
     * @throws Exception
     */
    public function catalogValue($key)
    {
        $catalog = $this->catalog();
        if (isset($catalog[$key])){
            return $catalog[$key];
        }

        return null;
    }

    /**
     * Возвращает данные для исползования с виджетом \yii\jui\AutoComplete
     *
     *   таким способом можно вызывать методы модели
     *   например если в модели сделать метод  getLabel()
     *   если метод getListSearchAttribute вернет 'label'
     *   будет вызван метод getLabel() модели
     *
     * @param null $term
     * @return array []
     * @throws Exception
     */
    public function getAutocompleteList($term = null)
    {
        if(!$term){
            return [];
        }

        $searchClass = $this->getRelatedSearchModelClass();
        $tableName = $searchClass::tableName();
        $searchKey = $searchClass::getListSearchKeyAttribute();
        $searchAttr = $searchClass::getListSearchAttribute();
        if (! $searchKey || ! $searchAttr){
            throw new Exception("please realize getListSearchKeyAttribute() and getListSearchAttribute() in ".$searchClass);
        }
        $where = [];
        $model = new $searchClass();
        if ($model->hasAttribute('status')){
            $where['status'] = StatusEnum::ACTIVE;
        }
        if ($model->hasAttribute('is_deleted')){
            $where['is_deleted'] = IsDeletedEnum::NOT_DELETED;
        }

        $query = $this->getQuery()
            ->select(["{$tableName}.{$searchAttr} as value", "{$tableName}.{$searchAttr} as  label","{$tableName}.{$searchKey} as id"])
            ->andWhere(['like', $searchAttr, $term])
            ->andWhere($where)
            ->asArray();
        $this->extendCatalogTraitQuery($query);

        return $query->all();
    }

    /**
     * Возвращает массив даных для выпадающих списков
     * Для использования у search модели должны быть определены методы
     * getListSearchAttribute и getListSearchKeyAttribute
     *
     *   таким способом можно вызывать методы модели
     *   например если в модели сделать метод  getLabel()
     *   если метод getListSearchAttribute вернет 'label'
     *   будет вызван метод getLabel() модели
     *
     *
     * @param array $queryParams
     * @param string $formName
     * @return array
     * @throws Exception
     */
    public function getDropDownList($queryParams = [], $formName = "")
    {
        $searchClass = $this->getRelatedSearchModelClass();
        $searchModel = Yii::createObject($searchClass);
        $searchAttribute = $searchClass::getListSearchAttribute();
        if (! $searchAttribute){
            throw new Exception("please realize  getListSearchAttribute() in ".$searchClass);
        }
        if ($searchModel->hasAttribute('status')) {
            $queryParams['status'] = StatusEnum::ACTIVE;
        }
        if ($searchModel->hasAttribute('is_deleted')) {
            $queryParams['is_deleted'] = IsDeletedEnum::NOT_DELETED;
        }
        $dataProvider = $this->getDataProvider($queryParams, [], null, $formName);

        return ArrayHelper::map($dataProvider->getModels(), $searchClass::getListSearchKeyAttribute(), $searchAttribute);
    }


    /**
     * Для расширения запроса для вывода каталога и списка для выпадашек
     *
     * @param ActiveQuery $query
     */
    protected function extendCatalogTraitQuery(ActiveQuery $query){}

    /**
     * Возвращает массив моделей для выпадающих списков
     *
     * @param array $where
     * @param bool $excludeDefault
     * @return mixed
     */
    public function getAllModelsForList($where = [], $excludeDefault = false)
    {
        if ($excludeDefault === false) {
            $modelClass = $this->getRelatedModelClass();
            $model = Yii::createObject($modelClass);
            if ($model->hasAttribute('status')) {
                $where['status'] = StatusEnum::ACTIVE;
            }
            if ($model->hasAttribute('is_deleted')) {
                $where['is_deleted'] = IsDeletedEnum::NOT_DELETED;
            }
        }

        $query = $this->getQuery()->andWhere($where);
        $this->extendCatalogTraitQuery($query);

        return $query->all();
    }

    /**
     * Возвращает массив записей таблицы для выпадающих списков
     *
     *   таким способом можно вызывать методы модели
     *   например если в модели сделать метод  getLabel()
     *   если метод getListSearchAttribute вернет 'label'
     *   будет вызван метод getLabel() модели
     *
     * @param string $from
     * @param string $to
     * @param array $where
     * @param boolean $excludeDefault
     * @return mixed
     */
    public function getAllList($from = 'id', $to, $where = [], $excludeDefault = false)
    {
        $models = $this->getAllModelsForList($where, $excludeDefault);

        return ArrayHelper::map($models, $from , $to);
    }
}