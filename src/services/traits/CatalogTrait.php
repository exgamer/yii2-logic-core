<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\models\traits\HasLocalizationTrait;
use concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait;
use concepture\yii2logic\models\traits\v2\property\HasLocalePropertyTrait;
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
     * @param bool $excludeDefault
     * @param null $searchKey
     * @return array
     */
    public function modelsCatalog($excludeDefault = false, $searchKey = null, $condition = null)
    {
        if (! $searchKey) {
            $searchClass = $this->getRelatedSearchModelClass();
            $searchKey = $searchClass::getListSearchKeyAttribute();
        }

        return ArrayHelper::index( $this->getAllModelsForList($condition, $excludeDefault), $searchKey);
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
     * @param string $from
     * @param string $to
     *
     * function(ActiveQuery $query) {
     *       $query->andWhere("object_type = :object_type", [':object_type' => 2]);
     * }
     * @param array|callable $condition
     * @param bool $excludeDefault
     * @param bool $resetModels - по умолчанию всегда будет делать запрсо на получение modelsCatalog
     * @return array
     * @throws Exception
     */
    public function catalog($from = null, $to = null, $condition = null, $excludeDefault = false, $resetModels = true)
    {
        /**
         * @todo сделать ключ для статики если передан $condition
         * но там может быть массив и анонимка
         */
        static $_catalog = null;
        static $class;

        if($class !== static::class) {
            $_catalog = null;
            $class = null;
        }

        if(! $class) {
            $class = static::class;
        }

        $searchClass = $this->getRelatedSearchModelClass();
        if (! $from && ! $to){
            $from = $searchClass::getListSearchKeyAttribute();
            $to = $searchClass::getListSearchAttribute();
        }

        if (! $from || ! $to){
            throw new Exception("please realize getListSearchKeyAttribute() and getListSearchAttribute()  ".$searchClass . ' OR pass $from and $to in method');
        }

        $models =  $_catalog;
        if (empty($models) || ! $resetModels || $condition !== null){
            $models = $this->modelsCatalog($excludeDefault, $from, $condition);
            $_catalog = $models;
        }

        return ArrayHelper::map($models, $from , $to);
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
     * @param null $from
     * @param null $to
     * function(ActiveQuery $query) {
     *       $query->andWhere("object_type = :object_type", [':object_type' => 2]);
     * }
     * @param array|callable $condition
     * @return mixed|null
     * @throws Exception
     */
    public function catalogKey($value, $from = null, $to = null, $condition = null)
    {
        $catalog = $this->catalog( $from, $to, $condition, false, true);
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
     * @param string $from
     * @param string $to
     * function(ActiveQuery $query) {
     *       $query->andWhere("object_type = :object_type", [':object_type' => 2]);
     * }
     * @param array|callable $condition
     * @return mixed|null
     * @throws Exception
     */
    public function catalogValue($key, $from = null, $to = null, $condition = null)
    {
        $catalog = $this->catalog($from, $to, $condition, false, true);
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

        $query = $this->getQuery();
        if (is_array($searchAttr)){
            $string = 'CONCAT(';
            $a = [];
            foreach ($searchAttr as $attr){
                $a[] = $this->processFieldName($attr, $tableName);
            }
            $string .= implode(', " ",', $a);
            $string .= ')';
            $query->select(["{$string} as value", "{$string} as  label", "{$tableName}.{$searchKey} as id"]);
        }else {
            $query->select([$this->processFieldName($searchAttr, $tableName) . " as value", $this->processFieldName($searchAttr, $tableName) . " as  label", "{$tableName}.{$searchKey} as id"]);
        }

        $attributes = $searchClass::getListSearchAttributes();
        if (! empty($attributes)){
            $query->addSelect(implode(', ', $attributes));
        }

        if (is_array($searchAttr)){
            foreach ($searchAttr as $attr){
                $query->orWhere(['like', $attr, $term]);
            }
        }else{
            $query->andWhere(['like', $searchAttr, $term]);
        }

        $query->andWhere($where)
            ->asArray();
        $this->extendCatalogTraitQuery($query);

        return $query->all();
    }

    protected function processFieldName($fieldName, $tableName)
    {
        if (strripos($fieldName, '.')){
            return $fieldName;
        }

        return $tableName . "." . $fieldName;
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
     * @param null $condition
     * @return array
     * @throws Exception
     */
    public function getDropDownList($queryParams = [],  $formName = "", $condition = null)
    {
        $searchClass = $this->getRelatedSearchModelClass();
        $searchModel = Yii::createObject($searchClass);
        $searchAttribute = $searchClass::getListSearchAttribute();
        if (! $searchAttribute){
            throw new Exception("please realize  getListSearchAttribute() in ".$searchClass);
        }

        if ($searchModel->hasAttribute('status')) {
            $queryParams[$searchClass::tableName() . '.status'] = StatusEnum::ACTIVE;
        }

        if ($searchModel->hasAttribute('is_deleted')) {
            $queryParams[$searchClass::tableName() . '.is_deleted'] = IsDeletedEnum::NOT_DELETED;
        }

        $dataProvider = $this->getDataProvider($queryParams, [], null, $formName, $condition);

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
     * @param array $condition
     * @param bool $excludeDefault
     * @return mixed
     */
    public function getAllModelsForList($condition = null, $excludeDefault = false)
    {
        $where = [];
        if ($excludeDefault === false) {
            $modelClass = $this->getRelatedModelClass();
            $model = Yii::createObject($modelClass);
            /**
             * следующие 2 переменные костыли. я торопился
             */
            $statusAlias = "";
            $deletedAlias = "";
            $traits = ClassHelper::getTraits($model);
            /**
             * Потому что статус и is_deleted могут быть в локализациях
             */
            if (in_array(HasDomainPropertyTrait::class, $traits) ||
                in_array(HasLocalePropertyTrait::class, $traits)){
                $propModelClass = $model::getPropertyModelClass();
                $propModel = Yii::createObject($propModelClass);
                if ($propModel->hasAttribute('status')){
                    $statusAlias = $model::propertyAlias() . ".";
                }

                if ($propModel->hasAttribute('is_deleted')){
                    $deletedAlias = $model::propertyAlias() . ".";
                }
            }

            if (in_array(HasLocalizationTrait::class, $traits)){
                $propModelClass = $model::getLocalizationModelClass();
                $propModel = Yii::createObject($propModelClass);
                if ($propModel->hasAttribute('status')){
                    $statusAlias = $model::localizationAlias() . ".";
                }

                if ($propModel->hasAttribute('is_deleted')){
                    $deletedAlias = $model::propertyAlias() . ".";
                }
            }

            if ($model->hasAttribute('status')) {
                $where[$statusAlias . 'status'] = StatusEnum::ACTIVE;
            }
            if ($model->hasAttribute('is_deleted')) {
                $where[$deletedAlias . 'is_deleted'] = IsDeletedEnum::NOT_DELETED;
            }
        }

        $query = $this->getQuery();
        if (! empty($where)) {
            $query->andWhere($where);
        }

        if (is_callable($condition)){
            call_user_func($condition, $query);
        }

        if (is_array($condition)){
            foreach ($condition as $name => $value){
                $query->andWhere([$name => $value]);
            }
        }
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