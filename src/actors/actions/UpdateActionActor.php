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
    /**
     * Форма
     *
     * @var Form
     */
    public $model;
    /**
     * Модель
     *
     * @var ActiveRecord
     */
    public $originModel;
    /**
     * Результ модель
     *
     * @var ActiveRecord
     */
    public $result;

    /**
     * @var string
     */
    public $serviceMethod = 'update';

    public function run()
    {
        $this->originModel = $this->getModel($this->id);
        if (!$this->originModel){
            throw new NotFoundHttpException();
        }

        if ($this->checkAccess && ! AccessHelper::checkAccess('update', ['model' => $this->originModel])){
            throw new ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $this->model = $this->getService()->getRelatedForm();
        $this->model->scenario = $this->scenario;
        $this->model->setAttributes($this->originModel->attributes, false);

        if (method_exists($this->model, 'customizeForm')) {
            $this->model->customizeForm($this->originModel);
        }

        $this->callback($this->beforeLoad);

        if ($this->model->load(Yii::$app->request->post())) {
            $this->originModel->setAttributes($this->model->attributes);
            $this->callback($this->beforeValidate);

            if ($this->model->validate(null, true, $this->originModel)) {
                $this->callback($this->beforeServiceAction);

                if (($this->result = $this->getService()->{$this->serviceMethod}($this->model, $this->originModel)) !== false) {
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
                        return $this->getController()->redirectPrevious([$this->redirect]);
                    }
                }
            }

            $this->model->addErrors($this->originModel->getErrors());
        }

        $this->callback($this->beforeRender);

        return $this->getController()->render($this->view, ArrayHelper::merge([
            'model' => $this->model,
            'originModel' => $this->originModel,
        ], $this->getViewParams()));
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