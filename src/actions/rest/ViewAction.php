<?php
namespace concepture\yii2logic\actions\rest;

use concepture\yii2logic\actions\Action;
use ReflectionException;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;

/**
 * Class ViewAction
 * @package concepture\yii2logic\actions\rest
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ViewAction extends Action
{
    public function run($id)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        return $model;
    }

    /**
     * Возвращает модель для редактирования
     *
     * @param $id
     * @return ActiveRecord
     * @throws ReflectionException
     * @throws ReflectionException
     */
    protected function getModel($id)
    {
        return $this->getService()->findById($id);
    }
}