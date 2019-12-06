<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use Yii;

/**
 * Экшен для вывода списка
 *
 * Class IndexAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexAction extends Action
{
    public $view = 'index';
    public $serviceMethod = 'getDataProvider';

    public function run()
    {
        $searchClass = $this->getSearchClass();
        $searchModel = Yii::createObject($searchClass);
        $this->extendSearch($searchModel);
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider =  $this->getService()->{$this->serviceMethod}([], [], $searchModel);

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