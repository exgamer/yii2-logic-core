<?php
namespace concepture\yii2logic\controllers\web\tree;

use concepture\yii2logic\actions\web\tree\CreateAction;
use concepture\yii2logic\actions\web\tree\DeleteAction;
use concepture\yii2logic\actions\web\tree\IndexAction;
use concepture\yii2logic\actions\web\tree\UpdateAction;
use concepture\yii2logic\actions\web\ViewAction;
use concepture\yii2logic\controllers\web\Controller as Base;

/**
 * Базовый веб контроллер для сущностей с деревьями
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
