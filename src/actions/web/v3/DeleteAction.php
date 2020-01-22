<?php
namespace concepture\yii2logic\actions\web\v3;

use concepture\yii2logic\actions\Action;
use ReflectionException;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;

/**
 * @todo эксперимент пока не юзать
 *
 * Экшен для удаления сущности
 *
 * Class DeleteAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DeleteAction extends Action
{
    public $redirect = 'index';
    public $serviceMethod = 'delete';

    public function run($id)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }
        $redirectParams = $this->getRedirectParams($model);
        $this->getService()->{$this->serviceMethod}($model);

        return $this->redirect($redirectParams);
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