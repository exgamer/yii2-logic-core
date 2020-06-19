<?php
namespace concepture\yii2logic\actions\web\localized\tree;

use Yii;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;

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
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()  && !$this->isReload()) {
            if (($result = $this->getService()->{$this->serviceMethod}($model)) !== false) {
                if ( RequestHelper::isMagicModal()){
                    return $this->controller->responseJson([
                        'data' => $result,
                    ]);
                }
                if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                    $redirectStore = $this->getController()->redirectStoreUrl();
                    if($redirectStore) {
                        return $redirectStore;
                    }

                    return $this->redirectPrevious([$this->redirect, 'id' => $result->id, 'locale' => $localeId, 'parent_id' => $parent_id]);
                } else {
                    return $this->redirect(['update', 'id' => $result->id, 'locale' => $localeId]);
                }
            }
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }
}