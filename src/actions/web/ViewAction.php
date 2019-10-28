<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use yii\web\NotFoundHttpException;

/**
 * Class ViewAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ViewAction extends Action
{

    public $view = 'view';

    public function run($id)
    {
        $model = $this->getService()->findById($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }
}