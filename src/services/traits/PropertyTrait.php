<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use Yii;

/**
 * Треит для подключения к сервисам которые используют модель со свойствами
 *
 * Trait PropertyTrait
 * @package concepture\yii2logic\services\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait PropertyTrait
{
    /**
     * Получить сущность со свойствами по md5 hash seo_name
     *
     * @param $seo_name
     * @return array
     */
    public function getByPropertySeoNameMd5Hash($seo_name)
    {

        return $this->getByProperty("seo_name_md5_hash", md5($seo_name));
    }

    /**
     * Получить локализованную сущность по локализованному аттрибуту
     *
     * @param $attribute
     * @param $value
     * @return array
     */
    public function getByProperty($attribute, $value)
    {
        $modelClass = $this->getRelatedModelClass();
        $propertyAlias = $modelClass::propertyAlias();

        return $this->getOneByCondition([
            "{$propertyAlias}." . $attribute => $value
        ]);
    }

    /**
     * Возвращает данные для исползования с виджетом \yii\jui\AutoComplete
     *
     * @param null $term
     * @return array []
     * @throws Exception
     */
    public function getLocalizedAutocompleteList($term = null)
    {
        if(!$term){
            return [];
        }

        $searchClass = $this->getRelatedSearchModelClass();
        $propertyAlias = $searchClass::propertyAlias();
        $searchKey = $searchClass::getListSearchKeyAttribute();
        $searchAttr = $searchClass::getListSearchAttribute();
        if (! $searchKey || ! $searchAttr){
            throw new Exception("please realize getListSearchKeyAttribute() and getListSearchAttribute() in ".$searchClass);
        }
        $tableName = $this->getTableName();
        $where = [];
        $model = new $searchClass();
        if ($model->hasAttribute('status')){
            $where[$tableName.'.status'] = StatusEnum::ACTIVE;
        }
        if ($model->hasAttribute('is_deleted')){
            $where[$tableName.'.is_deleted'] = IsDeletedEnum::NOT_DELETED;
        }


        $data = $this->getQuery()
            ->select(["{$propertyAlias}.{$searchAttr} as value", "{$propertyAlias}.{$searchAttr} as  label","{$tableName}.{$searchKey} as id"])
            ->andWhere(['like', "{$propertyAlias}.".$searchAttr, $term])
            ->andWhere($where)
            ->asArray()
            ->all();

        return $data;
    }
}

