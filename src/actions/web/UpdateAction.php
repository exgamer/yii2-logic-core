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
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)) {
                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) != false) {
                    $redirectParams = [$this->redirect, 'id' => $originModel->id];
                    $this->extendRedirectParams($redirectParams);

                    return $this->redirect($redirectParams);
                }
            }
            $model->addErrors($originModel->getErrors());
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
     * Для расширения парметров редиректа
     * @param $redirectParams
     */
    protected function extendRedirectParams(&$redirectParams){}

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