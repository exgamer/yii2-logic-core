<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;

/**
 * Class UpdateLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateLocalizedAction extends Action
{
    public $view = 'update';
    public $redirect = 'view';
    public $serviceMethod = 'update';

    public function run($id, $locale = "ru")
    {
        $originModelClass = $this->getService()->getRelatedModelClass();
        $originModelClass::$current_locale = $locale;
        $originModel = $originModelClass::find("with")->where(['id' => $id])->one();
        if (!$originModel){
            throw new NotFoundHttpException();
        }
        $model = $this->getForm();
        $model->locale = $locale;
        $model->setAttributes($originModel->attributes, false);
        $model->setAttributes($originModel->getLocalized(null, true), false);
        if ($model->load(Yii::$app->request->post())) {
            $originModel->load($model->attributes);
            if ($originModel->validate()) {
                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) != false) {
                    return $this->redirect([$this->redirect, 'id' => $originModel->id]);
                }
            }
        }

        return $this->render($this->view, [
            'model' => $model,
            'originModel' => $originModel
        ]);
    }
}