<?php
namespace concepture\yii2logic\actions\web\v2\domain;

use Yii;
use yii\web\NotFoundHttpException;
use concepture\yii2logic\actions\Action;

/**
 * Экшен для копирования сущности между доменами
 *
 * Class CopyAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CopyAction extends Action
{
    public $view = 'copy';
    public $serviceMethod = 'copy';

    public function run($id, $domain_id)
    {
        Yii::$app->domainService->setVirtualDomainId($domain_id);
        $originModel = $this->getService()->findById($id);
        Yii::$app->domainService->clearVirtualDomainId();
        if (!$originModel){
            throw new NotFoundHttpException();
        }

        $model = $this->getForm();
        $model->setAttributes($originModel->attributes, false);
        if (method_exists($model, 'loadProperties')) {
            $model->loadProperties($originModel);
        }
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)) {
                if (($result = $this->getService()->{$this->serviceMethod}($model, $originModel)) != false) {

                    return $this->redirect(['index']);
                }
            }

            $model->addErrors($originModel->getErrors());
        }

        return $this->render('copy', [
            'model' => $model,
            'originModel' => $originModel,
        ]);
    }
}