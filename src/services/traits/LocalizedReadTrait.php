<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use Yii;

/**
 * Треит для подключения к сервисам локализованных сущностей
 *
 * Trait LocalizedReadTrait
 * @package concepture\yii2logic\services\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait LocalizedReadTrait
{
    /**
     * Получить локализованную сущность по md5 hash seo_name
     *
     * @param $seo_name
     * @return array
     */
    public function getByLocalizedSeoNameMd5Hash($seo_name)
    {

        return $this->getByLocalized("seo_name_md5_hash", md5($seo_name));
    }

    /**
     * Получить локализованную сущность по локализованному аттрибуту
     *
     * @param $attribute
     * @param $value
     * @return array
     */
    public function getByLocalized($attribute, $value)
    {
        return $this->getOneByCondition([
            "p." . $attribute => $value
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
            ->select(["p.{$searchAttr} as value", "p.{$searchAttr} as  label","{$tableName}.{$searchKey} as id"])
            ->andWhere(['like', "p.".$searchAttr, $term])
            ->andWhere($where)
            ->asArray()
            ->all();

        return $data;
    }
}

