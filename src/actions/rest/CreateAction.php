<?php

namespace concepture\yii2logic\actions\rest;

use Yii;
use concepture\yii2logic\actions\Action;
use concepture\yii2logic\enum\ScenarioEnum;


/**
 * Class CreateAction
 * @package concepture\yii2logic\actions\rest
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateAction extends Action
{
    public $serviceMethod = 'create';
    public $scenario = ScenarioEnum::INSERT;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $model = $this->getForm();
        $model->scenario = $this->scenario;
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }

        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            if (($result = $this->getService()->{$this->serviceMethod}($model)) != false) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);

                return $result;
            }
        }

        return $model;
    }
}