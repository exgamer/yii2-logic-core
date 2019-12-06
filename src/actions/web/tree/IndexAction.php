<?php
namespace concepture\yii2logic\actions\web\tree;

use concepture\yii2logic\actions\Action;
use Yii;

/**
 * Экшен для вывода списка
 *
 * Class IndexAction
 * @package cconcepture\yii2logic\actions\web\tree
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexAction extends Action
{
    public $view = 'index';
    public $serviceMethod = 'getDataProvider';

    public function run($parent_id = null)
    {
        $searchClass = $this->getSearchClass();
        $searchModel = new $searchClass();
        $searchModel->parent_id = $parent_id;
        $this->extendSearch($searchModel);
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider =  $this->getService()->{$this->serviceMethod}(Yii::$app->request->queryParams);

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