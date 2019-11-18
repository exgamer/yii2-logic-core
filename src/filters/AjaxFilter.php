<?php

namespace concepture\yii2logic\filters;

use Yii;
use \yii\base\ActionFilter;
use yii\web\BadRequestHttpException;

/**
 * Фильтр пропускает только ajax запросы
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class AjaxFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if (! Yii::$app->request->isAjax) {

            throw new BadRequestHttpException('Allowed only ajax requests.');
        }

        return parent::beforeAction($action);
    }
}