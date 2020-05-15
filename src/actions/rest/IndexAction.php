<?php
namespace concepture\yii2logic\actions\rest;

use concepture\yii2logic\actions\Action;
use Yii;

/**
 * http://site.loc/api/entity?fields=id,caption
 * http://site.loc/api/entity/index?per_page=10
 * http://site.loc/api/entity/index?page=1
 * http://site.loc/api/entity/index?sort=-id,caption
 *
 *
 * Class IndexAction
 * @package concepture\yii2logic\actions\rest
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

        if (isset($requestParams['per_page'])) {
            $this->pageSize = $requestParams['per_page'];
            unset($requestParams['per_page']);
        }

        if (isset($requestParams['page'])) {
            $this->page = $requestParams['page'];
            unset($requestParams['page']);
        }

        return  $this->getService()->{$this->serviceMethod}($requestParams, $this->dataProviderConfig(), $searchModel, '');
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