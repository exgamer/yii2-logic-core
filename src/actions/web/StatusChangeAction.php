<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
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
        $model = $this->getService()->findById($id);
        $this->getService()->{$this->serviceMethod}($model, $status);

        return $this->redirect([$this->redirect]);
    }
}