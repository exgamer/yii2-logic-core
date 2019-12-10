<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;

/**
 * Экшен для смены статуса сущности
 *
 * Class StatusChangeAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StatusChangeAction extends Action
{
    public $redirect = 'index';
    public $serviceMethod = 'statusChange';

    public function run($id, $status)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        $this->getService()->{$this->serviceMethod}($model, $status);

        return $this->redirect([$this->redirect]);
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