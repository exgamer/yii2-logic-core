<?php

namespace concepture\yii2logic\actions\rest;

use concepture\yii2logic\helpers\AccessHelper;
use Yii;
use concepture\yii2logic\actions\Action;
use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use concepture\yii2logic\enum\ScenarioEnum;

/**
 * Class UpdateAction
 * @package concepture\yii2logic\actions\rest
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateAction extends Action
{
    public $serviceMethod = 'update';
    public $scenario = ScenarioEnum::UPDATE;

    /**
     * @inheritDoc
     */
    public function run($id)
    {
        $originModel = $this->getModel($id);
        if (!$originModel){
            if (! $this->originModelNotFoundCallback) {
                throw new NotFoundHttpException();
            }
        }

        if (! AccessHelper::checkAccess($this->id, ['model' => $originModel])){
            throw new \yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $model = $this->getForm();
        $model->scenario = $this->scenario;
        $model->setAttributes($originModel->attributes, false);
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if ($model->load(Yii::$app->request->post(), '')) {
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)) {
                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) != false) {

                    return $result;
                }
            }

            $model->addErrors($originModel->getErrors());
        }

        return $model;
    }

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