<?php
namespace concepture\yii2logic\actors\actions;

use concepture\yii2logic\actors\actions\ActionActor;
use concepture\yii2logic\db\HasPropertyActiveQuery;
use concepture\yii2logic\enum\ScenarioEnum;
use concepture\yii2logic\forms\Form;
use concepture\yii2logic\helpers\AccessHelper;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use ReflectionException;
use Yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
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
    /**
     * Форма
     *
     * @var Form
     */
    public $model;
    /**
     * Результ модель
     *
     * @var ActiveRecord
     */
    public $result;

    public function run()
    {
        $this->model = $this->getService()->getRelatedForm();
        $this->model->scenario = $this->scenario;
        if (method_exists($this->model, 'customizeForm')) {
            $this->model->customizeForm();
        }

        $this->callback($this->beforeLoad);

        if ($this->model->load(Yii::$app->request->post())) {
            $this->callback($this->beforeValidate);

            if ($this->model->validate()) {
                $this->callback($this->beforeServiceAction);

                if (($this->result = $this->getService()->{$this->serviceMethod}($this->model)) !== false) {
                    $this->callback($this->afterServiceAction);

                    # todo: объеденить все условия редиректов, в переопределенной функции redirect базового контролера ядра (logic)
                    if ( RequestHelper::isMagicModal()){
                        return $this->getController()->responseJson([
                            'data' => $this->result,
                        ]);
                    }
                    if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                        $redirectStore = $this->getController()->redirectStoreUrl();
                        if($redirectStore) {
                            return $redirectStore;
                        }

                        # todo: криво пашет
                        return $this->getController()->redirectPrevious([$this->redirect, 'id' => $this->result->id]);
                    } else {
                        return $this->getController()->redirect(['update', 'id' => $this->result->id]);
                    }
                }
            }
        }

        $this->callback($this->beforeRender);

        return $this->getController()->render($this->view, ArrayHelper::merge([
            'model' => $this->model,
        ], $this->getViewParams()));
    }
}