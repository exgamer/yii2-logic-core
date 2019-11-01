<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\db\Exception;

/**
 * Экшен для создания сущности
 *
 * Class CreateAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateAction extends Action
{
    public $view = 'create';
    public $redirect = 'view';
    public $serviceMethod = 'create';
    
    public function run()
    {
        $model = $this->getForm();
        $this->processModel($model);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (($result = $this->getService()->{$this->serviceMethod}($model)) != false) {
                return $this->redirect([$this->redirect, 'id' => $result->id]);
            }
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }

    /**
     * Для доп обработки модели
     * @param $model
     */
    protected function processModel($model){}
}