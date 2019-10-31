<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\db\Exception;

/**
 * Class CreateLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateLocalizedAction extends Action
{
    public $view = 'create';
    public $redirect = 'view';
    public $serviceMethod = 'create';

    public function run($locale = "ru")
    {
        $model = $this->getForm();
        $model->locale = $locale;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (($result = $this->getService()->{$this->serviceMethod}($model)) != false) {
                return $this->redirect([$this->redirect, 'id' => $result->id]);
            }
        }

        return $this->render($this->view, [
            'model' => $model,
            'params' => Yii::$app->request->getQueryParams(),
        ]);
    }
}