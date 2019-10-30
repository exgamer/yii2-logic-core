<?php
namespace concepture\yii2logic\services\traits;

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
     * Возвращает дочерние записи по дереву
     *
     * @param $id
     * @return array
     * @throws Exception
     */
    public function getChildsByTree($id)
    {
        $treeModel = $this->getTreeModelClass();
        $childs = $treeModel::find()->andWhere(['parent_id' => $id])->indexBy('child_id')->all();

        return $childs;
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
     * Возвращает класс модели дерева
     *
     * return string
     * @throws Exception
     */
    public function getTreeModelClass()
    {
        $modelClass = $this->getRelatedModelClass();
        if (! method_exists($modelClass, "getTreeModelClass")){
            throw new Exception($modelClass. " must have getTreeModelClass function ");
        }

        return $modelClass::getTreeModelClass();
    }
}

