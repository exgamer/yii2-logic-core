<?php
namespace concepture\yii2logic\actions\web\v2\domain;

use concepture\yii2logic\actions\Action;
use concepture\yii2logic\helpers\AccessHelper;
use kamaelkz\yii2admin\v1\enum\FlashAlertEnum;
use ReflectionException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;

/**
 * Экшен для смены статуса сущности
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StatusChangeAction extends Action
{
    /**
     * @var string
     */
    public $redirect = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'statusChange';

    /**
     * @param integer $id
     * @param integer $status
     */
    public function run($id, $status, $domain_id, $edited_domain_id = null, $locale_id = null)
    {
        if (! $edited_domain_id) {
            $edited_domain_id = $domain_id;
        }

        //Для случая создания сущности, когда у домена указаны используемые языки версий, чтобы подставить верную связку домена и языка
        Yii::$app->domainService->resolveLocaleId($edited_domain_id, $locale_id, $this->controller->domainByLocale);

        $model = $this->getModel($id, $edited_domain_id, $locale_id);
        if (! $model){
            throw new NotFoundHttpException();
        }

        if (! AccessHelper::checkAccess($this->id, ['model' => $model])){
            throw new \yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $result = $this->getService()->{$this->serviceMethod}($model, $status);

        $controller = $this->getController();

        if ($this->redirect) {
            if ($result) {
                $controller->setSuccessFlash();
            } else {
                $controller->setErrorFlash();
            }
            return $this->redirect([$this->redirect]);
        }

        if ($result) {
            return $controller->responseNotify(FlashAlertEnum::SUCCESS, $controller->getSuccessFlash());
        }
        return $controller->responseNotify(FlashAlertEnum::WARNING, $controller->getErrorFlash());
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