<?php
namespace concepture\yii2logic\actions\web\localized;

use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\web\CreateAction as Base;

/**
 * Экшен для создания сущности с локализацией
 *
 *
 * Class CreateLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateAction extends Base
{
    use LocalizedTrait;

    protected function processModel($model)
    {
        $model->locale = $this->getConvertedLocale();
    }

    protected function extendRedirectParams(&$redirectParams)
    {
        $redirectParams['locale'] = $this->getConvertedLocale();
    }
}