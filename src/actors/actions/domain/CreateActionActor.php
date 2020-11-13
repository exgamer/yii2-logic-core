<?php
namespace concepture\yii2logic\actors\actions\domain;

use concepture\yii2logic\actors\actions\ActionActor;
use concepture\yii2logic\enum\ScenarioEnum;
use concepture\yii2logic\forms\Form;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use Yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\Application;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

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
     * Идентификатор текущего домена
     *
     * @var integer
     */
    public $domain_id;
    /**
     * Идентификатор редактируемого домена
     *
     * @var integer
     */
    public $edited_domain_id;
    /**
     * Идентификатор редактируемого языка
     *
     * @var integer
     */
    public $locale_id;
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

        if (! $this->edited_domain_id) {
            $this->edited_domain_id = $this->domain_id;
        }

        //Для случая создания сущности, когда у домена указаны используемые языки версий, чтобы подставить верную связку домена и языка
        Yii::$app->domainService->resolveLocaleId($this->edited_domain_id, $this->locale_id, $this->getController()->domainByLocale);
        $this->model->domain_id = $this->edited_domain_id;
        if (property_exists($this->model, 'locale_id')) {
            $this->model->locale_id = $this->locale_id;
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
                        return $this->getController()->redirectPrevious([$this->redirect, 'id' => $this->result->id, 'domain_id' => $this->domain_id, 'edited_domain_id' => $this->edited_domain_id]);
                    } else {
                        return $this->getController()->redirect(['update', 'id' => $this->result->id, 'domain_id' => $this->domain_id, 'edited_domain_id' => $this->edited_domain_id]);
                    }
                }
            }
        }

        $this->callback($this->beforeRender);

        return $this->getController()->render($this->view, ArrayHelper::merge([
            'model' => $this->model,
            'domain_id' => $this->domain_id,
            'locale_id' => $this->locale_id,
            'edited_domain_id' => $this->edited_domain_id
        ], $this->getViewParams()));
    }
}