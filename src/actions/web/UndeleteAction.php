<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use ReflectionException;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;

/**
 * Экшен для восстановления нефизически удаленной сущности
 *
 * Class UndeleteAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UndeleteAction extends Action
{
    public $redirect = 'index';
    public $serviceMethod = 'undelete';

    public function run($id)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }
        $this->getService()->{$this->serviceMethod}($model);

        return $this->redirect([$this->redirect]);
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