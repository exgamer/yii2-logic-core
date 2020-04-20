<?php
namespace concepture\yii2logic\actions\web\localized\tree;

use concepture\yii2logic\helpers\AccessHelper;
use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;

/**
 * Class UpdateAction
 * @package concepture\yii2logic\actions\web\localized\tree
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateAction extends Action
{
    use LocalizedTrait;

    public $view = 'update';
    public $redirect = 'index';
    public $serviceMethod = 'update';
    public $originModelNotFoundCallback = null;

    public function run($id, $locale = null, $parent_id = null)
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

        if (! AccessHelper::checkAccess($this->id, ['model' => $originModel])){
            throw new yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $model = $this->getForm();
        $model->setAttributes($originModel->attributes, false);
        $model->locale = $this->getConvertedLocale($locale);
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->afterLoad();
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)  && !$this->isReload()) {
                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) !== false) {
                    if ( RequestHelper::isMagicModal()){
                        return $this->controller->responseJson([
                            'data' => $result,
                        ]);
                    }
                    if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                        return $this->redirect([$this->redirect, 'id' => $originModel->id, 'locale' => $model->locale, 'parent_id' => $parent_id]);
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
        $originModelClass = $this->getService()->getRelatedModelClass();
        $originModelClass::setLocale($locale);
        $model = $this->getService()->findById($id);
        if (! $model){

            return $originModelClass::clearFind()->where(['id' => $id])->one();
        }

        return $model;
    }
}