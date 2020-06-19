<?php

namespace concepture\yii2logic\actions\web\localized;

use Yii;
use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use concepture\yii2logic\enum\ScenarioEnum;

/**
 * @deprecated
 *
 * Экшон для обновления сущностей с локализациями
 * Class UpdateLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateAction extends Action
{
    use LocalizedTrait;

    public $view = 'update';
    public $redirect = 'index';
    public $serviceMethod = 'update';
    public $scenario = ScenarioEnum::UPDATE;
    public $originModelNotFoundCallback = null;

    /**
     * @inheritDoc
     */
    public function run($id, $locale = null)
    {
        $originModel = $this->getModel($id, $locale);
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
        $model->locale = $this->getConvertedLocale($locale);
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)  && !$this->isReload()) {
                if (($result =$this->getService()->{$this->serviceMethod}($model, $originModel)) !== false) {
                    if ( RequestHelper::isMagicModal()){
                        return $this->controller->responseJson([
                            'data' => $result,
                        ]);
                    }
                    if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                        $redirectStore = $this->getController()->redirectStoreUrl();
                        if($redirectStore) {
                            return $redirectStore;
                        }

                        return $this->redirectPrevious([$this->redirect, 'id' => $originModel->id, 'locale' => $model->locale]);
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
     * Возвращает локализованную сущность с учетом локали если текущей локализации нет атрибуты будут пустые
     *
     *
     * @param $id
     * @param null $locale
     * @return ActiveRecord
     * @throws ReflectionException
     */
    protected function getModel($id, $locale = null)
    {
        $originModelClass = $this->getService()->getRelatedModel();
        $originModelClass::setLocale($locale);
        $model = $this->getService()->findById($id);
        if (! $model){

            return $originModelClass::clearFind()->where(['id' => $id])->one();
        }

        return $model;
    }
}