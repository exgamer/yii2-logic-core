<?php

namespace concepture\yii2logic\actions\web\v2\domain;

use concepture\yii2logic\helpers\AccessHelper;
use Yii;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use concepture\yii2logic\enum\ScenarioEnum;
use yii\db\ActiveQuery;

/**
 * Экшен для обновления сущности
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateAction extends Action
{
    /**
     * @var string
     */
    public $view = 'update';
    /**
     * @var string
     */
    public $redirect = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'update';
    /**
     * @var string
     */
    public $scenario = ScenarioEnum::UPDATE;
    /**
     * @var null|\Closure
     */
    public $originModelNotFoundCallback = null;

    /**
     * @param integer $id
     * @param integer $domain_id
     *
     * @return string HTML
     */
    public function run($id, $domain_id, $locale_id = null)
    {
        $originModel = $this->getModel($id, $domain_id, $locale_id);
        if (! $originModel){
            if (! $this->originModelNotFoundCallback) {
                throw new NotFoundHttpException();
            }

            if (is_callable($this->originModelNotFoundCallback)){
                return call_user_func($this->originModelNotFoundCallback, $this);
            }
        }

        if (! AccessHelper::checkAccess($this->id, ['model' => $originModel])){
            throw new \yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $model = $this->getForm();
        $model->scenario = $this->scenario;
        $model->setAttributes($originModel->attributes, false);
        $model->domain_id = $domain_id;
        if (property_exists($model, 'locale_id')) {
            $model->locale_id = $locale_id;
        }

        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)) {
                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) !== false) {
                    # todo: объеденить все условия редиректов, в переопределенной функции redirect базового контролера ядра (logic)
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

                        # todo: криво пашет
                        return $this->redirectPrevious([$this->redirect]);
                    }
                }
            }

            $model->addErrors($originModel->getErrors());
        }

        return $this->render($this->view, [
            'model' => $model,
            'originModel' => $originModel,
            'domain_id' => $domain_id,
            'locale_id' => $locale_id,
        ]);
    }


    /**
     * Возвращает локализованную сущность с домена если текущей ломане нет атрибуты будут пустые
     *
     * @param $id
     * @param integer $domain_id
     * @return ActiveRecord
     * @throws ReflectionException
     */
    protected function getModel($id, $domain_id, $locale_id = null)
    {
        $originModelClass = $this->getService()->getRelatedModel();
        $fields = $originModelClass::uniqueField();
        $model = $this->getService()->getOneByCondition(function(ActiveQuery $query) use($id, $domain_id, $locale_id, $fields) {
            $query->andWhere(['id' => $id]);
            if (is_array($fields) && count($fields) > 1) {
                $query->applyPropertyUniqueValue(['domain_id' => $domain_id, 'locale_id' => $locale_id]);
            }else {
                $query->applyPropertyUniqueValue($domain_id);
            }
        });
        if (! $model){

            return $originModelClass::clearFind()->where(['id' => $id])->one();
        }

        return $model;
    }
}