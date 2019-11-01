<?php
namespace concepture\yii2logic\controllers\web\localized;

use concepture\yii2logic\actions\web\CreateLocalizedAction;
use concepture\yii2logic\actions\web\IndexLocalizedAction;
use concepture\yii2logic\actions\web\UpdateLocalizedAction;
use concepture\yii2logic\actions\web\DeleteAction;
use concepture\yii2logic\actions\web\ViewLocalizedAction;
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
            'index' => IndexLocalizedAction::class,
            'create' => CreateLocalizedAction::class,
            'update' => UpdateLocalizedAction::class,
            'view' => ViewLocalizedAction::class,
            'delete' => DeleteAction::class,
        ];
    }
}
