<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\traits\LocalizedTrait;

/**
 * Экшен для создания сущности с локализацией
 *
 *
 * Class CreateLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateLocalizedAction extends CreateAction
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