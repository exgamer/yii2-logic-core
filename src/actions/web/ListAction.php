<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\helpers\ArrayHelper;

/**
 *
 * @author CitizenZet
 */
class ListAction extends Action
{

    public function run($term = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(!$term){
            return [];
        }
        $searchClass = $this->getSearchClass();
        $searchModel = new $searchClass();
        $searchAttribute = $searchClass::getListSearchAttribute();
        $searchModel->{$searchAttribute} = $term;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return ArrayHelper::map($dataProvider->getModels(), $searchClass::getListSearchKeyAttribute(), $searchAttribute);
    }
}