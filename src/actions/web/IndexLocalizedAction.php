<?php
namespace concepture\yii2logic\actions\web;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;
use concepture\yii2logic\actions\traits\LocalizedTrait;

/**
 * Экшон для прсомотра списка локализованных сущностей
 *
 * Class IndexLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexLocalizedAction extends IndexAction
{
    use LocalizedTrait;

    protected function extendSearch($searchModel)
    {
        $searchModel::$current_locale = $this->getConvertedLocale();
        //        $searchModel::$by_locale_hard_search = false;
    }
}