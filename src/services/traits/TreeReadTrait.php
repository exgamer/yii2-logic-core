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
    public function getParentsByTree($id)
    {
        $modelClass = $this->getRelatedModelClass();
        $treeModelClass = $this->getTreeModelClass();
        $traits = class_uses($modelClass);
        if (! isset($traits[HasTreeTrait::class])){
            throw new Exception($modelClass . " must use " . HasTreeTrait::class);
        }

        return $this->getAllByCondition(function (ActiveQuery $query) use ($id, $treeModelClass){
            $query->join("JOIN", $treeModelClass::tableName() . " ot", "{$this->getTableName()}. id = ot.parent_id");
            $query->andWhere("ot.child_id = :ID", [':ID' => $id]);
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
        $treeModel = $this->getTreeModelClass();

        return $treeModel::find()->andWhere(['parent_id' => $id])->indexBy('child_id')->all();
    }

    /**
     * Возвращает ID дочерних элементов по дереву
     *
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getChildsIdsByTree($id)
    {
        $childs = $this->getChildsByTree($id);

        return array_keys($childs);
    }


    /**
     * Возвращает массив даных дочерних элементов для выпадающих списков
     *
     * @param integer $parent_id
     * @return array
     * @throws Exception
     */
    public function getChildsDropDownList($parent_id)
    {

        return $this->getDropDownList(['parent_id' => $parent_id]);
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
        $childsIds = $this->getChildsIdsByTree($id);
        if (empty($childsIds)){
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

