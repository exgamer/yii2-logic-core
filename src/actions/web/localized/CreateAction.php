<?php
namespace concepture\yii2logic\actions\web\localized;

use Yii;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\Action;

/**
 * Экшен для создания сущности с локализацией
 *
 *
 * Class CreateLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateAction extends Action
{
    use LocalizedTrait;

    public $view = 'create';
    public $redirect = 'index';
    public $serviceMethod = 'create';

    public function run($locale = null)
    {
        $localeId = $this->getConvertedLocale($locale);
        $model = $this->getForm();
        $model->locale = $localeId;
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()  && !$this->isReload()) {
            if (($result = $this->getService()->{$this->serviceMethod}($model)) != false) {

                return $this->redirect([$this->redirect, 'id' => $result->id, 'locale' => $localeId]);
            }
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }
}