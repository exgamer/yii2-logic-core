<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;

/**
 *
 * @author CitizenZet
 */
class UpdateAction extends Action
{
    public $view = 'update';
    public $redirect = 'view';
    public $serviceMethod = 'update';

    public function run($id)
    {
        $originModel = $this->getService()->findById($id);
        if (!$originModel){
            throw new NotFoundHttpException();
        }
        $model = $this->getForm();
        $model->setAttributes($originModel->attributes, false);
        if ($model->load(Yii::$app->request->post())) {
            $originModel->load($model->attributes);
            if ($originModel->validate()) {
                try {
                    if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) != false) {
                        return $this->redirect([$this->redirect, 'id' => $originModel->id]);
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return $this->render($this->view, [
            'model' => $model,
            'originModel' => $originModel,
        ]);
    }
}