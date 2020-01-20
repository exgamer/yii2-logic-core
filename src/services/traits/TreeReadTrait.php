<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\models\traits\HasTreeTrait;
use Exception;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use Yii;

/**
 * Треит содержащий методы для чтения деревьев
 *
 * Trait TreeReadTrait
 * @package concepture\yii2logic\services\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait TreeReadTrait
{
    /**
     * Получение предков объекта
     * @param $id
     * @return array
     * @throws Exception
     */
    public function getParentsByTree($id, $includeCurrent = false)
    {
        $modelClass = $this->getRelatedModelClass();
        $treeModelClass = $this->getTreeModelClass();
        $traits = class_uses($modelClass);
        if (! isset($traits[HasTreeTrait::class])){
            throw new Exception($modelClass . " must use " . HasTreeTrait::class);
        }

        return $this->getAllByCondition(function (ActiveQuery $query) use ($id, $treeModelClass, $includeCurrent){
            $query->join("JOIN", $treeModelClass::tableName() . " ot", "{$this->getTableName()}. id = ot.parent_id");
            $query->andWhere("ot.child_id = :ID", [':ID' => $id]);
            if (! $includeCurrent) {
                $query->andWhere("ot.parent_id != ot.child_id");
            }
            $query->indexBy("id");
        });
    }

    /**
     * Возвращает дочерние записи по дереву
     *
     * @param $id
     * @return array
     * @throws Exception
     */
    public function getChildsByTree($id)
    {
        $modelClass = $this->getRelatedModelClass();
        $treeModelClass = $this->getTreeModelClass();
        $traits = class_uses($modelClass);
        if (! isset($traits[HasTreeTrait::class])){
            throw new Exception($modelClass . " must use " . HasTreeTrait::class);
        }

        return $this->getAllByCondition(function (ActiveQuery $query) use ($id, $treeModelClass){
            $query->join("JOIN", $treeModelClass::tableName() . " ot", "{$this->getTableName()}. id = ot.child_id");
            $query->andWhere("ot.parent_id = :ID", [':ID' => $id]);
            $query->andWhere("ot.parent_id != ot.child_id");
            $query->indexBy("id");
        });
    }

//    /**
//     * Возвращает ID дочерних элементов по дереву
//     *
//     * @param $id
//     * @return mixed
//     * @throws Exception
//     */
//    public function getChildsIdsByTree($id)
//    {
//        $childs = $this->getChildsByTree($id);
//
//        return array_keys($childs);
//    }


    /**
     * Возвращает массив даных дочерних элементов для выпадающих списков
     *
     * @param integer $parent_id
     * @param string $formName
     * @return array
     */
    public function getChildsDropDownList($parent_id, $formName = "")
    {

        return $this->getDropDownList(["parent_id" => $parent_id], $formName);
    }

    /**
     * Проверка на существование дочерних элементов
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function hasChilds($id)
    {
        $childs = $this->getChildsByTree($id);
        if (empty($childs)){
            return false;
        }

        return true;
    }

    /**
     * Возвращает класс модели дерева
     *
     * return string
     * @throws Exception
     */
    public function getTreeModelClass()
    {
        $modelClass = $this->getRelatedModelClass();
        if (! method_exists($modelClass, "getTreeModelClass")){
            throw new Exception($modelClass. " must have getTreeModelClass function, use ". HasTreeTrait::class);
        }

        return $modelClass::getTreeModelClass();
    }
}

