<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;

/**
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
        $model = $this->getService()->findById($id);
        $this->getService()->{$this->serviceMethod}($model);

        return $this->redirect([$this->redirect]);
    }
}