<?php
namespace concepture\yii2logic\actors\actions\domain;

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

    public function run()
    {
        if (! $this->edited_domain_id) {
            $this->edited_domain_id = $this->domain_id;
        }

        //Для случая создания сущности, когда у домена указаны используемые языки версий, чтобы подставить верную связку домена и языка
        Yii::$app->domainService->resolveLocaleId($this->edited_domain_id, $this->locale_id, $this->getController()->domainByLocale);

        $originModel = $this->getModel($this->id, $this->edited_domain_id, $this->locale_id);
        if (! $originModel){
            throw new NotFoundHttpException();
        }

        if ($this->checkAccess && ! AccessHelper::checkAccess($this->id, ['model' => $originModel])){
            throw new \yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $model = $this->getService()->getRelatedForm();
        $model->scenario = $this->scenario;
        $model->setAttributes($originModel->attributes, false);
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if (! $model->domain_id) {
            $model->domain_id = $this->edited_domain_id;
        }

        if (property_exists($model, 'locale_id') && ! $model->locale_id) {
            $model->locale_id = $this->locale_id;
        }

        if (is_callable($this->beforeLoad)) {
            call_user_func($this->beforeLoad, $model, $originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if (is_callable($this->beforeValidate)) {
                call_user_func($this->beforeValidate, $model, $originModel);
            }

            if ($model->validate(null, true, $originModel)) {
                if (is_callable($this->beforeServiceAction)) {
                    call_user_func($this->beforeServiceAction, $model, $originModel);
                }

                if (($result = $this->getService()->{$this->getServiceMethod()}($model, $originModel)) !== false) {
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

        return $this->getController()->render($this->view, [
            'model' => $model,
            'originModel' => $originModel,
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
        if (! $model){

            return $originModelClass::clearFind()->where(['id' => $id])->one();
        }

        return $model;
    }
}