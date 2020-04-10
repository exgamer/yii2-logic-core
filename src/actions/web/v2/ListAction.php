<?php

namespace concepture\yii2logic\actions\web\v2;

use Yii;
use yii\helpers\ArrayHelper;
use concepture\yii2logic\actions\Action;

/**
 * Экшен для полчения списка для выпадающего списка
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ListAction extends Action
{
    /**
     * @inheritDoc
     */
    public function run($term = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(! $term) {
            return [];
        }

        $searchClass = $this->getSearchClass();
        $searchModel = Yii::createObject($searchClass);
        $searchAttribute = $searchClass::getListSearchAttribute();
        $searchModel->{$searchAttribute} = $term;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return ArrayHelper::map($dataProvider->getModels(), $searchClass::getListSearchKeyAttribute(), $searchAttribute);
    }
}