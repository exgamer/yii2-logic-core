<?php
namespace concepture\yii2logic\controllers\web\localized;

use concepture\yii2logic\actions\web\CreateLocalizedAction;
use concepture\yii2logic\actions\web\UpdateLocalizedAction;
use concepture\yii2logic\actions\web\DeleteAction;
use concepture\yii2logic\actions\web\IndexAction;
use concepture\yii2logic\actions\web\ViewAction;
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
            'create' => CreateLocalizedAction::class,
            'update' => UpdateLocalizedAction::class,
            'view' => ViewAction::class,
            'delete' => DeleteAction::class,
        ];
    }
}
