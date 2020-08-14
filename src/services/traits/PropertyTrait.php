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
     * Возвращает пропертюшку по дефолному значению уникального поля
     *
     * @param $id
     * @return mixed
     */
    public function getPropertyByDefaultUniqueValue($condition)
    {
        $modelClass = $this->getRelatedModelClass();
        $models = $modelClass::findProperties($condition, function (\concepture\yii2logic\db\ActiveQuery $query) use($modelClass) {
            $propertyUniqueAttribute = $modelClass::uniqueField();
            $propertyUniqueAttributeValue = $modelClass::uniqueFieldValue();
            $query->andWhere(
                [
                    $propertyUniqueAttribute => $propertyUniqueAttributeValue
                ]
            );

        });

        if (! $models) {
            return null;
        }

        return array_shift($models);
    }

    /**
     * Возвращает список аписей дял копирования по уникальному полю
     *
     * @param string $key
     * @param string $value
     * @param bool $current
     * @return array
     */
    public function getListForCopyFrom($key = 'id' , $value = 'name', $current = false)
    {
        $condition = [];
        $model = $this->getRelatedModel();
        $propertyClass = $model::getPropertyModelClass();
        $propertyModel = Yii::createObject($propertyClass);
        $table = $this->getTableName();
        $propertyTable = $propertyModel::tableName();
        $propertyAlias = $model::propertyAlias();
        $propertyUniqueAttribute = $model::uniqueField();
//        if ($propertyModel->hasAttribute('status')) {
//            $condition[$propertyAlias . '.status'] = StatusEnum::ACTIVE;
//        }

        if ($propertyModel->hasAttribute('is_deleted')){
            $condition[$propertyAlias . '.is_deleted'] = IsDeletedEnum::NOT_DELETED;
        }

//        if (! isset($condition[$propertyAlias . '.status']) && $model->hasAttribute('status')){
//            $condition['status'] = StatusEnum::ACTIVE;
//        }

        if (! isset($condition[$propertyAlias . '.is_deleted']) && $model->hasAttribute('is_deleted')){
            $condition['is_deleted'] = IsDeletedEnum::NOT_DELETED;
        }

        $currentModels = $this->getAllByCondition($condition);
        if ($current){
            $currentModels = ArrayHelper::toArray($currentModels);

            return  ArrayHelper::map($currentModels, function ($array, $default) use ($key){
                return $array[$key];
            }, $value);
        }

        $currentModels = ArrayHelper::map($currentModels, $key, $value);
        $currentModelsIds = array_keys($currentModels);
        if (empty($currentModelsIds)){
            $currentModelsIds[] = 0;
        }

        $extConditionSql = '';
        if (! empty($condition)){
            $extConditionSql.= " AND ";
            $tmp = [];
            foreach ($condition as $attr => $val){
                $tmp[] = $attr . "=" . $val;
            }

            $extConditionSql .=implode(" AND ", $tmp);
        }

        $sql = "SELECT * FROM {$propertyTable} {$propertyAlias} JOIN {$table} a ON a.id={$propertyAlias}.entity_id  WHERE {$propertyAlias}.entity_id NOT IN (".implode(',', $currentModelsIds).") AND {$propertyAlias}.`default`=1 {$extConditionSql}";
        $result = $this->getDb()->createCommand($sql)->queryAll();

        return  ArrayHelper::map($result, function ($array, $default) use ($propertyUniqueAttribute){
            return $array['entity_id']."-".$array[$propertyUniqueAttribute];
        }, $value);
    }

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

