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
use yii\web\NotFoundHttpException;

/**
 * Выполнение ряда операций при создании сущности
 *
 * Class CreateActionActor
 * @package concepture\yii2logic\actors\actions\domain
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateActionActor extends ActionActor
{
    /**
     * Предстваление для рендера
     *
     * @var string
     */
    public $view = 'create';
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
    public $scenario = ScenarioEnum::INSERT;
    /**
     * Нужна ли првоерка на доступ
     *
     * @var bool
     */
    public $checkAccess = true;

    public $serviceMethod = 'create';

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
        $model = $this->getService()->getRelatedForm();
        $model->scenario = $this->scenario;
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }

        if (is_callable($this->beforeLoad)) {
            call_user_func($this->beforeLoad, $model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if (is_callable($this->beforeValidate)) {
                call_user_func($this->beforeValidate, $model);
            }

            if ($model->validate()) {
                if (is_callable($this->beforeServiceAction)) {
                    call_user_func($this->beforeServiceAction, $model);
                }
                
                if (($result = $this->getService()->{$this->serviceMethod}($model)) !== false) {
                    if (is_callable($this->afterServiceAction)) {
                        call_user_func($this->afterServiceAction, $model, $result);
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
                        return $this->getController()->redirectPrevious([$this->redirect, 'id' => $result->id]);
                    } else {
                        return $this->getController()->redirect(['update', 'id' => $result->id]);
                    }
                }
            }
        }

        if (is_callable($this->beforeRender)) {
            call_user_func($this->beforeRender, $model);
        }

        return $this->getController()->render($this->view, [
            'model' => $model,
        ]);
    }
}