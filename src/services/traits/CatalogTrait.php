<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
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
     * @return array
     * @throws Exception
     */
    public function catalog()
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

        $_catalog = $this->getAllList($searchKey, $searchAttr);

        return $_catalog;
    }

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

        $data = $this->getQuery()
            ->select(["{$searchAttr} as value", "{$searchAttr} as  label","{$searchKey} as id"])
            ->where(['like', $searchAttr, $term])
            ->andWhere($where)
            ->asArray()
            ->all();

        return $data;
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
     * @return array
     * @throws Exception
     */
    public function getDropDownList($queryParams = [])
    {
        $searchClass = $this->getRelatedSearchModelClass();
        $searchModel = new $searchClass();
        $searchAttribute = $searchClass::getListSearchAttribute();
        if (! $searchAttribute){
            throw new Exception("please realize  getListSearchAttribute() in ".$searchClass);
        }
        $dataProvider = $searchModel->search($queryParams);

        return ArrayHelper::map($dataProvider->getModels(), $searchClass::getListSearchKeyAttribute(), $searchAttribute);
    }


    /**
     * Для расширения запроса для вывода каталога и списка для выпадашек
     *
     * @param ActiveQuery $query
     */
    public function extendCatalogTraitQuery(ActiveQuery $query)
    {

    }

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
            $model = new $modelClass();
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
     * @param $to
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