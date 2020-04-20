<?php
namespace concepture\yii2logic\actions\web\tree;

use concepture\yii2logic\actions\Action;
use concepture\yii2logic\helpers\AccessHelper;
use ReflectionException;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;

/**
 * Экшен для удаления сущности
 *
 * Class DeleteAction
 * @package cconcepture\yii2logic\actions\web\tree
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DeleteAction extends Action
{
    public $redirect = 'index';
    public $serviceMethod = 'delete';

    public function run($id, $parent_id = null)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        if (! AccessHelper::checkAccess($this->id, ['model' => $model])){
            throw new yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $this->getService()->{$this->serviceMethod}($model);

        return $this->redirect([$this->redirect, 'parent_id' => $parent_id]);
    }

    /**
     * Возвращает модель для удаления
     *
     * @param $id
     * @return ActiveRecord
     * @throws ReflectionException
     */
    protected function getModel($id)
    {
        return $this->getService()->findById($id);
    }
}