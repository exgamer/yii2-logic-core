<?php

namespace concepture\yii2logic\filters;

use Yii;
use \yii\base\ActionFilter;
use yii\web\BadRequestHttpException;

/**
 * Фильтр пропускает только pjax запросы
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class PjaxFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if (! Yii::$app->request->isPjax) {

            throw new BadRequestHttpException('Allowed only pjax requests.');
        }

        return parent::beforeAction($action);
    }
}