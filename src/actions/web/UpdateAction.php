<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;

/**
 * Экшен для обновления сущности
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateAction extends Action
{
    public $view = 'update';
    public $redirect = 'view';
    public $serviceMethod = 'update';

    public function run($id)
    {
        $originModel = $this->getModel($id);
        if (!$originModel){
            throw new NotFoundHttpException();
        }
        $model = $this->getForm();
        $this->processModel($model, $originModel);
        $model->setAttributes($originModel->attributes, false);
        if ($model->load(Yii::$app->request->post())) {
            $originModel->load($model->attributes);
            if ($originModel->validate()) {
                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) != false) {
                    return $this->redirect([$this->redirect, 'id' => $originModel->id]);
                }
            }
        }

        return $this->render($this->view, [
            'model' => $model,
            'originModel' => $originModel,
        ]);
    }

    /**
     * Для доп обработки модели
     * @param $model
     * @param $originModel
     */
    protected function processModel($model, $originModel){}

    /**
     * Возвращает модель для редактирования
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