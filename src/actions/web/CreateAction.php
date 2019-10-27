<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\db\Exception;

/**
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
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                if (($result = $this->getService()->{$this->serviceMethod}($model)) != false) {
                    return $this->redirect([$this->redirect, 'id' => $result->id]);
                }
            }catch (\Exception $e){

            }
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }
}