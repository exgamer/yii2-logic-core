<?php
namespace concepture\yii2logic\controllers\web\localized\tree;

use concepture\yii2logic\actions\web\localized\tree\CreateAction;
use concepture\yii2logic\actions\web\localized\tree\DeleteAction;
use concepture\yii2logic\actions\web\localized\tree\IndexAction;
use concepture\yii2logic\actions\web\localized\tree\UpdateAction;
use concepture\yii2logic\actions\web\localized\ViewAction;
use concepture\yii2logic\controllers\web\Controller as Base;

/**
 * Базовый веб контроллер для локализованных сущностей с деревьями
 *
 * Class Controller
 * @package concepture\yii2logic\controllers\web\tree
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
