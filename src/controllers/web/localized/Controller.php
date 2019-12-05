<?php
namespace concepture\yii2logic\controllers\web\localized;

use concepture\yii2logic\actions\web\localized\CreateAction;
use concepture\yii2logic\actions\web\localized\DeleteAction;
use concepture\yii2logic\actions\web\localized\IndexAction;
use concepture\yii2logic\actions\web\localized\UpdateAction;
use concepture\yii2logic\actions\web\localized\ViewAction;
use concepture\yii2logic\controllers\web\Controller as Base;

/**
 * Базовый веб контроллер для локализованных сущностей
 *
 * Class Controller
 * @package concepture\yii2logic\controllers\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Controller extends Base
{
    public function actions()
    {
        return [
            'index' => IndexAction::class,
            'create' => CreateAction::class,
            'update' => UpdateAction::class,
            'view' => ViewAction::class,
            'delete' => DeleteAction::class,
        ];
    }
}
