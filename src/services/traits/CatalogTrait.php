<?php
namespace concepture\yii2logic\services\traits;

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
     * переменная для хранения списка записей
     *
     * @var array
     */
    protected static $_catalog = [];

    /**
     * Возвращает массив с каталогом записей
     *
     * @return array
     */
    public function catalog()
    {
        if (! empty(static::$_catalog)){
            return static::$_catalog;
        }

        $searchClass = $this->getRelatedSearchModelClass();
        $searchKey = $searchClass::getListSearchKeyAttribute();
        $searchAttr = $searchClass::getListSearchAttribute();
        static::$_catalog = $this->getAllList($searchKey, $searchAttr);

        return static::$_catalog;
    }

    /**
     * Возвращает ключ из каталога по значению
     *
     * @param $value
     * @return mixed|null
     */
    public function getCatalogKey($value)
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
     *
     * @param $key
     * @return mixed|null
     */
    public function getCatalogValue($key)
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
     * @param null $term
     * @return array []
     */
    public function getAutocompleteList($term = null)
    {
        if(!$term){
            return [];
        }

        $searchClass = $this->getRelatedSearchModelClass();
        $searchKey = $searchClass::getListSearchKeyAttribute();
        $searchAttr = $searchClass::getListSearchAttribute();
        $data = $this->getQuery()
            ->select(["{$searchAttr} as value", "{$searchAttr} as  label","{$searchKey} as id"])
            ->where(['like', $searchAttr, $term])
            ->asArray()
            ->all();

        return $data;
    }

    /**
     * Возвращает массив даных для выпадающих списков
     * Для использования у search модели должны быть определены методы
     * getListSearchAttribute и getListSearchKeyAttribute
     *
     * @param array $queryParams
     * @return array
     */
    public function getDropDownList($queryParams = [])
    {
        $searchClass = $this->getRelatedSearchModelClass();
        $searchModel = new $searchClass();
        $searchAttribute = $searchClass::getListSearchAttribute();
        $dataProvider = $searchModel->search($queryParams);

        return ArrayHelper::map($dataProvider->getModels(), $searchClass::getListSearchKeyAttribute(), $searchAttribute);
    }

    /**
     * Возвращает массив записей таблицы для выпадающих списков
     *
     * @param string $from
     * @param $to
     * @param array $where
     * @return mixed
     */
    public function getAllList($from = 'id', $to, $where = [])
    {
        $models = $this->getQuery()->where($where)->all();

        return ArrayHelper::map($models, $from , $to);
    }
}

