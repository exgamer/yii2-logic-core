<?php
namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\helpers\ClassHelper;

/**
 * Треит для моделей у которых есть таблица с деревом
 *
 * Trait HasTreeTrait
 * @package concepture\yii2logic\models\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait HasTreeTrait
{
    /**
     * метод должен вызываться в afterSave модели для обновления дерева
     *
     *   public function afterSave($insert, $changedAttributes)
     *   {
     *       $this->bindTree();
     *       return parent::afterSave($insert, $changedAttributes);
     *   }
     *
     * @param bool $is_move
     */
    public function bindTree($is_move=true)
    {
        if($is_move){
            $this->unbindTree();
        }
        $treeModelClass = static::getTreeModelClass();
        $treeTable = $treeModelClass::tableName();
        $sql  = "
                    INSERT INTO {$treeTable} (parent_id,child_id,level,is_root)
                    SELECT at.parent_id,:id,(case when at.level<=0 then 0 else at.level end)+1,
                                    :is_root
                                    FROM {$treeTable} at WHERE at.child_id=:parent_id
                    union all select :id,:id,0,:is_root
                                    ON DUPLICATE KEY UPDATE is_root=VALUES(is_root)
                ";
        static::getDb()->createCommand($sql,
            [
                'id'=>$this->id,
                'parent_id'=>$this->parent_id,
                'is_root'=>($this->parent_id && $this->parent_id>0)?0:1
            ])->execute();
    }

    /**
     * @param bool $fully
     * @return mixed
     */
    public function  unbindTree($fully=false)
    {
        $treeModel=$this->getTreeModelClass();
        return $treeModel::deleteAll("child_id=:child_id".($fully?"":" and child_id!=parent_id"),[
            'child_id'=>$this->id
        ]);
    }

    /**
     * метод должен вызываться в beforeDelete модели для удаления дерева
     *
     *   public function beforeDelete()
     *   {
     *       $this->removeTree();
     *       return parent::beforeDelete();
     *   }
     *
     */
    public function  removeTree()
    {
        $treeModel=$this->getTreeModelClass();
        $treeModel::deleteAll("child_id=:obj_id OR parent_id=:obj_id",[
            'obj_id'=>$this->id
        ]);
        static::updateAll(['parent_id' => 0], 'parent_id = :obj_id' , ['obj_id' => $this->id]);
    }

    /**
     * метод для получения модели дерева
     * модель дерева должна иметь такое же имя с постфиксом Tree
     *
     * @return string
     */
    public static function getTreeModelClass()
    {
        $me = Yii::createObject(static::class);
        $class =  ClassHelper::getRelatedClass($me);

        return $class."Tree";
    }

    public function getParent()
    {
        return $this->hasOne(static::className(), ['id' => 'parent_id'])->from(['parent' => static::tableName()]);
    }
}

