<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\Action;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

/**
 *
 * @author CitizenZet
 */
class AutocompleteListAction extends Action
{

    public function run($term = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(!$term){
            return [];
        }
        $searchClass = $this->getSearchClass();
        $searchKey = $searchClass::getListSearchKeyAttribute();
        $searchAttr = $searchClass::getListSearchAttribute();
        $data = $searchClass::find()
            ->select(["{$searchAttr} as value", "{$searchAttr} as  label","{$searchKey} as id"])
            ->where(['like', $searchAttr, $term])
            ->asArray()
            ->all();
        
        return $data;
    }
}