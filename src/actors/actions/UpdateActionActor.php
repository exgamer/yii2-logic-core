<?php
namespace concepture\yii2logic\actors\actions;

use concepture\yii2logic\actors\actions\ActionActor;
use concepture\yii2logic\db\HasPropertyActiveQuery;
use concepture\yii2logic\enum\ScenarioEnum;
use concepture\yii2logic\helpers\AccessHelper;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use ReflectionException;
use Yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\Application;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Выполнение ряда операций при модификации сущности
 *
 * Class UpdateActionActor
 * @package concepture\yii2logic\actors
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateActionActor extends ActionActor
{
    /**
     * Идентификатор редактируемой записи
     *
     * @var integer
     */
    public $id;
    /**
     * Предстваление для рендера
     *
     * @var string
     */
    public $view = 'update';
    /**
     * Редирект после успешной модификации
     *
     * @var string
     */
    public $redirect = 'index';
    /**
     * Сценарий формы
     * 
     * @var string 
     */
    public $scenario = ScenarioEnum::UPDATE;
    /**
     * Нужна ли првоерка на доступ
     * 
     * @var bool 
     */
    public $checkAccess = true;
    /**
     * Действия до загрузки данных в форму
     *
     * @var callable
     */
    public $beforeLoad;
    /**
     * Действия до валидации формы
     *
     * @var callable
     */
    public $beforeValidate;
    /**
     * Действия до выполнения метода сервиса
     *
     * @var callable
     */
    public $beforeServiceAction;
    /**
     * Действия после выполнения метода сервиса
     *
     * @var callable
     */
    public $afterServiceAction;
    /**
     * Действия до рендера
     *
     * @var callable
     */
    public $beforeRender;

    public function run()
    {
        $originModel = $this->getModel($this->id);
        if (!$originModel){
            throw new NotFoundHttpException();
        }

        if ($this->checkAccess && ! AccessHelper::checkAccess('update', ['model' => $originModel])){
            throw new ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $model = $this->getService()->getRelatedForm();
        $model->scenario = $this->scenario;
        $model->setAttributes($originModel->attributes, false);

        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if (is_callable($this->beforeLoad)) {
            call_user_func($this->beforeLoad, $this, $model, $originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if (is_callable($this->beforeValidate)) {
                call_user_func($this->beforeValidate, $this, $model, $originModel);
            }

            if ($model->validate(null, true, $originModel)) {
                if (is_callable($this->beforeServiceAction)) {
                    call_user_func($this->beforeServiceAction, $this, $model, $originModel);
                }

                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) !== false) {
                    if (is_callable($this->afterServiceAction)) {
                        call_user_func($this->afterServiceAction, $this, $model, $originModel, $result);
                    }

                    # todo: объеденить все условия редиректов, в переопределенной функции redirect базового контролера ядра (logic)
                    if ( RequestHelper::isMagicModal()){
                        return $this->getController()->responseJson([
                            'data' => $result,
                        ]);
                    }
                    if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                        $redirectStore = $this->getController()->redirectStoreUrl();
                        if($redirectStore) {
                            return $redirectStore;
                        }

                        # todo: криво пашет
                        return $this->getController()->redirectPrevious([$this->redirect]);
                    }
                }
            }

            $model->addErrors($originModel->getErrors());
        }

        if (is_callable($this->beforeRender)) {
            call_user_func($this->beforeRender, $this, $model, $originModel);
        }

        return $this->getController()->render($this->view, [
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