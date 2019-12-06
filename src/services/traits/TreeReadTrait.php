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
    public function getParentByTree($id)
    {
        $treeModelClass = $this->getTreeModelClass();
        $sql =
            "
            SELECT * FROM {$this->getTableName()} o
            JOIN {$treeModelClass::tableName()} ot ON (o.id = ot.parent_id)
            WHERE ot.child_id = :ID
            ORDER BY ot.level DESC
        ";
        $command = $this->getDb()->createCommand($sql);
        $command->bindValue(':ID', $id);

        return $command->queryAll();
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
            throw new Exception($modelClass. " must have getTreeModelClass function, use ". HasTreeTrait::class);
        }

        return $modelClass::getTreeModelClass();
    }
}

