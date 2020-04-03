<?php

namespace concepture\yii2logic\actions\web;

use Yii;
use concepture\yii2logic\actions\Action;
use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use concepture\yii2logic\enum\ScenarioEnum;

/**
 * Экшен для обновления сущности
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateAction extends Action
{
    public $view = 'update';
    public $redirect = 'index';
    public $serviceMethod = 'update';
    public $scenario = ScenarioEnum::UPDATE;

    /**
     * @inheritDoc
     */
    public function run($id)
    {
        $originModel = $this->getModel($id);
        if (!$originModel){
            throw new NotFoundHttpException();
        }

        $model = $this->getForm();
        $model->scenario = $this->scenario;
        $this->processModel($model, $originModel);
        $model->setAttributes($originModel->attributes, false);
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)  && !$this->isReload()) {
                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) != false) {
                    $redirectParams = [$this->redirect, 'id' => $originModel->id];
                    $this->extendRedirectParams($redirectParams);

                    return $this->redirect($redirectParams);
                }
            }

            $model->addErrors($originModel->getErrors());
        }

        return $this->render($this->view, [
            'model' => $model,
            'originModel' => $originModel,
        ]);
    }

    /**
     * Для доп обработки модели
     * @param $model
     * @param $originModel
     */
    protected function processModel($model, $originModel){}

    /**
     * Для расширения парметров редиректа
     * @param $redirectParams
     */
    protected function extendRedirectParams(&$redirectParams){}

    /**
     * Возвращает модель для редактирования
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