<?php
namespace concepture\yii2logic\actions\web\v3;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\helpers\Url;

/**
 * @todo эксперимент пока не юзать
 *
 * Экшен для вывода списка
 *
 * Class IndexAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexAction extends Action
{
    /**
     * @var string
     */
    public $view = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'getDataProvider';

    /**
     * @var bool
     */
    public $storeUrl = true;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->rememberUrl();
        $searchModel = $this->getService()->getRelatedSearchModel();
        $this->extendSearch($searchModel);
        $searchModel->load(Yii::$app->request->queryParams);
        $this->setQueryParams($searchModel);
        $dataProvider =  $this->getService()->{$this->serviceMethod}([], [], $searchModel);
        if($this->storeUrl) {
            $this->getController()->storeUrl();
        }

        return $this->render($this->view, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Для доп обработки search модели
     * @param $searchModel
     */
    protected function extendSearch($searchModel){}
}