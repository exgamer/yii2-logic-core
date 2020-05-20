<?php

namespace concepture\yii2logic\actions\web;

use Yii;
use concepture\yii2logic\actions\Action;
use concepture\yii2logic\enum\ScenarioEnum;


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
    public $scenario = ScenarioEnum::INSERT;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $model = $this->getForm();
        $model->scenario = $this->scenario;
        $this->processModel($model);
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()  && ! $this->isReload()) {
            if (($result = $this->getService()->{$this->serviceMethod}($model)) != false) {
                $redirectParams = [$this->redirect, 'id' => $result->id];
                $this->extendRedirectParams($redirectParams);

                return $this->redirectPrevious($redirectParams);
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

    /**
     * Для расширения парметров редиректа
     * @param $redirectParams
     */
    protected function extendRedirectParams(&$redirectParams){}
}