<?php
namespace concepture\yii2logic\actors\actions\domain;

use concepture\yii2logic\actors\actions\ActionActor;
use concepture\yii2logic\enum\ScenarioEnum;
use concepture\yii2logic\forms\Form;
use concepture\yii2logic\helpers\AccessHelper;
use concepture\yii2logic\models\interfaces\HasDomainByLocalesPropertyInterface;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use ReflectionException;
use Yii;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\Application;
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
     * Использовать ли clearFind если запись не найдена
     *
     * @var bool
     */
    public $useClearFind = true;

    public $serviceMethod = 'update';

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

    public function run()
    {
        if (! $this->edited_domain_id) {
            $this->edited_domain_id = $this->domain_id;
        }

        //Для случая создания сущности, когда у домена указаны используемые языки версий, чтобы подставить верную связку домена и языка
        Yii::$app->domainService->resolveLocaleId($this->edited_domain_id, $this->locale_id, $this->getController()->domainByLocale);

        $this->originModel = $this->getModel($this->id, $this->edited_domain_id, $this->locale_id);
        if (! $this->originModel){
            throw new NotFoundHttpException();
        }

        if ($this->checkAccess && ! AccessHelper::checkAccess('update', ['model' => $this->originModel])){
            throw new \yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $this->model = $this->getService()->getRelatedForm();
        $this->model->scenario = $this->scenario;
        $this->model->setAttributes($this->originModel->attributes, false);
        if (method_exists($this->model, 'customizeForm')) {
            $this->model->customizeForm($this->originModel);
        }

        if (! $this->model->domain_id) {
            $this->model->domain_id = $this->edited_domain_id;
        }

        if (property_exists($this->model, 'locale_id') && ! $this->model->locale_id) {
            $this->model->locale_id = $this->locale_id;
        }

        $this->callback($this->beforeLoad);

        if ($this->model->load(Yii::$app->request->post())) {
            $this->originModel->setAttributes($this->model->attributes);
            $this->callback($this->beforeValidate);

            if ($this->model->validate(null, true, $this->originModel)) {
                $this->callback($this->beforeServiceAction);

                if (($this->result = $this->getService()->{$this->getServiceMethod()}($this->model, $this->originModel)) !== false) {
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

        return $this->getController()->render($this->view, [
            'model' => $this->model,
            'originModel' => $this->originModel,
            'domain_id' => $this->domain_id,
            'locale_id' => $this->locale_id,
            'edited_domain_id' => $this->edited_domain_id
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
                $params = ['domain_id' => $domain_id];
                if ($locale_id) {
                    $params['locale_id'] = $locale_id;
                }

                $query->applyPropertyUniqueValue($params);
            }else {
                $query->applyPropertyUniqueValue($domain_id);
            }
        });
        if (! $model && $this->useClearFind){
            $model = $originModelClass::clearFind()->where(['id' => $id])->one();
            if ($model && $model instanceof HasDomainByLocalesPropertyInterface) {
                $domainModel = $this->getService()->getOneByCondition(function (ActiveQuery $query) use ($id, $domain_id, $locale_id, $fields) {
                    $query->andWhere(['id' => $id]);
                    $query->applyPropertyUniqueValue(['domain_id' => $domain_id]);
                });
                if ($domainModel) {
                    $domainModel->loadUpdatedFieldsToModel($model);
                }
            }
        }

        return $model;
    }
}