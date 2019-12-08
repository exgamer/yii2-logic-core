<?php
namespace concepture\yii2logic\actions\web\localized\tree;

use Yii;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\Action;

/**
 * Class CreateAction
 * @package concepture\yii2logic\actions\web\localized\tree
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateAction extends Action
{
    use LocalizedTrait;

    public $view = 'create';
    public $redirect = 'index';
    public $serviceMethod = 'create';

    public function run($locale = null, $parent_id = null)
    {
        $localeId = $this->getConvertedLocale($locale);
        $model = $this->getForm();
        $model->locale = $localeId;
        $model->parent_id = $parent_id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()  && !$this->isReload()) {
            if (($result = $this->getService()->{$this->serviceMethod}($model)) != false) {

                return $this->redirect([$this->redirect, 'id' => $result->id, 'locale' => $localeId, 'parent_id' => $parent_id]);
            }
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }
}