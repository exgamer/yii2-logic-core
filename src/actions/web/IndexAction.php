<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\helpers\Url;

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
    /**
     * @var string
     */
    public $searchClass;

    public function run()
    {
        $this->rememberUrl();
        $searchClass = $this->searchClass;
        if (! $searchClass) {
            $searchClass = $this->getSearchClass();
        }

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