<?php
namespace concepture\yii2logic\actions\rest;

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
    public $serviceMethod = 'getDataProvider';
    public $searchClass;
    public $config = [];
    public $pageSize = 50;
    public $page = 0;

    public function run()
    {
        $searchModel = null;
        if ($this->searchClass) {
            $searchModel = Yii::createObject($this->searchClass);
        }

        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        if (isset($requestParams['pageSize'])) {
            $this->pageSize = $requestParams['pageSize'];
            unset($requestParams['pageSize']);
        }

        if (isset($requestParams['page'])) {
            $this->page = $requestParams['page'];
            unset($requestParams['page']);
        }

        $dataProvider =  $this->getService()->{$this->serviceMethod}($requestParams, $this->dataProviderConfig(), $searchModel, '');
        $models = $dataProvider->getModels();
        Yii::$app->response->headers->set("Total-Count", $dataProvider->getTotalCount());
        Yii::$app->response->headers->set("Page", $dataProvider->getPagination()->getPage() +1);
        Yii::$app->response->headers->set("Page-Count", $dataProvider->getPagination()->getPageCount());
        Yii::$app->response->headers->set("Page-Size", $dataProvider->getPagination()->getPageSize());
        Yii::$app->response->headers->set("Count", $dataProvider->getCount());

        return $models;
    }

    public function dataProviderConfig()
    {
        return [
            'pagination' => [
                'pageSize' => $this->pageSize,
                'pageSizeParam' => false,
                'forcePageParam' => false,
                'page' => $this->page > 0 ? $this->page -1 : $this->page,
                'pageSizeLimit' => [1, 1000]
            ]
        ];
    }
}