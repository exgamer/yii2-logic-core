<?php
namespace concepture\yii2logic\actions\rest;

use Yii;
use concepture\yii2logic\actions\Action;
use concepture\yii2logic\helpers\AccessHelper;
use ReflectionException;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;

/**
 * Class DeleteAction
 * @package concepture\yii2logic\actions\rest
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DeleteAction extends Action
{
    public $serviceMethod = 'delete';

    public function run($id)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        if (! AccessHelper::checkAccess($this->id, ['model' => $model])){
            throw new \yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $this->getService()->{$this->serviceMethod}($model);

        Yii::$app->getResponse()->setStatusCode(204);
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