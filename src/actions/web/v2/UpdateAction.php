<?php

namespace concepture\yii2logic\actions\web\v2;

use Yii;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
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
    public $originModelNotFoundCallback = null;

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

            if (is_callable($this->originModelNotFoundCallback)){
                return call_user_func($this->originModelNotFoundCallback, $this);
            }
        }

        $model = $this->getForm();
        $model->scenario = $this->scenario;
        $model->setAttributes($originModel->attributes, false);

        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)) {
                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) !== false) {
                    if ( RequestHelper::isMagicModal()){
                        return $this->controller->responseJson([
                            'data' => $result,
                        ]);
                    }
                    if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                        return $this->redirect([$this->redirect]);
                    }
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