<?php
namespace concepture\yii2logic\actions\web\v2;

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
    public $redirect = 'index';
    public $serviceMethod = 'create';
    
    public function run()
    {
        $model = $this->getForm();
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (($result = $this->getService()->{$this->serviceMethod}($model)) != false) {

                return $this->redirect([$this->redirect, 'id' => $result->id]);
            }
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }
}