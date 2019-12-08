<?php
namespace concepture\yii2logic\controllers\web\tree;

use concepture\yii2logic\actions\web\tree\CreateAction;
use concepture\yii2logic\actions\web\tree\DeleteAction;
use concepture\yii2logic\actions\web\tree\IndexAction;
use concepture\yii2logic\actions\web\tree\UpdateAction;
use concepture\yii2logic\actions\web\ViewAction;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\services\Service;
use ReflectionException;
use Yii;
use concepture\yii2logic\controllers\web\Controller as Base;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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

    /**
     * Возвращает класс формы сущности из сервиса
     *
     * @return string
     * @throws ReflectionException
     */
    public function getFormClass()
    {
        return $this->getService()->getRelatedFormClass();
    }

    /**
     * Возвращает сервис сущности
     *
     * @return Service
     */
    public function getService()
    {
        $name = ClassHelper::getServiceName($this, "Controller");

        return Yii::$app->{$name};
    }

    /**
     * Метод для определния нужно ли просто перезагрузить форму/вьюшку
     *
     * @param string $method
     * @return bool
     */
    public function isReload($method = "post")
    {
        $reload = Yii::$app->request->{$method}('reload');
        if ($reload){

            return true;
        }

        return false;
    }
}
