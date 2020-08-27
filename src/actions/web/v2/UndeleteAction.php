<?php

namespace concepture\yii2logic\actions\web\v2;

use concepture\yii2logic\actions\Action;
use concepture\yii2logic\helpers\AccessHelper;
use kamaelkz\yii2admin\v1\enum\FlashAlertEnum;
use ReflectionException;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;

/**
 * Экшен для восстановления нефизически удаленной сущности
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UndeleteAction extends Action
{
    /**
     * @var string
     */
    public $redirect = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'undelete';

    /**
     * @param integer $id
     */
    public function run($id)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        if (! AccessHelper::checkAccess($this->id, ['model' => $model])){
            throw new \yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $result = $this->getService()->{$this->serviceMethod}($model);
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
     * Возвращает модель для удаления
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