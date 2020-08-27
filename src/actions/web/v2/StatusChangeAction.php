<?php
namespace concepture\yii2logic\actions\web\v2;

use concepture\yii2logic\actions\Action;
use concepture\yii2logic\helpers\AccessHelper;
use kamaelkz\yii2admin\v1\enum\FlashAlertEnum;
use ReflectionException;
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
    public function run($id, $status)
    {
        $model = $this->getModel($id);
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
     * Возвращает модель для редактирования
     *
     * @param $id
     * @return ActiveRecord
     * @throws ReflectionException
     * @throws ReflectionException
     * @throws ReflectionException
     */
    protected function getModel($id)
    {
        return $this->getService()->findById($id);
    }
}